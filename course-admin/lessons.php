<?php
session_start();
require_once '../school-api/service/DBConnect.php';
$mysqli = getDBConnection();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

$stmt = $mysqli->prepare("SELECT * FROM lessons");
$stmt->execute();
$lessons = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$totalLessons = count($lessons);
$totalPages = ceil($totalLessons / $limit);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель - Уроки</title>
    <link rel="stylesheet" href="./styles/main.css">
</head>

<body>
    <div class="admin-container">
        <header class="header">
            <h1>🎓 Управление уроками</h1>
            <p>Создавайте, редактируйте и управляйте образовательными уроками</p>
            <a href="lesson_add.php" class="btn">➕ Создать новый урок</a>
            <nav>
                <a href="courses.php">Курсы</a><a href=""></a>
            </nav>
        </header>

        <?php if (empty($lessons)): ?>
            <div class="empty-state">
                <div style="font-size: 4rem;">📚</div>
                <h3>Уроков пока нет</h3>
                <p>Создайте первый урок, чтобы начать управление</p>
                <a href="lesson_add.php" class="btn">Начать</a>
            </div>
        <?php else: ?>
            <div class="courses-list">
                <?php foreach (array_slice($lessons, $offset, $limit) as $lesson): ?>
                    <div class="course-card">
                        <a href="<?= htmlspecialchars($lesson['video_link']) ?>">Смотреть видео</a>
                        <div class="course-content">
                            <div class="course-header">
                                <h3 class="course-title"><?= htmlspecialchars($lesson['name']) ?></h3>

                                <?php if ($lesson['description']): ?>
                                    <p class="course-description"><?= htmlspecialchars($lesson['description']) ?></p>
                                <?php endif; ?>

                                <div class="course-actions">
                                    <a href="lesson_edit.php?id=<?= $lesson['id'] ?>" class="btn-small btn-edit">
                                        ✏️ Редактировать
                                    </a>
                                    <a href="lesson_delete.php?id=<?= $lesson['id'] ?>"
                                        class="btn-small btn-delete"
                                        onclick="return confirm('Удалить курс «<?= htmlspecialchars($lesson['name']) ?>»?')">
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