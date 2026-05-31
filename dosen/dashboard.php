<?php
session_start();
include '../config/koneksi.php';

// ==========================
// PROTEKSI DOSEN
// ==========================
if (!isset($_SESSION['role'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SESSION['role'] != 'dosen') {
    header("Location: ../auth/login.php");
    exit;
}

// ==========================
// AMBIL DATA DOSEN LOGIN
// ==========================
$id_dosen = $_SESSION['id_ref'];

$query = mysqli_query($koneksi, "
    SELECT * FROM dosen
    WHERE id_dosen='$id_dosen'
");

$dosen = mysqli_fetch_assoc($query);

// ==========================
// STATISTIK DASHBOARD
// ==========================

// jumlah mahasiswa PA
$qMahasiswaPA = mysqli_query($koneksi,"
    SELECT COUNT(*) as total
    FROM mahasiswa
    WHERE id_dosen_pa='$id_dosen'
");

$totalMahasiswaPA =
mysqli_fetch_assoc($qMahasiswaPA)['total'];

// jumlah mata kuliah diampu
$qMK = mysqli_query($koneksi,"
    SELECT COUNT(*) as total
    FROM mata_kuliah
    WHERE id_dosen='$id_dosen'
");

$totalMK =
mysqli_fetch_assoc($qMK)['total'];

// jumlah krs pending
$qKRS = mysqli_query($koneksi,"
    SELECT COUNT(*) as total
    FROM krs k
    JOIN mata_kuliah mk
    ON k.id_mk = mk.id_mk
    WHERE mk.id_dosen='$id_dosen'
    AND k.status='pending'
");

$totalKRSPending =
mysqli_fetch_assoc($qKRS)['total'];

// jumlah nilai belum input
$qNilai = mysqli_query($koneksi,"
    SELECT COUNT(*) as total
    FROM nilai n
    JOIN mata_kuliah mk
    ON n.id_mk = mk.id_mk
    WHERE mk.id_dosen='$id_dosen'
    AND (
        n.nilai_akhir IS NULL
        OR n.nilai_akhir = ''
    )
");

$totalNilai =
mysqli_fetch_assoc($qNilai)['total'];

// jadwal hari ini
$hari = date('l');

$hariIndonesia = [
    'Sunday' => 'Minggu',
    'Monday' => 'Senin',
    'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis',
    'Friday' => 'Jumat',
    'Saturday' => 'Sabtu'
];

$hariIni = $hariIndonesia[$hari];

$qJadwal = mysqli_query($koneksi,"
    SELECT jk.*, mk.nama_mk
    FROM jadwal_kuliah jk
    JOIN mata_kuliah mk
    ON jk.id_mk = mk.id_mk
    WHERE jk.id_dosen='$id_dosen'
    AND jk.hari='$hariIni'
    ORDER BY jk.jam_mulai ASC
");

$jadwalHariIni =
mysqli_num_rows($qJadwal);

// FOTO DOSEN
$fotoPath =
"../assets/uploads/dosen/" .
$dosen['foto'];

if (
    !empty($dosen['foto'])
    && file_exists($fotoPath)
) {
    $foto = $fotoPath;
} else {
    $foto =
    "https://cdn-icons-png.flaticon.com/512/3135/3135715.png";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard Dosen SIAKAD</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    background:#eef4ff;
}

/* SIDEBAR */
.sidebar{
    width:280px;
    height:100vh;
    position:fixed;
    background:linear-gradient(180deg,#0f172a,#172554);
    padding:30px 20px;
    color:white;
    overflow-y:auto;
}

.brand{
    text-align:center;
    margin-bottom:35px;
}

.brand i{
    font-size:55px;
    color:#60a5fa;
}

.brand h3{
    margin-top:10px;
    font-weight:800;
}

.brand small{
    color:#cbd5e1;
}

.menu a{
    display:flex;
    align-items:center;
    gap:12px;
    padding:14px;          /* FIX dari 15 → 14 */
    color:#cbd5e1;
    text-decoration:none;
    border-radius:14px;
    margin-bottom:8px;
    transition:.3s;
    font-weight:500;
    font-size:14px;        /* TAMBAHAN penting biar sama */
}

.menu a:hover,
.menu .active{
    background:rgba(96,165,250,.15);
    color:#60a5fa;
    transform:translateX(5px);
}

/* CONTENT */
.content{
    margin-left:280px;
    padding:30px;
}

/* TOPBAR */
.topbar{
    background:white;
    border-radius:24px;
    padding:20px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 15px 40px rgba(0,0,0,.08);
    margin-bottom:25px;
}

.topbar h3{
    font-weight:800;
    margin:0;
}

.topbar p{
    color:#64748b;
    margin-top:5px;
}

.time-box{
    text-align:right;
    font-weight:600;
    color:#334155;
}

/* PROFILE */
.profile-card{
    background:white;
    border-radius:30px;
    padding:35px;
    box-shadow:0 15px 40px rgba(0,0,0,.08);
    margin-bottom:30px;
}

.profile-content{
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.profile-left h4{
    font-weight:800;
    margin-bottom:25px;
}

.data-dosen{
    display:grid;
    grid-template-columns:200px 20px auto;
}

.data-dosen p{
    margin-bottom:13px;
    color:#334155;
}

.label{
    font-weight:600;
}

.profile-img{
    width:170px;
    height:170px;
    border-radius:30px;
    object-fit:cover;
    border:6px solid #dbeafe;
}

/* STAT CARD */
.stat-card{
    border-radius:25px;
    padding:25px;
    color:white;
    transition:.3s;
    box-shadow:0 10px 30px rgba(0,0,0,.08);
}

.stat-card:hover{
    transform:translateY(-8px);
}

.stat-card i{
    font-size:45px;
    opacity:.8;
}

.stat-card h2{
    font-size:35px;
    font-weight:800;
    margin-top:15px;
}

.stat-card p{
    margin:0;
    font-weight:500;
}

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

/* MENU CARD */
.menu-card{
    background:white;
    border-radius:25px;
    padding:25px;
    text-decoration:none;
    display:block;
    color:#0f172a;
    box-shadow:0 10px 25px rgba(0,0,0,.06);
    transition:.3s;
    height:100%;
}

.menu-card:hover{
    transform:translateY(-7px);
}

.menu-card i{
    font-size:45px;
    margin-bottom:15px;
    color:#2563eb;
}

.menu-card h4{
    font-weight:700;
}

.menu-card p{
    color:#64748b;
}

/* REMINDER */
.reminder{
    background:white;
    border-radius:25px;
    padding:25px;
    margin-top:30px;
    box-shadow:0 10px 30px rgba(0,0,0,.06);
}

.reminder h4{
    font-weight:800;
    margin-bottom:20px;
}

.alert-box{
    padding:18px;
    border-radius:16px;
    margin-bottom:15px;
    font-weight:500;
}

.warning{
    background:#fef3c7;
}

.success{
    background:#dcfce7;
}

.info{
    background:#dbeafe;
}

</style>
</head>

<body>

<div class="sidebar">

    <div class="brand">
        <i class="fas fa-chalkboard-teacher"></i>
        <h3>SIAKAD</h3>
        <small>Dosen Panel</small>
    </div>

    <div class="menu">

        <a href="dashboard.php" class="active">
            <i class="fas fa-house"></i> Dashboard
        </a>

        <a href="mahasiswa_pa.php">
            <i class="fas fa-users"></i> Mahasiswa PA
        </a>

        <a href="acc_krs.php">
            <i class="fas fa-file-signature"></i> ACC KRS
        </a>

        <a href="input_nilai.php">
            <i class="fas fa-pen"></i> Input Nilai
        </a>

        <a href="mahasiswa_ajar.php">
            <i class="fas fa-user-graduate"></i> Daftar Mahasiswa
        </a>

        <a href="jadwal.php">
            <i class="fas fa-calendar-days"></i> Jadwal Mengajar
        </a>

        <a href="profil.php">
            <i class="fas fa-user"></i> Profil
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

    <div class="topbar">
        <div>
            <h3 id="greeting"></h3>
            <p>Sistem Informasi Akademik Universitas</p>
        </div>

        <div class="time-box">
            <div id="tanggal"></div>
            <div id="jam"></div>
        </div>
    </div>

    <!-- PROFILE -->
    <div class="profile-card">

        <div class="profile-content">

            <div class="profile-left">

                <h4>Informasi Dosen</h4>

                <div class="data-dosen">

                    <p class="label">Nama</p><p>:</p><p><?= $dosen['nama']; ?></p>
                    <p class="label">NIP</p><p>:</p><p><?= $dosen['nip']; ?></p>
                    <p class="label">Jenis Kelamin</p><p>:</p><p>
    <?= !empty($dosen['jenis_kelamin']) ? $dosen['jenis_kelamin'] : '-' ?>
</p>
                    <p class="label">Jabatan</p><p>:</p><p><?= $dosen['jabatan']; ?></p>
                    <p class="label">Fakultas</p><p>:</p><p><?= $dosen['fakultas']; ?></p>
                    <p class="label">Prodi</p><p>:</p><p><?= $dosen['prodi']; ?></p>
                    <p class="label">Email</p><p>:</p><p><?= $dosen['email']; ?></p>
                    <p class="label">No HP</p><p>:</p><p><?= $dosen['no_hp']; ?></p>
                    <p class="label">Alamat</p><p>:</p><p><?= $dosen['alamat']; ?></p>

                </div>

            </div>

            <img src="<?= $foto ?>" class="profile-img">

        </div>
    </div>

    <br>

<div class="row g-4">

    <div class="col-md-3">
        <div class="stat-card bg1">
            <i class="fas fa-users"></i>
            <h2><?= $totalMahasiswaPA ?></h2>
            <p>Mahasiswa PA</p>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card bg2">
            <i class="fas fa-book"></i>
            <h2><?= $totalMK ?></h2>
            <p>Mata Kuliah</p>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card bg3">
            <i class="fas fa-file-signature"></i>
            <h2><?= $totalKRSPending ?></h2>
            <p>KRS Pending</p>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card bg4">
            <i class="fas fa-exclamation-circle"></i>
            <h2><?= $totalNilai ?></h2>
            <p>Nilai Belum Input</p>
        </div>
    </div>

</div>

<br>

<div class="row g-4">

    <div class="col-md-3">
        <a href="mahasiswa_pa.php" class="menu-card">
            <i class="fas fa-users"></i>
            <h4>Mahasiswa PA</h4>
            <p>Daftar mahasiswa bimbingan akademik</p>
        </a>
    </div>

    <div class="col-md-3">
        <a href="acc_krs.php" class="menu-card">
            <i class="fas fa-file-signature"></i>
            <h4>ACC KRS</h4>
            <p>Validasi pengisian KRS mahasiswa</p>
        </a>
    </div>

    <div class="col-md-3">
        <a href="input_nilai.php" class="menu-card">
            <i class="fas fa-pen"></i>
            <h4>Input Nilai</h4>
            <p>Input nilai mahasiswa per mata kuliah</p>
        </a>
    </div>

    <div class="col-md-3">
        <a href="jadwal.php" class="menu-card">
            <i class="fas fa-calendar"></i>
            <h4>Jadwal Mengajar</h4>
            <p>Lihat jadwal perkuliahan Anda</p>
        </a>
    </div>

</div>

<div class="reminder">

    <h4>⚠️ Reminder Dosen</h4>

    <?php if($totalKRSPending > 0){ ?>
        <div class="alert-box warning">
            ⚠️ Ada <b><?= $totalKRSPending ?></b> KRS mahasiswa yang belum di ACC
        </div>
    <?php } else { ?>
        <div class="alert-box success">
            ✅ Semua KRS sudah di ACC
        </div>
    <?php } ?>

    <?php if($totalNilai > 0){ ?>
        <div class="alert-box info">
            📝 Ada <b><?= $totalNilai ?></b> nilai mahasiswa belum diinput
        </div>
    <?php } ?>

    <?php if($jadwalHariIni > 0){ ?>
        <div class="alert-box warning">
            📅 Anda memiliki <b><?= $jadwalHariIni ?></b> jadwal mengajar hari ini
        </div>
    <?php } else { ?>
        <div class="alert-box info">
            📅 Tidak ada jadwal mengajar hari ini
        </div>
    <?php } ?>

</div>

<script>

function updateTime(){

    const now = new Date();

    document.getElementById("jam").innerText =
        now.toLocaleTimeString("id-ID");

    document.getElementById("tanggal").innerText =
        now.toLocaleDateString("id-ID",{
            weekday:'long',
            year:'numeric',
            month:'long',
            day:'numeric'
        });

    let h = now.getHours();
    let greet = "";

    if(h < 11) greet = "Selamat Pagi";
    else if(h < 15) greet = "Selamat Siang";
    else if(h < 18) greet = "Selamat Sore";
    else greet = "Selamat Malam";

    document.getElementById("greeting").innerText =
        greet + ", <?= $dosen['nama']; ?> 👋";
}

setInterval(updateTime,1000);
updateTime();

</script>

</body>
</html>