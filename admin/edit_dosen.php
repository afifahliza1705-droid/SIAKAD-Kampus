<?php
session_start();
include '../config/koneksi.php';

// proteksi admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// ambil id
$id = mysqli_real_escape_string($koneksi, $_GET['id']);

// ambil data dosen
$data = mysqli_fetch_assoc(
    mysqli_query($koneksi, "SELECT * FROM dosen WHERE id_dosen='$id'")
);

if(!$data){
    echo "<script>
        alert('Data tidak ditemukan');
        window.location='dosen.php';
    </script>";
    exit;
}

// update data
if(isset($_POST['update'])){

    $nip = mysqli_real_escape_string($koneksi,$_POST['nip']);
    $nama = mysqli_real_escape_string($koneksi,$_POST['nama']);
    $jabatan = mysqli_real_escape_string($koneksi,$_POST['jabatan']);
    $fakultas = mysqli_real_escape_string($koneksi,$_POST['fakultas']);
    $prodi = mysqli_real_escape_string($koneksi,$_POST['prodi']);
    $jenis_kelamin = mysqli_real_escape_string($koneksi,$_POST['jenis_kelamin']);
    $email = mysqli_real_escape_string($koneksi,$_POST['email']);
    $no_hp = mysqli_real_escape_string($koneksi,$_POST['no_hp']);
    $alamat = mysqli_real_escape_string($koneksi,$_POST['alamat']);

    $fotoBaru = $data['foto'];

    // upload foto baru
    if(isset($_FILES['foto']) && $_FILES['foto']['error'] == 0){

        $allowed = ['jpg','jpeg','png','webp'];
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

        if(in_array($ext,$allowed)){

            $fotoBaru = time().'_'.rand(100,999).'.'.$ext;

            $folder = "../assets/uploads/dosen/";

            if(!file_exists($folder)){
                mkdir($folder,0777,true);
            }

            move_uploaded_file($_FILES['foto']['tmp_name'],$folder.$fotoBaru);
        }
    }

    mysqli_query($koneksi,"
        UPDATE dosen SET
        nip='$nip',
        nama='$nama',
        jabatan='$jabatan',
        fakultas='$fakultas',
        prodi='$prodi',
        jenis_kelamin='$jenis_kelamin',
        email='$email',
        no_hp='$no_hp',
        alamat='$alamat',
        foto='$fotoBaru'
        WHERE id_dosen='$id'
    ");

    echo "<script>
        alert('Data dosen berhasil diupdate');
        window.location='dosen.php';
    </script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Dosen</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>

body{
    background:#f4f7fc;
    font-family:'Segoe UI',sans-serif;
}

.container-box{
    max-width:1200px;
    margin:45px auto;
    padding:20px;
}

.card-custom{
    background:white;
    border-radius:35px;
    overflow:hidden;
    box-shadow:0 10px 35px rgba(15,23,42,.08);
}

.left-side{
    background:linear-gradient(180deg,#2563eb,#38bdf8);
    padding:50px 30px;
    text-align:center;
}

.preview{
    width:220px;
    height:220px;
    border-radius:30px;
    object-fit:cover;
    border:5px solid rgba(255,255,255,.4);
}

.left-side h3{
    color:white;
    margin-top:20px;
    font-weight:700;
}

.right-side{
    padding:45px;
}

.form-control,
.form-select{
    height:55px;
    border-radius:16px;
    background:#f8fafc;
}

.form-control:focus,
.form-select:focus{
    border:1px solid #38bdf8;
    box-shadow:none;
    background:white;
}

label{
    font-weight:600;
}

.btn-save{
    background:linear-gradient(135deg,#2563eb,#38bdf8);
    border:none;
    color:white;
    padding:14px 28px;
    border-radius:15px;
    font-weight:600;
}

.btn-back{
    background:#e2e8f0;
    color:#0f172a;
    border:none;
    padding:14px 28px;
    border-radius:15px;
    font-weight:600;
}

</style>

</head>

<body>

<div class="container-box">

<div class="card-custom">

<div class="row g-0">

<!-- LEFT -->
<div class="col-md-4 left-side">

<img id="preview"
class="preview"
src="../assets/uploads/dosen/<?= $data['foto'] ?: 'default.png' ?>">

<h3><?= $data['nama'] ?></h3>
<p>DN<?= str_pad($data['id_dosen'],4,'0',STR_PAD_LEFT) ?></p>

</div>

<!-- RIGHT -->
<div class="col-md-8 right-side">

<h2 class="mb-4">Edit Data Dosen</h2>

<form method="POST" enctype="multipart/form-data">

<div class="row">

<div class="col-md-6 mb-3">
<label>NIP</label>
<input type="text" name="nip" class="form-control" value="<?= $data['nip'] ?>" required>
</div>

<div class="col-md-6 mb-3">
<label>Nama</label>
<input type="text" name="nama" class="form-control" value="<?= $data['nama'] ?>" required>
</div>

<div class="col-md-6 mb-3">
<label>Jabatan</label>
<input type="text" name="jabatan" class="form-control" value="<?= $data['jabatan'] ?>">
</div>

<div class="col-md-6 mb-3">
<label>Fakultas</label>
<input type="text" name="fakultas" class="form-control" value="<?= $data['fakultas'] ?>">
</div>

<div class="col-md-6 mb-3">
<label>Prodi</label>
<input type="text" name="prodi" class="form-control" value="<?= $data['prodi'] ?>">
</div>

<div class="col-md-6 mb-3">
<label>Jenis Kelamin</label>
<select name="jenis_kelamin" class="form-select">
<option <?= $data['jenis_kelamin']=='Laki-laki'?'selected':'' ?>>Laki-laki</option>
<option <?= $data['jenis_kelamin']=='Perempuan'?'selected':'' ?>>Perempuan</option>
</select>
</div>

<div class="col-md-6 mb-3">
<label>Email</label>
<input type="email" name="email" class="form-control" value="<?= $data['email'] ?>">
</div>

<div class="col-md-6 mb-3">
<label>No HP</label>
<input type="text" name="no_hp" class="form-control" value="<?= $data['no_hp'] ?>">
</div>

<div class="col-md-12 mb-3">
<label>Alamat</label>
<textarea name="alamat" class="form-control"><?= $data['alamat'] ?></textarea>
</div>

<div class="col-md-12 mb-4">
<label>Ganti Foto</label>
<input type="file" name="foto" class="form-control" onchange="previewImage(event)">
</div>

</div>

<button class="btn btn-save" name="update">
<i class="fas fa-save"></i> Update
</button>

<a href="dosen.php" class="btn btn-back">Kembali</a>

</form>

</div>

</div>

</div>

</div>

<script>
function previewImage(event){
    document.getElementById('preview').src =
    URL.createObjectURL(event.target.files[0]);
}
</script>

</body>
</html>