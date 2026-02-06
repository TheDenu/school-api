<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['id_user'];
    $course_id = $_POST['id_course'];

    $jsonData = json_encode([
        'student_id' => $user_id,
        'course_id' => $course_id
    ]);

    $first_number = makeRequest($jsonData);
    $second_number = mt_rand(10000, 99999) . '1';

    $number = $first_number . $second_number;
}

function makeRequest($jsonData)
{
    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://certificate.local/create-sertificate/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $jsonData
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    $data = json_decode($response, true) ?: [];

    if ($httpCode === 200) {
        return $data['course_number'];
    } else {
        return $data['message'] ?? '';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1>номер сертификата: <?= $number ?></h1>
</body>

</html>