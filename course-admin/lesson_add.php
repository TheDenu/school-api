<?php
session_start();
require_once '../school-api/service/DBConnect.php';
$mysqli = getDBConnection();
$error = '';

$stmt = $mysqli->prepare("SELECT name, id FROM courses");
$stmt->execute();
$courses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_course = $_POST['id_course'];
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $hours = (int)($_POST['hours'] ?? 0);
    $video_link = trim($_POST['video_link'] ?? '');

    $errors = [];
    if (strlen($name) > 50) $errors[] = 'Название не больше 50 символов';
    if ($hours < 1 || $hours > 4) $errors[] = 'Часы от 1 до 4';

    $stmt = $mysqli->prepare("SELECT id FROM lessons WHERE id_course = ?");
    $stmt->bind_param('i', $id_course);
    $stmt->execute();
    $count = $stmt->get_result()->num_rows;

    if($count >= 5){
        $errors[] = 'Курс не нуждается в добавлении уроков';
    }

    if (empty($errors)) {
        $stmt = $mysqli->prepare("INSERT INTO lessons (id_course, name, description, hours, video_link) VALUES (?,?,?,?,?)");
        $stmt->bind_param('issis', $id_course, $name, $description, $hours, $video_link);

        if ($stmt->execute()) {
            header('Location: lessons.php?msg=success');
            exit;
        } else {
            $errors[] = 'Ошибка БД:' . $stmt->error;
        }
    }
    $error = implode('; ', $errors);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новый урок</title>
    <link rel="stylesheet" href="./styles/form.css">
</head>

<body>
    <div class="form-container">
        <h1 class="form-title">➕ Новый урок</h1>
        <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label class="form-label">Выберите курс <span style="color: #ef4444;">*</span></label>
                <select name="id_course" id="id_course" class="form-control">
                    <?php
                    foreach ($courses as $course) {
                        echo '<option value="' . $course['id'] . '">' . $course['name'] . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Название урока <span style="color: #ef4444;">*</span></label>
                <input type="text" class="form-control" name="name" maxlength="50" required>
                <small style="color: #6b7280;">Максимум 50 символов</small>
            </div>

            <div class="form-group">
                <label class="form-label">Описание урока <span style="color: #ef4444;">*</span></label>
                <textarea class="form-control" name="description" rows="4" required></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Часы <span style="color: #ef4444;">*</span></label>
                    <input type="number" class="form-control" name="hours" min="1" max="4" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Ссылка на урок</label>
                    <input type="text" class="form-control" name="video_link">
                </div>
            </div>

            <div class="form-actions">
                <a href="lessons.php" class="btn btn-secondary">❌ Отмена</a>
                <button type="submit" class="btn btn-primary">💾 Создать урок</button>
            </div>
        </form>
    </div>
</body>

</html>