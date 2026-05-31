<?php
session_start();
include '../config/koneksi.php';

// ==========================
// PROTEKSI DOSEN
// ==========================
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'dosen') {
    header("Location: ../auth/login.php");
    exit;
}

$id_dosen = $_SESSION['id_ref'];

// ==========================
// AMBIL DATA LAMA (UNTUK FOTO LAMA)
// ==========================
$get = mysqli_query($koneksi, "
    SELECT * FROM dosen WHERE id_dosen='$id_dosen'
");

$dataLama = mysqli_fetch_assoc($get);

if (!$dataLama) {
    die("Data dosen tidak ditemukan!");
}

// ==========================
// AMBIL INPUT FORM
// ==========================
$nama = $_POST['nama'];
$nip = $_POST['nip'];
$jenis_kelamin = $_POST['jenis_kelamin'];
$jabatan = $_POST['jabatan'];
$fakultas = $_POST['fakultas'];
$prodi = $_POST['prodi'];
$email = $_POST['email'];
$no_hp = $_POST['no_hp'];
$alamat = $_POST['alamat'];

// ==========================
// FOTO DEFAULT (PAKAI LAMA)
// ==========================
$fotoBaru = $dataLama['foto'];

// ==========================
// CEK UPLOAD FOTO
// ==========================
if (!empty($_FILES['foto']['name'])) {

    $allowed = ['jpg','jpeg','png','webp'];

    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        echo "<script>
            alert('Format foto harus jpg/jpeg/png/webp!');
            window.location.href='profil.php';
        </script>";
        exit;
    }

    // nama file unik
    $fotoBaru = time() . '_' . rand(1000,9999) . '.' . $ext;

    $uploadPath = "../assets/uploads/dosen/" . $fotoBaru;

    if (!move_uploaded_file($_FILES['foto']['tmp_name'], $uploadPath)) {
        echo "<script>
            alert('Upload foto gagal!');
            window.location.href='profil.php';
        </script>";
        exit;
    }

    // HAPUS FOTO LAMA (biar tidak numpuk)
    if (!empty($dataLama['foto'])) {
        $fotoLamaPath = "../assets/uploads/dosen/" . $dataLama['foto'];

        if (file_exists($fotoLamaPath)) {
            unlink($fotoLamaPath);
        }
    }
}

// ==========================
// UPDATE DATABASE
// ==========================
$query = mysqli_query($koneksi, "
    UPDATE dosen SET
        nama='$nama',
        nip='$nip',
        jenis_kelamin='$jenis_kelamin',
        jabatan='$jabatan',
        fakultas='$fakultas',
        prodi='$prodi',
        email='$email',
        no_hp='$no_hp',
        alamat='$alamat',
        foto='$fotoBaru'
    WHERE id_dosen='$id_dosen'
");

// ==========================
// RESPONSE
// ==========================
if ($query) {
    echo "<script>
        alert('Profil berhasil diperbarui!');
        window.location.href='profil.php';
    </script>";
} else {
    echo "<script>
        alert('Gagal update profil!');
        window.location.href='profil.php';
    </script>";
}
?>