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

    $coverPath = null;
    if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
        $coverPath = processCoverImg($_FILES['img']);
        if ($coverPath === false) $errors[] = 'Ошибка обработки изображения';
    } else {
        $errors[] = 'Обложка обязательна';
    }

    if (empty($errors)) {
        $stmt = $mysqli->prepare("INSERT INTO courses (name, description, hours, price, start_date, end_date, img) VALUES (?,?,?,?,?,?,?)");
        $stmt->bind_param('ssissss', $name, $description, $hours, $price, $start_date, $end_date, $coverPath);

        if ($stmt->execute()) {
            header('Location: courses.php?msg=success');
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
    <title>Новый курс</title>
    <link rel="stylesheet" href="./styles/form.css">
</head>

<body>
    <div class="form-container">
        <h1 class="form-title">➕ Новый курс</h1>
        <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label class="form-label">Название курса <span style="color: #ef4444;">*</span></label>
                <input type="text" class="form-control" name="name" maxlength="30" required>
                <small style="color: #6b7280;">Максимум 30 символов</small>
            </div>

            <div class="form-group">
                <label class="form-label">Описание курса</label>
                <textarea class="form-control" name="description" rows="4" maxlength="100"></textarea>
                <small style="color: #6b7280;">Максимум 100 символов</small>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Часы <span style="color: #ef4444;">*</span></label>
                    <input type="number" class="form-control" name="hours" min="1" max="10" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Цена (₽) <span style="color: #ef4444;">*</span></label>
                    <input type="number" class="form-control" name="price" step="0.01" min="100" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Дата начала <span style="color: #ef4444;">*</span></label>
                    <input type="date" class="form-control" name="start_date" min="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Дата окончания <span style="color: #ef4444;">*</span></label>
                    <input type="date" class="form-control" name="end_date" min="<?= date('Y-m-d') ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Обложка (JPG, макс. 2МБ) <span style="color: #ef4444;">*</span></label>
                <input type="file" class="form-control" name="img" accept="image/jpeg,image/jpg" required>
                <small style="color: #6b7280;">Автоматически создастся миниатюра 300×300</small>
            </div>

            <div class="form-actions">
                <a href="courses.php" class="btn btn-secondary">❌ Отмена</a>
                <button type="submit" class="btn btn-primary">💾 Создать курс</button>
            </div>
        </form>
    </div>
</body>

</html>