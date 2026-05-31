<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: ../auth/login.php");
    exit;
}

$id_mahasiswa = $_SESSION['id_ref'];

$mhs = mysqli_fetch_assoc(mysqli_query($koneksi,"
    SELECT * FROM mahasiswa
    WHERE id_mahasiswa='$id_mahasiswa'
"));

if (!$mhs) {
    die("Data tidak ditemukan");
}

if (!isset($_POST['update_profil'])) {
    die("Form tidak terkirim");
}

// DATA
$nim = $_POST['nim'];
$nama = $_POST['nama'];
$jurusan = $_POST['jurusan'];
$prodi = $_POST['prodi'];
$email = $_POST['email'];
$jenis_kelamin = $_POST['jenis_kelamin'];
$agama = $_POST['agama'];
$tempat_lahir = $_POST['tempat_lahir'];
$tanggal_lahir = $_POST['tanggal_lahir'];
$status = $_POST['status'];
$alamat = $_POST['alamat'];

$foto_baru = $mhs['foto'];

// UPLOAD
if (!empty($_FILES['foto']['name'])) {

    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $nama_baru = time().".".$ext;

    $folder = "../assets/uploads/mahasiswa/";

    if (!file_exists($folder)) {
        mkdir($folder, 0777, true);
    }

    if (move_uploaded_file($_FILES['foto']['tmp_name'], $folder.$nama_baru)) {

        if (!empty($mhs['foto']) && file_exists($folder.$mhs['foto'])) {
            unlink($folder.$mhs['foto']);
        }

        $foto_baru = $nama_baru;
    }
}

// UPDATE
$update = mysqli_query($koneksi,"
    UPDATE mahasiswa SET
    nim='$nim',
    nama='$nama',
    jurusan='$jurusan',
    prodi='$prodi',
    email='$email',
    jenis_kelamin='$jenis_kelamin',
    agama='$agama',
    tempat_lahir='$tempat_lahir',
    tanggal_lahir='$tanggal_lahir',
    status='$status',
    alamat='$alamat',
    foto='$foto_baru'
    WHERE id_mahasiswa='$id_mahasiswa'
");

if (!$update) {
    die(mysqli_error($koneksi));
}

echo "<script>
alert('Berhasil update profil!');
window.location='profil.php';
</script>";
?>