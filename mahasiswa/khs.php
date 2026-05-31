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

// NILAI + KRS
$query = mysqli_query($koneksi,"
    SELECT 
        krs.id_krs,
        krs.status,
        mata_kuliah.kode_mk,
        mata_kuliah.nama_mk,
        mata_kuliah.sks,
        mata_kuliah.semester,
        dosen.nama AS nama_dosen,
        nilai.tugas,
        nilai.uts,
        nilai.uas,
        nilai.nilai_akhir,
        nilai.grade
    FROM krs
    JOIN mata_kuliah ON krs.id_mk = mata_kuliah.id_mk
    LEFT JOIN nilai 
        ON nilai.id_mk = krs.id_mk 
        AND nilai.id_mahasiswa = krs.id_mahasiswa
    LEFT JOIN dosen ON mata_kuliah.id_dosen = dosen.id_dosen
    WHERE krs.id_mahasiswa='$id_mahasiswa'
");

// HITUNG IP
$total_sks = 0;
$total_mutu = 0;

$data = [];

while($row = mysqli_fetch_assoc($query)){

    $bobot = 0;

    if($row['grade']=='A') $bobot=4;
    else if($row['grade']=='B') $bobot=3;
    else if($row['grade']=='C') $bobot=2;
    else if($row['grade']=='D') $bobot=1;

    $total_sks += $row['sks'];
    $total_mutu += ($bobot * $row['sks']);

    $data[] = $row;
}

$ip = ($total_sks>0) ? $total_mutu/$total_sks : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>KHS Mahasiswa</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">

<style>

/* ================= GLOBAL (SAMA KRS) ================= */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    background:#f1f5f9;
}

/* SIDEBAR (100% SAMA KRS) */
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

/* TOPBAR (DIBUAT SAMA PERSIS KRS) */
.topbar{
    background:white;
    padding:20px 25px;
    border-radius:20px;
    box-shadow:0 10px 30px rgba(0,0,0,.06);
    margin-bottom:25px;
}

.topbar h3{
    font-weight:800; /* FIX BOLD */
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

/* JUDUL CARD HARUS BOLD (INI FIX YANG KAMU MAU) */
.card-custom h5{
    font-weight:700;
}

/* TABLE (SAMA KRS STYLE) */
.table th{
    background:#eff6ff;
    color:#1e3a8a;
    font-weight:700; /* FIX BOLD HEADER */
}

.table td{
    font-size:13px;
    vertical-align:middle;
}

/* BADGE */
.badge{
    border-radius:10px;
    padding:8px 12px;
    font-weight:600;
}

.badge-A{background:#16a34a;}
.badge-B{background:#2563eb;}
.badge-C{background:#f59e0b;}
.badge-D{background:#dc2626;}

</style>

</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">

    <div class="brand">
        <i class="fas fa-user-graduate"></i>
        <h3>SIAKAD</h3>
        <small>Mahasiswa Panel</small>
    </div>

    <div class="menu">

        <a href="dashboard.php"><i class="fas fa-house"></i> Dashboard</a>
        <a href="krs.php"><i class="fas fa-book-open"></i> KRS</a>
        <a href="khs.php" class="active"><i class="fas fa-chart-line"></i> KHS / Nilai</a>
        <a href="jadwal.php"><i class="fas fa-calendar-days"></i> Jadwal</a>
        <a href="profil.php"><i class="fas fa-user"></i> Profil</a>
        <a href="ganti_password.php"><i class="fas fa-key"></i> Ganti Password</a>
        <a href="logout.php"><i class="fas fa-right-from-bracket"></i> Logout</a>

    </div>

</div>

<!-- CONTENT -->
<div class="content">

    <div class="topbar">
        <h3>Kartu Hasil Studi (KHS)</h3>
        <p>Halo, <?= $mhs['nama']; ?></p>
    </div>

    <!-- IP CARD (DIBIKIN SAMA FEEL KRS) -->
    <div class="card-custom">
        <h5>Indeks Prestasi (IP)</h5>
        <h3 class="text-primary fw-bold">
            <?= number_format($ip,2) ?>
        </h3>
    </div>

    <!-- TABLE -->
    <div class="card-custom">

        <table class="table table-hover">

            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode MK</th>
                    <th>Mata Kuliah</th>
                    <th>SKS</th>
                    <th>Dosen</th>
                    <th>Nilai Akhir</th>
                    <th>Grade</th>
                </tr>
            </thead>

            <tbody>

            <?php $no=1; foreach($data as $d){ ?>

                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $d['kode_mk'] ?></td>
                    <td><?= $d['nama_mk'] ?></td>
                    <td><?= $d['sks'] ?></td>
                    <td><?= $d['nama_dosen'] ?></td>
                    <td><?= $d['nilai_akhir'] ?? '-' ?></td>

                    <td>
                        <?php if($d['grade']) { ?>
                            <span class="badge badge-<?= $d['grade'] ?>">
                                <?= $d['grade'] ?>
                            </span>
                        <?php } else { echo "-"; } ?>
                    </td>

                </tr>

            <?php } ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>