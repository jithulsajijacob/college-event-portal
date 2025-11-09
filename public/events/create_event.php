<?php
require_once __DIR__ . '/../../src/php/session_config.php';
require_once __DIR__ . '/../../src/php/db.php';
require_once __DIR__ . '/../../src/php/auth.php';
require_once __DIR__ . '/../../src/php/functions.php';

ensure_role('organizer');

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title']);
    $description = trim($_POST['description']);
    $event_date  = $_POST['event_date'];
    $event_time  = $_POST['event_time'];
    $location    = trim($_POST['location']);
    $capacity    = (int)$_POST['capacity'];

    if (!$title || !$event_date || !$location) {
        $message = "⚠️ Please fill all required fields.";
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO events (title, description, event_date, event_time, location, organizer_id, capacity)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$title, $description, $event_date, $event_time, $location, $_SESSION['user_id'], $capacity]);
        $message = "✅ Event created successfully!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Create Event</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <div class="card fade-in">
        <h2>➕ Create New Event</h2>
        <p><a href="../index.php" class="btn">🏠 Home</a></p>
        <?php if ($message): ?><p><?= e($message) ?></p><?php endif; ?>

        <form method="POST">
            <label>Event Title *</label>
            <input type="text" name="title" required>

            <label>Description</label>
            <textarea name="description" rows="4"></textarea>

            <label>Date *</label>
            <input type="date" name="event_date" required>

            <label>Time</label>
            <input type="time" name="event_time">

            <label>Location *</label>
            <input type="text" name="location" required>

            <label>Capacity</label>
            <input type="number" name="capacity" value="50">

            <button type="submit">Create Event</button>
        </form>
    </div>
</body>

</html>