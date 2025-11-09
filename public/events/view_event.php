<?php
session_start();
require_once __DIR__ . '/../../src/php/db.php';

// Fetch all upcoming events
$stmt = $pdo->query("SELECT e.*, u.name AS organizer_name
                     FROM events e
                     JOIN users u ON e.organizer_id = u.user_id
                     WHERE e.event_date >= CURDATE()
                     ORDER BY e.event_date ASC");
$events = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>View Events</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/app.js" defer></script>
</head>

<body>
    <h2>📅 Upcoming College Events</h2>
    <p><a href="../index.php">🏠 Back to Home</a></p>

    <?php if (count($events) === 0): ?>
        <p>No upcoming events yet.</p>
    <?php else: ?>
        <?php foreach ($events as $event): ?>
            <div class="event-card">
                <h3><?= htmlspecialchars($event['title']) ?></h3>
                <p><b>Date:</b> <?= htmlspecialchars($event['event_date']) ?> at <?= htmlspecialchars($event['event_time']) ?></p>
                <p><b>Location:</b> <?= htmlspecialchars($event['location']) ?></p>
                <p><b>Organizer:</b> <?= htmlspecialchars($event['organizer_name']) ?></p>
                <p><?= nl2br(htmlspecialchars($event['description'])) ?></p>

                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'student'): ?>
                    <button class="btn-register" data-event-id="<?= $event['event_id'] ?>">Register</button>
                <?php endif; ?>
            </div>
            <hr>
        <?php endforeach; ?>
    <?php endif; ?>
</body>

</html>