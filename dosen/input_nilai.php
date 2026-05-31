<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'dosen') {
    header("Location: ../auth/login.php");
    exit;
}

$id_dosen = $_SESSION['id_ref'];

// DOSEN
$dosen = mysqli_fetch_assoc(mysqli_query($koneksi,"
    SELECT * FROM dosen WHERE id_dosen='$id_dosen'
"));

// ===============================
// QUERY FIX (SEMUA FIELD NILAI)
// ===============================
$query = mysqli_query($koneksi,"
SELECT 
    m.id_mahasiswa,
    m.nama,
    m.nim,
    mk.id_mk,
    mk.nama_mk,
    mk.sks,

    COALESCE(n.tugas,0) AS tugas,
    COALESCE(n.uts,0) AS uts,
    COALESCE(n.uas,0) AS uas,
    n.nilai_akhir,
    n.grade

FROM mata_kuliah mk
JOIN krs k ON k.id_mk = mk.id_mk
JOIN mahasiswa m ON m.id_mahasiswa = k.id_mahasiswa

LEFT JOIN nilai n 
    ON n.id_mahasiswa = m.id_mahasiswa 
    AND n.id_mk = mk.id_mk

WHERE mk.id_dosen = '$id_dosen'
ORDER BY m.nama ASC
");

if (!$query) {
    die("SQL ERROR: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Input Nilai</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>

/* GLOBAL */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    background:#eef4ff;
}

/* SIDEBAR (FIX ACC_KRS STYLE) */
.sidebar{
    width:280px;
    height:100vh;
    position:fixed;
    background:linear-gradient(180deg,#0f172a,#172554);
    padding:30px 20px;
    color:white;
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
    transform:translateX(4px);
}

/* CONTENT */
.content{
    margin-left:280px;
    padding:30px;
}

/* TOPBAR */
.topbar{
    background:white;
    padding:22px;
    border-radius:22px;
    box-shadow:0 15px 40px rgba(0,0,0,.08);
    margin-bottom:25px;
}

.topbar h3{
    font-weight:800;
}

.topbar p{
    color:#64748b;
}

/* CARD */
.card-box{
    background:white;
    padding:25px;
    border-radius:22px;
    box-shadow:0 10px 30px rgba(0,0,0,.06);
}

/* TABLE */
.table thead{
    background:linear-gradient(135deg,#2563eb,#7c3aed);
    color:white;
}

.badge-sukses{
    background:#22c55e;
    color:white;
    padding:6px 10px;
    border-radius:10px;
    font-size:12px;
}

.badge-proses{
    background:#facc15;
    padding:6px 10px;
    border-radius:10px;
    font-size:12px;
}

.btn-input{
    background:#2563eb;
    color:white;
    padding:6px 12px;
    border-radius:8px;
    text-decoration:none;
    font-size:13px;
}

.btn-input:hover{
    opacity:.85;
}
.table td, .table th {
    vertical-align: middle !important;
}

.status-box {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
}

.badge-proses, .badge-sukses {
    display: inline-block;
    padding: 6px 10px;
    border-radius: 10px;
    font-size: 12px;
    text-align: center;
    white-space: nowrap;
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

        <a href="dashboard.php">
            <i class="fas fa-house"></i> Dashboard
        </a>

        <a href="mahasiswa_pa.php">
            <i class="fas fa-users"></i> Mahasiswa PA
        </a>

        <a href="acc_krs.php">
            <i class="fas fa-file-signature"></i> ACC KRS
        </a>

        <a href="input_nilai.php" class="active">
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
        <h3>Input Nilai Mahasiswa</h3>
        <p>Kelola nilai mahasiswa per mata kuliah</p>
    </div>

    <div class="card-box">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold">Daftar Input Nilai</h4>
            <span class="text-muted">Silakan isi nilai tugas, UTS, dan UAS</span>
        </div>

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Mahasiswa</th>
                        <th>NIM</th>
                        <th>Mata Kuliah</th>
                        <th>SKS</th>
                        <th>Tugas</th>
                        <th>UTS</th>
                        <th>UAS</th>
                        <th>Nilai Akhir</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

<?php 
$no=1; 
while($row=mysqli_fetch_assoc($query)){ 

$belum_input = is_null($row['nilai_akhir']);
?>

<tr>
    <td><?= $no++ ?></td>
    <td><?= $row['nama'] ?></td>
    <td><?= $row['nim'] ?></td>
    <td><?= $row['nama_mk'] ?></td>
    <td><?= $row['sks'] ?></td>

    <!-- TUGAS -->
    <td>
        <input type="number" form="form<?= $no ?>" name="tugas"
            value="<?= $row['tugas'] ?>"
            class="form-control form-control-sm" style="width:70px;">
    </td>

    <!-- UTS -->
    <td>
        <input type="number" form="form<?= $no ?>" name="uts"
            value="<?= $row['uts'] ?>"
            class="form-control form-control-sm" style="width:70px;">
    </td>

    <!-- UAS -->
    <td>
        <input type="number" form="form<?= $no ?>" name="uas"
            value="<?= $row['uas'] ?>"
            class="form-control form-control-sm" style="width:70px;">
    </td>

    <!-- NILAI AKHIR (AUTO HASIL PROSES) -->
    <td>
        <?= $belum_input ? '-' : number_format($row['nilai_akhir'],2) ?>
    </td>

    <!-- STATUS -->
    <td>
    <div class="status-box">
        <?php if($belum_input){ ?>
            <span class="badge-proses">Belum Input</span>
        <?php } else { ?>
            <span class="badge-sukses"><?= $row['grade'] ?></span>
        <?php } ?>
    </div>
</td>

    <!-- AKSI -->
    <td>

        <form method="POST" action="proses_nilai.php" id="form<?= $no ?>" class="d-flex gap-1">

            <input type="hidden" name="id_mahasiswa" value="<?= $row['id_mahasiswa'] ?>">
            <input type="hidden" name="id_mk" value="<?= $row['id_mk'] ?>">

            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-save"></i>
            </button>

        </form>

    </td>

</tr>

<?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>