<?php
session_start();

require_once 'service/DBConnect.php';
$mysqli = getDBConnection();


if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
    header('Location: index.php');
    exit;
}

$stmt = $mysqli->prepare("
    SELECT users.full_name, users.email, users.user_id, courses.name as title, courses.course_id, orders.order_date, payments.name as payment
    FROM orders 
    JOIN users using(user_id) 
    JOIN courses using(course_id)
    JOIN payments using(payment_id)
");
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Админ панель - Студенты</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet" />
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <button
                type="button"
                class="navbar-toggler"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav me-auto">
                    <div class="nav-item">
                        <a href="courses.php" class="nav-link">Курсы</a>
                    </div>
                    <div class="nav-item">
                        <a href="students.php" class="nav-link active">Студенты</a>
                    </div>
                </div>
                <div class="navbar-nav">
                    <div class="nav-item">
                        <a href="logout.php" class="nav-link">Выход</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Студенты</h2>
        </div>
        <div class="card shadow">
            <div class="table-responsive">
                <table class="table table-bordered mb-0 align-middle">
                    <thead class="sticky-top border-dark border-1">
                        <tr>
                            <th
                                scope="col"
                                class="text-center py-3 border-end border-dark border-1 bg-primary text-white">
                                ФИО
                            </th>
                            <th
                                scope="col"
                                class="text-center py-3 border-end border-dark border-1 bg-primary text-white">
                                e-mail
                            </th>
                            <th
                                scope="col"
                                class="text-center py-3 border-end border-dark border-1 bg-primary text-white">
                                Курс
                            </th>
                            <th
                                scope="col"
                                class="text-center py-3 border-end border-dark border-1 bg-primary text-white">
                                Дата записи
                            </th>
                            <th
                                scope="col"
                                class="text-center py-3 border-end border-dark border-1 bg-primary text-white">
                                Статус оплаты
                            </th>
                            <th
                                scope="col"
                                class="text-center py-3 border-end border-dark border-1 bg-primary text-white">
                                Сертификат
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr class="align-middle border border-dark border-1">
                                <td class="fw-semibold bg-secondary-subtle text-center border-end border-dark border-1">
                                    <?= $order['full_name'] ?>
                                </td>
                                <td class="text-center bg-secondary-subtle border-end border-dark border-1">
                                    <?= $order['email'] ?>
                                </td>
                                <td class="text-center bg-secondary-subtle border-end border-dark border-1">
                                    <?= $order['title'] ?>
                                </td>
                                <td class="text-center bg-secondary-subtle border-end border-dark border-1">
                                    <small> <?= $order['order_date'] ?></small>
                                </td>
                                <td class="text-center bg-secondary-subtle border-end border-dark border-1">
                                    <?php
                                    $badgeClass = match ($order['payment']) {
                                        'оплачено' => 'bg-success',
                                        'ожидает оплаты' => 'bg-warning',
                                        'ошибка оплаты', 'отклонено' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= $order['payment'] ?></span>
                                </td>
                                <td class="text-center bg-secondary-subtle">
                                    <form method="POST" action="certificate.php">
                                        <input type="text" name="course_id" value="<?= $order['course_id'] ?>" hidden>
                                        <input type="text" name="user_id" value="<?= $order['user_id'] ?>" hidden>
                                        <input type="submit" value="Распечатать сертификат" class="btn btn-sm btn-primary">
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


</body>

</html>