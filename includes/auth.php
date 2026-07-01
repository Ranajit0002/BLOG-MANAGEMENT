<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function checkAccess(string $required_role)
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.php");
        exit;
    }

    if ($_SESSION['user_role'] !== $required_role) {
        if ($_SESSION['user_role'] === 'author') {
            header("Location: ../author/author-dashboard.php");
        } else {
            header("Location: ../login.php");
        }
        exit;
    }
}