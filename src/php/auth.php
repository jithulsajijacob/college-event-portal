<?php
require_once __DIR__ . '/session_config.php';
function ensure_logged_in()
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../../public/auth/login.php');
        exit;
    }
}
function ensure_role($role)
{
    ensure_logged_in();
    if ($_SESSION['role'] !== $role) {
        header('Location: ../../public/index.php');
        exit;
    }
}
function ensure_any_role(array $roles)
{
    ensure_logged_in();
    if (!in_array($_SESSION['role'], $roles)) {
        header('Location: ../../public/index.php');
        exit;
    }
}
