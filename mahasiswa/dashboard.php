<?php
session_start();
include '../config/koneksi.php';

// ==========================
// PROTEKSI MAHASISWA
// ==========================
if (!isset($_SESSION['role'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SESSION['role'] != 'mahasiswa') {
    header("Location: ../auth/login.php");
    exit;
}

// ==========================
// AMBIL DATA MAHASISWA LOGIN
// ==========================
$id_mahasiswa = $_SESSION['id_ref'];

$query = mysqli_query($koneksi, "
    SELECT * FROM mahasiswa
    WHERE id_mahasiswa='$id_mahasiswa'
");

$mahasiswa = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard Mahasiswa SIAKAD</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>

/* ==============================
   BASE
============================== */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    background:#f1f5f9;
}

/* ==============================
   SIDEBAR
============================== */
.sidebar{
    width:270px;
    height:100vh;
    position:fixed;
    background:linear-gradient(180deg,#0f172a,#1e293b);
    padding:30px 20px;
    color:white;
}

.brand{
    text-align:center;
    margin-bottom:35px;
}

.brand i{
    font-size:55px;
    color:#38bdf8;
}

.brand h3{
    margin-top:10px;
    font-weight:800;
}

.menu a{
    display:flex;
    gap:10px;
    align-items:center;
    padding:14px;
    color:#cbd5e1;
    text-decoration:none;
    border-radius:12px;
    margin-bottom:8px;
    transition:.2s;
    font-weight:500;
}

.menu a:hover,
.menu .active{
    background:rgba(56,189,248,.15);
    color:#38bdf8;
    transform:translateX(5px);
}

/* ==============================
   CONTENT
============================== */
.content{
    margin-left:270px;
    padding:30px;
}

/* ==============================
   TOPBAR
============================== */
.topbar{
    background:white;
    padding:20px 25px;
    border-radius:20px;
    box-shadow:0 10px 30px rgba(0,0,0,.06);
    margin-bottom:25px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.topbar h3{
    font-weight:800;
    margin-bottom:3px;
}

.topbar p{
    color:#64748b;
    margin-bottom:0;
}

.topbar-right{
    text-align:right;
    font-weight:600;
    color:#334155;
}

/* ==============================
   PROFILE CARD
============================== */
.profile-card{
    background:white;
    border-radius:22px;
    padding:30px;
    box-shadow:0 10px 30px rgba(0,0,0,.06);
    margin-bottom:25px;
}

.profile-content{
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.profile-left h4{
    font-weight:800;
    margin-bottom:20px;
    color:#0f172a;
}

.data-mahasiswa{
    display:grid;
    grid-template-columns:180px 15px auto;
    gap:10px;
}

.data-mahasiswa p{
    margin-bottom:12px;
    color:#334155;
    font-size:15px;
}

.label{
    font-weight:600;
}

.profile-img{
    width:145px;
    height:145px;
    border-radius:20px;
    object-fit:cover;
    border:5px solid #e2e8f0;
}

/* ==============================
   MENU CARD
============================== */
.card-box{
    padding:25px;
    border-radius:20px;
    color:white;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
    transition:.3s;
    height:100%;
    text-decoration:none;
    display:block;
}

.card-box:hover{
    transform:translateY(-8px);
}

.card-box i{
    font-size:45px;
    opacity:.7;
    margin-bottom:15px;
}

.card-box h4{
    font-size:24px;
    font-weight:800;
}

.card-box p{
    margin:0;
    opacity:.9;
}

/* WARNA */
.bg1{
    background:linear-gradient(135deg,#2563eb,#1d4ed8);
}

.bg2{
    background:linear-gradient(135deg,#7c3aed,#6d28d9);
}

.bg3{
    background:linear-gradient(135deg,#059669,#047857);
}

.bg4{
    background:linear-gradient(135deg,#ea580c,#c2410c);
}

/* ==============================
   INFO CARD
============================== */
.card-mini{
    background:white;
    border-radius:18px;
    padding:15px;
    box-shadow:0 10px 20px rgba(0,0,0,.06);
    font-size:14px;
    color:#334155;
}

</style>

</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">

    <div class="brand">
        <i class="fas fa-user-graduate"></i>
        <h3>SIAKAD</h3>
        <small>Mahasiswa Panel</small>
    </div>

    <div class="menu">

        <a href="dashboard.php" class="active">
            <i class="fas fa-house"></i> Dashboard
        </a>

        <a href="krs.php">
            <i class="fas fa-book-open"></i> KRS
        </a>

        <a href="khs.php">
            <i class="fas fa-chart-line"></i> KHS / Nilai
        </a>

        <a href="jadwal.php">
            <i class="fas fa-calendar-days"></i> Jadwal Kuliah
        </a>

        <a href="profil.php">
            <i class="fas fa-user"></i> Profil
        </a>

        <a href="ganti_password.php">
            <i class="fas fa-key"></i> Ganti Password
        </a>

        <a href="logout.php">
            <i class="fas fa-right-from-bracket"></i> Logout
        </a>

    </div>

</div>

<!-- CONTENT -->
<div class="content">

    <!-- TOPBAR -->
    <div class="topbar">

        <div>
            <h3 id="greeting"></h3>
            <p>Sistem Informasi Akademik Universitas</p>
        </div>

        <div class="topbar-right">
            <div id="tanggal"></div>
            <div id="jam"></div>
        </div>

    </div>

    <!-- PROFILE -->
    <div class="profile-card">

        <div class="profile-content">

            <div class="profile-left">

                <h4>Informasi Mahasiswa</h4>

                <div class="data-mahasiswa">

                    <p class="label">Nama Lengkap</p>
                    <p>:</p>
                    <p><?= $mahasiswa['nama']; ?></p>

                    <p class="label">NIM</p>
                    <p>:</p>
                    <p><?= $mahasiswa['nim']; ?></p>

                    <p class="label">Program Studi</p>
                    <p>:</p>
                    <p><?= $mahasiswa['prodi']; ?></p>

                    <p class="label">Jurusan</p>
                    <p>:</p>
                    <p><?= $mahasiswa['jurusan']; ?></p>

                    <p class="label">Email</p>
                    <p>:</p>
                    <p><?= $mahasiswa['email']; ?></p>

                    <p class="label">Status</p>
                    <p>:</p>
                    <p><?= $mahasiswa['status']; ?></p>

                </div>

            </div>

            <div>
                <?php
$fotoPath = "../assets/uploads/mahasiswa/" . $mahasiswa['foto'];

if (!empty($mahasiswa['foto']) && file_exists($fotoPath)) {
    $foto = $fotoPath;
} else {
    $foto = "https://cdn-icons-png.flaticon.com/512/3135/3135715.png";
}
?>

<div>
    <img src="<?= $foto ?>" class="profile-img">
</div>
            </div>

        </div>

    </div>

    <!-- MENU -->
    <div class="row g-4">

        <div class="col-md-3">
            <a href="krs.php" class="card-box bg1">
                <i class="fas fa-book-open"></i>
                <h4>KRS</h4>
                <p>Isi KRS & lihat status ACC dosen pembimbing.</p>
            </a>
        </div>

        <div class="col-md-3">
            <a href="khs.php" class="card-box bg2">
                <i class="fas fa-chart-line"></i>
                <h4>KHS</h4>
                <p>Lihat nilai mata kuliah dan IP semester.</p>
            </a>
        </div>

        <div class="col-md-3">
            <a href="jadwal.php" class="card-box bg3">
                <i class="fas fa-calendar-alt"></i>
                <h4>Jadwal</h4>
                <p>Lihat jadwal perkuliahan mahasiswa.</p>
            </a>
        </div>

        <div class="col-md-3">
            <a href="profil.php" class="card-box bg4">
                <i class="fas fa-user-gear"></i>
                <h4>Profil</h4>
                <p>Kelola data diri dan akun mahasiswa.</p>
            </a>
        </div>

    </div>

    <!-- EXTRA INFO -->
    <div class="row mt-4">

        <div class="col-md-4">
            <div class="card-mini">
                🎓 <b>Semester Aktif</b><br>
                Semester Genap 2025/2026 sedang berlangsung aktif.
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-mini">
                📚 <b>Status Akademik</b><br>
                Pastikan KRS telah disetujui dosen pembimbing.
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-mini">
                ⚙️ <b>Status Sistem</b><br>
                Server akademik berjalan normal tanpa gangguan.
            </div>
        </div>

    </div>

</div>

<script>

// JAM REAL TIME
function updateTime() {

    const now = new Date();

    document.getElementById("jam").innerText =
        now.toLocaleTimeString("id-ID");

    document.getElementById("tanggal").innerText =
        now.toLocaleDateString("id-ID", {
            weekday:'long',
            year:'numeric',
            month:'long',
            day:'numeric'
        });

    let hour = now.getHours();
    let greet = "";

    if(hour < 11) greet = "Selamat Pagi";
    else if(hour < 15) greet = "Selamat Siang";
    else if(hour < 18) greet = "Selamat Sore";
    else greet = "Selamat Malam";

    document.getElementById("greeting").innerText =
        greet + ", <?= $mahasiswa['nama']; ?> 👋";
}

setInterval(updateTime,1000);
updateTime();

</script>

</body>
</html>

