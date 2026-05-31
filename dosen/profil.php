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

// UPDATE PROFIL
if (isset($_POST['update'])) {

    $nama = $_POST['nama'];
    $nip = $_POST['nip'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $jabatan = $_POST['jabatan'];
    $fakultas = $_POST['fakultas'];
    $prodi = $_POST['prodi'];
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];
    $alamat = $_POST['alamat'];

    $foto = $dosen['foto'];

    if (!empty($_FILES['foto']['name'])) {
        $namaFile = time() . "_" . $_FILES['foto']['name'];
        move_uploaded_file($_FILES['foto']['tmp_name'], "../assets/uploads/dosen/" . $namaFile);
        $foto = $namaFile;
    }

    mysqli_query($koneksi,"
        UPDATE dosen SET
        nama='$nama',
        nip='$nip',
        jenis_kelamin='$jenis_kelamin',
        jabatan='$jabatan',
        fakultas='$fakultas',
        prodi='$prodi',
        email='$email',
        no_hp='$no_hp',
        alamat='$alamat',
        foto='$foto'
        WHERE id_dosen='$id_dosen'
    ");

    $pesan = "✅ Profil berhasil diperbarui!";

    $dosen = mysqli_fetch_assoc(mysqli_query($koneksi,"
        SELECT * FROM dosen WHERE id_dosen='$id_dosen'
    "));
}

// FOTO
$fotoPath = "../assets/uploads/dosen/" . $dosen['foto'];

if (!empty($dosen['foto']) && file_exists($fotoPath)) {
    $foto = $fotoPath;
} else {
    $foto = "https://cdn-icons-png.flaticon.com/512/3135/3135715.png";
}

// GREETING
$jam = date("H");
if ($jam < 11) $greet = "Selamat Pagi";
elseif ($jam < 15) $greet = "Selamat Siang";
elseif ($jam < 18) $greet = "Selamat Sore";
else $greet = "Selamat Malam";
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Profil Dosen</title>

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

/* SIDEBAR (SAMA DASHBOARD) */
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
    font-weight:800;
    margin-top:10px;
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
    transform:translateX(5px);
}

/* CONTENT */
.content{
    margin-left:280px;
    padding:30px;
}

/* TOPBAR (UPDATED ADA NAMA DOSEN) */
.topbar{
    background:white;
    border-radius:24px;
    padding:20px;
    box-shadow:0 15px 40px rgba(0,0,0,.08);
    margin-bottom:25px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.topbar h3{
    font-weight:800;
    margin:0;
}

.topbar p{
    margin:0;
    color:#64748b;
}

.user-box{
    text-align:right;
    font-weight:600;
    color:#334155;
}

.user-box small{
    display:block;
    color:#64748b;
}

/* CARD */
.card-box{
    background:white;
    border-radius:25px;
    padding:30px;
    box-shadow:0 15px 35px rgba(0,0,0,.08);
}

/* FOTO */
.profile-img{
    width:160px;
    height:160px;
    border-radius:25px;
    object-fit:cover;
    border:6px solid #dbeafe;
}

/* FORM */
.form-control{
    border-radius:12px;
    padding:10px;
}

.btn-primary{
    background:linear-gradient(135deg,#2563eb,#7c3aed);
    border:none;
    border-radius:12px;
    padding:10px 20px;
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
        <a href="profil.php" class="active"><i class="fas fa-user"></i> Profil</a>
        <a href="ganti_password.php"><i class="fas fa-key"></i> Ganti Password</a>
        <a href="../auth/logout.php"><i class="fas fa-right-from-bracket"></i> Logout</a>
    </div>

</div>

<!-- CONTENT -->
<div class="content">

    <!-- TOPBAR UPDATED -->
    <div class="topbar">

        <div>
            <h3><?= $greet ?>, <?= $dosen['nama']; ?> 👋</h3>
            <p>Profil dan data akun dosen</p>
        </div>

        <div class="user-box">
            <div><?= $dosen['nama']; ?></div>
            <small><?= $dosen['jabatan']; ?></small>
        </div>

    </div>

    <div class="card-box">

        <?php if($pesan != "") { ?>
            <div class="alert alert-success">
                <?= $pesan ?>
            </div>
        <?php } ?>

        <form method="POST" enctype="multipart/form-data">

        <div class="row">

            <div class="col-md-3 text-center">
                <img src="<?= $foto ?>" class="profile-img mb-3">
                <input type="file" name="foto" class="form-control">
            </div>

            <div class="col-md-9">

                <div class="row g-3">

                    <div class="col-md-6">
                        <label>Nama</label>
                        <input type="text" name="nama" value="<?= $dosen['nama'] ?>" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label>NIP</label>
                        <input type="text" name="nip" value="<?= $dosen['nip'] ?>" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label>Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-control">
                            <option value="Laki-laki" <?= ($dosen['jenis_kelamin'] == 'Laki-laki') ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="Perempuan" <?= ($dosen['jenis_kelamin'] == 'Perempuan') ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Jabatan</label>
                        <input type="text" name="jabatan" value="<?= $dosen['jabatan'] ?>" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label>Fakultas</label>
                        <input type="text" name="fakultas" value="<?= $dosen['fakultas'] ?>" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label>Prodi</label>
                        <input type="text" name="prodi" value="<?= $dosen['prodi'] ?>" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label>Email</label>
                        <input type="text" name="email" value="<?= $dosen['email'] ?>" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label>No HP</label>
                        <input type="text" name="no_hp" value="<?= $dosen['no_hp'] ?>" class="form-control">
                    </div>

                    <div class="col-md-12">
                        <label>Alamat</label>
                        <textarea name="alamat" class="form-control"><?= $dosen['alamat'] ?></textarea>
                    </div>

                </div>

                <br>

                <button class="btn btn-primary" name="update">
                    <i class="fas fa-save"></i> Simpan
                </button>

            </div>

        </div>

        </form>

    </div>

</div>

</body>
</html>