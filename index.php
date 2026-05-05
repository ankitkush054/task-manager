<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] == 'admin') {
    header("Location: admin/dashboard.php");
} else {
    header("Location: member/dashboard.php");
}
exit;
?>