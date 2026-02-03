<?php
date_default_timezone_set('Europe/Moscow');
session_start();
require_once '../school-api/service/DBConnect.php';
$mysqli = getDBConnection();

$idCourse = $_GET['id'] ?? null;

if(!$idCourse){
    header('Location: courses.php');
    exit;
}else{
    $stmt = $mysqli->prepare("DELETE FROM courses WHERE id = ?");
    $stmt->bind_param('i', $idCourse);
    if($stmt->execute()){
        $msg = 'success';
    }else{
        $msg = 'error';
    }
    header('Location: courses.php?mgs='. $msg);
}
