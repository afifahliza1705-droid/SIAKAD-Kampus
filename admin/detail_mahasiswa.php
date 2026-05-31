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

// cek id
if(!isset($_GET['id'])){
    header("Location: mahasiswa.php");
    exit;
}

$id = intval($_GET['id']);

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

// cek foto
$fotoPath =
"../assets/uploads/mahasiswa/" .
$mhs['foto'];

if(
    !empty($mhs['foto']) &&
    file_exists($fotoPath)
){
    $foto = $fotoPath;
}else{
    // avatar otomatis jika foto kosong
    $foto =
    "https://ui-avatars.com/api/?name=" .
    urlencode($mhs['nama']) .
    "&background=ffffff&color=2563eb&size=300";
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Detail Mahasiswa</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

body{
    background:#f4f7fc;
    font-family:'Segoe UI',sans-serif;
}

.container-box{
    max-width:1200px;
    margin:45px auto;
    padding:20px;
}

.card-custom{
    background:white;
    border-radius:35px;
    overflow:hidden;
    box-shadow:
    0 10px 35px rgba(15,23,42,.08);
}

.left-side{
    background:linear-gradient(
        180deg,
        #2563eb,
        #38bdf8
    );
    padding:50px 30px;
    text-align:center;
    min-height:100%;
}

.profile-box img{
    width:220px;
    height:220px;
    object-fit:cover;
    border-radius:30px;
    border:5px solid rgba(255,255,255,.4);
    box-shadow:
    0 8px 25px rgba(0,0,0,.15);
}

.profile-box h3{
    color:white;
    margin-top:25px;
    font-weight:700;
}

.profile-box p{
    color:rgba(255,255,255,.9);
    font-size:18px;
}

.right-side{
    padding:50px;
}

.title{
    margin-bottom:25px;
}

.title h2{
    font-weight:700;
    color:#0f172a;
}

.title p{
    color:#64748b;
}

.info-card{
    background:#f8fafc;
    border-radius:25px;
    padding:30px;
    border:1px solid #e2e8f0;
}

.table td{
    border:none;
    padding:18px 12px;
}

.label{
    width:250px;
    font-weight:600;
    color:#64748b;
}

.value{
    color:#0f172a;
    font-weight:500;
}

.btn-edit{
    background:linear-gradient(
        135deg,
        #2563eb,
        #38bdf8
    );
    border:none;
    padding:14px 28px;
    border-radius:15px;
    color:white;
    font-weight:600;
}

.btn-back{
    background:#e2e8f0;
    color:#0f172a;
    border:none;
    padding:14px 28px;
    border-radius:15px;
    font-weight:600;
}

.btn-edit:hover,
.btn-back:hover{
    transform:translateY(-2px);
    transition:.3s;
}

</style>

</head>

<body>

<div class="container-box">

<div class="card-custom">

<div class="row g-0">

<!-- kiri -->
<div class="col-md-4 left-side">

<div class="profile-box">

<img src="<?= $foto ?>">

<h3>
<?= htmlspecialchars($mhs['nama']) ?>
</h3>

<p>
<?= htmlspecialchars($mhs['nim']) ?>
</p>

</div>

</div>

<!-- kanan -->
<div class="col-md-8 right-side">

<div class="title">
<h2>Detail Mahasiswa</h2>
<p>Informasi lengkap mahasiswa</p>
</div>

<div class="info-card">

<table class="table">

<tr>
<td class="label">NIM</td>
<td class="value"><?= htmlspecialchars($mhs['nim']) ?></td>
</tr>

<tr>
<td class="label">Nama Lengkap</td>
<td class="value"><?= htmlspecialchars($mhs['nama']) ?></td>
</tr>

<tr>
<td class="label">Jurusan</td>
<td class="value"><?= htmlspecialchars($mhs['jurusan']) ?></td>
</tr>

<tr>
<td class="label">Program Studi</td>
<td class="value"><?= htmlspecialchars($mhs['prodi']) ?></td>
</tr>

<tr>
<td class="label">Email</td>
<td class="value"><?= htmlspecialchars($mhs['email']) ?></td>
</tr>

<tr>
<td class="label">Jenis Kelamin</td>
<td class="value"><?= htmlspecialchars($mhs['jenis_kelamin']) ?></td>
</tr>

<tr>
<td class="label">Agama</td>
<td class="value"><?= htmlspecialchars($mhs['agama']) ?></td>
</tr>

<tr>
<td class="label">Tempat, Tanggal Lahir</td>
<td class="value">
<?= htmlspecialchars($mhs['tempat_lahir']) ?>,
<?= htmlspecialchars($mhs['tanggal_lahir']) ?>
</td>
</tr>

<tr>
<td class="label">Status</td>
<td class="value"><?= htmlspecialchars($mhs['status']) ?></td>
</tr>

<tr>
<td class="label">Alamat</td>
<td class="value"><?= htmlspecialchars($mhs['alamat']) ?></td>
</tr>

</table>

<div class="mt-4">

<a href="edit_mahasiswa.php?id=<?= $mhs['id_mahasiswa'] ?>"
class="btn btn-edit">

<i class="fas fa-pen"></i>
Edit Data

</a>

<a href="mahasiswa.php"
class="btn btn-back">

Kembali

</a>

</div>

</div>

</div>

</div>
</div>
</div>

</body>
</html>