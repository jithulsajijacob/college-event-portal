<?php
require_once __DIR__ . '/../../src/php/session_config.php';
require_once __DIR__ . '/../../src/php/db.php';
require_once __DIR__ . '/../../src/php/auth.php';
require_once __DIR__ . '/../../src/php/functions.php';

ensure_role('admin');

// Event count and registrations
$totalEvents = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
$totalUsers  = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalRegs   = $pdo->query("SELECT COUNT(*) FROM registrations")->fetchColumn();

// Average ratings
$avgRatings = $pdo->query("
  SELECT e.title, ROUND(AVG(f.rating),2) AS avg_rating
  FROM feedback f JOIN events e ON f.event_id=e.event_id
  GROUP BY e.title
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
    </div>

    <script>
        const ctx = document.getElementById('ratingChart');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($avgRatings, 'title')) ?>,
                datasets: [{
                    label: 'Average Rating',
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