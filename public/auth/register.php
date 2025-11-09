<?php
require_once __DIR__ . '/../../src/php/session_config.php';
require_once __DIR__ . '/../../src/php/db.php';
require_once __DIR__ . '/../../src/php/functions.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    $role = $_POST['role'];
    $admin_code_input = trim($_POST['admin_code'] ?? '');

    if (!$name || !$email || !$password || !$confirm) {
        $message = "⚠️ Please fill all fields.";
    } elseif ($password !== $confirm) {
        $message = "⚠️ Passwords do not match.";
    } else {
        if ($role === 'admin') {
            // Fetch admin code from DB
            $stmt = $pdo->prepare("SELECT value FROM settings WHERE `key`='admin_code' LIMIT 1");
            $stmt->execute();
            $row = $stmt->fetch();
            $admin_code = $row ? $row['value'] : '';

            if ($admin_code_input === '' || $admin_code_input !== $admin_code) {
                $message = "❌ Invalid admin code. Contact DB Manager.";
            }
        }

        if (!$message) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            try {
                $pdo->prepare("INSERT INTO users (name,email,password,role) VALUES (?,?,?,?)")
                    ->execute([$name, $email, $hash, $role]);
                $message = "✅ Registration successful! You can log in.";
            } catch (PDOException $e) {
                $message = "❌ Email already exists.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/app.js" defer></script>
</head>

<body>
    <div class="card fade-in">
        <h2>📝 Register</h2>
        <?php if ($message): ?><p><?= e($message) ?></p><?php endif; ?>
        <form method="POST" id="registerForm" onsubmit="return validateRegisterForm();">
            <label>Name</label><input type="text" name="name" required>
            <label>Email</label><input type="email" name="email" required>
            <label>Password</label><input type="password" name="password" required>
            <label>Confirm Password</label><input type="password" name="confirm_password" required>
            <label>Role</label>
            <select name="role" id="roleSelect" onchange="toggleAdminCode()">
                <option value="student">Student</option>
                <option value="organizer">Organizer</option>
                <option value="admin">Admin</option>
            </select>
            <div id="adminCodeBox" style="display:none;">
                <label>Admin Code (required if registering as Admin)</label>
                <input type="text" name="admin_code" placeholder="Enter admin code">
            </div>
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
    <script>
        function toggleAdminCode() {
            const role = document.getElementById('roleSelect').value;
            document.getElementById('adminCodeBox').style.display = role === 'admin' ? 'block' : 'none';
        }
    </script>
</body>

</html>