<?php
date_default_timezone_set('Europe/Moscow');
session_start();
require_once 'service/DBConnect.php';
$mysqli = getDBConnection();

if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
    header('Location: index.php');
    exit;
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

$stmt = $mysqli->prepare("SELECT * FROM courses limit ? offset ?");
$stmt->bind_param('ii', $limit, $offset);
$stmt->execute();
$courses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt = $mysqli->prepare("SELECT count(course_id) FROM courses");
$stmt->execute();
$totalCourses = $stmt->get_result()->fetch_row()[0];
$totalPages = ceil($totalCourses / $limit);
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Админ панель - курсы</title>
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
                        <a href="courses.php" class="nav-link active">Курсы</a>
                    </div>
                    <div class="nav-item">
                        <a href="students.php" class="nav-link">Студенты</a>
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
        <!-- Кнопка добавить курс -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Курсы</h2>
            <a href="course_add.php" class="btn btn-success">Добавить курс</a>
        </div>
        <!-- Курсы -->
        <?php if (empty($courses)): ?>
            <div class="mx-auto">
                <div style="font-size: 4rem;">📚</div>
                <h3>Курсов пока нет</h3>
                <p>Создайте первый курс, чтобы начать управление</p>
                <a href="course_add.php" class="btn btn-success">Начать</a>
            </div>
        <?php else: ?>
            <div class="row g-3 g-lg-4 mb-4">
                <?php foreach ($courses as $course): ?>
                    <div class="col-12">
                        <div class="card h-100 shadow-sm">
                            <div class="row g-0 h-100">
                                <div
                                    class="col-12 col-md-5 col-lg-4 col-xxl-3 p-3 p-md-0 d-flex">
                                    <img
                                        src="uploads/<?= $course['img'] ?>"
                                        class="mx-auto rounded border border-5 w-100"
                                        alt="<?= $course['name'] ?>" />
                                </div>
                                <div class="col-12 col-md-7 col-lg-8 col-xxl-9">
                                    <div class="card-body d-flex flex-column h-100 p-3 p-md-4">
                                        <div class="h5 card-title"><?= $course['name'] ?? '' ?></div>
                                        <div class="card-text">
                                            <?= $course['description'] ?? '' ?>
                                        </div>
                                        <ul class="list-unstyled">
                                            <li><strong>Часов:</strong> <?= $course['hours'] ?? '' ?></li>
                                            <li><strong>Цена:</strong> <?= $course['price'] ?? '' ?> ₽</li>
                                            <li><strong>Начало:</strong> <?= $course['start_date'] ?? '' ?></li>
                                            <li><strong>Конец:</strong> <?= $course['end_date'] ?? '' ?></li>
                                        </ul>
                                        <div class="mt-auto">
                                            <div
                                                class="d-grid gap-2 d-sm-flex justify-content-sm-start">
                                                <a
                                                    href="lessons.php?id=<?= $course['course_id'] ?>"
                                                    class="btn btn-primary btn-sm">Подробнее</a>
                                                <a
                                                    href="course_edit.php?id=<?= $course['course_id'] ?>"
                                                    class="btn btn-warning btn-sm text-white">Редактировать</a>
                                                <a
                                                    href="course_delete.php?id=<?= $course['course_id'] ?>"
                                                    class="btn btn-danger btn-sm">Удалить</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <!-- Пагинация -->
                <nav aria-label="Пагинация курсов" class="d-flex justify-content-center mb-5">
                    <div class="pagination pagination-sm">
                        <div class="page-item <?= $page === 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page - 1 ?>">«</a>
                        </div>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <div class="page-item <?= $i === $page ? 'active' : '' ?>"><a class=" page-link" href="?page=<?= $i ?>"><?= $i ?></a></div>
                        <?php endfor; ?>
                        <div class="page-item <?= $page === (int)$totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page + 1 ?>">»</a>
                        </div>
                    </div>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>