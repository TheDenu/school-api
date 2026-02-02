<?php
date_default_timezone_set('Europe/Moscow');
session_start();


if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
    header('Location: index.php');
    exit;
}

require_once '../school-api/service/DBConnect.php';
$mysqli = getDBConnection();

$courseId = $_GET['id'] ?? null;
$isEdit = $courseId !== null;
$course = [];
$error = '';

if ($isEdit) {
    $stmt = $mysqli->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->bind_param('i', $courseId);
    $stmt->execute();
    $course = $stmt->get_result()->fetch_assoc();
    $courseImg = $course['img'];
}


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

    $coverPath = $isEdit ? $course['img'] : null;

    if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
        $newCoverPath = processCoverImg($_FILES['img']);
        if ($newCoverPath !== false) {
            $coverPath = $newCoverPath;
            // Удаляем старую при редактировании
            if ($isEdit && $course['img'] && file_exists("./uploads/cover/{$course['img']}")) {
                unlink("./uploads/cover/{$course['img']}");
            }
        } else {
            $errors[] = 'Ошибка обработки изображения';
        }
    }

    if (!$isEdit && empty($coverPath)) {
        $errors[] = 'Обложка обязательна при создании';
    }

    if (empty($errors)) {
        if (!$isEdit) {
            $stmt = $mysqli->prepare("INSERT INTO courses (name, description,hours, price, start_date, end_date, img) VALUES (?,?,?,?,?,?,?)");
            $stmt->bind_param('ssidsss', $name, $description, $hours, $price, $start_date, $end_date, $coverPath);
        } else {
            $stmt = $mysqli->prepare("UPDATE courses SET name=?, description=?, hours=?, price=?, start_date=?, end_date=?, img=? WHERE id=?");
            $stmt->bind_param("ssidsssi", $name, $description, $hours, $price, $start_date, $end_date, $coverPath, $courseId);
        }

        if ($stmt->execute()) {
            header('Location: adminPanel.php?success=1');
            exit;
        } else {
            $errors[] = 'Ошибка БД: ' . $mysqli->error;
        }
    }

    $error = implode('; ', $errors);
}

function processCoverImg($file)
{
    $maxSize = 2 * 1024 * 1024;
    $allowedTypes = ['image/jpeg', 'image/jpg'];

    if ($file['size'] > $maxSize) {
        return false;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);

    if (!in_array($mimeType, $allowedTypes)) {
        return false;
    }

    $uploadDir = './uploads/cover';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $fileName = 'mpic_' . time() . '_' . microtime() . '.jpg';
    $filePath = $uploadDir . '/' . $fileName;

    if (createThumbnail($file['tmp_name'], $filePath, 300, 300)) {
        return $fileName;
    }
}

function createThumbnail($sourcePath, $destPath, $maxWidth, $maxHeight)
{
    // Загружаем изображение
    $imageInfo = getimagesize($sourcePath);
    if (!$imageInfo) return false;

    $sourceWidth = $imageInfo[0];
    $sourceHeight = $imageInfo[1];

    // Вычисляем пропорции (как CSS object-fit: cover)
    $widthRatio = $maxWidth / $sourceWidth;
    $heightRatio = $maxHeight / $sourceHeight;
    $ratio = max($widthRatio, $heightRatio);

    // Центрируем обрезку
    $srcX = ($sourceWidth - $maxWidth / $ratio) / 2;
    $srcY = ($sourceHeight - $maxHeight / $ratio) / 2;

    // Создаем миниатюру
    $thumb = imagecreatetruecolor($maxWidth, $maxHeight);

    switch ($imageInfo['mime']) {
        case 'image/jpeg':
            $source = imagecreatefromjpeg($sourcePath);
            break;
        default:
            return false;
    }

    // Заполняем белым фоном
    $white = imagecolorallocate($thumb, 255, 255, 255);
    imagefill($thumb, 0, 0, $white);

    // Копируем с обрезкой и масштабированием
    imagecopyresampled(
        $thumb,
        $source,
        0,
        0,
        $srcX,
        $srcY,
        $maxWidth,
        $maxHeight,
        $maxWidth / $ratio,
        $maxHeight / $ratio
    );

    $result = imagejpeg($thumb, $destPath, 90);

    return $result;
}

?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isEdit ? 'Редактировать курс' : 'Новый курс' ?></title>
    <style>
        /* Тот же CSS что в adminPanel.php */
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            padding: 3rem;
            width: 100%;
            max-width: 700px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
        }

        .form-title {
            text-align: center;
            color: #4a5568;
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 2.5rem;
        }

        .form-group {
            margin-bottom: 2rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 0.75rem;
            font-size: 1rem;
        }

        .form-control {
            width: 100%;
            padding: 1rem 1.25rem;
            border: 2px solid rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }

        .form-control:focus {
            outline: none;
            border-color: #4dabf7;
            box-shadow: 0 0 0 4px rgba(77, 171, 247, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .form-actions {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            margin-top: 2.5rem;
        }

        .btn {
            padding: 1rem 2.5rem;
            border: none;
            border-radius: 15px;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 160px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4dabf7 0%, #3b82f6 100%);
            color: white;
        }

        .btn-secondary {
            background: rgba(0, 0, 0, 0.1);
            color: #4a5568;
            border: 2px solid rgba(0, 0, 0, 0.1);
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        .error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid #ef4444;
            color: #dc2626;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 2rem;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h1 class="form-title">
            <?= $isEdit ? '✏️ Редактировать курс' : '➕ Новый курс' ?>
        </h1>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" action="courseForm.php?id=<?= $courseId ?>">
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
                <label class="form-label">Обложка (JPG, макс. 2МБ) <?= $isEdit ? '' : '<span style="color: #ef4444;">*</span>' ?></label>
                <input type="file" class="form-control" name="img" accept="image/jpeg,image/jpg">
                <small style="color: #6b7280;">
                    Автоматически создастся миниатюра <code>mpic*.jpg</code> (300×300)
                </small>
            </div>

            <div class="form-actions">
                <a href="adminPanel.php" class="btn btn-secondary">❌ Отмена</a>
                <button type="submit" class="btn btn-primary">
                    💾 <?= $isEdit ? 'Сохранить изменения' : 'Создать курс' ?>
                </button>
            </div>
        </form>
    </div>
</body>

</html>