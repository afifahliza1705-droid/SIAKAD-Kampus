<?php
session_start();
include '../config/koneksi.php';

// proteksi admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// simpan data
if(isset($_POST['simpan'])){

    $nim = mysqli_real_escape_string($koneksi,$_POST['nim']);
    $nama = mysqli_real_escape_string($koneksi,$_POST['nama']);
    $jurusan = mysqli_real_escape_string($koneksi,$_POST['jurusan']);
    $prodi = mysqli_real_escape_string($koneksi,$_POST['prodi']);
    $email = mysqli_real_escape_string($koneksi,$_POST['email']);
    $jenis_kelamin = mysqli_real_escape_string($koneksi,$_POST['jenis_kelamin']);
    $agama = mysqli_real_escape_string($koneksi,$_POST['agama']);
    $tempat_lahir = mysqli_real_escape_string($koneksi,$_POST['tempat_lahir']);
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $status = mysqli_real_escape_string($koneksi,$_POST['status']);
    $alamat = mysqli_real_escape_string($koneksi,$_POST['alamat']);

    // default foto
    $fotoBaru = '';

    // upload foto
    if(isset($_FILES['foto']) && $_FILES['foto']['error'] == 0){

        $allowed = ['jpg','jpeg','png','webp'];

        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

        if(in_array($ext,$allowed)){

            $fotoBaru = time() . "_" . rand(100,999) . "." . $ext;

            $folder = "../assets/uploads/mahasiswa/";

            if(!file_exists($folder)){
                mkdir($folder,0777,true);
            }

            move_uploaded_file($_FILES['foto']['tmp_name'], $folder . $fotoBaru);
        }
    }

    // =========================
    // INSERT MAHASISWA
    // =========================
    $insert_mhs = mysqli_query($koneksi,"
        INSERT INTO mahasiswa
        (
            nim,
            nama,
            jurusan,
            prodi,
            email,
            jenis_kelamin,
            agama,
            tempat_lahir,
            tanggal_lahir,
            status,
            alamat,
            foto
        )
        VALUES
        (
            '$nim',
            '$nama',
            '$jurusan',
            '$prodi',
            '$email',
            '$jenis_kelamin',
            '$agama',
            '$tempat_lahir',
            '$tanggal_lahir',
            '$status',
            '$alamat',
            '$fotoBaru'
        )
    ");

    if(!$insert_mhs){
        die("Gagal insert mahasiswa: " . mysqli_error($koneksi));
    }

    $id_mahasiswa = mysqli_insert_id($koneksi);

    // =========================
    // AUTO AKUN LOGIN USERS
    // =========================

    $hashedPassword = password_hash($nim, PASSWORD_DEFAULT);

    $insert_user = mysqli_query($koneksi,"
        INSERT INTO users (username, password, role, id_ref)
        VALUES ('$nim', '$hashedPassword', 'mahasiswa', '$id_mahasiswa')
    ");

    if(!$insert_user){
        die("Gagal insert user: " . mysqli_error($koneksi));
    }

    echo "
    <script>
        alert('Mahasiswa berhasil ditambahkan');
        window.location='mahasiswa.php';
    </script>
    ";
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Tambah Mahasiswa</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

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
    box-shadow:
    0 10px 35px rgba(15,23,42,.08);
}

.left-side{
    background:linear-gradient(
        180deg,
        #2563eb,
        #38bdf8
    );

    padding:50px 30px;
    text-align:center;
    min-height:100%;
}

.preview{
    width:220px;
    height:220px;
    border-radius:30px;
    object-fit:cover;
    border:5px solid rgba(255,255,255,.4);
    box-shadow:
    0 8px 25px rgba(0,0,0,.15);
}

.left-side h3{
    color:white;
    margin-top:20px;
    font-weight:700;
}

.left-side p{
    color:rgba(255,255,255,.9);
}

.right-side{
    padding:45px;
}

.title h2{
    font-weight:700;
    color:#0f172a;
}

.title p{
    color:#64748b;
}

.form-control,
.form-select{
    height:55px;
    border-radius:16px;
    border:1px solid #dbeafe;
    background:#f8fafc;
}

.form-control:focus,
.form-select:focus{
    border:1px solid #38bdf8;
    box-shadow:none;
    background:white;
}

textarea.form-control{
    height:120px;
}

label{
    font-weight:600;
    margin-bottom:8px;
    color:#334155;
}

.btn-save{
    background:linear-gradient(
        135deg,
        #2563eb,
        #38bdf8
    );

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

<div class="col-md-4 left-side">

<img
id="preview"
class="preview"
src="https://ui-avatars.com/api/?name=Mahasiswa&background=ffffff&color=2563eb&size=300">

<h3 id="namaPreview">
Nama Mahasiswa
</h3>

<p id="nimPreview">
NIM Mahasiswa
</p>

</div>

<div class="col-md-8 right-side">

<div class="title mb-4">
<h2>Tambah Mahasiswa</h2>
<p>Lengkapi data mahasiswa</p>
</div>

<form method="POST"
enctype="multipart/form-data">

<div class="row">

<div class="col-md-6 mb-3">
<label>NIM</label>
<input type="text"
name="nim"
id="nim"
class="form-control"
required>
</div>

<div class="col-md-6 mb-3">
<label>Nama Lengkap</label>
<input type="text"
name="nama"
id="nama"
class="form-control"
required>
</div>

<div class="col-md-6 mb-3">
<label>Jurusan</label>
<input type="text"
name="jurusan"
class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Program Studi</label>
<input type="text"
name="prodi"
class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Email</label>
<input type="email"
name="email"
class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Jenis Kelamin</label>
<select name="jenis_kelamin" class="form-select">
<option value="">Pilih</option>
<option value="Laki-laki">Laki-laki</option>
<option value="Perempuan">Perempuan</option>
</select>
</div>

<div class="col-md-6 mb-3">
<label>Agama</label>
<select name="agama" class="form-select">
<option value="">Pilih Agama</option>
<option>Islam</option>
<option>Kristen</option>
<option>Katolik</option>
<option>Hindu</option>
<option>Buddha</option>
<option>Konghucu</option>
</select>
</div>

<div class="col-md-6 mb-3">
<label>Tempat Lahir</label>
<input type="text"
name="tempat_lahir"
class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Tanggal Lahir</label>
<input type="date"
name="tanggal_lahir"
class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Status</label>
<select name="status" class="form-select">
<option value="Aktif">Aktif</option>
<option value="Cuti">Cuti</option>
<option value="Lulus">Lulus</option>
</select>
</div>

<div class="col-md-12 mb-3">
<label>Alamat</label>
<textarea
name="alamat"
class="form-control"></textarea>
</div>

<div class="col-md-12 mb-4">
<label>Foto Mahasiswa</label>
<input type="file"
name="foto"
class="form-control"
accept="image/*"
onchange="previewImage(event)">
</div>

</div>

<button
name="simpan"
class="btn btn-save">

<i class="fas fa-save"></i>
Simpan

</button>

<a href="mahasiswa.php"
class="btn btn-back">
Kembali
</a>

</form>

</div>
</div>
</div>
</div>

<script>

function previewImage(event){

    const image =
    document.getElementById('preview');

    image.src =
    URL.createObjectURL(
        event.target.files[0]
    );
}

document
.getElementById('nama')
.addEventListener('input', function(){

document
.getElementById('namaPreview')
.innerText =
this.value || 'Nama Mahasiswa';

});

document
.getElementById('nim')
.addEventListener('input', function(){

document
.getElementById('nimPreview')
.innerText =
this.value || 'NIM Mahasiswa';

});

</script>

</body>
</html>