<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>College Event Portal</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <h1>🎓 College Event Management Portal</h1>

    <?php if (!isset($_SESSION['user_id'])): ?>
        <p><a href="auth/login.php">Login</a> | <a href="auth/register.php">Register</a></p>
    <?php else: ?>
        <p>Welcome, <?= htmlspecialchars($_SESSION['name']) ?> (<?= htmlspecialchars($_SESSION['role']) ?>)</p>
        <p><a href="auth/logout.php">Logout</a></p>
    <?php endif; ?>
</body>

</html>