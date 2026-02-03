<?php

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
