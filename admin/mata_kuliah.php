<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$cari = "";

// QUERY DENGAN JOIN DOSEN DAN KELAS
if (isset($_GET['cari']) && $_GET['cari'] != '') {

    $cari = mysqli_real_escape_string($koneksi, $_GET['cari']);

    $query = mysqli_query($koneksi, "
        SELECT 
            mata_kuliah.*,
            dosen.nama AS nama_dosen,
            kelas.nama_kelas
        FROM mata_kuliah
        LEFT JOIN dosen
            ON mata_kuliah.id_dosen = dosen.id_dosen
        LEFT JOIN kelas
            ON mata_kuliah.id_kelas = kelas.id_kelas
        WHERE 
            mata_kuliah.kode_mk LIKE '%$cari%'
            OR mata_kuliah.nama_mk LIKE '%$cari%'
            OR dosen.nama LIKE '%$cari%'
            OR kelas.nama_kelas LIKE '%$cari%'
        ORDER BY mata_kuliah.id_mk DESC
    ");

} else {

    $query = mysqli_query($koneksi, "
        SELECT 
            mata_kuliah.*,
            dosen.nama AS nama_dosen,
            kelas.nama_kelas
        FROM mata_kuliah
        LEFT JOIN dosen
            ON mata_kuliah.id_dosen = dosen.id_dosen
        LEFT JOIN kelas
            ON mata_kuliah.id_kelas = kelas.id_kelas
        ORDER BY mata_kuliah.id_mk DESC
    ");
}

// CEK ERROR BIAR TIDAK FATAL
if (!$query) {
    die("Query error: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Data Mata Kuliah</title>

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

/* BUTTON */
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
    text-align:center;
}

.table td{
    vertical-align:middle;
    white-space:nowrap;
    font-size:13px;
    text-align:center;
}

/* ACTION */
.btn-action{
    font-size:12px;
    border-radius:10px;
    padding:6px 10px;
    margin-right:4px;
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
        <a href="mata_kuliah.php" class="active"><i class="fas fa-book-open"></i> Mata Kuliah</a>
        <a href="jadwal.php"><i class="fas fa-calendar"></i> Jadwal</a>
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
            <h2>Data Mata Kuliah</h2>
            <p class="text-muted">Kelola data mata kuliah</p>
        </div>

        <a href="tambah_mk.php" class="btn-tambah">
            + Tambah Mata Kuliah
        </a>
    </div>

    <form method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-4">
                <input type="text" name="cari"
                class="form-control search-box"
                placeholder="Cari mata kuliah / dosen..."
                value="<?= $cari ?>">
            </div>

            <div class="col-md-2">
                <button class="btn btn-primary w-100 search-box">
                    Search
                </button>
            </div>
        </div>
    </form>

    <div class="table-responsive">

        <table class="table table-hover table-bordered">

            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode MK</th>
                    <th>Nama MK</th>
                    <th>SKS</th>
                    <th>Semester</th>
                    <th>Kelas</th>
                    <th>Nama Dosen</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

            <?php
            $no = 1;
            while ($mk = mysqli_fetch_assoc($query)) :
            ?>

            <tr>
                <td><?= $no++ ?></td>
                <td><?= $mk['kode_mk'] ?></td>
                <td><?= $mk['nama_mk'] ?></td>
                <td><?= $mk['sks'] ?></td>
                <td><?= $mk['semester'] ?? '-' ?></td>
                <td><?= $mk['nama_kelas'] ?? '-' ?></td>

                <td>
                    <?= $mk['nama_dosen'] ?? '-' ?>
                </td>

                <td>
                    <a href="edit_mk.php?id=<?= $mk['id_mk'] ?>"
                    class="btn btn-edit btn-action">
                        <i class="fas fa-pen me-1"></i> Edit
                    </a>

                    <a href="hapus_mk.php?id=<?= $mk['id_mk'] ?>"
                    class="btn btn-delete btn-action"
                    onclick="return confirm('Hapus mata kuliah?')">
                        <i class="fas fa-trash me-1"></i> Hapus
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