<?php
session_start();
require_once 'service/DBConnect.php';
include 'service/validateLesson.php';
$mysqli = getDBConnection();
$lesson_id = $_GET['lesson_id'];
$course_id = $_GET['course_id'];
$errors = [];

$stmt = $mysqli->prepare("SELECT * FROM lessons WHERE lesson_id = ?");
$stmt->bind_param('i', $lesson_id);
$stmt->execute();
$result = $stmt->get_result();
$lesson = $result->fetch_assoc() ?: [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $hours = (int)($_POST['hours'] ?? 0);
    $video_link = trim($_POST['video_link']);

    $errors = validate($name, $description, $hours, $video_link);

    if (empty($errors)) {
        $stmt = $mysqli->prepare("UPDATE lessons SET name=?, description=?, hours=?, video_link=? WHERE lesson_id=?");
        $stmt->bind_param('ssisi', $name, $description, $hours, $video_link, $lesson_id);

        if ($stmt->execute()) {
            header('Location: lessons.php?msg=success&id=' . $course_id);
            exit;
        } else {
            $errors[] = 'Ошибка БД:' . $stmt->error;
        }
    }
    $error = implode('; ', $errors);
}

?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Админ панель - Редактирование курса</title>
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
                        <a href="courses.html" class="nav-link">Курсы</a>
                    </div>
                    <div class="nav-item">
                        <a href="students.html" class="nav-link">Студенты</a>
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
        <div class="container-fluid mt-5 pt-4">
            <div class="row justify-content-center">
                <div class="card shadow p-0">
                    <div class="card-header bg-primary text-white">
                        <div class="h3 mb-0">Добавить урок</div>
                    </div>
                    <div class="card-body p-4">
                        <?php if (isset($errors['error'])): ?><div class="text-danger"><?= $errors['error'] ?></div><?php endif; ?>
                        <form method="POST" enctype="multipart/form-data" id="lessonsForm">
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="name" class="form-label fw-bold">Название урока
                                            <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name" value="<?= $lesson['name'] ?>">
                                        <small style="color: #e70d0d;"><?= $errors['name'] ?? '' ?></small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label fw-bold">Описание <span class="text-danger">*</span></label>
                                        <textarea class="form-control" name="description" rows="4"><?= $lesson['description'] ?></textarea>
                                        <small style="color: #e70d0d;"><?= $errors['description'] ?? '' ?></small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="hours" class="form-label fw-bold">Длительность <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="hours" value="<?= $lesson['hours'] ?>">
                                        <small style="color: #e70d0d;"><?= $errors['hours'] ?? '' ?></small>

                                    </div>
                                    <div class="mb-3">
                                        <label for="video_link" class="form-label fw-bold">Ссылка на видео</label>
                                        <input type="text" class="form-control" name="video_link" value="<?= $lesson['video_link'] ?? '' ?>">
                                        <small style="color: #e70d0d;"><?= $errors['video_link'] ?? '' ?></small>

                                    </div>
                                </div>
                            </div>
                            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                                <a href="lessons.php?id=<?= $course_id ?>" class="btn btn-danger w-25">Отмена</a>
                                <button type="submit" class="btn btn-primary w-25">
                                    Добавить
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>