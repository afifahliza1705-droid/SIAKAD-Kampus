<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

if (isset($_POST['simpan'])) {

    $nip = mysqli_real_escape_string($koneksi, $_POST['nip']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jabatan = mysqli_real_escape_string($koneksi, $_POST['jabatan']);
    $fakultas = mysqli_real_escape_string($koneksi, $_POST['fakultas']);
    $prodi = mysqli_real_escape_string($koneksi, $_POST['prodi']);
    $jenis_kelamin = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $no_hp = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);

    // FOTO
    $fotoBaru = "";

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];

        if (in_array($ext, $allowed)) {
            $fotoBaru = time().'_'.rand(100,999).'.'.$ext;
            $folder = "../assets/uploads/dosen/";

            if (!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }

            move_uploaded_file($_FILES['foto']['tmp_name'], $folder.$fotoBaru);
        }
    }

    // ==========================
    // GENERATE ID DOSEN MANUAL (ANTI 0 & DUPLIKAT)
    // ==========================
    $getLast = mysqli_query($koneksi, "
        SELECT MAX(id_dosen) AS max_id FROM dosen
    ");
    $data = mysqli_fetch_assoc($getLast);

    $id_dosen = $data['max_id'] + 1;

    if (!$id_dosen) {
        $id_dosen = 1;
    }

    // ==========================
    // INSERT DOSEN (MANUAL ID)
    // ==========================
    $insert_dosen = mysqli_query($koneksi, "
        INSERT INTO dosen (
            id_dosen,
            nip, nama, jabatan, fakultas, prodi,
            jenis_kelamin, email, no_hp, alamat, foto
        ) VALUES (
            '$id_dosen',
            '$nip', '$nama', '$jabatan', '$fakultas', '$prodi',
            '$jenis_kelamin', '$email', '$no_hp', '$alamat', '$fotoBaru'
        )
    ");

    if (!$insert_dosen) {
        die("ERROR DOSEN: " . mysqli_error($koneksi));
    }

    // ==========================
    // USER LOGIN
    // ==========================
    $hashedPassword = password_hash($nip, PASSWORD_DEFAULT);

    $cekUser = mysqli_query($koneksi, "
        SELECT id_ref FROM users WHERE username='$nip'
    ");

    if (mysqli_num_rows($cekUser) == 0) {

        $insert_user = mysqli_query($koneksi, "
            INSERT INTO users (
                username, password, role, id_ref
            ) VALUES (
                '$nip', '$hashedPassword', 'dosen', '$id_dosen'
            )
        ");

        if (!$insert_user) {
            die("ERROR USERS: " . mysqli_error($koneksi));
        }
    }

    echo "<script>
        alert('Dosen berhasil ditambahkan (manual ID)');
        window.location='dosen.php';
    </script>";
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tambah Dosen</title>

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

.right-side{
    padding:45px;
}

.form-control,
.form-select{
    height:55px;
    border-radius:16px;
}

.btn-save{
    background:linear-gradient(135deg,#2563eb,#38bdf8);
    border:none;
    color:white;
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

<img class="preview"
src="https://ui-avatars.com/api/?name=Dosen&background=ffffff&color=2563eb&size=300">


</div>

<!-- RIGHT -->
<div class="col-md-8 right-side">

<h2>Tambah Dosen</h2>
<p>Lengkapi data dosen</p>

<form method="POST" enctype="multipart/form-data">

<div class="row">

<div class="col-md-6 mb-3">
<label>NIP</label>
<input type="text" name="nip" class="form-control" required>
</div>

<div class="col-md-6 mb-3">
<label>Nama</label>
<input type="text" name="nama" class="form-control" required>
</div>

<div class="col-md-6 mb-3">
<label>Jabatan</label>
<input type="text" name="jabatan" class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Fakultas</label>
<input type="text" name="fakultas" class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Prodi</label>
<input type="text" name="prodi" class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Jenis Kelamin</label>
<select name="jenis_kelamin" class="form-select">
<option>Laki-laki</option>
<option>Perempuan</option>
</select>
</div>

<div class="col-md-6 mb-3">
<label>Email</label>
<input type="email" name="email" class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>No HP</label>
<input type="text" name="no_hp" class="form-control">
</div>

<div class="col-md-12 mb-3">
<label>Alamat</label>
<textarea name="alamat" class="form-control"></textarea>
</div>

<div class="col-md-12 mb-3">
<label>Foto</label>
<input type="file" name="foto" class="form-control">
</div>

</div>

<button class="btn btn-save" name="simpan">
Simpan
</button>

<a href="dosen.php" class="btn btn-secondary">
Kembali
</a>

</form>

</div>

</div>

</div>

</div>

</body>
</html>