<?php
session_start();
include '../config/koneksi.php';

// PROTEKSI ADMIN
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

/* =========================
   ASSIGN MAHASISWA PA
========================= */
if (isset($_POST['set_pa'])) {

    $id_mhs = $_POST['id_mahasiswa'];
    $id_dosen = $_POST['id_dosen'];

    mysqli_query($koneksi, "
        UPDATE mahasiswa 
        SET id_dosen_pa='$id_dosen'
        WHERE id_mahasiswa='$id_mhs'
    ");

    $notif = "🔥 Mahasiswa berhasil di-assign ke Dosen PA!";
}

/* =========================
   DATA DOSEN
========================= */
$dosen = mysqli_query($koneksi, "SELECT * FROM dosen ORDER BY nama ASC");

/* =========================
   FILTER DOSEN
========================= */
$filter = isset($_GET['dosen']) ? $_GET['dosen'] : "";

/* =========================
   DATA MAHASISWA
========================= */
$sql = "
SELECT m.*, d.nama AS nama_dosen
FROM mahasiswa m
LEFT JOIN dosen d ON m.id_dosen_pa = d.id_dosen
";

if ($filter != "") {
    $sql .= " WHERE m.id_dosen_pa='$filter'";
}

$mahasiswa = mysqli_query($koneksi, $sql);

/* =========================
   COUNT
========================= */
$total_mhs = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM mahasiswa"));
$total_dosen = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM dosen"));
$total_pa = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM mahasiswa WHERE id_dosen_pa IS NOT NULL"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Atur PA</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

/* ===== DASHBOARD STYLE FULL COPY ===== */
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
}

.topbar h3{
    font-weight:800;
}

/* CARD */
.card-box{
    padding:25px;
    border-radius:20px;
    color:white;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
}

.bg1{background:linear-gradient(135deg,#2563eb,#1d4ed8);}
.bg2{background:linear-gradient(135deg,#7c3aed,#6d28d9);}
.bg3{background:linear-gradient(135deg,#059669,#047857);}

/* BOX */
.box{
    background:white;
    padding:25px;
    border-radius:20px;
    box-shadow:0 10px 30px rgba(0,0,0,.06);
    margin-top:20px;
}

/* TABLE */
.table th{
    background:#eff6ff;
    font-size:13px;
    text-align:center;
}

.table td{
    font-size:13px;
    text-align:center;
}

/* BUTTON */
.btn-assign{
    background:#2563eb;
    color:white;
    padding:6px 10px;
    border-radius:8px;
    text-decoration:none;
    font-size:12px;
}

</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">

    <div class="brand">
        <i class="fas fa-graduation-cap"></i>
        <h3>SIAKAD</h3>
        <small>Admin Panel</small>
    </div>

    <div class="menu">
        <a href="dashboard.php"><i class="fas fa-house"></i> Dashboard</a>
        <a href="mahasiswa.php"><i class="fas fa-user-graduate"></i> Mahasiswa</a>
        <a href="dosen.php"><i class="fas fa-chalkboard-user"></i> Dosen</a>
        <a href="mata_kuliah.php"><i class="fas fa-book"></i> Mata Kuliah</a>
        <a href="jadwal.php"><i class="fas fa-calendar"></i> Jadwal</a>
        <a href="kelas.php"><i class="fas fa-layer-group"></i> Kelas</a>
        <a href="atur_pa.php" class="active"><i class="fas fa-users"></i> Pengaturan PA</a>
        <a href="ganti_password.php"><i class="fas fa-key"></i> Ganti Password</a>
        <a href="../auth/logout.php"><i class="fas fa-right-from-bracket"></i> Logout</a>
    </div>

</div>

<!-- CONTENT -->
<div class="content">

    <div class="topbar">
        <h3>Pengaturan Dosen PA</h3>
        <p>Kelola pembagian mahasiswa ke dosen pembimbing akademik</p>
    </div>

    <!-- STAT -->
    <div class="row g-4">

        <div class="col-md-4">
            <div class="card-box bg1">
                <h2><?= $total_mhs ?></h2>
                <p>Total Mahasiswa</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-box bg2">
                <h2><?= $total_dosen ?></h2>
                <p>Total Dosen</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-box bg3">
                <h2><?= $total_pa ?></h2>
                <p>Sudah Punya PA</p>
            </div>
        </div>

    </div>

    <!-- FILTER -->
    <div class="box">

        <form method="GET">
            <label><b>Filter Dosen PA</b></label>
            <select name="dosen" class="form-control mt-2" onchange="this.form.submit()">
                <option value="">Semua Dosen</option>
                <?php while($d=mysqli_fetch_assoc($dosen)) { ?>
                    <option value="<?= $d['id_dosen'] ?>">
                        <?= $d['nama'] ?>
                    </option>
                <?php } ?>
            </select>
        </form>

    </div>

    <!-- TABLE MAHASISWA -->
    <div class="box">

        <h5>Data Mahasiswa</h5>

        <table class="table table-hover">

            <thead>
                <tr>
                    <th>No</th>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Prodi</th>
                    <th>PA Saat Ini</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

            <?php $no=1; while($m=mysqli_fetch_assoc($mahasiswa)) { ?>

                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $m['nim'] ?></td>
                    <td><?= $m['nama'] ?></td>
                    <td><?= $m['prodi'] ?></td>
                    <td><?= $m['nama_dosen'] ?? 'Belum Ada' ?></td>
                    <td>

                        <form method="POST" style="display:flex; gap:5px;">

                            <input type="hidden" name="id_mahasiswa" value="<?= $m['id_mahasiswa'] ?>">

                            <select name="id_dosen" required class="form-control form-control-sm">

                                <?php
                                $ds = mysqli_query($koneksi,"SELECT * FROM dosen");
                                while($d=mysqli_fetch_assoc($ds)) {
                                ?>
                                    <option value="<?= $d['id_dosen'] ?>">
                                        <?= $d['nama'] ?>
                                    </option>
                                <?php } ?>

                            </select>

                            <button class="btn-assign" name="set_pa">
                                Set
                            </button>

                        </form>

                    </td>
                </tr>

            <?php } ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>