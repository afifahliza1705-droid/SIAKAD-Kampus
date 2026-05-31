<?php
session_start();
include '../config/koneksi.php';

$username = mysqli_real_escape_string($koneksi, $_POST['username']);
$password = $_POST['password'];

$query = mysqli_query($koneksi, "
    SELECT * FROM users 
    WHERE username='$username'
");

$data = mysqli_fetch_assoc($query);

if ($data) {

    // CEK PASSWORD HASH
    if (password_verify($password, $data['password'])) {

        $_SESSION['id_user'] = $data['id_user'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['role'] = $data['role'];
        $_SESSION['id_ref'] = $data['id_ref'];

        // redirect role
        if ($data['role'] == 'admin') {
            header("Location: ../admin/dashboard.php");
        }

        elseif ($data['role'] == 'mahasiswa') {
            header("Location: ../mahasiswa/dashboard.php");
        }

        elseif ($data['role'] == 'dosen') {
            header("Location: ../dosen/dashboard.php");
        }

        exit;

    } else {
        header("Location: login.php?pesan=gagal");
        exit;
    }

} else {
    header("Location: login.php?pesan=gagal");
    exit;
}
?>