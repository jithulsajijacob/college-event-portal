<?php
require_once __DIR__ . '/../../src/php/session_config.php';
require_once __DIR__ . '/../../src/php/db.php';
require_once __DIR__ . '/../../src/php/auth.php';
ensure_role('organizer');

$event_id = (int)($_POST['event_id'] ?? 0);
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if (!$event_id || !$subject || !$message) {
    header('Location: view_event.php?msg=invalid');
    exit;
}

$stmt = $pdo->prepare("SELECT organizer_id,title FROM events WHERE event_id=?");
$stmt->execute([$event_id]);
$event = $stmt->fetch();
if (!$event || $event['organizer_id'] != $_SESSION['user_id']) {
    header('Location: view_event.php?msg=not_owner');
    exit;
}

$q = $pdo->prepare("SELECT u.email,u.user_id FROM registrations r JOIN users u ON r.user_id=u.user_id WHERE r.event_id=?");
$q->execute([$event_id]);
$rows = $q->fetchAll();

$ins = $pdo->prepare("INSERT INTO notifications (event_id,to_user_id,from_user_id,subject,message,sent_via) VALUES (?,?,?,?,?,'db')");
foreach ($rows as $r) {
    $ins->execute([$event_id, $r['user_id'], $_SESSION['user_id'], $subject, $message]);
    if (!empty($r['email'])) {
        @mail($r['email'], $subject, "Organizer message for " . $event['title'] . ":\n\n" . $message, "From: organizer@collegeportal.local");
    }
}
header('Location: view_event.php?msg=sent');
exit;
