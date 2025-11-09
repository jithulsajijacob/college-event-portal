<?php

/**
 * Secure PDO connection handler for College Event Management Portal
 * ---------------------------------------------------------------
 * Reads credentials from .env file (outside public directory)
 * Falls back to safe defaults for local XAMPP testing
 * Uses PDO with proper error handling and exceptions
 */

$envPath = __DIR__ . '/../../.env';

// Default config (so the app still runs if .env missing)
$defaults = [
    'DB_HOST' => '127.0.0.1',
    'DB_NAME' => 'college_events',
    'DB_USER' => 'root',
    'DB_PASS' => ''
];

// Load environment variables
if (file_exists($envPath)) {
    $env = parse_ini_file($envPath, false, INI_SCANNER_RAW);
    $cfg = array_merge($defaults, $env);
} else {
    $cfg = $defaults;
}

$dsn  = "mysql:host={$cfg['DB_HOST']};dbname={$cfg['DB_NAME']};charset=utf8mb4";
$user = $cfg['DB_USER'];
$pass = $cfg['DB_PASS'];

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Return assoc arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Use real prepared stmts
    PDO::ATTR_PERSISTENT         => false                   // Avoid long-lived conn for XAMPP
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Never expose DB details in production
    error_log("Database connection failed: " . $e->getMessage());
    if (php_sapi_name() !== 'cli') {
        header('Content-Type: application/json');
    }
    exit(json_encode(['success' => false, 'error' => 'Database connection failed.']));
}
