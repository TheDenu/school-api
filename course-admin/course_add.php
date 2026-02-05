<?php
date_default_timezone_set('Europe/Moscow');
session_start();

if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
    header('Location: index.php');
    exit;
}

require_once '../school-api/service/DBConnect.php';
require_once 'service/coverCreate.php';

$mysqli = getDBConnection();
$errors = [];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $hours = (int)($_POST['hours'] ?? 0);
    $price = (float)($_POST['price'] ?? 0);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    if (empty($name)) $errors['name'] = 'Заполните название';
    if (empty($description)) $errors['description'] = 'Заполните описание';
    if (empty($start_date)) $errors['start_date'] = 'Заполните дату начала';
    if (empty($end_date)) $errors['end_date'] = 'Заполните дату окончания';
    if ($hours < 1 || $hours > 10) $errors['hours'] = 'Часы от 1 до 10';
    if ($price < 100) $errors['price'] = 'Цена минимум 100₽';
    if (strlen($name) > 30) $errors['name'] = 'Название не больше 30 символов';
    if (strlen($description) > 100) $errors['description'] = 'Описание не больше 100 символов';
    if (date('Y-m-d') > $start_date) $errors['start_date'] = 'Дата начала курса должна быть не позже ' . date('d-m-Y');
    if (date('Y-m-d') > $end_date) $errors['end_date'] = 'Дата конца курса должна быть не позже ' . date('d-m-Y');
    if ($start_date > $end_date) $errors['end_date'] = 'Дата конца курса не может быть раньше даты начала';

    $coverPath = null;
    if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новый курс</title>
    <link rel="stylesheet" href="./styles/form.css">
</head>

<body>
    <div class="form-container">
        <h1 class="form-title">➕ Новый курс</h1>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label class="form-label">Название курса <span style="color: #ef4444;">*</span></label>
                <input type="text" class="form-control" name="name">
                <small style="color: #e70d0d;"><?= $errors['name'] ?? ''  ?></small>
            </div>

            <div class="form-group">
                <label class="form-label">Описание курса</label>
                <textarea class="form-control" name="description" rows="4"></textarea>
                <small style="color: #e70d0d;"><?= $errors['description'] ?? ''  ?></small>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Часы <span style="color: #ef4444;">*</span></label>
                    <input type="number" class="form-control" name="hours">
                    <small style="color: #e70d0d;"><?= $errors['hours'] ?? ''  ?></small>
                </div>
                <div class="form-group">
                    <label class="form-label">Цена (₽) <span style="color: #ef4444;">*</span></label>
                    <input type="number" class="form-control" name="price" step="0.01">
                    <small style="color: #e70d0d;"><?= $errors['price'] ?? ''  ?></small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Дата начала <span style="color: #ef4444;">*</span></label>
                    <input type="date" class="form-control" name="start_date">
                    <small style="color: #e70d0d;"><?= $errors['start_date'] ?? ''  ?></small>
                </div>
                <div class="form-group">
                    <label class="form-label">Дата окончания <span style="color: #ef4444;">*</span></label>
                    <input type="date" class="form-control" name="end_date">
                    <small style="color: #e70d0d;"><?= $errors['end_date'] ?? ''  ?></small>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Обложка (JPG, макс. 2МБ) <span style="color: #ef4444;">*</span></label>
                <input type="file" class="form-control" name="img" accept="image/jpeg,image/jpg">
                <small style="color: #e70d0d;"><?= $errors['img'] ?? '' ?></small>
            </div>

            <div class="form-actions">
                <a href="courses.php" class="btn btn-secondary">❌ Отмена</a>
                <button type="submit" class="btn btn-primary">💾 Создать курс</button>
            </div>
        </form>
    </div>
</body>

</html>