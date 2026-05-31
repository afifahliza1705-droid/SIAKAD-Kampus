<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../config/koneksi.php';

// PROTEKSI
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: dosen.php");
    exit;
}

$id = mysqli_real_escape_string($koneksi, $_GET['id']);

// DOSEN
$dosen = mysqli_fetch_assoc(mysqli_query($koneksi,"
    SELECT * FROM dosen WHERE id_dosen='$id'
"));

// MAHASISWA PA
$mhs_pa = mysqli_query($koneksi,"
    SELECT * FROM mahasiswa WHERE id_dosen_pa='$id' ORDER BY nama ASC
");

// FOTO DOSEN
$fotoDosenPath = "../assets/uploads/dosen/" . ($dosen['foto'] ?? '');
$fotoDosen = (!empty($dosen['foto']) && file_exists($fotoDosenPath))
    ? $fotoDosenPath
    : "https://ui-avatars.com/api/?name=" . urlencode($dosen['nama']) . "&background=2563eb&color=fff&size=300";
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Detail Dosen</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

/* =========================
   BACKGROUND WAH (ANIMATED)
========================= */
body{
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background: linear-gradient(135deg,#0f172a,#1e3a8a,#2563eb,#38bdf8);
    background-size:400% 400%;
    animation: gradientMove 12s ease infinite;
    color:#fff;
}

@keyframes gradientMove{
    0%{background-position:0% 50%}
    50%{background-position:100% 50%}
    100%{background-position:0% 50%}
}

/* DARK OVERLAY */
.overlay{
    position:fixed;
    top:0;left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,.35);
    z-index:-1;
}

/* WRAPPER */
.wrapper{
    max-width:1200px;
    margin:40px auto;
    padding:20px;
}

/* =========================
   DOSEN CARD (GLASS STYLE)
========================= */
.dosen-card{
    display:flex;
    border-radius:30px;
    overflow:hidden;
    backdrop-filter: blur(12px);
    background: rgba(255,255,255,0.10);
    border:1px solid rgba(255,255,255,0.2);
    box-shadow:0 25px 60px rgba(0,0,0,.3);
}

/* LEFT */
.left{
    width:35%;
    padding:40px;
    text-align:center;
    background: rgba(255,255,255,0.12);
}

.left img{
    width:170px;
    height:170px;
    border-radius:25px;
    object-fit:cover;
    border:4px solid rgba(255,255,255,.5);
    box-shadow:0 10px 30px rgba(0,0,0,.4);
    transition:.3s;
}

.left img:hover{
    transform:scale(1.05);
}

.left h3{
    margin-top:15px;
    font-weight:800;
}

.left small{
    opacity:.9;
}

/* RIGHT */
.right{
    width:65%;
    padding:40px;
    background: rgba(255,255,255,0.08);
}

.title{
    font-weight:800;
    margin-bottom:20px;
}

/* INFO TABLE */
.info table{
    width:100%;
}

.info td{
    padding:10px;
    font-size:14px;
}

.label{
    width:180px;
    font-weight:600;
    color:#dbeafe;
}

.value{
    font-weight:700;
    color:#ffffff;
}

/* =========================
   MAHASISWA CARD
========================= */
.mhs-card{
    margin-top:30px;
    padding:25px;
    border-radius:25px;
    backdrop-filter: blur(10px);
    background: rgba(255,255,255,0.10);
    border:1px solid rgba(255,255,255,0.2);
    box-shadow:0 20px 50px rgba(0,0,0,.25);
}

.mhs-title{
    font-weight:800;
}

.badge-count{
    background:#38bdf8;
    padding:6px 12px;
    border-radius:12px;
    font-size:12px;
    color:#0f172a;
    font-weight:700;
}

/* TABLE */
.table{
    color:white;
}

.table th{
    background:rgba(255,255,255,.15);
    font-size:13px;
}

.table td{
    font-size:13px;
    vertical-align:middle;
}

/* FOTO */
.avatar{
    width:45px;
    height:45px;
    border-radius:50%;
    border:2px solid #38bdf8;
}

/* =========================
   BUTTON BACK
========================= */
.back-btn{
    display:inline-flex;
    align-items:center;
    gap:8px;
    margin-bottom:20px;
    padding:10px 18px;
    border-radius:12px;
    background:rgba(255,255,255,0.15);
    color:white;
    text-decoration:none;
    font-weight:600;
    transition:.3s;
}

.back-btn:hover{
    background:#38bdf8;
    color:#0f172a;
    transform:translateY(-2px);
}

</style>
</head>

<body>

<div class="overlay"></div>

<div class="wrapper">

<!-- BUTTON BACK -->
<a href="dosen.php" class="back-btn">
<i class="fas fa-arrow-left"></i> Kembali
</a>

<!-- DOSEN CARD -->
<div class="dosen-card">

    <div class="left">
        <img src="<?= $fotoDosen ?>">
        <h3><?= htmlspecialchars($dosen['nama']) ?></h3>
        <small><?= $dosen['nip'] ?></small>
    </div>

    <div class="right">

        <h2 class="title">Detail Biodata Dosen</h2>

        <div class="info">
            <table>
                <tr><td class="label">NIP</td><td class="value"><?= $dosen['nip'] ?></td></tr>
                <tr><td class="label">Nama</td><td class="value"><?= $dosen['nama'] ?></td></tr>
                <tr><td class="label">JK</td><td class="value"><?= $dosen['jenis_kelamin'] ?></td></tr>
                <tr><td class="label">Jabatan</td><td class="value"><?= $dosen['jabatan'] ?></td></tr>
                <tr><td class="label">Fakultas</td><td class="value"><?= $dosen['fakultas'] ?></td></tr>
                <tr><td class="label">Prodi</td><td class="value"><?= $dosen['prodi'] ?></td></tr>
                <tr><td class="label">Email</td><td class="value"><?= $dosen['email'] ?></td></tr>
                <tr><td class="label">HP</td><td class="value"><?= $dosen['no_hp'] ?></td></tr>
                <tr><td class="label">Alamat</td><td class="value"><?= $dosen['alamat'] ?></td></tr>
            </table>
        </div>

    </div>

</div>

<!-- MAHASISWA PA -->
<div class="mhs-card">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mhs-title">Mahasiswa PA</h3>
        <span class="badge-count"><?= mysqli_num_rows($mhs_pa) ?> Mahasiswa</span>
    </div>

    <table class="table table-hover">

        <thead>
        <tr>
            <th>Foto</th>
            <th>NIM</th>
            <th>Nama</th>
            <th>Prodi</th>
            <th>Jurusan</th>
            <th>Tgl Lahir</th>
            <th>Email</th>
        </tr>
        </thead>

        <tbody>

        <?php while($m = mysqli_fetch_assoc($mhs_pa)):

            $foto = "../assets/uploads/mahasiswa/" . ($m['foto'] ?? '');
            $foto = (!empty($m['foto']) && file_exists($foto))
                ? $foto
                : "https://ui-avatars.com/api/?name=" . urlencode($m['nama']) . "&background=38bdf8&color=0f172a";

        ?>

        <tr>
            <td><img src="<?= $foto ?>" class="avatar"></td>
            <td><?= $m['nim'] ?></td>
            <td><?= $m['nama'] ?></td>
            <td><?= $m['prodi'] ?? '-' ?></td>
            <td><?= $m['jurusan'] ?? '-' ?></td>
            <td><?= $m['tgl_lahir'] ?? '-' ?></td>
            <td><?= $m['email'] ?? '-' ?></td>
        </tr>

        <?php endwhile; ?>

        </tbody>

    </table>

</div>

</div>

</body>
</html>