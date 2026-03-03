<?php
session_start();

require_once 'service/DBConnect.php';
$mysqli = getDBConnection();

$lesson_id = $_GET['id'];

$stmt = $mysqli->prepare("SELECT course_id FROM lessons WHERE lesson_id = ?");
$stmt->bind_param('i', $lesson_id);
$stmt->execute();
$course_id = $stmt->get_result()->fetch_row()[0];

$stmt = $mysqli->prepare("SELECT order_id FROM orders WHERE course_id = ?");
$stmt->bind_param('i', $course_id);
$stmt->execute();
$result = $stmt->get_result();
$count = $result->num_rows;

if ($count >= 1) {
    header('Location: lessons.php?msg="failed"&id=' . $course_id);
    exit;
} else {
    $stmt = $mysqli->prepare("DELETE FROM lessons WHERE lesson_id = ?");
    $stmt->bind_param('i', $lesson_id);
    $stmt->execute();
    header('Location: lessons.php?msg="success"&id=' . $course_id);
    exit;
}
