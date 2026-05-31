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

/* NOTIF */
$notif = "";

/* TAMBAH */
if (isset($_POST['tambah'])) {

    $nama_kelas = $_POST['nama_kelas'];
    $semester = $_POST['semester'];
    $tahun_ajaran = $_POST['tahun_ajaran'];

    mysqli_query($koneksi, "
        INSERT INTO kelas (nama_kelas, semester, tahun_ajaran)
        VALUES ('$nama_kelas','$semester','$tahun_ajaran')
    ");

    $notif = "Kelas berhasil ditambahkan!";
}

/* EDIT */
if (isset($_POST['edit'])) {

    $id_kelas = $_POST['id_kelas'];
    $nama_kelas = $_POST['nama_kelas'];
    $semester = $_POST['semester'];
    $tahun_ajaran = $_POST['tahun_ajaran'];

    mysqli_query($koneksi, "
        UPDATE kelas SET
        nama_kelas='$nama_kelas',
        semester='$semester',
        tahun_ajaran='$tahun_ajaran'
        WHERE id_kelas='$id_kelas'
    ");

    $notif = "Kelas berhasil diperbarui!";
}

/* DELETE */
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM kelas WHERE id_kelas='$id'");
    header("Location: kelas.php");
    exit;
}

/* DATA */
$kelas = mysqli_query($koneksi, "SELECT * FROM kelas ORDER BY id_kelas DESC");

/* EDIT DATA */
$editData = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $editData = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT * FROM kelas WHERE id_kelas='$id'"
    ));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Data Kelas</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
/* ================= DASHBOARD STYLE (FULL SAMA) ================= */

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

/* TOPBAR (SAMA PERSIS DASHBOARD) */
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

.topbar-right{
    text-align:right;
    font-weight:600;
    color:#334155;
}

/* CARD BOX */
.box{
    background:white;
    padding:25px;
    border-radius:20px;
    box-shadow:0 10px 30px rgba(0,0,0,.06);
}

/* FORM */
.form-box{
    background:white;
    padding:25px;
    border-radius:20px;
    margin-top:20px;
    box-shadow:0 10px 25px rgba(0,0,0,.05);
}

.form-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:15px;
}

label{
    font-size:13px;
    font-weight:700;
    color:#334155;
}

input,select{
    width:100%;
    padding:10px;
    border-radius:10px;
    border:1px solid #d1d5db;
}

input:focus,select:focus{
    outline:none;
    border-color:#38bdf8;
}

/* BUTTON */
.btn-submit{
    width:100%;
    background:linear-gradient(135deg,#1f2937,#111827);
    color:white;
    border:none;
    padding:12px;
    border-radius:10px;
    font-weight:800;
    transition:.3s;
}

.btn-submit:hover{
    transform:translateY(-3px);
}

/* TABLE */
.table-card{
    background:white;
    margin-top:30px;
    padding:25px;
    border-radius:20px;
    box-shadow:0 10px 30px rgba(0,0,0,.06);
}

.table th{
    background:#eff6ff;
    color:#1e3a8a;
    font-size:13px;
    text-align:center;
}

.table td{
    font-size:13px;
    text-align:center;
}

.btn-edit{
    background:#facc15;
    padding:6px 10px;
    border-radius:8px;
    text-decoration:none;
    color:black;
    font-weight:700;
}

.btn-hapus{
    background:#ef4444;
    padding:6px 10px;
    border-radius:8px;
    text-decoration:none;
    color:white;
    font-weight:700;
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
        <a href="mata_kuliah.php"><i class="fas fa-book-open"></i> Mata Kuliah</a>
        <a href="jadwal.php"><i class="fas fa-calendar"></i> Jadwal</a>
        <a href="kelas.php" class="active"><i class="fas fa-layer-group"></i> Kelas</a>
        <a href="atur_pa.php"><i class="fas fa-users"></i> Pengaturan PA</a>
        <a href="ganti_password.php"><i class="fas fa-key"></i> Ganti Password</a>
        <a href="../auth/logout.php"><i class="fas fa-right-from-bracket"></i> Logout</a>
    </div>
</div>

<!-- CONTENT -->
<div class="content">

<!-- TOPBAR -->
<div class="topbar">
    <div>
        <h3 style="font-weight:800;">Kelas Perkuliahan</h3>
        <p>Kelola kelas perkuliahan</p>
    </div>
</div>

<?php if($notif): ?>
<div class="alert alert-success"><?= $notif ?></div>
<?php endif; ?>

<!-- FORM -->
<div class="form-box">

<h5 style="font-weight:800;">
<?= $editData ? "EDIT KELAS" : "TAMBAH KELAS" ?>
</h5>

<form method="POST">

<?php if($editData): ?>
<input type="hidden" name="id_kelas" value="<?= $editData['id_kelas'] ?>">
<?php endif; ?>

<div class="form-grid">

<div>
<label>Nama Kelas</label>
<input type="text" name="nama_kelas" required
value="<?= $editData['nama_kelas'] ?? '' ?>">
</div>

<div>
<label>Semester</label>
<select name="semester">
<option>1</option><option>2</option><option>3</option><option>4</option>
<option>5</option><option>6</option><option>7</option><option>8</option>
</select>
</div>

<div>
<label>Tahun Ajaran</label>
<input type="text" name="tahun_ajaran"
value="<?= $editData['tahun_ajaran'] ?? '' ?>">
</div>

</div>

<br>

<button class="btn-submit" name="<?= $editData?'edit':'tambah' ?>">
Simpan
</button>

</form>
</div>

<!-- TABLE -->
<div class="table-card">

<h4 style="font-weight:800;">DAFTAR KELAS</h4>

<table class="table table-hover mt-3">

<thead>
<tr>
<th>No</th>
<th>Kelas</th>
<th>Semester</th>
<th>Tahun Ajaran</th>
<th>Aksi</th>
</tr>
</thead>

<tbody>
<?php $no=1; while($k=mysqli_fetch_assoc($kelas)): ?>
<tr>
<td><?= $no++ ?></td>
<td><?= $k['nama_kelas'] ?></td>
<td><?= $k['semester'] ?></td>
<td><?= $k['tahun_ajaran'] ?></td>
<td>
<a href="?edit=<?= $k['id_kelas'] ?>" class="btn-edit">Edit</a> 
<a href="?hapus=<?= $k['id_kelas'] ?>" class="btn-hapus"
onclick="return confirm('Hapus kelas?')">Hapus</a>
</td>
</tr>
<?php endwhile; ?>
</tbody>

</table>

</div>

</div>

</body>
</html>