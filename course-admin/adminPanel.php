<?php
date_default_timezone_set('Europe/Moscow');
session_start();
require_once '../school-api/service/DBConnect.php';
$mysqli = getDBConnection();

// Проверка авторизации
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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #333;
            padding: 2rem;
        }

        .admin-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .header h1 {
            color: #4a5568;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .btn {
            display: inline-block;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #4dabf7 0%, #3b82f6 100%);
            color: white;
            text-decoration: none;
            border-radius: 15px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(77, 171, 247, 0.3);
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(77, 171, 247, 0.4);
        }

        /* ✅ НОВЫЕ КАРТОЧКИ: 1 в ряд */
        .courses-list {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .course-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            /* ✅ Flex для двух колонок */
            height: 220px;
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }

        /* ✅ Картинка слева (30%) */
        .course-thumb {
            width: 30%;
            height: 100%;
            object-fit: cover;
            background: linear-gradient(45deg, #e5e7eb, #f3f4f6);
        }

        /* ✅ Контент справа (70%) */
        .course-content {
            flex: 1;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .course-header {
            flex-grow: 1;
        }

        .course-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }

        .course-description {
            color: #6b7280;
            margin-bottom: 1rem;
            line-height: 1.5;
            font-size: 0.95rem;
        }

        .course-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .course-duration {
            color: #10b981;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .course-price {
            font-size: 1.6rem;
            font-weight: 800;
            color: #10b981;
        }

        .course-dates {
            color: #6b7280;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        /* ✅ Кнопки снизу справа */
        .course-actions {
            display: flex;
            gap: 0.75rem;
            justify-content: flex-end;
        }

        .btn-small {
            padding: 0.6rem 1.2rem;
            font-size: 0.9rem;
            text-align: center;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-edit {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            flex: 1;
            max-width: 140px;
        }

        .btn-delete {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            flex: 1;
            max-width: 120px;
        }

        .btn-small:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        /* Пагинация */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .page-link {
            padding: 0.875rem 1.25rem;
            background: rgba(255, 255, 255, 0.9);
            color: #4a5568;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .page-link:hover,
        .page-link.active {
            background: linear-gradient(135deg, #4dabf7 0%, #3b82f6 100%);
            color: white;
            transform: translateY(-2px);
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #9ca3af;
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .header h1 {
                font-size: 2rem;
            }

            .course-card {
                flex-direction: column;
                height: auto;
            }

            .course-thumb {
                width: 100%;
                height: 200px;
            }

            .course-actions {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <div class="admin-container">
        <header class="header">
            <h1>🎓 Управление курсами</h1>
            <p>Создавайте, редактируйте и управляйте образовательными курсами</p>
            <a href="courseForm.php" class="btn">➕ Создать новый курс</a>
        </header>

        <?php if (empty($courses)): ?>
            <div class="empty-state">
                <div style="font-size: 4rem;">📚</div>
                <h3>Курсов пока нет</h3>
                <p>Создайте первый курс, чтобы начать управление</p>
                <a href="courseForm.php" class="btn">Начать</a>
            </div>
        <?php else: ?>
            <div class="courses-list">
                <?php foreach (array_slice($courses, $offset, $limit) as $course): ?>
                    <div class="course-card">
                        <!-- ✅ Картинка слева -->
                        <img src="uploads/cover/<?= htmlspecialchars($course['img']) ?>"
                            alt="<?= htmlspecialchars($course['name']) ?>"
                            class="course-thumb">

                        <!-- ✅ Контент справа -->
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
                                    <a href="courseForm.php?id=<?= $course['id'] ?>" class="btn-small btn-edit">
                                        ✏️ Редактировать
                                    </a>
                                    <a href="delete_course.php?id=<?= $course['id'] ?>"
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
            <a href="logout.php" style="color: inherit;">← Выйти из админки</a>
        </div>
    </div>
</body>

</html>