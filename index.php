<?php
session_start();

if (isset($_SESSION['role'])) {

    if ($_SESSION['role'] == 'admin') {
        header("Location: admin/dashboard.php");
        exit;
    }

    elseif ($_SESSION['role'] == 'mahasiswa') {
        header("Location: mahasiswa/dashboard.php");
        exit;
    }

    elseif ($_SESSION['role'] == 'dosen') {
        header("Location: dosen/dashboard.php");
        exit;
    }
}

header("Location: auth/login.php");
exit;
?>