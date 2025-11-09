<?php
require_once __DIR__ . '/../../src/php/session_config.php';
require_once __DIR__ . '/../../src/php/db.php';
require_once __DIR__ . '/../../src/php/functions.php';

$events = $pdo->query("SELECT e.*, u.name AS organizer_name
  FROM events e JOIN users u ON e.organizer_id=u.user_id
  WHERE e.event_date >= CURDATE() ORDER BY e.event_date ASC")->fetchAll();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>View Events</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/app.js" defer></script>
</head>

<body>
    <h2>📅 Upcoming Events</h2>
    <p><a href="../index.php" class="btn">🏠 Home</a></p>
    <?php foreach ($events as $e): ?>
        <div class="event-card fade-in">
            <h3><?= e($e['title']) ?></h3>
            <p><b>Date:</b> <?= format_date($e['event_date']) ?> <?= format_time($e['event_time']) ?></p>
            <p><b>Location:</b> <?= e($e['location']) ?></p>
            <p><b>Organizer:</b> <?= e($e['organizer_name']) ?></p>
            <p><?= nl2br(e($e['description'])) ?></p>
            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'student'): ?>
                <button class="btn-register" data-event-id="<?= $e['event_id'] ?>">Register</button>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</body>

</html>