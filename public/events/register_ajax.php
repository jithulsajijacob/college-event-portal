<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../src/php/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['success' => false, 'error' => 'Not logged in or not a student']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$event_id = intval($input['event_id'] ?? 0);

if (!$event_id) {
    echo json_encode(['success' => false, 'error' => 'Invalid event ID']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO registrations (user_id, event_id) VALUES (?, ?)");
    $stmt->execute([$_SESSION['user_id'], $event_id]);
    echo json_encode(['success' => true, 'message' => 'Registered successfully']);
} catch (PDOException $e) {
    if ($e->getCode() === '23000') { // duplicate
        echo json_encode(['success' => false, 'error' => 'You are already registered for this event']);
    } else {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
