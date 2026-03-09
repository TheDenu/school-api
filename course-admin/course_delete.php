<?php
session_start();

require_once 'service/DBConnect.php';
$mysqli = getDBConnection();

$course_id = $_GET['id'] ?? null;

if (!$course_id) {
    header('Location: courses.php?msg="курс не выбран"');
    exit;
}

$stmt = $mysqli->prepare("SELECT img FROM courses WHERE course_id = ?");
$stmt->bind_param('i', $course_id);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();

if (!$course) {
    header('Location: courses.php?msg="курс не найден"');
    exit;
}

$stmt = $mysqli->prepare("SELECT order_id FROM orders WHERE course_id = ?");
$stmt->bind_param('i', $course_id);
$stmt->execute();
$result = $stmt->get_result();
$count = $result->num_rows;

if ($count >= 1) {
    header('Location: courses.php?msg="failed"');
    exit;
}
if ($course['img'] && file_exists("./uploads/{$course['img']}")) {
    unlink("./uploads/{$course['img']}");
}

$stmt = $mysqli->prepare("DELETE FROM courses WHERE course_id = ?");
$stmt->bind_param('i', $course_id);
$stmt->execute();
header('Location: courses.php?mgs="success"');
exit;
