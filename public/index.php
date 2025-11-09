<?php
// Start or resume session for all users
require_once __DIR__ . '/../src/php/session_config.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>College Event Management Portal</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            color: #333;
            text-align: center;
            padding: 50px;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
            padding: 25px;
            width: 60%;
            max-width: 700px;
        }

        h1 {
            color: #0056b3;
        }

        button,
        .btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            cursor: pointer;
            margin: 5px;
        }

        button:hover,
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <h1>🎓 College Event Management Portal</h1>

    <?php if (!isset($_SESSION['user_id'])): ?>
        <!-- ==========================
         Guest View (Not Logged In)
         ========================== -->
        <div class="card">
            <p>Welcome to the College Event Management Portal.</p>
            <p>Please log in or register to continue.</p>
            <a class="btn" href="auth/login.php">🔐 Login</a>
            <a class="btn" href="auth/register.php">📝 Register</a>
        </div>

    <?php else: ?>
        <!-- ==========================
         Logged In User View
         ========================== -->
        <div class="card">
            <p>Welcome, <b><?= htmlspecialchars($_SESSION['name']) ?></b> (<?= htmlspecialchars($_SESSION['role']) ?>)</p>
            <a class="btn" href="auth/logout.php">🚪 Logout</a>

            <hr style="margin: 25px 0;">

            <!-- ========== Organizer Links ========== -->
            <?php if ($_SESSION['role'] === 'organizer'): ?>
                <h3>📋 Organizer Tools</h3>
                <p>
                    <a class="btn" href="events/create_event.php">➕ Create New Event</a>
                    <a class="btn" href="events/view_event.php">📅 View All Events</a>
                </p>
            <?php endif; ?>

            <!-- ========== Student Links ========== -->
            <?php if ($_SESSION['role'] === 'student'): ?>
                <h3>🎟️ Student Options</h3>
                <p>
                    <a class="btn" href="events/view_event.php">📅 View Upcoming Events</a>
                    <a class="btn" href="events/feedback.php">🗒️ Submit Feedback</a>
                </p>
            <?php endif; ?>

            <!-- ========== Admin Links ========== -->
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <h3>🧭 Admin Controls</h3>
                <p>
                    <a class="btn" href="admin/dashboard.php">📊 Admin Dashboard</a>
                    <a class="btn" href="events/view_event.php">📅 View All Events</a>
                </p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <footer style="margin-top: 60px; font-size: 14px; color: #777;">
        <p>Developed for Internet and Web Programming Course (BCSE206L)</p>
        <p>Technologies: HTML5 | CSS3 | JavaScript | PHP | MySQL | AJAX</p>
    </footer>
</body>

</html>