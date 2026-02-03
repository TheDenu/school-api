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

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $hours = (int)($_POST['hours'] ?? 0);
    $price = (float)($_POST['price'] ?? 0);
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';

    $errors = [];
    if (strlen($name) > 30) $errors[] = 'Название не больше 30 символов';
    if (strlen($description) > 100) $errors[] = 'Описание не больше 100 символов';
    if ($hours < 1 || $hours > 10) $errors[] = 'Часы от 1 до 10';
    if ($price < 100) $errors[] = 'Цена минимум 100₽';

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
            header('Location: adminPanel.php?success=2');
            exit;
        } else {
            $errors[] = 'Ошибка БД: ' . $stmt->error;
        }
    }
    $error = implode('; ', $errors);
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
        <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label class="form-label">Название курса <span style="color: #ef4444;">*</span></label>
                <input type="text" class="form-control" name="name" maxlength="30" value="<?= $course['name'] ?? '' ?>" required>
                <small style="color: #6b7280;">Максимум 30 символов</small>
            </div>

            <div class="form-group">
                <label class="form-label">Описание курса</label>
                <textarea class="form-control" name="description" rows="4" maxlength="100"><?= $course['description'] ?? '' ?></textarea>
                <small style="color: #6b7280;">Максимум 100 символов</small>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Продолжительность (часы) <span style="color: #ef4444;">*</span></label>
                    <input type="number" class="form-control" name="hours" min="1" max="10" value="<?= $course['hours'] ?? '' ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Цена (₽) <span style="color: #ef4444;">*</span></label>
                    <input type="number" class="form-control" name="price" step="0.01" min="100" value="<?= $course['price'] ?? '' ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Дата начала <span style="color: #ef4444;">*</span></label>
                    <input type="date" class="form-control" name="start_date" min="<?= date('Y-m-d') ?>" value="<?= $course['start_date'] ?? '' ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Дата окончания <span style="color: #ef4444;">*</span></label>
                    <input type="date" class="form-control" name="end_date" min="<?= date('Y-m-d') ?>" value="<?= $course['end_date'] ?? '' ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Обложка (JPG, макс. 2МБ)</label>
                <input type="file" class="form-control" name="img" accept="image/jpeg,image/jpg">
                <small style="color: #6b7280;">
                    Автоматически создастся миниатюра <code>mpic*.jpg</code> (300×300)
                </small>
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