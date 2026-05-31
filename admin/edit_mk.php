<?php
session_start();
include '../config/koneksi.php';

/* =========================
   PROTEKSI ADMIN
========================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

/* =========================
   VALIDASI ID
========================= */
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>
        alert('ID Mata Kuliah tidak ditemukan!');
        window.location='mata_kuliah.php';
    </script>";
    exit;
}

$id_mk = mysqli_real_escape_string($koneksi, $_GET['id']);

/* =========================
   DATA MK
========================= */
$query = mysqli_query($koneksi, "SELECT * FROM mata_kuliah WHERE id_mk='$id_mk'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>
        alert('Data tidak ditemukan!');
        window.location='mata_kuliah.php';
    </script>";
    exit;
}

/* =========================
   DOSEN & KELAS
========================= */
$dosen = mysqli_query($koneksi, "SELECT id_dosen,nama,nip FROM dosen ORDER BY nama ASC");
$kelas = mysqli_query($koneksi, "SELECT id_kelas,nama_kelas FROM kelas ORDER BY nama_kelas ASC");

/* =========================
   UPDATE
========================= */
$notif = "";
$notif_type = "";

if (isset($_POST['update'])) {

    $kode_mk  = mysqli_real_escape_string($koneksi, $_POST['kode_mk']);
    $nama_mk  = mysqli_real_escape_string($koneksi, $_POST['nama_mk']);
    $sks      = mysqli_real_escape_string($koneksi, $_POST['sks']);
    $semester = mysqli_real_escape_string($koneksi, $_POST['semester']);
    $id_dosen = mysqli_real_escape_string($koneksi, $_POST['id_dosen']);
    $id_kelas = mysqli_real_escape_string($koneksi, $_POST['id_kelas']);

    if ($kode_mk=='' || $nama_mk=='' || $sks=='' || $semester=='' || $id_dosen=='' || $id_kelas=='') {
        $notif = "Semua field wajib diisi!";
        $notif_type = "danger";
    } else {

        $qD = mysqli_query($koneksi,"SELECT nama FROM dosen WHERE id_dosen='$id_dosen'");
        $dD = mysqli_fetch_assoc($qD);
        $nama_dosen = $dD['nama'];

        $qK = mysqli_query($koneksi,"SELECT nama_kelas FROM kelas WHERE id_kelas='$id_kelas'");
        $dK = mysqli_fetch_assoc($qK);
        $nama_kelas = $dK['nama_kelas'];

        $update = mysqli_query($koneksi,"
            UPDATE mata_kuliah SET
                kode_mk='$kode_mk',
                nama_mk='$nama_mk',
                sks='$sks',
                semester='$semester',
                id_dosen='$id_dosen',
                nama_dosen='$nama_dosen',
                id_kelas='$id_kelas',
                kelas='$nama_kelas'
            WHERE id_mk='$id_mk'
        ");

        if ($update) {
            echo "<script>
                alert('UPDATE BERHASIL');
                window.location='mata_kuliah.php';
            </script>";
            exit;
        } else {
            $notif = "Gagal update data!";
            $notif_type = "danger";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Mata Kuliah</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

/* =========================
   BACKGROUND PREMIUM
========================= */
body{
    margin:0;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    font-family:Segoe UI;
    background: linear-gradient(135deg,#0ea5e9,#2563eb,#1e3a8a);
    overflow:hidden;
}

/* floating light */
body::before,
body::after{
    content:"";
    position:absolute;
    width:400px;
    height:400px;
    border-radius:50%;
    filter:blur(90px);
    animation: float 6s infinite ease-in-out;
}

body::before{
    background:#38bdf8;
    top:-100px;
    left:-100px;
}

body::after{
    background:#2563eb;
    bottom:-120px;
    right:-120px;
}

@keyframes float{
    0%{transform:translateY(0px);}
    50%{transform:translateY(25px);}
    100%{transform:translateY(0px);}
}

/* =========================
   GLASS CARD
========================= */
.card-glass{
    width:1000px;
    border-radius:30px;
    background:rgba(255,255,255,0.12);
    backdrop-filter: blur(20px);
    box-shadow:0 25px 80px rgba(0,0,0,.4);
    overflow:hidden;
    border:1px solid rgba(255,255,255,0.2);
}

/* HEADER */
.header{
    padding:35px;
    text-align:center;
    background: linear-gradient(135deg,#ffffff,#e0f2fe);
}

.header i{
    font-size:55px;
    color:#2563eb;
    margin-bottom:10px;
}

.header h2{
    font-weight:900;
    color:#1e3a8a;
    letter-spacing:1px;
}

/* BODY */
.body{
    padding:45px;
    background:white;
}

label{
    font-weight:700;
    color:#1e3a8a;
    margin-bottom:8px;
}

.form-control,
.form-select{
    height:55px;
    border-radius:14px;
    border:2px solid #e0f2fe;
    transition:.3s;
}

.form-control:focus,
.form-select:focus{
    border-color:#2563eb;
    box-shadow:0 0 0 4px rgba(37,99,235,.25);
}

/* BUTTON */
.btn-save{
    background:linear-gradient(135deg,#2563eb,#0ea5e9);
    border:none;
    padding:14px 30px;
    border-radius:14px;
    color:white;
    font-weight:800;
    transition:.2s;
}

.btn-save:hover{
    transform:scale(1.05);
}

.btn-back{
    background:#e5e7eb;
    padding:14px 30px;
    border-radius:14px;
    text-decoration:none;
    color:#1e293b;
    font-weight:700;
    transition:.2s;
}

.btn-back:hover{
    background:#cbd5e1;
}

/* ALERT */
.alert{
    border-radius:14px;
    font-weight:600;
}

</style>
</head>

<body>

<div class="card-glass">

<!-- HEADER -->
<div class="header">
    <i class="fa fa-book"></i>
    <h2>Edit Mata Kuliah</h2>
    <p style="color:#475569;">Sistem Akademik Kampus</p>
</div>

<div class="body">

<?php if($notif!=""): ?>
<div class="alert alert-<?= $notif_type ?>">
    <?= $notif ?>
</div>
<?php endif; ?>

<form method="POST">

<div class="row">

<!-- KODE -->
<div class="col-md-6 mb-3">
<label>Kode MK</label>
<input type="text" name="kode_mk" class="form-control" value="<?= $data['kode_mk'] ?>">
</div>

<!-- NAMA -->
<div class="col-md-6 mb-3">
<label>Nama MK</label>
<input type="text" name="nama_mk" class="form-control" value="<?= $data['nama_mk'] ?>">
</div>

<!-- SKS -->
<div class="col-md-4 mb-3">
<label>SKS</label>
<input type="number" name="sks" class="form-control" value="<?= $data['sks'] ?>">
</div>

<!-- SEMESTER -->
<div class="col-md-4 mb-3">
<label>Semester</label>
<select name="semester" class="form-select">
<?php for($i=1;$i<=8;$i++): ?>
<option value="<?= $i ?>" <?= ($data['semester']==$i)?'selected':'' ?>>
Semester <?= $i ?>
</option>
<?php endfor; ?>
</select>
</div>

<!-- DOSEN -->
<div class="col-md-6 mb-3">
<label>Dosen</label>
<select name="id_dosen" class="form-select">
<?php while($d=mysqli_fetch_assoc($dosen)): ?>
<option value="<?= $d['id_dosen'] ?>" <?= ($data['id_dosen']==$d['id_dosen'])?'selected':'' ?>>
<?= $d['nama'] ?> - <?= $d['nip'] ?>
</option>
<?php endwhile; ?>
</select>
</div>

<!-- KELAS -->
<div class="col-md-6 mb-3">
<label>Kelas</label>
<select name="id_kelas" class="form-select">
<?php while($k=mysqli_fetch_assoc($kelas)): ?>
<option value="<?= $k['id_kelas'] ?>" <?= ($data['id_kelas']==$k['id_kelas'])?'selected':'' ?>>
<?= $k['nama_kelas'] ?>
</option>
<?php endwhile; ?>
</select>
</div>

</div>

<div class="d-flex justify-content-between mt-4">
<a href="mata_kuliah.php" class="btn-back">Kembali</a>
<button class="btn-save" name="update">
<i class="fa fa-save"></i> UPDATE DATA
</button>
</div>

</form>

</div>
</div>

</body>
</html>