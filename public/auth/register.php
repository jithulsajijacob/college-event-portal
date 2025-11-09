<?php
require_once __DIR__ . '/../../src/php/session_config.php';
require_once __DIR__ . '/../../src/php/db.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    $role = $_POST['role'];
    if (!$name || !$email || $password !== $confirm) {
        $message = "⚠️ Please fill all fields correctly.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $pdo->prepare("INSERT INTO users (name,email,password,role) VALUES (?,?,?,?)")
                ->execute([$name, $email, $hash, $role]);
            $message = "✅ Registered successfully! You can now log in.";
        } catch (PDOException $e) {
            $message = "❌ Email already exists.";
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
            <select name="role">
                <option value="student">Student</option>
                <option value="organizer">Organizer</option>
                <option value="admin">Admin</option>
            </select>
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
</body>

</html>