<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_admin_logged_in(): bool
{
    return !empty($_SESSION['admin_id']);
}

function current_admin_name(): ?string
{
    return $_SESSION['admin_name'] ?? null;
}

function require_admin(): void
{
    if (!is_admin_logged_in()) {
        header('Location: account.php');
        exit;
    }
}
