<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: ../auth/login.php");
    exit;
}

$id_mahasiswa = $_SESSION['id_ref'];

// MAHASISWA
$mhs = mysqli_fetch_assoc(mysqli_query($koneksi,"
    SELECT * FROM mahasiswa
    WHERE id_mahasiswa='$id_mahasiswa'
"));

// ==========================
// JADWAL DARI KRS YANG SUDAH ACC
// ==========================
$query = mysqli_query($koneksi,"
    SELECT DISTINCT
        jadwal_kuliah.*,
        mata_kuliah.kode_mk,
        mata_kuliah.nama_mk,
        mata_kuliah.sks,
        dosen.nama AS nama_dosen,
        kelas.nama_kelas

    FROM krs

    JOIN mata_kuliah
        ON krs.id_mk = mata_kuliah.id_mk

    JOIN jadwal_kuliah
        ON jadwal_kuliah.id_mk = krs.id_mk
        AND jadwal_kuliah.id_kelas = krs.id_kelas

    LEFT JOIN dosen
        ON jadwal_kuliah.id_dosen = dosen.id_dosen

    LEFT JOIN kelas
        ON jadwal_kuliah.id_kelas = kelas.id_kelas

    WHERE krs.id_mahasiswa='$id_mahasiswa'
    AND LOWER(krs.status) = 'disetujui'

    ORDER BY
    FIELD(
        jadwal_kuliah.hari,
        'Senin',
        'Selasa',
        'Rabu',
        'Kamis',
        'Jumat',
        'Sabtu'
    ),
    jadwal_kuliah.jam_mulai ASC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Jadwal Mahasiswa</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">

<style>

/* ================= SAMA PERSIS KRS ================= */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    background:#f1f5f9;
}

/* SIDEBAR (COPY 100% KRS STYLE) */
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

/* CONTENT (SAMA) */
.content{
    margin-left:270px;
    padding:30px;
}

/* TOPBAR (SAMA KRS) */
.topbar{
    background:white;
    padding:20px 25px;
    border-radius:20px;
    box-shadow:0 10px 30px rgba(0,0,0,.06);
    margin-bottom:25px;
}

.topbar h3{
    font-weight:800;
}

.topbar p{
    margin:0;
    color:#64748b;
}

/* CARD (SAMA KRS) */
.card-custom{
    background:white;
    border-radius:20px;
    padding:25px;
    box-shadow:0 10px 30px rgba(0,0,0,.06);
    margin-bottom:25px;
}

/* TABLE (SAMA KRS STYLE) */
.table th{
    background:#eff6ff;
    color:#1e3a8a;
    font-weight:700;
}

.table td{
    font-size:13px;
    vertical-align:middle;
}

/* BUTTON (SAMA KRS) */
.btn-primary{
    border:none;
    border-radius:12px;
    padding:12px 20px;
}

/* BADGE OPTIONAL */
.badge{
    border-radius:10px;
    padding:8px 12px;
}

</style>

</head>

<body>

<!-- SIDEBAR (IDENTIK KRS) -->
<div class="sidebar">

    <div class="brand">
        <i class="fas fa-user-graduate"></i>
        <h3>SIAKAD</h3>
        <small>Mahasiswa Panel</small>
    </div>

    <div class="menu">

        <a href="dashboard.php"><i class="fas fa-house"></i> Dashboard</a>
        <a href="krs.php"><i class="fas fa-book-open"></i> KRS</a>
        <a href="khs.php"><i class="fas fa-chart-line"></i> KHS / Nilai</a>
        <a href="jadwal.php" class="active"><i class="fas fa-calendar-days"></i> Jadwal</a>
        <a href="profil.php"><i class="fas fa-user"></i> Profil</a>
        <a href="ganti_password.php"><i class="fas fa-key"></i> Ganti Password</a>
        <a href="logout.php"><i class="fas fa-right-from-bracket"></i> Logout</a>

    </div>

</div>

<!-- CONTENT -->
<div class="content">

    <div class="topbar">
        <h3>Jadwal Kuliah</h3>
        <p>Halo, <?= $mhs['nama']; ?></p>
    </div>

    <div class="card-custom">

        <table class="table table-hover">

            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode MK</th>
                    <th>Mata Kuliah</th>
                    <th>Kelas</th>
                    <th>Hari</th>
                    <th>Jam</th>
                    <th>Ruangan</th>
                    <th>Dosen</th>
                </tr>
            </thead>

            <tbody>

            <?php $no=1; while($row=mysqli_fetch_assoc($query)) { ?>

                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row['kode_mk'] ?></td>
                    <td><?= $row['nama_mk'] ?></td>
                    <td><?= $row['nama_kelas'] ?></td>
                    <td><?= $row['hari'] ?></td>
                    <td><?= $row['jam_mulai'] ?> - <?= $row['jam_selesai'] ?></td>
                    <td><?= $row['ruangan'] ?></td>
                    <td><?= $row['nama_dosen'] ?></td>
                </tr>

            <?php } ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>