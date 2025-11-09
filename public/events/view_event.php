<?php
require_once __DIR__ . '/../../src/php/session_config.php';
require_once __DIR__ . '/../../src/php/db.php';
require_once __DIR__ . '/../../src/php/functions.php';

$q = trim($_GET['q'] ?? '');
$params = [];
$sql = "SELECT e.*, u.name AS organizer_name
        FROM events e
        JOIN users u ON e.organizer_id = u.user_id
        WHERE e.event_date >= CURDATE()";

if ($q !== '') {
    $sql .= " AND (e.title LIKE ? OR e.location LIKE ? OR u.name LIKE ? OR e.description LIKE ?)";
    $like = "%$q%";
    $params = [$like, $like, $like, $like];
}
$sql .= " ORDER BY e.event_date ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$events = $stmt->fetchAll();
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
    <div class="card fade-in">
        <h2>📅 Upcoming Events</h2>
        <form method="GET" action="view_event.php" style="margin-bottom:12px;">
            <input type="text" name="q" placeholder="Search..." value="<?= e($q) ?>" style="padding:8px; width:60%;">
            <button type="submit" class="btn">Search</button>
            <a href="view_event.php" class="btn">Clear</a>
        </form>
    </div>

    <?php foreach ($events as $event): ?>
        <div class="event-card fade-in">
            <h3><?= e($event['title']) ?></h3>
            <p><b>Date:</b> <?= format_date($event['event_date']) ?> <?= format_time($event['event_time']) ?></p>
            <p><b>Location:</b> <?= e($event['location']) ?></p>
            <p><b>Organizer:</b> <?= e($event['organizer_name']) ?></p>
            <p><?= nl2br(e($event['description'])) ?></p>

            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'student'): ?>
                <button class="btn-register" data-event-id="<?= $event['event_id'] ?>">Register</button>
            <?php endif; ?>

            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'organizer' && $_SESSION['user_id'] == $event['organizer_id']): ?>
                <?php
                $start = new DateTime($event['event_date'] . ' ' . ($event['event_time'] ?: '00:00:00'));
                $started = (new DateTime()) >= $start;
                ?>
                <?php if (!$started): ?>
                    <a class="btn" href="edit_event.php?id=<?= $event['event_id'] ?>">Edit</a>
                    <a class="btn" href="delete_event.php?id=<?= $event['event_id'] ?>" onclick="return confirm('Delete this event?')">Delete</a>
                <?php else: ?>
                    <span style="color:gray;">Event started — editing disabled</span>
                <?php endif; ?>

                <form method="POST" action="notify_registrants.php" style="margin-top:8px;">
                    <input type="hidden" name="event_id" value="<?= $event['event_id'] ?>">
                    <input type="text" name="subject" placeholder="Subject" required style="width:45%;">
                    <input type="text" name="message" placeholder="Message" required style="width:45%;">
                    <button type="submit" class="btn">Notify Registrants</button>
                </form>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</body>

</html>