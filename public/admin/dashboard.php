<?php
session_start();
require_once __DIR__ . '/../../src/php/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

// Count users, events, registrations
$totalUsers = $pdo->query("SELECT COUNT(*) AS count FROM users")->fetch()['count'];
$totalEvents = $pdo->query("SELECT COUNT(*) AS count FROM events")->fetch()['count'];
$totalRegs = $pdo->query("SELECT COUNT(*) AS count FROM registrations")->fetch()['count'];

// Top 5 most registered events
$topEvents = $pdo->query("
  SELECT e.title, COUNT(r.reg_id) AS registrations
  FROM events e
  LEFT JOIN registrations r ON e.event_id = r.event_id
  GROUP BY e.event_id
  ORDER BY registrations DESC
  LIMIT 5
")->fetchAll();

// Average ratings
$avgRatings = $pdo->query("
  SELECT e.title, AVG(f.rating) AS avg_rating
  FROM feedback f
  JOIN events e ON e.event_id = f.event_id
  GROUP BY e.event_id
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <h2>🧭 Admin Dashboard</h2>
    <p><a href="../index.php">🏠 Back to Home</a></p>

    <h3>📊 Overview</h3>
    <ul>
        <li><b>Total Users:</b> <?= $totalUsers ?></li>
        <li><b>Total Events:</b> <?= $totalEvents ?></li>
        <li><b>Total Registrations:</b> <?= $totalRegs ?></li>
    </ul>

    <h3>🏆 Top 5 Events by Registration</h3>
    <table border="1" cellpadding="5">
        <tr>
            <th>Event</th>
            <th>Registrations</th>
        </tr>
        <?php foreach ($topEvents as $ev): ?>
            <tr>
                <td><?= htmlspecialchars($ev['title']) ?></td>
                <td><?= $ev['registrations'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h3>⭐ Average Ratings per Event</h3>
    <canvas id="ratingsChart" width="600" height="300"></canvas>

    <script>
        const ctx = document.getElementById('ratingsChart');
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($avgRatings, 'title')) ?>,
                datasets: [{
                    label: 'Average Rating',
                    data: <?= json_encode(array_column($avgRatings, 'avg_rating')) ?>,
                    borderWidth: 1
                }]
            },
            options: {
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