<?php
session_start();
include '../config/koneksi.php';

// proteksi admin
if (!isset($_SESSION['role'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: mahasiswa.php");
    exit;
}

$id = $_GET['id'];

// ambil data mahasiswa
$query = mysqli_query(
    $koneksi,
    "SELECT * FROM mahasiswa
    WHERE id_mahasiswa='$id'"
);

$mhs = mysqli_fetch_assoc($query);

if (!$mhs) {
    header("Location: mahasiswa.php");
    exit;
}

// hapus foto jika ada
if (
    !empty($mhs['foto']) &&
    file_exists(
        "../assets/uploads/mahasiswa/" .
        $mhs['foto']
    )
) {
    unlink(
        "../assets/uploads/mahasiswa/" .
        $mhs['foto']
    );
}

// hapus akun login mahasiswa
mysqli_query(
    $koneksi,
    "DELETE FROM users
    WHERE id_ref='$id'
    AND role='mahasiswa'"
);

// hapus data mahasiswa
mysqli_query(
    $koneksi,
    "DELETE FROM mahasiswa
    WHERE id_mahasiswa='$id'"
);

echo "
<script>
alert('Data mahasiswa berhasil dihapus');
window.location='mahasiswa.php';
</script>
";
?>