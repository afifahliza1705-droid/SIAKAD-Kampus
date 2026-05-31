<?php
session_start();
include '../config/koneksi.php';

// PROTEKSI MAHASISWA
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: ../auth/login.php");
    exit;
}

$id_mahasiswa = $_SESSION['id_ref'];

// AMBIL DATA MAHASISWA
$query = mysqli_query($koneksi, "
    SELECT * FROM mahasiswa
    WHERE id_mahasiswa='$id_mahasiswa'
");

$mhs = mysqli_fetch_assoc($query);

if (!$mhs) {
    die("Data tidak ditemukan!");
}

// FOTO HANDLING
$fotoPath = "../assets/uploads/mahasiswa/" . $mhs['foto'];

if (!empty($mhs['foto']) && file_exists($fotoPath)) {
    $foto = $fotoPath;
} else {
    $foto = "https://ui-avatars.com/api/?name=" . urlencode($mhs['nama']) . "&background=2563eb&color=fff&size=300";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Profil Mahasiswa</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    background:#f1f5f9;
}

/* SIDEBAR (SAMA KRS) */
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

/* TOPBAR (SAMA KRS) */
.topbar{
    background:white;
    padding:20px 25px;
    border-radius:20px;
    box-shadow:0 10px 30px rgba(0,0,0,.06);
    margin-bottom:25px;
}

.topbar h3{
    font-weight:800;
    color:#0f172a;
}

.topbar p{
    margin:0;
    color:#64748b;
}

/* CARD STYLE KRS */
.card-custom{
    background:white;
    border-radius:20px;
    padding:25px;
    box-shadow:0 10px 30px rgba(0,0,0,.06);
    margin-bottom:25px;
}

/* PROFILE GRID */
.profile-wrapper{
    display:grid;
    grid-template-columns: 250px 1fr;
    gap:25px;
    align-items:start;
}

/* FOTO */
.profile-photo{
    width:250px;
    height:300px;
    border-radius:18px;
    overflow:hidden;
    border:3px solid #e2e8f0;
    box-shadow:0 10px 25px rgba(0,0,0,.10);
}

.profile-photo img{
    width:100%;
    height:100%;
    object-fit:cover;
}

/* TABLE */
.table-profile td{
    padding:10px 12px;
    font-size:14px;
    border-bottom:1px solid #e2e8f0;
}

.table-profile td:first-child{
    font-weight:700;
    color:#1e3a8a;
    width:200px;
}

.table-profile td:nth-child(2){
    color:#334155;
}

/* ICON EDIT */
.edit-icon{
    position:absolute;
    top:18px;
    right:18px;
}

.card-custom{
    position:relative;
}

.edit-icon a{
    width:42px;
    height:42px;
    display:flex;
    align-items:center;
    justify-content:center;
    background:#eff6ff;
    color:#2563eb;
    border-radius:12px;
    text-decoration:none;
    transition:.2s;
}

.edit-icon a:hover{
    background:#2563eb;
    color:white;
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
        <a href="dashboard.php"><i class="fas fa-house"></i> Dashboard</a>
        <a href="krs.php"><i class="fas fa-book-open"></i> KRS</a>
        <a href="khs.php"><i class="fas fa-chart-line"></i> KHS / Nilai</a>
        <a href="jadwal.php"><i class="fas fa-calendar-days"></i> Jadwal</a>
        <a href="profil.php" class="active"><i class="fas fa-user"></i> Profil</a>
        <a href="ganti_password.php"><i class="fas fa-key"></i> Password</a>
        <a href="logout.php"><i class="fas fa-right-from-bracket"></i> Logout</a>
    </div>

</div>

<!-- CONTENT -->
<div class="content">

    <div class="topbar">
        <h3>Profil Mahasiswa</h3>
        <p>Data lengkap mahasiswa aktif</p>
    </div>

    <div class="card-custom">

        <!-- EDIT ICON -->
        <div class="edit-icon">
            <a href="update_profil.php">
                <i class="fas fa-pen"></i>
            </a>
        </div>

        <div class="profile-wrapper">

            <!-- FOTO -->
            <div class="profile-photo">
                <img src="<?= $foto ?>">
            </div>

            <!-- DATA -->
            <table class="table-profile w-100">

                <tr><td>NIM</td><td><?= $mhs['nim'] ?></td></tr>
                <tr><td>Nama</td><td><b><?= $mhs['nama'] ?></b></td></tr>
                <tr><td>Jurusan</td><td><?= $mhs['jurusan'] ?></td></tr>
                <tr><td>Prodi</td><td><?= $mhs['prodi'] ?></td></tr>
                <tr><td>Email</td><td><?= $mhs['email'] ?></td></tr>
                <tr><td>Jenis Kelamin</td><td><?= $mhs['jenis_kelamin'] ?></td></tr>
                <tr><td>Agama</td><td><?= $mhs['agama'] ?></td></tr>
                <tr><td>Tempat Lahir</td><td><?= $mhs['tempat_lahir'] ?></td></tr>
                <tr><td>Tanggal Lahir</td><td><?= $mhs['tanggal_lahir'] ?></td></tr>
                <tr><td>Status</td><td><?= $mhs['status'] ?></td></tr>
                <tr><td>Alamat</td><td><?= $mhs['alamat'] ?></td></tr>

            </table>

        </div>

    </div>

</div>

</body>
</html>