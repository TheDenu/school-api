<?php

/**
 * Контроллер для работы с курсами и уроками
 * Список всех курсов (пагинация), уроки конкретного курса
 */
require_once 'BaseController.php';

class CourseController extends BaseController
{
    /** @var mysqli Подключение к базе данных */
    protected $mysqli;

    /**
     * Конструктор - DI для БД
     */
    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    /**
     * Главный роутер курсов
     * GET /api/courses → список всех курсов (пагинация)
     * GET /api/courses/1 → уроки конкретного курса
     */
    public function coursesHandler(array $input = [])
    {
        if (isset($_GET['id'])) {
            $this->listLessons();
        } else {
            $this->listCourses();
        }
    }

    /**
     * Список всех доступных курсов с пагинацией
     * GET /api/courses?page=1&limit=5
     * Поддерживает параметры: page (1+), limit (1-20)
     */
    public function listCourses()
    {
        // === ПАГИНАЦИЯ ===
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = isset($_GET['limit']) ? min(20, max(1, (int)$_GET['limit'])) : 5;
        $offset = ($page - 1) * $limit;

        // Общее количество курсов
        $count = $this->mysqli->query("SELECT count(*) as total FROM courses");
        $total = $count->fetch_assoc()['total'];

        // Курсы с пагинацией
        $stmt = $this->mysqli->prepare("
            SELECT 
                course_id as id, 
                name, 
                description, 
                hours, 
                img, 
                start_date, 
                end_date, 
                price 
            FROM courses
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $courses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $per_page = count($courses);

        // Форматируем даты и цену
        foreach ($courses as &$course) {
            $course['start_date'] = date('Y-m-d', strtotime($course['start_date']));
            $course['end_date'] = date('Y-m-d', strtotime($course['end_date']));
            $course['price'] = number_format((float)$course['price'], 2, '.', '');
        }

        if ($courses) {
            $this->sendSuccess([
                'data' => $courses,
                'pagination' => [
                    'total' => (int)$total,
                    'current' => $page,
                    'per_page' => $per_page,
                    'last_page' => ceil($total / $limit)
                ]
            ]);
        } else {
            $this->sendBadRequest('Курсы не найдены');
        }
    }

    /**
     * Список уроков конкретного курса
     * GET /api/courses/1
     */
    public function listLessons()
    {
        $course_id = (int)$_GET['id'];

        if (!$course_id) {
            $this->sendBadRequest('ID курса обязателен');
            return;
        }

        $stmt = $this->mysqli->prepare("
            SELECT 
                lesson_id as id, 
                course_id, 
                name, 
                description, 
                video_link, 
                hours 
            FROM lessons 
            WHERE course_id = ?
            ORDER BY lesson_id ASC
        ");
        $stmt->bind_param('i', $course_id);
        $stmt->execute();
        $lessons = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        if ($lessons) {
            $this->sendSuccess(['data' => $lessons]);
        } else {
            $this->sendNotFound('Уроки не найдены');
        }
    }
}
