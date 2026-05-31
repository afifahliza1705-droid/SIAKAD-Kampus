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

    $id_dosen  = $_POST['id_dosen'] ?? '';
    $id_mk     = $_POST['id_mk'] ?? '';
    $id_kelas  = $_POST['id_kelas'] ?? '';
    $hari      = $_POST['hari'] ?? '';
    $jam_mulai = substr($_POST['jam_mulai'] ?? '', 0, 5);
    $jam_selesai = substr($_POST['jam_selesai'] ?? '', 0, 5);
    $ruangan   = $_POST['ruangan'] ?? '';

    if($id_dosen && $id_mk && $id_kelas){

        mysqli_query($koneksi,"
            INSERT INTO jadwal_kuliah 
            (id_dosen,id_mk,id_kelas,hari,jam_mulai,jam_selesai,ruangan)
            VALUES 
            ('$id_dosen','$id_mk','$id_kelas','$hari','$jam_mulai','$jam_selesai','$ruangan')
        ");

        $notif = "Jadwal berhasil ditambahkan!";
    }
}

/* EDIT */
if (isset($_POST['edit'])) {

    $id_jadwal = $_POST['id_jadwal'] ?? '';

    $id_dosen  = $_POST['id_dosen'] ?? '';
    $id_mk     = $_POST['id_mk'] ?? '';
    $id_kelas  = $_POST['id_kelas'] ?? '';
    $hari      = $_POST['hari'] ?? '';
    $jam_mulai = substr($_POST['jam_mulai'] ?? '', 0, 5);
    $jam_selesai = substr($_POST['jam_selesai'] ?? '', 0, 5);
    $ruangan   = $_POST['ruangan'] ?? '';

    if($id_jadwal){

        mysqli_query($koneksi,"
            UPDATE jadwal_kuliah SET
            id_dosen='$id_dosen',
            id_mk='$id_mk',
            id_kelas='$id_kelas',
            hari='$hari',
            jam_mulai='$jam_mulai',
            jam_selesai='$jam_selesai',
            ruangan='$ruangan'
            WHERE id_jadwal='$id_jadwal'
        ");

        $notif = "Jadwal berhasil diperbarui!";
    }
}

/* DATA */
$dosen = mysqli_query($koneksi,"SELECT * FROM dosen");
$mk = mysqli_query($koneksi,"SELECT * FROM mata_kuliah");
$kelas = mysqli_query($koneksi,"SELECT * FROM kelas ORDER BY nama_kelas ASC");

/* JADWAL */
$jadwal = mysqli_query($koneksi,"
    SELECT 
        j.*,
        d.nama AS nama_dosen,
        mk.nama_mk,
        k.nama_kelas
    FROM jadwal_kuliah j
    LEFT JOIN dosen d ON j.id_dosen=d.id_dosen
    LEFT JOIN mata_kuliah mk ON j.id_mk=mk.id_mk
    LEFT JOIN kelas k ON j.id_kelas=k.id_kelas
    ORDER BY j.id_jadwal DESC
");

/* EDIT DATA */
$editData = null;
if(isset($_GET['edit'])){
    $id = $_GET['edit'];
    $editData = mysqli_fetch_assoc(mysqli_query($koneksi,"
        SELECT * FROM jadwal_kuliah WHERE id_jadwal='$id'
    "));
}

function jam($t){
    if (!$t) return '';
    return substr($t, 0, 5);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Jadwal Kuliah</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
/* ===== CSS KAMU (TETAP 100% DIPERTAHANKAN) ===== */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

body{background:#f1f5f9;}

.sidebar{
    width:270px;
    height:100vh;
    position:fixed;
    background:linear-gradient(180deg,#0f172a,#1e293b);
    padding:30px 20px;
    color:white;
}

.brand{text-align:center;margin-bottom:35px;}
.brand i{font-size:55px;color:#38bdf8;}
.brand h3{margin-top:10px;font-weight:800;}

.menu a{
    display:flex;
    gap:10px;
    align-items:center;
    padding:14px;
    color:#cbd5e1;
    text-decoration:none;
    border-radius:12px;
    margin-bottom:8px;
}

.menu a:hover,.menu .active{
    background:rgba(56,189,248,.15);
    color:#38bdf8;
    transform:translateX(5px);
}

.content{
    margin-left:270px;
    padding:30px;
}

.box{
    background:white;
    padding:25px;
    border-radius:20px;
    box-shadow:0 10px 30px rgba(0,0,0,.06);
}

.header-page{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.header-page h2{font-weight:800;}

.form-box{
    background:white;
    padding:25px;
    border-radius:12px;
    border:1px solid #e5e7eb;
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
    font-weight:600;
    color:#374151;
}

input,select{
    width:100%;
    padding:10px;
    border-radius:8px;
    border:1px solid #d1d5db;
}

input:focus,select:focus{
    outline:none;
    border-color:#38bdf8;
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

.btn-submit{
    width:100%;
    background:linear-gradient(135deg,#1f2937,#111827);
    color:white;
    border:none;
    padding:12px;
    border-radius:10px;
    font-weight:700;
}

.btn-edit{
    background:#facc15;
    color:black;
    padding:6px 10px;
    border-radius:8px;
    text-decoration:none;
    font-weight:600;
}

.badge-kelas{
    background:#dbeafe;
    color:#1e40af;
    padding:5px 10px;
    border-radius:8px;
    font-size:12px;
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
        <a href="dashboard.php"><i class="fas fa-house"></i> Dashboard</a>
        <a href="mahasiswa.php"><i class="fas fa-user-graduate"></i> Mahasiswa</a>
        <a href="dosen.php"><i class="fas fa-chalkboard-user"></i> Dosen</a>
        <a href="mata_kuliah.php"><i class="fas fa-book-open"></i> Mata Kuliah</a>
        <a href="jadwal.php" class="active"><i class="fas fa-calendar"></i> Jadwal</a>
        <a href="kelas.php"><i class="fas fa-layer-group"></i> Kelas</a>
        <a href="atur_pa.php"><i class="fas fa-users"></i> Pengaturan PA</a>
        <a href="ganti_password.php"><i class="fas fa-key"></i> Ganti Password</a>
        <a href="../auth/logout.php"><i class="fas fa-right-from-bracket"></i> Logout</a>
    </div>
</div>

<div class="content">

<div class="box">
    <div class="header-page">
        <div>
            <h2>Jadwal Perkuliahan</h2>
            <p>Kelola jadwal dosen, kelas, dan mata kuliah</p>
        </div>
    </div>
</div>

<?php if($notif): ?>
<div class="alert alert-success mt-3"><?= $notif ?></div>
<?php endif; ?>

<!-- FORM -->
<div class="form-box">
<h5><?= $editData ? "EDIT JADWAL" : "TAMBAH JADWAL" ?></h5>

<form method="POST">

<?php if($editData): ?>
<input type="hidden" name="id_jadwal" value="<?= $editData['id_jadwal'] ?>">
<?php endif; ?>

<div class="form-grid">

<!-- DOSEN -->
<div>
<label>Dosen</label>
<select name="id_dosen" required>
<option value="">Pilih</option>
<?php while($d=mysqli_fetch_assoc($dosen)): ?>
<option value="<?= $d['id_dosen'] ?>"
<?= ($editData['id_dosen']??'')==$d['id_dosen']?'selected':'' ?>>
<?= $d['nama'] ?>
</option>
<?php endwhile; ?>
</select>
</div>

<!-- MK -->
<div>
<label>Mata Kuliah</label>
<select name="id_mk" required>
<option value="">Pilih</option>
<?php while($m=mysqli_fetch_assoc($mk)): ?>
<option value="<?= $m['id_mk'] ?>"
<?= ($editData['id_mk']??'')==$m['id_mk']?'selected':'' ?>>
<?= $m['nama_mk'] ?>
</option>
<?php endwhile; ?>
</select>
</div>

<!-- KELAS (FIX ERROR UTAMA) -->
<div>
<label>Kelas</label>
<select name="id_kelas" required>
<option value="">Pilih</option>
<?php while($k=mysqli_fetch_assoc($kelas)): ?>
<option value="<?= $k['id_kelas'] ?>"
<?= ($editData['id_kelas']??'')==$k['id_kelas']?'selected':'' ?>>
<?= $k['nama_kelas'] ?>
</option>
<?php endwhile; ?>
</select>
</div>

<div>
<label>Hari</label>
<select name="hari">
<option>Senin</option>
<option>Selasa</option>
<option>Rabu</option>
<option>Kamis</option>
<option>Jumat</option>
</select>
</div>

<div>
<label>Jam Mulai</label>
<input type="time" name="jam_mulai" value="<?= $editData['jam_mulai']??'' ?>">
</div>

<div>
<label>Jam Selesai</label>
<input type="time" name="jam_selesai" value="<?= $editData['jam_selesai']??'' ?>">
</div>

<div>
<label>Ruangan</label>
<input type="text" name="ruangan" value="<?= $editData['ruangan']??'' ?>">
</div>

</div>

<br>

<button class="btn-submit" name="<?= $editData?'edit':'tambah' ?>">
Simpan
</button>

</form>
</div>

<!-- TABLE -->
<div class="box mt-3">
<h3>DAFTAR JADWAL</h3>

<table class="table table-hover mt-3">
<thead>
<tr>
<th>No</th>
<th>MK</th>
<th>Kelas</th>
<th>Dosen</th>
<th>Hari</th>
<th>Jam</th>
<th>Ruangan</th>
<th>Aksi</th>
</tr>
</thead>

<tbody>
<?php $no=1; while($j=mysqli_fetch_assoc($jadwal)): ?>
<tr>
<td><?= $no++ ?></td>
<td><?= $j['nama_mk'] ?></td>
<td><span class="badge-kelas"><?= $j['nama_kelas'] ?></span></td>
<td><?= $j['nama_dosen'] ?></td>
<td><?= $j['hari'] ?></td>
<td><?= jam($j['jam_mulai']) ?> - <?= jam($j['jam_selesai']) ?></td>
<td><?= $j['ruangan'] ?></td>
<td>
<a href="?edit=<?= $j['id_jadwal'] ?>" class="btn-edit">Edit</a>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

</div>

</div>

</body>
</html>