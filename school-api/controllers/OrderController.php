<?php

/**
 * Контроллер для управления заказами и платежами
 * Создание заказа, список заказов, webhook платежей, отмена заказа
 */
require_once 'BaseController.php';

class OrderController extends BaseController
{
    /** @var mysqli Подключение к базе данных */
    private $mysqli;

    /**
     * Конструктор - DI для БД
     */
    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    /**
     * Создание заказа на курс
     * POST /api/courses/{id}/buy
     * Проверяет доступность курса и генерирует URL для оплаты
     */
    public function order()
    {
        $course_id = $_GET['id'];
        $user_id = $_SERVER['AUTH_USER_ID'];

        // Проверка ID курса
        if (!$course_id) {
            $this->sendBadRequest('Курс не выбран');
            return;
        }

        // Получаем информацию о курсе
        $stmt = $this->mysqli->prepare("
            SELECT name, start_date, end_date, price 
            FROM courses 
            WHERE course_id = ?
        ");
        $stmt->bind_param('i', $course_id);
        $stmt->execute();
        $course = $stmt->get_result()->fetch_assoc();

        if (!$course) {
            $this->sendNotFound('Курс не найден');
            return;
        }

        $now = date('Y-m-d');

        // Проверяем доступность курса по датам
        if ($now >= $course['start_date']) {
            $this->sendBadRequest('Курс уже начался');
            return;
        }
        if ($now > $course['end_date']) {
            $this->sendBadRequest('Курс завершился');
            return;
        }

        // Создаем заказ (payment_id по умолчанию 1)
        $stmt = $this->mysqli->prepare("
            INSERT INTO orders (user_id, course_id, order_date) 
            VALUES (?,?,?)
        ");
        $stmt->bind_param("iis", $user_id, $course_id, $now);

        if ($stmt->execute()) {
            $order_id = $this->mysqli->insert_id;
            $pay_url = "https://payment.b?order_id={$order_id}&api_host=https://module.b/school-api&price=" . $course['price'];
            $this->sendSuccess(['pay_url' => $pay_url]);
        } else {
            $this->sendBadRequest('Ошибка записи на курс');
        }
    }

    /**
     * Получение списка заказов студента
     * GET /api/orders
     * Возвращает курсы с информацией о статусе оплаты
     */
    public function orders()
    {
        $user_id = $_SERVER['AUTH_USER_ID'] ?? null;

        if (!$user_id) {
            $this->sendUnauthorized();
            return;
        }

        // === ЗАПРОС ЗАКАЗОВ С КУРСАМИ И ПЛАТЕЖАМИ ===
        $stmt = $this->mysqli->prepare("
            SELECT 
                o.order_id,
                o.course_id,
                p.name as payment_status,
                c.name as course_name,
                c.description,
                c.hours,
                c.price,
                c.start_date,
                c.end_date,
                c.img
            FROM orders o
            JOIN courses c ON o.course_id = c.course_id
            JOIN payments p ON o.payment_id = p.payment_id
            WHERE o.user_id = ?
            ORDER BY o.order_date DESC
        ");

        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $courses = [];
        while ($row = $result->fetch_assoc()) {
            $courses[] = [
                'id' => (int)$row['order_id'],
                'payment_status' => $row['payment_status'],
                'course' => [
                    'id' => (int)$row['course_id'],
                    'name' => $row['course_name'],
                    'description' => $row['description'],
                    'hours' => (int)$row['hours'],
                    'img' => $row['img'],
                    'start_date' => date('Y-m-d', strtotime($row['start_date'])),
                    'end_date' => date('Y-m-d', strtotime($row['end_date'])),
                    'price' => number_format((float)$row['price'], 2, '.', '')
                ]
            ];
        }

        $this->sendSuccess(['data' => $courses]);
    }

    /**
     * Webhook от платежной системы
     * POST /api/payment-webhook
     * @param array $input {order_id, status}
     */
    public function payment(array $input)
    {
        $order_id = (int)($input['order_id']);
        $status = $input['status'];
        $id_payment = ($status === 'success') ? 2 : 3;

        $stmt = $this->mysqli->prepare("
            UPDATE orders SET payment_id = ? WHERE order_id = ?
        ");
        $stmt->bind_param('ii', $id_payment, $order_id);
        $stmt->execute();

        $this->sendNoContent(); 
    }

    /**
     * Отмена заказа
     * DELETE /api/orders?id={order_id}
     * Запрещает отмену оплаченных заказов
     * @param array $input JSON данные (не используются)
     */
    public function deleteOrder()
    {
        $order_id = (int)$_GET['id'];

        // Проверяем существование заказа
        $stmt = $this->mysqli->prepare("
            SELECT payment_id FROM orders WHERE order_id = ?
        ");
        $stmt->bind_param('i', $order_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$order = $result->fetch_assoc()) {
            $this->sendNotFound('Order not found');
            return;
        }

        // Нельзя отменить оплаченный заказ
        if ($order['payment_id'] == 2) {
            http_response_code(418);
            echo json_encode(['status' => 'was payed']);
            return;
        }

        // Удаляем заказ
        $deleteStmt = $this->mysqli->prepare("
            DELETE FROM orders WHERE order_id = ?
        ");
        $deleteStmt->bind_param('i', $order_id);

        if ($deleteStmt->execute()) {
            $this->sendSuccess(['status' => 'success']);
        } else {
            $this->sendBadRequest('Ошибка отмены записи');
        }
    }
}
