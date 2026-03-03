<?php
require_once 'service/DBConnect.php';
$mysqli = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['user_id'];
    $course_id = $_POST['course_id'];

    $stmt = $mysqli->prepare("SELECT full_name, email FROM users WHERE user_id = ?");
    $stmt->bind_param('i', $student_id);
    $stmt->execute();
    $student = $stmt->get_result()->fetch_assoc();

    $stmt = $mysqli->prepare("SELECT * FROM courses WHERE course_id = ?");
    $stmt->bind_param('i', $course_id);
    $stmt->execute();
    $course = $stmt->get_result()->fetch_assoc();

    $json = json_encode([
        'student_id' => $student_id,
        'course_id' => $course_id
    ]);

    $first_number = makeRequest($json);
    $second_number = mt_rand(10000, 99999) . '1';

    $number = $first_number . $second_number;
}

function makeRequest($json)
{
    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => $json,
        CURLOPT_URL => 'https://certificate.local/create-sertificate/'
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
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сертификат № <?= $number ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* ОСНОВНЫЕ ОТСТУПЫ УМЕНЬШЕНЫ ДЛЯ A4 */
        @page {
            size: A4;
            margin: 1cm;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
                font-size: 11pt;
                line-height: 1.3;
            }

            .no-print {
                display: none !important;
            }

            .container {
                max-width: 100% !important;
                padding: 0 !important;
            }

            .print-area {
                box-shadow: none !important;
                margin: 0 !important;
                border: 2px solid #0d6efd !important;
                max-width: none !important;
                width: 100% !important;
            }

            .certificate-header {
                padding: 20px 15px !important;
            }

            .student-data {
                padding: 25px 20px !important;
            }
        }

        .certificate-body {
            background: #f8f9fa;
            padding: 10px 0;
        }

        .print-area {
            background: white;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border: 3px solid #0d6efd;
            max-width: 210mm;
            /* Ширина A4 */
            margin: 10px auto;
        }

        .certificate-header {
            background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);
            color: white;
            padding: 25px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .certificate-number {
            font-size: 1rem;
            background: rgba(255, 255, 255, 0.2);
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            display: inline-block;
            margin-top: 10px;
            letter-spacing: 1px;
        }

        .seal {
            position: absolute;
            top: 15px;
            right: 20px;
            width: 60px;
            height: 60px;
            background: radial-gradient(circle, #ffd700 25%, #ff8c00 55%, transparent 65%);
            border-radius: 50%;
            border: 3px solid #ffd700;
        }

        .student-data {
            padding: 30px 25px;
            background: linear-gradient(to bottom, #ffffff 0%, #f8f9fa 100%);
            line-height: 1.4;
        }

        .course-info {
            font-size: 0.95rem;
        }

        .signature-area {
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px dashed #dee2e6;
        }

        .signature-line {
            height: 40px;
            border-bottom: 1.5px solid #0d6efd;
            margin-bottom: 12px;
        }

        /* МОБИЛЬНАЯ АДАПТАЦИЯ */
        @media (max-width: 768px) {
            .print-area {
                margin: 5px;
                border-width: 2px;
            }

            .certificate-header {
                padding: 20px 15px;
            }

            .student-data {
                padding: 20px 15px;
            }

            .seal {
                width: 50px;
                height: 50px;
                top: 12px;
                right: 15px;
            }
        }
    </style>
</head>

<body class="certificate-body">
    <div class="container-fluid px-0">
        <!-- Кнопки печати -->
        <div class="row justify-content-center no-print mb-3 px-3">
            <div class="col-auto">
                <button class="btn btn-primary btn-sm me-2" onclick="window.print()">
                    <i class="bi bi-printer"></i> Печать
                </button>
                <a href="students.php" class="btn btn-outline-secondary btn-sm">← Студенты</a>
            </div>
        </div>

        <!-- СЕРТИФИКАТ -->
        <div class="print-area">
            <!-- Шапка -->
            <div class="certificate-header">
                <div class="seal"></div>
                <h1 class="h2 fw-bold mb-2">СЕРТИФИКАТ</h1>
                <p class="mb-0 small fw-semibold">о прохождении профессионального обучения</p>
                <div class="certificate-number">№ <?= $number ?></div>
            </div>

            <!-- Данные -->
            <div class="student-data">
                <div class="row mb-4">
                    <div class="col-md-8">
                        <h6 class="text-muted text-uppercase mb-2">Выдан:</h6>
                        <h4 class="fw-bold mb-2"><?= $student['full_name'] ?></h4>
                        <p class="text-muted mb-0 small"><?= $student['email'] ?></p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="text-muted text-uppercase mb-2">Профессиональная переподготовка по программе:</h6>
                        <div class="border rounded p-3 bg-light">
                            <h5 class="fw-bold mb-2"><?= $course['name'] ?></h5>
                            <p class="mb-3 course-info"><?= $course['description'] ?></p>
                            <div class="row small text-muted">
                                <div class="col-sm-6 col-md-3 mb-2">
                                    <strong>Объем:</strong> <?= $course['hours'] ?? '' ?> ч.
                                </div>
                                <div class="col-sm-6 col-md-3 mb-2">
                                    <strong>Срок:</strong> <?= date('d.m.Y', strtotime($course['start_date'] ?? 'now')) ?> - <?= date('d.m.Y', strtotime($course['end_date'] ?? 'now')) ?>
                                </div>
                                <div class="col-sm-6 col-md-3 mb-2">
                                    <strong>Стоимость:</strong> <?= $course['price'] ?> ₽
                                </div>
                                <div class="col-sm-6 col-md-3 mb-2">
                                    <span class="badge bg-success">Завершен</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Подписи -->
                <div class="signature-area">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted text-uppercase small mb-2">Руководитель платформы</h6>
                            <div class="signature-line"></div>
                            <p class="small mb-0">Иванов И.И.</p>
                            <small class="text-muted">Директор</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted text-uppercase small mb-2">Технический специалист</h6>
                            <div class="signature-line"></div>
                            <p class="small mb-0">Петров П.П.</p>
                            <small class="text-muted">Администратор</small>
                        </div>
                    </div>
                    <div class="text-center mt-3 pt-2">
                        <p class="small mb-0">Дата выдачи: <?= date('d.m.Y') ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>