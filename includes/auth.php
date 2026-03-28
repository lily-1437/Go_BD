<?php
// includes/auth.php
// Session helpers — call these at the top of protected pages.

session_start();

function isLoggedIn() {
    return isset($_SESSION['tourist_id']);
}

function isAdmin() {
    return isset($_SESSION['admin_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: /public/login.php");
        exit();
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        header("Location: /public/login.php");
        exit();
    }
}

function getCurrentTouristId() {
    return $_SESSION['tourist_id'] ?? null;
}

function getCurrentAdminId() {
    return $_SESSION['admin_id'] ?? null;
}
