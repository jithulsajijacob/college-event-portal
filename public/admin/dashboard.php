<?php
require_once __DIR__ . '/../../src/php/session_config.php';
require_once __DIR__ . '/../../src/php/db.php';
require_once __DIR__ . '/../../src/php/auth.php';
require_once __DIR__ . '/../../src/php/functions.php';
ensure_role('admin');

$totalEvents = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
$totalUsers  = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalRegs   = $pdo->query("SELECT COUNT(*) FROM registrations")->fetchColumn();
$avgRatings  = $pdo->query("
  SELECT e.title, ROUND(AVG(f.rating),2) AS avg_rating
  FROM feedback f JOIN events e ON f.event_id=e.event_id GROUP BY e.title
")->fetchAll();

$q = trim($_GET['q'] ?? '');
$params = [];
$sql = "SELECT e.*, u.name AS organizer_name, u.user_id AS organizer_id
        FROM events e JOIN users u ON e.organizer_id = u.user_id";
if ($q !== '') {
    $sql .= " WHERE e.title LIKE ? OR e.location LIKE ? OR u.name LIKE ?";
    $like = "%$q%";
    $params = [$like, $like, $like];
}
$sql .= " ORDER BY e.event_date DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$events = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="card fade-in">
        <h2>📊 Admin Dashboard</h2>
        <p><a href="../index.php" class="btn">🏠 Home</a></p>
        <table>
            <tr>
                <th>Metric</th>
                <th>Count</th>
            </tr>
            <tr>
                <td>Total Events</td>
                <td><?= e($totalEvents) ?></td>
            </tr>
            <tr>
                <td>Total Users</td>
                <td><?= e($totalUsers) ?></td>
            </tr>
            <tr>
                <td>Total Registrations</td>
                <td><?= e($totalRegs) ?></td>
            </tr>
        </table>

        <h3>⭐ Average Ratings by Event</h3>
        <canvas id="ratingChart" width="600" height="300"></canvas>

        <h3 style="margin-top:30px;">🔍 Search Events</h3>
        <form method="GET" action="dashboard.php">
            <input type="text" name="q" placeholder="Search by title/location/organizer" value="<?= e($q) ?>" style="width:60%;padding:8px;">
            <button type="submit" class="btn">Search</button>
        </form>

        <?php foreach ($events as $ev): ?>
            <div class="event-card fade-in">
                <h3><?= e($ev['title']) ?></h3>
                <p><b>Date:</b> <?= e($ev['event_date']) ?> | <b>Organizer:</b> <?= e($ev['organizer_name']) ?></p>
                <form method="POST" action="send_notification.php">
                    <input type="hidden" name="event_id" value="<?= $ev['event_id'] ?>">
                    <input type="hidden" name="to_user" value="<?= $ev['organizer_id'] ?>">
                    <input type="text" name="subject" placeholder="Subject" required style="width:45%;">
                    <input type="text" name="message" placeholder="Message" required style="width:45%;">
                    <button type="submit" class="btn">Send Message to Organizer</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        const ctx = document.getElementById('ratingChart');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($avgRatings, 'title')) ?>,
                datasets: [{
                    label: 'Avg Rating',
                    data: <?= json_encode(array_column($avgRatings, 'avg_rating')) ?>,
                    backgroundColor: 'rgba(0,91,187,0.7)',
                    borderColor: '#003f88',
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 5
                    }
                }
            }
        });
    </script>
</body>

</html>