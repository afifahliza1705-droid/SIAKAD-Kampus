<?php
session_start();
include '../config/koneksi.php';

// PROTEKSI DOSEN
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'dosen') {
    header("Location: ../auth/login.php");
    exit;
}

$id_dosen = $_SESSION['id_ref'];

// AMBIL DATA DOSEN
$dosen = mysqli_fetch_assoc(mysqli_query($koneksi,"
    SELECT * FROM dosen WHERE id_dosen='$id_dosen'
"));

$pesan = "";

// ==========================
// PROSES GANTI PASSWORD
// ==========================
if (isset($_POST['submit'])) {

    $password_lama = md5($_POST['password_lama']);
    $password_baru = md5($_POST['password_baru']);
    $konfirmasi = md5($_POST['konfirmasi']);

    if ($password_lama != $dosen['password']) {
        $pesan = "❌ Password lama salah!";
    }
    elseif ($password_baru != $konfirmasi) {
        $pesan = "❌ Konfirmasi password tidak cocok!";
    }
    else {
        mysqli_query($koneksi,"
            UPDATE dosen
            SET password='$password_baru'
            WHERE id_dosen='$id_dosen'
        ");

        $pesan = "✅ Password berhasil diganti!";
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

/* ================= GLOBAL ================= */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    background:#eef4ff;
}

/* ================= SIDEBAR (FIX SAMA DASHBOARD) ================= */
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
    padding:14px;
    color:#cbd5e1;
    text-decoration:none;
    border-radius:14px;
    margin-bottom:8px;
    transition:.3s;
    font-weight:500;
    font-size:14px;
}

.menu a:hover,
.menu .active{
    background:rgba(96,165,250,.15);
    color:#60a5fa;
    transform:translateX(5px);
}

/* ================= CONTENT ================= */
.content{
    margin-left:280px;
    padding:30px;
}

/* TOPBAR */
.topbar{
    background:white;
    border-radius:24px;
    padding:20px;
    box-shadow:0 15px 40px rgba(0,0,0,.08);
    margin-bottom:25px;
}

.topbar h3{
    font-weight:800;
}

/* CARD */
.card-box{
    background:white;
    border-radius:25px;
    padding:30px;
    box-shadow:0 15px 35px rgba(0,0,0,.08);
}

/* FORM */
.form-control{
    border-radius:12px;
    padding:12px;
}

.btn-primary{
    background:linear-gradient(135deg,#2563eb,#7c3aed);
    border:none;
    padding:10px 20px;
    border-radius:12px;
    font-weight:600;
}

.btn-primary:hover{
    opacity:.9;
}

.alert{
    border-radius:12px;
    font-weight:600;
}

</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">

    <div class="brand">
        <i class="fas fa-chalkboard-teacher"></i>
        <h3>SIAKAD</h3>
        <small>Dosen Panel</small>
    </div>

    <div class="menu">

        <a href="dashboard.php"><i class="fas fa-house"></i> Dashboard</a>
        <a href="mahasiswa_pa.php"><i class="fas fa-users"></i> Mahasiswa PA</a>
        <a href="acc_krs.php"><i class="fas fa-file-signature"></i> ACC KRS</a>
        <a href="input_nilai.php"><i class="fas fa-pen"></i> Input Nilai</a>
        <a href="mahasiswa_ajar.php"><i class="fas fa-user-graduate"></i> Daftar Mahasiswa</a>
        <a href="jadwal.php"><i class="fas fa-calendar-days"></i> Jadwal Mengajar</a>
        <a href="profil.php"><i class="fas fa-user"></i> Profil</a>
        <a href="ganti_password.php" class="active"><i class="fas fa-key"></i> Ganti Password</a>
        <a href="../auth/logout.php"><i class="fas fa-right-from-bracket"></i> Logout</a>

    </div>
</div>

<!-- CONTENT -->
<div class="content">

    <div class="topbar">
        <h3>Ganti Password</h3>
        <p>Keamanan akun dosen</p>
    </div>

    <div class="card-box">

        <?php if($pesan != "") { ?>
            <div class="alert alert-info">
                <?= $pesan ?>
            </div>
        <?php } ?>

        <form method="POST">

            <div class="mb-3">
                <label>Password Lama</label>
                <input type="password" name="password_lama" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Password Baru</label>
                <input type="password" name="password_baru" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Konfirmasi Password</label>
                <input type="password" name="konfirmasi" class="form-control" required>
            </div>

            <button type="submit" name="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan
            </button>

        </form>

    </div>

</div>

</body>
</html>