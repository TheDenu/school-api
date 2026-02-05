<?php
session_start();

require_once '../school-api/service/DBConnect.php';
$mysqli = getDBConnection();


if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
    header('Location: index.php');
    exit;
}

$stmt = $mysqli->prepare("
    SELECT users.name, users.email, users.id_user, courses.name as title, courses.id, orders.date_order, statuses_payment.status_payment
    FROM orders 
    JOIN users using(id_user) 
    JOIN courses ON orders.id_course = courses.id 
    JOIN statuses_payment using(id_status_payment)
");
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/style.css">
    <title>Adminka</title>
</head>

<body>
    <div class="container-courses">
        <header class="header">
            <div class="header-top">
                <p class="header-top__name">Admin panel (students)</p>
                <p class="header-top__logout">Выход</p>
            </div>
            <div class="header-bottom">
                <nav class="header-bottom__nav">
                    <a class="header-bottom__nav-a" href="courses.php">Courses</a>
                    <a class="header-bottom__nav-a" href="lessons.php">Lessons</a>
                </nav>
                <form action="">
                </form>
            </div>
        </header>
        <div class="main-student">
            <div class="student-item__header">
                <p class="student-info">FIO</p>
                <p class="student-info">e-mail</p>
                <p class="student-info">Course</p>
                <p class="student-info">Record dates</p>
                <p class="student-info">Paid status</p>
                <p class="student-info">Certificate</p>
            </div>
            <?php foreach ($orders as $order): ?>
                <div class="student-item">
                    <p><?= $order['name'] ?></p>
                    <p><?= $order['email'] ?></p>
                    <p><?= $order['title'] ?></p>
                    <p><?= $order['date_order'] ?></p>
                    <p class="student__paid-info"><?= $order['status_payment'] ?></p>
                    <form class="student-item__right" method="POST" action="certificate.php">
                        <input type="text" name="id_course" value="<?= $order['id'] ?>" hidden>
                        <input type="text" name="id_user" value="<?= $order['id_user'] ?>" hidden>
                        <input type="submit" value="Print the certificate" class="student-item__btn">
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>

</html>