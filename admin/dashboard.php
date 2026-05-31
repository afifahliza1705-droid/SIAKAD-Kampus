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

// statistik
$total_mahasiswa = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM mahasiswa"));
$total_dosen = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM dosen"));
$total_user = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM users"));
$total_mk = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM mata_kuliah"));
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard Admin SIAKAD</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

/* BASE */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

body{
    background:#f1f5f9;
}

/* SIDEBAR */
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
}

.menu a:hover,
.menu .active{
    background:rgba(56,189,248,.15);
    color:#38bdf8;
    transform:translateX(5px);
}

/* CONTENT */
.content{
    margin-left:270px;
    padding:30px;
}

/* TOPBAR */
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
}

.topbar p{
    color:#64748b;
    margin-bottom:0;
}

/* RIGHT INFO */
.topbar-right{
    text-align:right;
    font-weight:600;
    color:#334155;
}

/* STAT CARDS */
.card-box{
    padding:25px;
    border-radius:20px;
    color:white;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
    transition:.3s;
}

.card-box:hover{
    transform:translateY(-8px);
}

.card-box i{
    font-size:45px;
    opacity:.7;
}

.card-box h2{
    font-size:32px;
    font-weight:800;
}

.card-box p{
    margin:0;
    opacity:.9;
}

/* COLORS */
.bg1{background:linear-gradient(135deg,#2563eb,#1d4ed8);}
.bg2{background:linear-gradient(135deg,#7c3aed,#6d28d9);}
.bg3{background:linear-gradient(135deg,#059669,#047857);}
.bg4{background:linear-gradient(135deg,#ea580c,#c2410c);}

/* EXTRA CARD */
.card-mini{
    background:white;
    border-radius:18px;
    padding:15px;
    box-shadow:0 10px 20px rgba(0,0,0,.06);
    font-size:14px;
    color:#334155;
}

/* TABLE */
.table-card{
    background:white;
    margin-top:30px;
    padding:25px;
    border-radius:20px;
    box-shadow:0 10px 30px rgba(0,0,0,.06);
}

.table-card h4{
    font-weight:800;
    margin-bottom:20px;
}

.table th{
    background:#eff6ff;
    color:#1e3a8a;
    font-size:13px;
}

.table td{
    font-size:13px;
    vertical-align:middle;
}

.badge{
    padding:8px 12px;
    border-radius:10px;
}

</style>

</head>

<body>

<div class="sidebar">

    <div class="brand">
        <i class="fas fa-graduation-cap"></i>
        <h3>SIAKAD</h3>
        <small>Admin Panel</small>
    </div>

    <div class="menu">

        <a href="dashboard.php" class="active">
            <i class="fas fa-house"></i> Dashboard
        </a>

        <a href="mahasiswa.php">
            <i class="fas fa-user-graduate"></i> Mahasiswa
        </a>

        <a href="dosen.php">
            <i class="fas fa-chalkboard-user"></i> Dosen
        </a>

        <a href="mata_kuliah.php">
            <i class="fas fa-book-open"></i> Mata Kuliah
        </a>

        <a href="jadwal.php">
            <i class="fas fa-calendar"></i> Jadwal
        </a>

        <a href="kelas.php">
            <i class="fas fa-layer-group"></i> Kelas
        </a>

        <a href="atur_pa.php">
            <i class="fas fa-users"></i> Pengaturan PA
        </a>

        <a href="ganti_password.php">
            <i class="fas fa-key"></i> Ganti Password
        </a>

        <a href="../auth/logout.php">
            <i class="fas fa-right-from-bracket"></i> Logout
        </a>

    </div>

</div>

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

    <!-- STATISTIK -->
    <div class="row g-4">

        <div class="col-md-3">
            <div class="card-box bg1">
                <i class="fas fa-user-graduate"></i>
                <h2><?= $total_mahasiswa ?></h2>
                <p>Mahasiswa</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card-box bg2">
                <i class="fas fa-chalkboard-user"></i>
                <h2><?= $total_dosen ?></h2>
                <p>Dosen</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card-box bg3">
                <i class="fas fa-book"></i>
                <h2><?= $total_mk ?></h2>
                <p>Mata Kuliah</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card-box bg4">
                <i class="fas fa-users"></i>
                <h2><?= $total_user ?></h2>
                <p>User</p>
            </div>
        </div>

    </div>

    <!-- EXTRA INFO (UPDATED) -->
<div class="row mt-4">

    <div class="col-md-4">
        <div class="card-mini">
            🎓 <b>Semester Aktif</b><br>
            Genap 2025/2026 — Sistem akademik sedang berjalan aktif untuk input nilai & KRS.
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-mini">
            📊 <b>Aktivitas Data</b><br>
            Data mahasiswa & dosen diperbarui secara berkala melalui sistem admin.
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-mini">
            ⚙️ <b>Status Sistem</b><br>
            Server akademik berjalan stabil • response normal • tidak ada gangguan.
        </div>
    </div>

</div>

    <!-- TABLE -->
    <div class="table-card">

        <h4>Aktivitas Sistem</h4>

        <table class="table table-hover">

            <thead>
                <tr>
                    <th>No</th>
                    <th>Aktivitas</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>

                <tr>
                    <td>1</td>
                    <td>Login Admin</td>
                    <td><span class="badge bg-success">Berhasil</span></td>
                </tr>

                <tr>
                    <td>2</td>
                    <td>Monitoring Data Akademik</td>
                    <td><span class="badge bg-primary">Aktif</span></td>
                </tr>

            </tbody>

        </table>

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
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

    let hour = now.getHours();
    let greet = "";

    if (hour < 11) greet = "Selamat Pagi";
    else if (hour < 15) greet = "Selamat Siang";
    else if (hour < 18) greet = "Selamat Sore";
    else greet = "Selamat Malam";

    document.getElementById("greeting").innerText =
        greet + ", <?= $_SESSION['username']; ?> 👋";
}

setInterval(updateTime, 1000);
updateTime();
</script>

</body>
</html>