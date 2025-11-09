<?php
require_once __DIR__ . '/../../src/php/session_config.php';
require_once __DIR__ . '/../../src/php/db.php';
require_once __DIR__ . '/../../src/php/auth.php';
require_once __DIR__ . '/../../src/php/functions.php';

ensure_role('organizer');

$event_id = intval($_GET['id'] ?? 0);
if (!$event_id) {
    header('Location: view_event.php');
    exit;
}

// fetch event and check ownership
$stmt = $pdo->prepare("SELECT * FROM events WHERE event_id = ?");
$stmt->execute([$event_id]);
$event = $stmt->fetch();
if (!$event || $event['organizer_id'] != $_SESSION['user_id']) {
    header('Location: view_event.php');
    exit;
}

// check if event already started
$now = new DateTime();
$start = new DateTime($event['event_date'] . ' ' . ($event['event_time'] ?: '00:00:00'));
$started = $now >= $start;

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$started) {
    // Only allow date, time, location updates
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $location = trim($_POST['location']);

    if (!$event_date || !$location) {
        $message = '⚠️ Date and location required.';
    } else {
        $u = $pdo->prepare("UPDATE events SET event_date = ?, event_time = ?, location = ? WHERE event_id = ?");
        $u->execute([$event_date, $event_time, $location, $event_id]);
        $message = '✅ Event updated.';
        // refresh event data and recompute $started
        $stmt->execute([$event_id]);
        $event = $stmt->fetch();
        $start = new DateTime($event['event_date'] . ' ' . ($event['event_time'] ?: '00:00:00'));
        $started = $now >= $start;
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Edit Event</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <div class="card">
        <h2>Edit Event</h2>
        <p><a href="view_event.php" class="btn">Back</a></p>
        <?php if ($started): ?>
            <p style="color:orange;">This event has already started or the start time is reached. Editing is disabled.</p>
        <?php endif; ?>
        <?php if ($message): ?><p><?= e($message) ?></p><?php endif; ?>

        <form method="POST">
            <label>Event Title (read-only)</label>
            <input type="text" value="<?= e($event['title']) ?>" disabled>

            <label>Date</label>
            <input type="date" name="event_date" value="<?= e($event['event_date']) ?>" <?= $started ? 'disabled' : '' ?>>

            <label>Time</label>
            <input type="time" name="event_time" value="<?= e($event['event_time']) ?>" <?= $started ? 'disabled' : '' ?>>

            <label>Location</label>
            <input type="text" name="location" value="<?= e($event['location']) ?>" <?= $started ? 'disabled' : '' ?>>

            <?php if (!$started): ?>
                <button type="submit">Save Changes</button>
            <?php endif; ?>
        </form>
    </div>
</body>

</html>