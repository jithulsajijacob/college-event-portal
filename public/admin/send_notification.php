<?php
require_once __DIR__ . '/../../src/php/session_config.php';
require_once __DIR__ . '/../../src/php/db.php';
require_once __DIR__ . '/../../src/php/auth.php';
ensure_role('admin');

$from = $_SESSION['user_id'];
$event_id = (int)($_POST['event_id'] ?? 0);
$to_user = (int)($_POST['to_user'] ?? 0);
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($to_user && $subject && $message) {
    $ins = $pdo->prepare("INSERT INTO notifications (event_id,to_user_id,from_user_id,subject,message,sent_via)
                        VALUES (?,?,?,?,?,'db')");
    $ins->execute([$event_id ?: NULL, $to_user, $from, $subject, $message]);

    // Optional email
    $stmt = $pdo->prepare("SELECT email FROM users WHERE user_id=?");
    $stmt->execute([$to_user]);
    if ($u = $stmt->fetch()) {
        @mail($u['email'], $subject, $message, "From: admin@collegeportal.local");
    }
}
header('Location: dashboard.php');
exit;
