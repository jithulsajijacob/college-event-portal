<?php
require_once __DIR__ . '/../../src/php/session_config.php';
require_once __DIR__ . '/../../src/php/db.php';
require_once __DIR__ . '/../../src/php/auth.php';

ensure_role('organizer');

$event_id = intval($_GET['id'] ?? 0);
if (!$event_id) header('Location: view_event.php');

$stmt = $pdo->prepare("SELECT * FROM events WHERE event_id = ?");
$stmt->execute([$event_id]);
$event = $stmt->fetch();
if (!$event || $event['organizer_id'] != $_SESSION['user_id']) {
    header('Location: view_event.php');
    exit;
}
// prevent deletion if event started
$now = new DateTime();
$start = new DateTime($event['event_date'] . ' ' . ($event['event_time'] ?: '00:00:00'));
if ($now >= $start) {
    // cannot delete
    header('Location: view_event.php?msg=cannot_delete_started');
    exit;
}
// delete registrations and event (FK CASCADE should handle reg/feedback as configured)
$del = $pdo->prepare("DELETE FROM events WHERE event_id = ?");
$del->execute([$event_id]);
header('Location: view_event.php?msg=deleted');
