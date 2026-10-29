<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireLogin() {
    if (empty($_SESSION['user'])) {
        header('Location: /beauty-service/login.php');
        exit;
    }
}

function requireRole($role) {
    requireLogin();

    if ($_SESSION['user']['role'] !== $role) {
        http_response_code(403);
        exit('Access denied');
    }
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function csrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrfField() {
    return '<input type="hidden" name="csrf_token" value="' . e(csrfToken()) . '">';
}

function checkCsrf() {
    $token = $_POST['csrf_token'] ?? '';

    if (!hash_equals(csrfToken(), (string)$token)) {
        http_response_code(403);
        exit('Invalid form token. Reload the page.');
    }
}
