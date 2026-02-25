<?php
date_default_timezone_set('Europe/Moscow');
session_start();
require_once '../service/DBConnect.php';
$mysqli = getDBConnection();

if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
    header('Location: index.php');
    exit;
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

$stmt = $mysqli->prepare("SELECT * FROM courses");
$stmt->execute();
$courses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$totalCourses = count($courses);
$totalPages = ceil($totalCourses / $limit);
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель - Курсы</title>
    <link rel="stylesheet" href="./styles/main.css">
    <link rel="stylesheet" href="css/style.css">

</head>

<body>
    <div class="admin-container">
        <header class="header">
            <h1>🎓 Управление курсами</h1>
            <p>Создавайте, редактируйте и управляйте образовательными курсами</p>
            <a href="course_add.php" class="btn">➕ Создать новый курс</a>
            <nav>
                <a href="lessons.php">Уроки </a><a href="students.php">Студенты</a>
            </nav>
        </header>

        <?php if (empty($courses)): ?>
            <div class="empty-state">
                <div style="font-size: 4rem;">📚</div>
                <h3>Курсов пока нет</h3>
                <p>Создайте первый курс, чтобы начать управление</p>
                <a href="course_add.php" class="btn">Начать</a>
            </div>
        <?php else: ?>
            <div class="courses-list">
                <?php foreach (array_slice($courses, $offset, $limit) as $course): ?>
                    <div class="course-card">
                        <img src="uploads/cover/<?= htmlspecialchars($course['img']) ?>"
                            alt="<?= htmlspecialchars($course['name']) ?>"
                            class="course-thumb">
                        <div class="course-content">
                            <div class="course-header">
                                <h3 class="course-title"><?= htmlspecialchars($course['name']) ?></h3>

                                <?php if ($course['description']): ?>
                                    <p class="course-description"><?= htmlspecialchars($course['description']) ?></p>
                                <?php endif; ?>

                                <div class="course-meta">
                                    <div class="course-duration"><?= $course['hours'] ?> час.</div>
                                    <div class="course-price">₽<?= number_format($course['price'], 0, ',', ' ') ?></div>
                                </div>

                                <div class="course-dates">
                                    <?= date('d.m.Y', strtotime($course['start_date'])) ?>
                                    — <?= date('d.m.Y', strtotime($course['end_date'])) ?>
                                </div>

                                <div class="course-actions">
                                    <a href="course_edit.php?id=<?= $course['id'] ?>" class="btn-small btn-edit">
                                        ✏️ Редактировать
                                    </a>
                                    <a href="course_delete.php?id=<?= $course['id'] ?>"
                                        class="btn-small btn-delete"
                                        onclick="return confirm('Удалить курс «<?= htmlspecialchars($course['name']) ?>»?')">
                                        🗑️ Удалить
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?>"
                            class="page-link <?= $i === $page ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div style="text-align: center; margin-top: 3rem; padding: 1rem; color: #9ca3af;">
            <a href="index.php" style="color: inherit;">← Выйти из админки</a>
        </div>
    </div>
</body>

</html>