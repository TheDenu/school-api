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

$courseId = $_GET['id'] ?? null;
if (!$courseId || !is_numeric($courseId)) {
    header('Location: courses.php');
    exit;
}

$course = [];
$stmt = $mysqli->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->bind_param('i', $courseId);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc() ?: [];
if (!$course) {
    header('Location: courses.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $hours = (int)($_POST['hours'] ?? 0);
    $price = (float)($_POST['price'] ?? 0);
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';

    $errors = validate($name, $description, $hours, $price, $start_date, $end_date);

    $coverPath = $course['img']; // сохраняем старую
    if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
        $newCoverPath = processCoverImg($_FILES['img']);
        if ($newCoverPath !== false) {
            $coverPath = $newCoverPath;
            if ($course['img'] && file_exists("./uploads/cover/{$course['img']}")) {
                unlink("./uploads/cover/{$course['img']}");
            }
        } else {
            $errors[] = 'Ошибка обработки изображения';
        }
    }

    if (empty($errors)) {
        $stmt = $mysqli->prepare("UPDATE courses SET name=?, description=?, hours=?, price=?, start_date=?, end_date=?, img=? WHERE id=?");
        $stmt->bind_param('ssissssi', $name, $description, $hours, $price, $start_date, $end_date, $coverPath, $courseId);

        if ($stmt->execute()) {
            header('Location: courses.php?success=2');
            exit;
        } else {
            $errors[] = 'Ошибка БД: ' . $stmt->error;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>✏️ Редактировать курс</title>
    <link rel="stylesheet" href="./styles/form.css">
</head>

<body>
    <div class="form-container">
        <h1 class="form-title">✏️ Редактировать курс</h1>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label class="form-label">Название курса <span style="color: #ef4444;">*</span></label>
                <input type="text" class="form-control" name="name" value="<?= $course['name'] ?? '' ?>">
                <small style="color: #e70d0d;"><?= $errors['name'] ?? ''  ?></small>
            </div>

            <div class="form-group">
                <label class="form-label">Описание курса</label>
                <textarea class="form-control" name="description" rows="4" maxlength="100"><?= $course['description'] ?? '' ?></textarea>
                <small style="color: #e70d0d;"><?= $errors['description'] ?? ''  ?></small>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Продолжительность (часы) <span style="color: #ef4444;">*</span></label>
                    <input type="number" class="form-control" name="hours" value="<?= $course['hours'] ?? '' ?>">
                    <small style="color: #e70d0d;"><?= $errors['hours'] ?? ''  ?></small>
                </div>
                <div class="form-group">
                    <label class="form-label">Цена (₽) <span style="color: #ef4444;">*</span></label>
                    <input type="number" class="form-control" name="price" step="0.01" value="<?= $course['price'] ?? '' ?>">
                    <small style="color: #e70d0d;"><?= $errors['price'] ?? ''  ?></small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Дата начала <span style="color: #ef4444;">*</span></label>
                    <input type="date" class="form-control" name="start_date" value="<?= $course['start_date'] ?? '' ?>">
                    <small style="color: #e70d0d;"><?= $errors['start_date'] ?? ''  ?></small>
                </div>
                <div class="form-group">
                    <label class="form-label">Дата окончания <span style="color: #ef4444;">*</span></label>
                    <input type="date" class="form-control" name="end_date" value="<?= $course['end_date'] ?? '' ?>">
                    <small style="color: #e70d0d;"><?= $errors['end_date'] ?? ''  ?></small>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Обложка (JPG, макс. 2МБ)</label>
                <input type="file" class="form-control" name="img" accept="image/jpeg,image/jpg">
                <small style="color: #e70d0d;"><?= $errors['img'] ?? ''  ?></small>
            </div>

            <div class="form-actions">
                <a href="courses.php" class="btn btn-secondary">❌ Отмена</a>
                <button type="submit" class="btn btn-primary">
                    💾 Сохранить изменения
                </button>
            </div>
        </form>
    </div>
</body>

</html>