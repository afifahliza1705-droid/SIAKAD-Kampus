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

// search
$cari = "";

if(isset($_GET['cari'])){

    $cari = mysqli_real_escape_string($koneksi,$_GET['cari']);

    $query = mysqli_query($koneksi,"
        SELECT * FROM mahasiswa
        WHERE nim LIKE '%$cari%'
        OR nama LIKE '%$cari%'
        OR prodi LIKE '%$cari%'
        OR jurusan LIKE '%$cari%'
        ORDER BY id_mahasiswa DESC
    ");

}else{

    $query = mysqli_query($koneksi,"
        SELECT * FROM mahasiswa
        ORDER BY id_mahasiswa DESC
    ");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Data Mahasiswa</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

/* BASE */
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

/* BOX */
.box{
    background:white;
    padding:25px;
    border-radius:20px;
    box-shadow:0 10px 30px rgba(0,0,0,.06);
}

/* HEADER */
.header-page{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.header-page h2{
    font-weight:800;
}

/* BUTTON TAMBAH */
.btn-tambah{
    background:linear-gradient(135deg,#2563eb,#38bdf8);
    color:white;
    padding:12px 18px;
    border-radius:12px;
    text-decoration:none;
    font-weight:600;
}

/* SEARCH */
.search-box{
    border-radius:12px;
    height:45px;
}

/* TABLE */
.table th{
    background:#eff6ff;
    color:#1e3a8a;
    white-space:nowrap;
    font-size:13px;
    text-align: center;
}

.table td{
    vertical-align:middle;
    white-space:nowrap;
    font-size:13px;
    text-align: center;
}

/* FOTO */
.foto{
    width:55px;
    height:55px;
    border-radius:50%;
    object-fit:cover;
    border:2px solid #38bdf8;
}

/* ACTION BUTTON */
.btn-action{
    font-size:12px;
    border-radius:10px;
    padding:6px 10px;
    margin-right:4px;
}

.btn-detail{
    background:#0ea5e9;
    color:white;
}

.btn-edit{
    background:#facc15;
    color:black;
}

.btn-delete{
    background:#ef4444;
    color:white;
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
        <a href="mahasiswa.php" class="active"><i class="fas fa-user-graduate"></i> Mahasiswa</a>
        <a href="dosen.php"><i class="fas fa-chalkboard-user"></i> Dosen</a>
        <a href="mata_kuliah.php"><i class="fas fa-book-open"></i> Mata Kuliah</a>
        <a href="jadwal.php"><i class="fas fa-calendar"></i> Jadwal</a>
        <a href="kelas.php"><i class="fas fa-layer-group"></i> Kelas</a>
        <a href="atur_pa.php"><i class="fas fa-users"></i> Pengaturan PA</a>
        <a href="ganti_password.php"><i class="fas fa-key"></i> Ganti Password</a>
        <a href="../auth/logout.php"><i class="fas fa-right-from-bracket"></i> Logout</a>

    </div>
</div>

<!-- CONTENT -->
<div class="content">

<div class="box">

    <!-- HEADER -->
    <div class="header-page">

        <div>
            <h2>Data Mahasiswa</h2>
            <p class="text-muted">Kelola data mahasiswa</p>
        </div>

        <a href="tambah_mahasiswa.php" class="btn-tambah">
            <i class="fas fa-plus me-2"></i> Tambah Mahasiswa
        </a>

    </div>

    <!-- SEARCH -->
    <form method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-4">
                <input type="text" name="cari" class="form-control search-box"
                placeholder="Cari mahasiswa..." value="<?= $cari ?>">
            </div>

            <div class="col-md-2">
                <button class="btn btn-primary w-100 search-box">Search</button>
            </div>
        </div>
    </form>

    <!-- TABLE -->
    <div class="table-responsive">

        <table class="table table-hover table-bordered">

            <thead>
                <tr>
                    <th>No</th>
                    <th>Foto</th>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Jurusan</th>
                    <th>Prodi</th>
                    <th>Email</th>
                    <th>JK</th>
                    <th>Agama</th>
                    <th>Tempat Lahir</th>
                    <th>Tanggal Lahir</th>
                    <th>Status</th>
                    <th>Alamat</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

            <?php $no=1; while($m = mysqli_fetch_assoc($query)) :

            $foto = !empty($m['foto']) && file_exists("../assets/uploads/mahasiswa/".$m['foto'])
            ? "../assets/uploads/mahasiswa/".$m['foto']
            : "https://via.placeholder.com/100";

            ?>

            <tr>
                <td><?= $no++ ?></td>

                <td>
                    <img src="<?= $foto ?>" class="foto">
                </td>

                <td><?= $m['nim'] ?></td>
                <td><?= $m['nama'] ?></td>
                <td><?= $m['jurusan'] ?></td>
                <td><?= $m['prodi'] ?></td>
                <td><?= $m['email'] ?></td>
                <td><?= $m['jenis_kelamin'] ?></td>
                <td><?= $m['agama'] ?></td>
                <td><?= $m['tempat_lahir'] ?></td>
                <td><?= $m['tanggal_lahir'] ?></td>
                <td><?= $m['status'] ?></td>
                <td><?= $m['alamat'] ?></td>

                <td>

                    <a href="detail_mahasiswa.php?id=<?= $m['id_mahasiswa'] ?>"
                    class="btn btn-detail btn-action">
                        <i class="fas fa-eye me-1"></i> Detail
                    </a>

                    <a href="edit_mahasiswa.php?id=<?= $m['id_mahasiswa'] ?>"
                    class="btn btn-edit btn-action">
                        <i class="fas fa-pen me-1"></i> Edit
                    </a>

                    <a href="hapus_mahasiswa.php?id=<?= $m['id_mahasiswa'] ?>"
                    onclick="return confirm('Hapus data?')"
                    class="btn btn-delete btn-action">
                        <i class="fas fa-trash me-1"></i> Delete
                    </a>

                </td>

            </tr>

            <?php endwhile; ?>

            </tbody>

        </table>

    </div>

</div>
</div>

</body>
</html>