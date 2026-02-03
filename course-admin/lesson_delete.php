<?php

session_start();
require_once '../school-api/service/DBConnect.php';

$mysqli = getDBConnection();

$idLesson = $_GET['id'];

$stmt = $mysqli->prepare("SELECT id_course FROM lessons WHERE id = ?");
$stmt->bind_param('i', $idLesson);
$stmt->execute();
$idCourse = $stmt->get_result()->fetch_row()[0];

$stmt = $mysqli->prepare("SELECT id_order FROM orders WHERE id_course = ?");
$stmt->bind_param('i', $idCourse);
$stmt->execute();
$result = $stmt->get_result();
$count = $result->num_rows;

if($count >= 1){
    header('Location: lessons.php?msg="failed"');
    exit;
}else{
    $stmt = $mysqli->prepare("DELETE FROM lessons WHERE id = ?");
    $stmt->bind_param('i', $idLesson);
    $stmt->execute();
    header('Location: lessons.php?msg="success"');
    exit;
}
