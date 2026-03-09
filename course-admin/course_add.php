<?php
date_default_timezone_set('Europe/Moscow');
session_start();

if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
    header('Location: index.php');
    exit;
}

require_once '../school-api/service/DBConnect.php';
require_once 'service/coverCreate.php';
require_once 'service/validateCourse.php';

$mysqli = getDBConnection();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $hours = (int)($_POST['hours'] ?? 0);
    $price = (float)($_POST['price'] ?? 0);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $errors = validate($name, $description, $hours, $price, $start_date, $end_date);

    $coverPath = null;
    if (empty($errors) && isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
        $coverPath = processCoverImg($_FILES['img']);
        if ($coverPath === false) $errors['img'] = 'Ошибка обработки изображения';
    } else {
        $errors['img'] = 'Обложка обязательна';
    }

    if (empty($errors)) {
        $stmt = $mysqli->prepare("INSERT INTO courses (name, description, hours, price, start_date, end_date, img) VALUES (?,?,?,?,?,?,?)");
        $stmt->bind_param('ssissss', $name, $description, $hours, $price, $start_date, $end_date, $coverPath);

        if ($stmt->execute()) {
            header('Location: courses.php?msg=success');
            exit;
        } else {
            $errors['error'] = 'Ошибка БД: ' . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Админ панель - Добавление курса</title>
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
                        <div class="h3 mb-0">
                            Добавить курс
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-lg-9">
                                    <div class="mb-3">
                                        <label for="name" class="form-label fw-bold">Название курса
                                            <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name">
                                        <small class="text-danger"><?= $errors['name'] ?? ''  ?></small>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Описание курса</label>
                                        <textarea class="form-control" name="description" rows="4"></textarea>
                                        <small class="text-danger"><?= $errors['description'] ?? ''  ?></small>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="hours" class="form-label fw-bold">Длительность
                                                    <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" name="hours">
                                                <small style="color: #e70d0d;"><?= $errors['hours'] ?? ''  ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="price" class="form-label fw-bold">Цена <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" name="price" step="0.01">
                                                <small style="color: #e70d0d;"><?= $errors['price'] ?? ''  ?></small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="start_date" class="form-label fw-bold">Дата начала
                                                    <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control" name="start_date">
                                                <small style="color: #e70d0d;"><?= $errors['start_date'] ?? ''  ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="end_date" class="form-label fw-bold">Дата конца
                                                    <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control" name="end_date">
                                                <small style="color: #e70d0d;"><?= $errors['end_date'] ?? ''  ?></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="mb-4">
                                        <label for="img" class="form-label fw-bold">Обложка курса <span class="text-danger">*</span></label>
                                        <input
                                            type="file"
                                            class="form-control"
                                            id="img"
                                            name="img"
                                            accept="image/*"
                                            required />
                                        <img
                                            id="preview"
                                            class="mt-2 mx-auto img-thumbnail rounded border"
                                            style="max-height: 250px; display: none" />
                                        <small style="color: #e70d0d;"><?= $errors['img'] ?? '' ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                                <a href="courses.html" class="btn btn-danger w-25">Отмена</a>
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
    <script>
        // Предпросмотр изображения
        document.getElementById('img').addEventListener('change', function(e) {
            const file = e.target.files[0]
            const preview = document.getElementById('preview')

            if (file) {
                const reader = new FileReader()
                reader.onload = function(e) {
                    preview.src = e.target.result
                    preview.style.display = 'block'
                }
                reader.readAsDataURL(file)
            }
        })
    </script>
</body>

</html>