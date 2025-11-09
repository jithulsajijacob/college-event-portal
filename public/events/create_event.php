<?php
session_start();
require_once __DIR__ . '/../../src/php/db.php';

// Only organizers can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organizer') {
    header('Location: ../auth/login.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $location = trim($_POST['location']);
    $capacity = intval($_POST['capacity']);

    if (!$title || !$event_date || !$location) {
        $message = "⚠️ Please fill all required fields.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO events (title, description, event_date, event_time, location, organizer_id, capacity)
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
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
    <h2>Create New Event</h2>
    <p><a href="../index.php">🏠 Back to Home</a></p>

    <?php if ($message): ?>
        <p style="color: green;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Event Title *</label><br>
        <input type="text" name="title" required><br><br>

        <label>Description</label><br>
        <textarea name="description" rows="4" cols="40"></textarea><br><br>

        <label>Date *</label><br>
        <input type="date" name="event_date" required><br><br>

        <label>Time</label><br>
        <input type="time" name="event_time"><br><br>

        <label>Location *</label><br>
        <input type="text" name="location" required><br><br>

        <label>Capacity</label><br>
        <input type="number" name="capacity" value="50"><br><br>

        <button type="submit">Create Event</button>
    </form>
</body>

</html>