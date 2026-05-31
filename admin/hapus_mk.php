<?php
session_start();
include '../config/koneksi.php';

/*
|---------------------------------------------------------
| PROTEKSI ADMIN
|---------------------------------------------------------
*/
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

/*
|---------------------------------------------------------
| VALIDASI ID
|---------------------------------------------------------
*/
if (!isset($_GET['id']) || empty($_GET['id'])) {

    echo "
    <script>
        alert('ID Mata Kuliah tidak ditemukan!');
        window.location='mata_kuliah.php';
    </script>
    ";

    exit;
}

/*
|---------------------------------------------------------
| AMBIL ID
|---------------------------------------------------------
*/
$id_mk = mysqli_real_escape_string(
    $koneksi,
    $_GET['id']
);

/*
|---------------------------------------------------------
| CEK DATA ADA / TIDAK
|---------------------------------------------------------
*/
$cek = mysqli_query($koneksi, "
    SELECT *
    FROM mata_kuliah
    WHERE id_mk = '$id_mk'
");

if (mysqli_num_rows($cek) == 0) {

    echo "
    <script>
        alert('Data Mata Kuliah tidak ditemukan!');
        window.location='mata_kuliah.php';
    </script>
    ";

    exit;
}

/*
|---------------------------------------------------------
| HAPUS DATA
|---------------------------------------------------------
*/
$hapus = mysqli_query($koneksi, "
    DELETE FROM mata_kuliah
    WHERE id_mk = '$id_mk'
");

/*
|---------------------------------------------------------
| HASIL HAPUS
|---------------------------------------------------------
*/
if ($hapus) {

    echo "
    <script>
        alert('Data Mata Kuliah berhasil dihapus!');
        window.location='mata_kuliah.php';
    </script>
    ";

} else {

    echo "
    <script>
        alert('Gagal menghapus data!');
        window.location='mata_kuliah.php';
    </script>
    ";
}
?>