<?php
session_start();
require_once 'service/DBConnect.php';
require_once 'service/validateLesson.php';
$mysqli = getDBConnection();

$course_id = (int)($_GET['course_id']);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $hours = (int)($_POST['hours'] ?? 0);
    $video_link = trim($_POST['video_link'] ?? '');

    $errors = validate($name, $description, $hours, $video_link);

    $stmt = $mysqli->prepare("SELECT lesson_id FROM lessons WHERE course_id = ?");
    $stmt->bind_param('i', $course_id);
    $stmt->execute();
    $count = $stmt->get_result()->num_rows;

    if ($count >= 5) {
        $errors['error'] = 'Курс не нуждается в добавлении уроков';
    }

    if (empty($errors)) {
        $stmt = $mysqli->prepare("INSERT INTO lessons (course_id, name, description, hours, video_link) VALUES (?,?,?,?,?)");
        $stmt->bind_param('issis', $course_id, $name, $description, $hours, $video_link);

        if ($stmt->execute()) {
            header('Location: lessons.php?msg=success&id=' . $course_id);
            exit;
        } else {
            $errors['error'] = 'Ошибка БД:' . $stmt->error;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Админ панель - Добавление урока</title>
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
                                        <input type="text" class="form-control" name="name">
                                        <small style="color: #e70d0d;"><?= $errors['name'] ?? '' ?></small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label fw-bold">Описание <span class="text-danger">*</span></label>
                                        <textarea class="form-control" name="description" rows="4"></textarea>
                                        <small style="color: #e70d0d;"><?= $errors['description'] ?? '' ?></small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="hours" class="form-label fw-bold">Длительность <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="hours">
                                        <small style="color: #e70d0d;"><?= $errors['hours'] ?? '' ?></small>

                                    </div>
                                    <div class="mb-3">
                                        <label for="video_link" class="form-label fw-bold">Ссылка на видео</label>
                                        <input type="text" class="form-control" name="video_link">
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