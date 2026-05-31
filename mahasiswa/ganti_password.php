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

$id_mahasiswa = $_SESSION['id_ref'];

// ==========================
// AMBIL DATA MAHASISWA
// ==========================
$mhs = mysqli_fetch_assoc(mysqli_query($koneksi,"
    SELECT * FROM mahasiswa
    WHERE id_mahasiswa='$id_mahasiswa'
"));

// ==========================
// PROSES GANTI PASSWORD
// ==========================
$notif = '';
$notif_type = '';

if (isset($_POST['ganti_password'])) {

    $password_lama = trim($_POST['password_lama']);
    $password_baru = trim($_POST['password_baru']);
    $konfirmasi    = trim($_POST['konfirmasi_password']);

    if ($password_lama == '' || $password_baru == '' || $konfirmasi == '') {

        $notif = "Semua field wajib diisi!";
        $notif_type = "danger";

    } else {

        $valid = false;

        if (password_verify($password_lama, $mhs['password'])) {
            $valid = true;
        }

        if ($password_lama === $mhs['password']) {
            $valid = true;
        }

        if (!$valid) {

            $notif = "Password lama salah!";
            $notif_type = "danger";

        } elseif ($password_baru != $konfirmasi) {

            $notif = "Konfirmasi password tidak sama!";
            $notif_type = "warning";

        } elseif (strlen($password_baru) < 6) {

            $notif = "Password minimal 6 karakter!";
            $notif_type = "warning";

        } else {

            $hash = password_hash($password_baru, PASSWORD_DEFAULT);

            $update = mysqli_query($koneksi,"
                UPDATE mahasiswa
                SET password='$hash'
                WHERE id_mahasiswa='$id_mahasiswa'
            ");

            if ($update) {
                echo "<script>
                    alert('Password berhasil diganti!');
                    window.location='dashboard.php';
                </script>";
                exit;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ganti Password</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>

/* ================= KRS STYLE FULL COPY ================= */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
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
    font-weight:500;
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

/* TOPBAR SAMA KRS */
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
    font-size:14px;
}

.topbar p span{
    font-weight:800;
    color:#1e3a8a;
}

/* CARD */
.card-custom{
    background:white;
    border-radius:20px;
    padding:25px;
    box-shadow:0 10px 30px rgba(0,0,0,.06);
}

/* FORM STYLE */
label{
    font-weight:700;
    color:#1e3a8a;
    margin-bottom:6px;
}

.form-control{
    height:48px;
    border-radius:12px;
    border:1px solid #e2e8f0;
    font-size:14px;
}

.form-control:focus{
    border-color:#38bdf8;
    box-shadow:0 0 0 3px rgba(56,189,248,.2);
}

/* BUTTON */
.btn-save{
    background:linear-gradient(135deg,#2563eb,#38bdf8);
    border:none;
    color:white;
    padding:12px 22px;
    border-radius:12px;
    font-weight:700;
}

.btn-back{
    background:#e2e8f0;
    color:#1e293b;
    padding:12px 22px;
    border-radius:12px;
    text-decoration:none;
    font-weight:700;
}

/* ALERT */
.alert{
    border-radius:12px;
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
        <a href="jadwal.php"><i class="fas fa-calendar"></i> Jadwal</a>
        <a href="profil.php"><i class="fas fa-user"></i> Profil</a>
        <a href="ganti_password.php" class="active"><i class="fas fa-key"></i> Ganti Password</a>
        <a href="logout.php"><i class="fas fa-right-from-bracket"></i> Logout</a>
    </div>

</div>

<!-- CONTENT -->
<div class="content">

    <div class="topbar">
        <h3>Ganti Password</h3>
        <p>Halo, <span><?= $mhs['nama']; ?></span></p>
    </div>

    <div class="card-custom">

        <?php if($notif != ''): ?>
            <div class="alert alert-<?= $notif_type ?>">
                <?= $notif ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">
                <label>Password Lama</label>
                <input type="password" name="password_lama" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Password Baru</label>
                <input type="password" name="password_baru" class="form-control" required>
            </div>

            <div class="mb-4">
                <label>Konfirmasi Password</label>
                <input type="password" name="konfirmasi_password" class="form-control" required>
            </div>

            <div class="d-flex gap-3">
                <a href="dashboard.php" class="btn-back">Kembali</a>
                <button type="submit" name="ganti_password" class="btn-save">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>

        </form>

    </div>

</div>

</body>
</html>