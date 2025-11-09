<?php
require_once __DIR__ . '/../../src/php/session_config.php';
require_once __DIR__ . '/../../src/php/db.php';
require_once __DIR__ . '/../../src/php/auth.php';
require_once __DIR__ . '/../../src/php/functions.php';
ensure_role('student');

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = (int)$_POST['event_id'];
    $rating = (int)$_POST['rating'];
    $comments = trim($_POST['comments']);

    if ($event_id && $rating) {
        $chk = $pdo->prepare("SELECT COUNT(*) FROM feedback WHERE user_id=? AND event_id=?");
        $chk->execute([$_SESSION['user_id'], $event_id]);
        if ($chk->fetchColumn() > 0) {
            $message = "⚠️ You already submitted feedback for this event.";
        } else {
            $pdo->prepare("INSERT INTO feedback (user_id,event_id,rating,comments) VALUES (?,?,?,?)")
                ->execute([$_SESSION['user_id'], $event_id, $rating, $comments]);
            $message = "✅ Feedback submitted successfully!";
        }
    } else $message = "⚠️ Please fill all fields.";
}
$events = $pdo->query("SELECT event_id,title FROM events ORDER BY event_date DESC")->fetchAll();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Feedback</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <div class="card fade-in">
        <h2>🗒️ Submit Event Feedback</h2>
        <p><a href="../index.php" class="btn">🏠 Home</a></p>
        <?php if ($message): ?><p><?= e($message) ?></p><?php endif; ?>
        <form method="POST">
            <label>Event</label>
            <select name="event_id" required>
                <option value="">-- Select Event --</option>
                <?php foreach ($events as $e): ?><option value="<?= $e['event_id'] ?>"><?= e($e['title']) ?></option><?php endforeach; ?>
            </select>
            <label>Rating (1–5)</label>
            <select name="rating" required><?php for ($i = 1; $i <= 5; $i++): ?><option value="<?= $i ?>"><?= $i ?></option><?php endfor; ?></select>
            <label>Comments</label><textarea name="comments" rows="3"></textarea>
            <button type="submit">Submit Feedback</button>
        </form>
    </div>
</body>

</html>