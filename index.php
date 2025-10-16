<?php
session_start();
require_once 'maintenance_config.php';

// Check maintenance mode
check_maintenance();

// Cek apakah user sudah login
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    // Redirect ke dashboard sesuai role
    switch ($_SESSION['user_role']) {
        case 'Project Manager':
            header('Location: dashboard_pm.php');
            break;
        case 'Staff Accountant':
            header('Location: dashboard_sa.php');
            break;
        case 'Finance Manager':
            header('Location: dashboard_fm.php');
            break;
        case 'Direktur':
            header('Location: dashboard_dir.php');
            break;
        default:
            header('Location: login.php');
    }
    exit();
} else {
    // Jika belum login, redirect ke halaman login
    header('Location: login.php');
    exit();
}
?>