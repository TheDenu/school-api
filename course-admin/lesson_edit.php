<?php
session_start();
require_once '../school-api/service/DBConnect.php';
include 'service/validateLesson.php';
$mysqli = getDBConnection();
$id_lesson = $_GET['id'];
$errors = [];

$stmt = $mysqli->prepare("SELECT name, id FROM courses");
$stmt->execute();
$courses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt = $mysqli->prepare("SELECT * FROM lessons WHERE id = ?");
$stmt->bind_param('i', $id_lesson);
$stmt->execute();
$result = $stmt->get_result();
$lesson = $result->fetch_assoc() ?: [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_course = $_POST['id_course'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $hours = (int)($_POST['hours'] ?? 0);
    $video_link = trim($_POST['video_link']);

    $errors = validate($name, $description, $hours, $video_link);

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
        <h1 class="form-title">➕ Редактирование урока</h1>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label class="form-label">Выберите курс <span style="color: #ef4444;">*</span></label>
                <select name="id_course" id="id_course" class="form-control" disabled>
                    <?php
                    foreach ($courses as $course) {
                        echo '<option value="' . $course['id'] . '"' . (($course['id'] === $lesson['id_course']) ? 'selected' : '') . '>' . $course['name'] . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Название урока <span style="color: #ef4444;">*</span></label>
                <input type="text" class="form-control" name="name" value="<?= $lesson['name'] ?? '' ?>">
                <small style="color: #ef4444;"><?= $errors['name'] ?? '' ?></small>
            </div>

            <div class="form-group">
                <label class="form-label">Описание урока <span style="color: #ef4444;">*</span></label>
                <textarea class="form-control" name="description" rows="4"><?= $lesson['description'] ?? ''?></textarea>
                <small style="color: #ef4444;"><?= $errors['description'] ?? '' ?></small>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Часы <span style="color: #ef4444;">*</span></label>
                    <input type="number" class="form-control" name="hours" value="<?= $lesson['hours'] ?? '' ?>">
                    <small style="color: #ef4444;"><?= $errors['hours'] ?? '' ?></small>
                </div>
                <div class="form-group">
                    <label class="form-label">Ссылка на урок</label>
                    <input type="text" class="form-control" name="video_link" value="<?= $lesson['video_link'] ?? '' ?>">
                    <small style="color: #ef4444;"><?= $errors['video_link'] ?? '' ?></small>
                </div>
            </div>

            <div class="form-actions">
                <a href="lessons.php" class="btn btn-secondary">❌ Отмена</a>
                <button type="submit" class="btn btn-primary">💾 Сохранить изменения</button>
            </div>
        </form>
    </div>
</body>

</html>