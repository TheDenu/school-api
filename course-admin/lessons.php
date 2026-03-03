<?php
session_start();
require_once '../school-api/service/DBConnect.php';
$mysqli = getDBConnection();

$course_id = (int)($_GET['id']);

$stmt = $mysqli->prepare("SELECT name FROM courses WHERE course_id = ?");
$stmt->bind_param('i', $course_id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();

$stmt = $mysqli->prepare("SELECT * FROM lessons WHERE course_id = ?");
$stmt->bind_param('i', $course_id);
$stmt->execute();
$lessons = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Админ панель - уроки</title>
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
        <!-- Кнопка добавить урок -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Уроки курса - <?= $course['name'] ?></h2>
            <a href="lessons_add.html" class="btn btn-success">Добавить урок</a>
        </div>
        <!-- Уроки -->
        <?php if (empty($lessons)): ?>
            <div class="empty-state">
                <div style="font-size: 4rem;">📚</div>
                <h3>Уроков пока нет</h3>
                <p>Создайте первый урок, чтобы начать управление</p>
                <a href="lesson_add.php" class="btn btn-success">Начать</a>
            </div>
        <?php else: ?>
            <div class="row g-3 g-lg-4 mb-4">
                <?php foreach ($lessons as $lesson): ?>
                    <div class="col-12">
                        <div class="card h-100 shadow-sm">
                            <div class="row g-0 h-100">
                                <div class="card-body d-flex flex-column h-100 p-3 p-md-4">
                                    <div class="h5 card-title"><?= $lesson['name'] ?? '' ?></div>
                                    <div class="card-text"><strong>Часов:</strong> <?= $lesson['hours'] ?? '' ?></div>
                                    <div class="card-text mb-3">
                                        <?= $lesson['description'] ?? '' ?>
                                    </div>
                                    <div class="mt-auto">
                                        <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                                            <?php if ($lesson['video_link']): ?>
                                                <a href="<?= $lesson['video_link'] ?? '' ?>" class="btn btn-primary btn-sm">Видео</a>
                                            <?php endif; ?>
                                            <a
                                                href="lesson_edit.php?id<?= $lesson['lesson_id'] ?>"
                                                class="btn btn-warning btn-sm text-white">Редактировать</a>
                                            <a href="lesson_delete.php?id=<?= $lesson['lesson_id'] ?>" class="btn btn-danger btn-sm">Удалить</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>