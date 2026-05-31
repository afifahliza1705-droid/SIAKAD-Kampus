<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['id']) || $_GET['id'] == '') {
    die("ID tidak ditemukan di URL");
}

$id = $_GET['id'];

// cek data dulu
$cek = mysqli_query($koneksi, "SELECT * FROM dosen WHERE id_dosen = '$id'");

if (!$cek) {
    die("Query error: " . mysqli_error($koneksi));
}

$data = mysqli_fetch_assoc($cek);

if (!$data) {
    die("Data tidak ditemukan di database untuk ID: " . $id);
}

// hapus foto jika ada
if (!empty($data['foto'])) {
    $path = "../assets/uploads/dosen/" . $data['foto'];
    if (file_exists($path)) {
        unlink($path);
    }
}

// delete data
$hapus = mysqli_query($koneksi, "DELETE FROM dosen WHERE id_dosen = '$id'");

if (!$hapus) {
    die("Gagal delete: " . mysqli_error($koneksi));
}

echo "<script>
alert('Data berhasil dihapus');
window.location='dosen.php';
</script>";
?>