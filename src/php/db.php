<?php
// Database connection using PDO
$cfg = parse_ini_file(__DIR__ . '/../../.env'); // reads DB creds
$host = $cfg['DB_HOST'] ?? '127.0.0.1';
$db   = $cfg['DB_NAME'] ?? 'college_events';
$user = $cfg['DB_USER'] ?? 'root';
$pass = $cfg['DB_PASS'] ?? '';

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "✅ Database connection successful!";
} catch (PDOException $e) {
    exit("❌ Connection failed: " . $e->getMessage());
}
