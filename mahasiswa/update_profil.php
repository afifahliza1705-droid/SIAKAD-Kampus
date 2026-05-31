<?php
session_start();
include '../config/koneksi.php';

// PROTEKSI MAHASISWA
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: ../auth/login.php");
    exit;
}

// AMBIL ID (FIX PENTING)
$id_mahasiswa = $_SESSION['id_ref'];

$mhs = mysqli_fetch_assoc(mysqli_query($koneksi,"
    SELECT * FROM mahasiswa
    WHERE id_mahasiswa='$id_mahasiswa'
"));

if (!$mhs) {
    die("Data tidak ditemukan");
}

// FOTO
$fotoPath = "../assets/uploads/mahasiswa/" . $mhs['foto'];

if (!empty($mhs['foto']) && file_exists($fotoPath)) {
    $foto = $fotoPath;
} else {
    $foto = "https://ui-avatars.com/api/?name=" . urlencode($mhs['nama']);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Edit Profil</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    font-family:'Poppins',sans-serif;
}

body{
    background:#f1f5f9;
    display:flex;
    justify-content:center;
    align-items:center;
    min-height:100vh;
    padding:30px;
}

/* CARD CENTER */
.card-custom{
    width:100%;
    max-width:1050px;
    background:white;
    border-radius:25px;
    overflow:hidden;
    box-shadow:0 15px 40px rgba(0,0,0,.12);
}

/* LEFT */
.left-side{
    background:linear-gradient(180deg,#2563eb,#38bdf8);
    padding:40px;
    text-align:center;
    color:white;
}

.preview{
    width:200px;
    height:200px;
    border-radius:20px;
    object-fit:cover;
    border:4px solid rgba(255,255,255,.4);
}

/* RIGHT */
.right-side{
    padding:40px;
}

.form-control,.form-select{
    height:50px;
    border-radius:12px;
}

label{
    font-weight:700;
}

/* BUTTON */
.btn-save{
    background:linear-gradient(135deg,#2563eb,#38bdf8);
    border:none;
    color:white;
    padding:12px 25px;
    border-radius:12px;
    font-weight:700;
}

.btn-back{
    background:#e2e8f0;
    padding:12px 25px;
    border-radius:12px;
    text-decoration:none;
    color:black;
    font-weight:700;
}
</style>

</head>
<body>

<div class="card-custom">
<div class="row g-0">

<!-- LEFT -->
<div class="col-md-4 left-side">
    <img id="preview" src="<?= $foto ?>" class="preview">
    <h4><?= $mhs['nama']; ?></h4>
    <p><?= $mhs['nim']; ?></p>
</div>

<!-- RIGHT -->
<div class="col-md-8 right-side">

<h3 class="mb-4">Edit Profil</h3>

<!-- 🔥 FIX PENTING: POST KE PROSES -->
<form method="POST" action="proses_update_profil.php" enctype="multipart/form-data">

<div class="row">

<div class="col-md-6 mb-3">
<label>NIM</label>
<input type="text" name="nim" class="form-control" value="<?= $mhs['nim']; ?>">
</div>

<div class="col-md-6 mb-3">
<label>Nama</label>
<input type="text" name="nama" class="form-control" value="<?= $mhs['nama']; ?>">
</div>

<div class="col-md-6 mb-3">
<label>Jurusan</label>
<input type="text" name="jurusan" class="form-control" value="<?= $mhs['jurusan']; ?>">
</div>

<div class="col-md-6 mb-3">
<label>Prodi</label>
<input type="text" name="prodi" class="form-control" value="<?= $mhs['prodi']; ?>">
</div>

<div class="col-md-6 mb-3">
<label>Email</label>
<input type="email" name="email" class="form-control" value="<?= $mhs['email']; ?>">
</div>

<div class="col-md-6 mb-3">
<label>Jenis Kelamin</label>
<select name="jenis_kelamin" class="form-select">
<option value="Laki-laki" <?= $mhs['jenis_kelamin']=='Laki-laki'?'selected':''; ?>>Laki-laki</option>
<option value="Perempuan" <?= $mhs['jenis_kelamin']=='Perempuan'?'selected':''; ?>>Perempuan</option>
</select>
</div>

<div class="col-md-6 mb-3">
<label>Agama</label>
<input type="text" name="agama" class="form-control" value="<?= $mhs['agama']; ?>">
</div>

<div class="col-md-6 mb-3">
<label>Tempat Lahir</label>
<input type="text" name="tempat_lahir" class="form-control" value="<?= $mhs['tempat_lahir']; ?>">
</div>

<div class="col-md-6 mb-3">
<label>Tanggal Lahir</label>
<input type="date" name="tanggal_lahir" class="form-control" value="<?= $mhs['tanggal_lahir']; ?>">
</div>

<div class="col-md-6 mb-3">
<label>Status</label>
<select name="status" class="form-select">
<option value="Aktif" <?= $mhs['status']=='Aktif'?'selected':''; ?>>Aktif</option>
<option value="Cuti" <?= $mhs['status']=='Cuti'?'selected':''; ?>>Cuti</option>
<option value="Lulus" <?= $mhs['status']=='Lulus'?'selected':''; ?>>Lulus</option>
</select>
</div>

<div class="col-md-12 mb-3">
<label>Alamat</label>
<textarea name="alamat" class="form-control"><?= $mhs['alamat']; ?></textarea>
</div>

<div class="col-md-12 mb-3">
<label>Foto</label>
<input type="file" name="foto" class="form-control">
</div>

</div>

<button type="submit" name="update_profil" class="btn-save">
<i class="fas fa-save"></i> Simpan
</button>

<a href="profil.php" class="btn-back">Kembali</a>

</form>

</div>
</div>
</div>

</body>
</html>