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

// cek id
if(!isset($_GET['id'])){
    header("Location: mahasiswa.php");
    exit;
}

$id = intval($_GET['id']);

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM mahasiswa
    WHERE id_mahasiswa='$id'"
);

$mhs = mysqli_fetch_assoc($query);

if (!$mhs) {
    header("Location: mahasiswa.php");
    exit;
}

// UPDATE DATA
if(isset($_POST['update'])){

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

    $fotoLama = $mhs['foto'];
    $fotoBaru = $fotoLama;

    // upload foto baru
    if(
        isset($_FILES['foto']) &&
        $_FILES['foto']['error'] == 0
    ){

        $allowed =
        ['jpg','jpeg','png','webp'];

        $ext = strtolower(
            pathinfo(
                $_FILES['foto']['name'],
                PATHINFO_EXTENSION
            )
        );

        if(in_array($ext,$allowed)){

            $fotoBaru =
            time() . "_" .
            rand(100,999) .
            "." . $ext;

            $folder =
            "../assets/uploads/mahasiswa/";

            if(!file_exists($folder)){
                mkdir(
                    $folder,
                    0777,
                    true
                );
            }

            move_uploaded_file(
                $_FILES['foto']['tmp_name'],
                $folder . $fotoBaru
            );

            // hapus foto lama
            if(
                !empty($fotoLama) &&
                file_exists(
                    $folder .
                    $fotoLama
                )
            ){
                unlink(
                    $folder .
                    $fotoLama
                );
            }
        }
    }

    mysqli_query(
        $koneksi,
        "UPDATE mahasiswa SET

        nim='$nim',
        nama='$nama',
        jurusan='$jurusan',
        prodi='$prodi',
        email='$email',
        jenis_kelamin='$jenis_kelamin',
        agama='$agama',
        tempat_lahir='$tempat_lahir',
        tanggal_lahir='$tanggal_lahir',
        status='$status',
        alamat='$alamat',
        foto='$fotoBaru'

        WHERE id_mahasiswa='$id'"
    );

    // update akun login
    mysqli_query(
        $koneksi,
        "UPDATE users SET
        username='$nim',
        password='$nim'
        WHERE id_ref='$id'
        AND role='mahasiswa'"
    );

    echo "
    <script>
        alert('Data berhasil diupdate');
        window.location='mahasiswa.php';
    </script>
    ";
}

// cek foto
$fotoPath =
"../assets/uploads/mahasiswa/" .
$mhs['foto'];

if(
    !empty($mhs['foto']) &&
    file_exists($fotoPath)
){
    $foto = $fotoPath;
}else{
    $foto =
    "https://ui-avatars.com/api/?name=" .
    urlencode($mhs['nama']) .
    "&background=ffffff&color=2563eb&size=300";
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Edit Mahasiswa</title>

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
    height:100%;
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
    height:120px !important;
}

label{
    font-weight:600;
    margin-bottom:8px;
    color:#334155;
}

.btn-update{
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
src="<?= $foto ?>"
class="preview">

<h3 id="namaPreview">
<?= htmlspecialchars($mhs['nama']) ?>
</h3>

<p id="nimPreview">
<?= htmlspecialchars($mhs['nim']) ?>
</p>

</div>

<div class="col-md-8 right-side">

<div class="title mb-4">
<h2>Edit Mahasiswa</h2>
<p>Perbarui data mahasiswa</p>
</div>

<form method="POST"
enctype="multipart/form-data">

<div class="row">

<div class="col-md-6 mb-3">
<label>NIM</label>
<input type="text"
id="nim"
name="nim"
value="<?= htmlspecialchars($mhs['nim']) ?>"
class="form-control"
required>
</div>

<div class="col-md-6 mb-3">
<label>Nama Lengkap</label>
<input type="text"
id="nama"
name="nama"
value="<?= htmlspecialchars($mhs['nama']) ?>"
class="form-control"
required>
</div>

<div class="col-md-6 mb-3">
<label>Jurusan</label>
<input type="text"
name="jurusan"
value="<?= htmlspecialchars($mhs['jurusan']) ?>"
class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Program Studi</label>
<input type="text"
name="prodi"
value="<?= htmlspecialchars($mhs['prodi']) ?>"
class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Email</label>
<input type="email"
name="email"
value="<?= htmlspecialchars($mhs['email']) ?>"
class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Jenis Kelamin</label>

<select
name="jenis_kelamin"
class="form-select">

<option value="Laki-laki"
<?= $mhs['jenis_kelamin']=='Laki-laki' ? 'selected' : '' ?>>
Laki-laki
</option>

<option value="Perempuan"
<?= $mhs['jenis_kelamin']=='Perempuan' ? 'selected' : '' ?>>
Perempuan
</option>

</select>
</div>

<div class="col-md-6 mb-3">
<label>Agama</label>
<input type="text"
name="agama"
value="<?= htmlspecialchars($mhs['agama']) ?>"
class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Tempat Lahir</label>
<input type="text"
name="tempat_lahir"
value="<?= htmlspecialchars($mhs['tempat_lahir']) ?>"
class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Tanggal Lahir</label>
<input type="date"
name="tanggal_lahir"
value="<?= $mhs['tanggal_lahir'] ?>"
class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Status</label>

<select
name="status"
class="form-select">

<option value="Aktif"
<?= $mhs['status']=='Aktif' ? 'selected' : '' ?>>
Aktif
</option>

<option value="Cuti"
<?= $mhs['status']=='Cuti' ? 'selected' : '' ?>>
Cuti
</option>

<option value="Lulus"
<?= $mhs['status']=='Lulus' ? 'selected' : '' ?>>
Lulus
</option>

</select>
</div>

<div class="col-md-12 mb-3">
<label>Alamat</label>
<textarea
name="alamat"
class="form-control"><?= htmlspecialchars($mhs['alamat']) ?></textarea>
</div>

<div class="col-md-12 mb-4">
<label>Ganti Foto</label>

<input type="file"
name="foto"
class="form-control"
accept="image/*"
onchange="previewImage(event)">
</div>

</div>

<button
name="update"
class="btn btn-update">

<i class="fas fa-save"></i>
Update Data
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