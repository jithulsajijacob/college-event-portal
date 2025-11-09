<?php
session_start();
require_once __DIR__ . '/../../src/php/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../auth/login.php');
    exit;
}

$message = '';

// Fetch events this student registered for
$stmt = $pdo->prepare("SELECT e.event_id, e.title
                       FROM events e
                       JOIN registrations r ON e.event_id = r.event_id
                       WHERE r.user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$events = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = intval($_POST['event_id']);
    $rating = intval($_POST['rating']);
    $comments = trim($_POST['comments']);

    if ($rating < 1 || $rating > 5) {
        $message = "⚠️ Please select a valid rating (1–5).";
    } else {
        $stmt = $pdo->prepare("INSERT INTO feedback (event_id, user_id, rating, comments)
                           VALUES (?, ?, ?, ?)");
        try {
            $stmt->execute([$event_id, $_SESSION['user_id'], $rating, $comments]);
            $message = "✅ Feedback submitted successfully!";
        } catch (PDOException $e) {
            $message = "⚠️ You have already submitted feedback for this event.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Submit Feedback</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <h2>🗒️ Submit Event Feedback</h2>
    <p><a href="../index.php">🏠 Back to Home</a></p>

    <?php if ($message): ?>
        <p style="color:green"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Choose Event:</label><br>
        <select name="event_id" required>
            <option value="">-- Select Event --</option>
            <?php foreach ($events as $event): ?>
                <option value="<?= $event['event_id'] ?>"><?= htmlspecialchars($event['title']) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Rating (1–5):</label><br>
        <input type="number" name="rating" min="1" max="5" required><br><br>

        <label>Comments:</label><br>
        <textarea name="comments" rows="4" cols="40"></textarea><br><br>

        <button type="submit">Submit Feedback</button>
    </form>
</body>

</html>