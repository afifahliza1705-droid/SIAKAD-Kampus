<?php
session_start();
include '../config/koneksi.php';

// ==========================
// PROTEKSI DOSEN
// ==========================
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'dosen') {
    header("Location: ../auth/login.php");
    exit;
}

$id_dosen = $_SESSION['id_ref'];

// ==========================
// ACTION ACC
// ==========================
if (isset($_GET['acc'])) {

    $id = mysqli_real_escape_string($koneksi, $_GET['acc']);

    mysqli_query($koneksi,"
        UPDATE krs
        SET status='disetujui'
        WHERE id_krs='$id'
    ");

    header("Location: acc_krs.php?id_mahasiswa=".$_GET['id_mahasiswa']);
    exit;
}

// ==========================
// ACTION TOLAK
// ==========================
if (isset($_GET['tolak'])) {

    $id = mysqli_real_escape_string($koneksi, $_GET['tolak']);

    mysqli_query($koneksi,"
        UPDATE krs
        SET status='ditolak'
        WHERE id_krs='$id'
    ");

    header("Location: acc_krs.php?id_mahasiswa=".$_GET['id_mahasiswa']);
    exit;
}

// ==========================
// BATALKAN STATUS
// ==========================
if (isset($_GET['batal'])) {

    $id = mysqli_real_escape_string($koneksi, $_GET['batal']);

    mysqli_query($koneksi,"
        UPDATE krs
        SET status='pending'
        WHERE id_krs='$id'
    ");

    header("Location: acc_krs.php?id_mahasiswa=".$_GET['id_mahasiswa']);
    exit;
}

// ==========================
// LIST MAHASISWA PA
// ==========================
$list_mahasiswa = mysqli_query($koneksi,"
    SELECT
        m.id_mahasiswa,
        m.nama,
        m.nim,

        COUNT(k.id_krs) AS total_krs,

        SUM(
            CASE
                WHEN k.status='pending'
                THEN 1
                ELSE 0
            END
        ) AS total_pending,

        SUM(
            CASE
                WHEN k.status='disetujui'
                THEN 1
                ELSE 0
            END
        ) AS total_acc,

        SUM(
            CASE
                WHEN k.status='ditolak'
                THEN 1
                ELSE 0
            END
        ) AS total_tolak

    FROM mahasiswa m

    LEFT JOIN krs k
        ON m.id_mahasiswa = k.id_mahasiswa

    WHERE m.id_dosen_pa='$id_dosen'

    GROUP BY m.id_mahasiswa

    ORDER BY m.nama ASC
");

// ==========================
// DETAIL KRS MAHASISWA
// ==========================
$detail_krs = null;
$detail_mhs = null;

if(isset($_GET['id_mahasiswa'])){

    $id_mahasiswa =
    mysqli_real_escape_string(
        $koneksi,
        $_GET['id_mahasiswa']
    );

    // DATA MAHASISWA
    $q_mhs = mysqli_query($koneksi,"
        SELECT *
        FROM mahasiswa
        WHERE id_mahasiswa='$id_mahasiswa'
        LIMIT 1
    ");

    $detail_mhs =
    mysqli_fetch_assoc($q_mhs);

    // DATA KRS MAHASISWA
    $detail_krs = mysqli_query($koneksi,"
        SELECT
            krs.*,
            mk.nama_mk,
            mk.sks,
            mk.kode_mk,
            k.nama_kelas

        FROM krs

        JOIN mata_kuliah mk
            ON krs.id_mk = mk.id_mk

        LEFT JOIN kelas k
            ON krs.id_kelas = k.id_kelas

        WHERE krs.id_mahasiswa='$id_mahasiswa'

        ORDER BY krs.id_krs DESC
    ");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>ACC KRS - Dosen</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700;800&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    background:#eef4ff;
}

/* SIDEBAR */
.sidebar{
    width:280px;
    height:100vh;
    position:fixed;
    background:linear-gradient(180deg,#0f172a,#172554);
    padding:30px 20px;
    color:white;
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
    margin-top:10px;
    font-weight:800;
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

/* TOPBAR */
.topbar{
    background:white;
    border-radius:24px;
    padding:20px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 15px 40px rgba(0,0,0,.08);
    margin-bottom:25px;
}

.topbar h3{
    font-weight:800;
}

.topbar p{
    color:#64748b;
}

/* ===================== CARD WARNA BARU (GACOR MODE 🔥) ===================== */
.stats{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:15px;
    margin-bottom:20px;
}

.stat-box{
    padding:22px;
    border-radius:22px;
    color:white;
    box-shadow:0 10px 25px rgba(0,0,0,.12);
    transition:.3s;
    position:relative;
    overflow:hidden;
}

.stat-box:hover{
    transform:translateY(-8px) scale(1.02);
}

/* ICON STYLE */
.stat-box i{
    font-size:28px;
    opacity:.9;
    margin-bottom:10px;
}

/* NUMBER */
.stat-box h4{
    font-size:28px;
    font-weight:800;
}

/* TEXT */
.stat-box small{
    opacity:.9;
    font-weight:500;
}

/* EFFECT LIGHT */
.stat-box::before{
    content:"";
    position:absolute;
    width:120px;
    height:120px;
    background:rgba(255,255,255,.15);
    border-radius:50%;
    top:-40px;
    right:-40px;
}

/* ================= WARNA GRADIENT BARU ================= */

/* TOTAL */
.c1{
    background:linear-gradient(135deg,#4f46e5,#06b6d4);
}

/* PENDING */
.c2{
    background:linear-gradient(135deg,#f97316,#facc15);
}

/* ACC */
.c3{
    background:linear-gradient(135deg,#22c55e,#16a34a);
}

/* TOLAK */
.c4{
    background:linear-gradient(135deg,#ef4444,#ec4899);
}

/* TABLE */
.card-box{
    background:white;
    padding:25px;
    border-radius:22px;
    box-shadow:0 10px 30px rgba(0,0,0,.06);
}

.table thead{
    background:#1e3a8a;
    color:white;
}

.badge-pending{
    background:#facc15;
    padding:6px 10px;
    border-radius:10px;
    font-weight:600;
}

.badge-acc{
    background:#22c55e;
    color:white;
    padding:6px 10px;
    border-radius:10px;
}

.badge-tolak{
    background:#ef4444;
    color:white;
    padding:6px 10px;
    border-radius:10px;
}

.btn-acc{
    background:#22c55e;
    color:white;
    padding:6px 10px;
    border-radius:8px;
    text-decoration:none;
}

.btn-tolak{
    background:#ef4444;
    color:white;
    padding:6px 10px;
    border-radius:8px;
    text-decoration:none;
}

.btn-batal{
    background:#64748b;
    color:white;
    padding:6px 10px;
    border-radius:8px;
    text-decoration:none;
}

</style>
</head>

<body>

<!-- ================= SIDEBAR FIX FULL ================= -->
<div class="sidebar">

    <div class="brand">
        <i class="fas fa-chalkboard-teacher"></i>
        <h3>SIAKAD</h3>
        <small>Dosen Panel</small>
    </div>

    <div class="menu">

        <a href="dashboard.php">
            <i class="fas fa-house"></i> Dashboard
        </a>

        <a href="mahasiswa_pa.php">
            <i class="fas fa-users"></i> Mahasiswa PA
        </a>

        <a href="acc_krs.php" class="active">
            <i class="fas fa-file-signature"></i> ACC KRS
        </a>

        <a href="input_nilai.php">
            <i class="fas fa-pen"></i> Input Nilai
        </a>

        <a href="mahasiswa_ajar.php">
            <i class="fas fa-user-graduate"></i> Daftar Mahasiswa
        </a>

        <a href="jadwal.php">
            <i class="fas fa-calendar-days"></i> Jadwal Mengajar
        </a>

        <a href="profil.php">
            <i class="fas fa-user"></i> Profil
        </a>

        <a href="ganti_password.php">
            <i class="fas fa-key"></i> Ganti Password
        </a>

        <a href="../auth/logout.php">
            <i class="fas fa-right-from-bracket"></i> Logout
        </a>

    </div>
</div>

<!-- CONTENT -->
<div class="content">

    <div class="topbar">
        <div>
            <h3>ACC KRS Mahasiswa</h3>
            <p>Validasi KRS mahasiswa bimbingan akademik</p>
        </div>
    </div>

    <!-- 🔥 CARD STAT PREMIUM COLORFUL -->
    <div class="stats">

        <div class="stat-box c1">
    <i class="fas fa-user-graduate"></i>

    <?php
    $total_mhs = mysqli_query($koneksi,"
        SELECT COUNT(*) as total
        FROM mahasiswa
        WHERE id_dosen_pa='$id_dosen'
    ");
    $tm = mysqli_fetch_assoc($total_mhs);
    ?>

    <h4><?= $tm['total'] ?></h4>
    <small>Total Mahasiswa PA</small>
</div>

        <div class="stat-box c2">
            <i class="fas fa-clock"></i>
            <?php
            $p = mysqli_query($koneksi,"
                SELECT COUNT(*) as t FROM krs k
                JOIN mahasiswa m ON k.id_mahasiswa=m.id_mahasiswa
                WHERE m.id_dosen_pa='$id_dosen' AND k.status='pending'
            ");
            ?>
            <h4><?= mysqli_fetch_assoc($p)['t'] ?></h4>
            <small>Pending</small>
        </div>

        <div class="stat-box c3">
            <?php
            $a = mysqli_query($koneksi,"
                SELECT COUNT(*) as t FROM krs k
                JOIN mahasiswa m ON k.id_mahasiswa=m.id_mahasiswa
                WHERE m.id_dosen_pa='$id_dosen' AND k.status='disetujui'
            ");
            ?>
            <i class="fas fa-check"></i>
            <h4><?= mysqli_fetch_assoc($a)['t'] ?></h4>
            <small>Disetujui</small>
        </div>

        <div class="stat-box c4">
            <?php
            $t = mysqli_query($koneksi,"
                SELECT COUNT(*) as t FROM krs k
                JOIN mahasiswa m ON k.id_mahasiswa=m.id_mahasiswa
                WHERE m.id_dosen_pa='$id_dosen' AND k.status='ditolak'
            ");
            ?>
            <i class="fas fa-times"></i>
            <h4><?= mysqli_fetch_assoc($t)['t'] ?></h4>
            <small>Ditolak</small>
        </div>

    </div>

    <!-- TABLE -->
    <div class="card-box">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                Mahasiswa Bimbingan Akademik
            </h4>

            <small class="text-muted">
                Pilih mahasiswa untuk melihat detail KRS
            </small>
        </div>

        <?php if(isset($_GET['id_mahasiswa'])){ ?>

            <a href="acc_krs.php"
            class="btn btn-secondary">

                <i class="fas fa-arrow-left"></i>
                Kembali

            </a>

        <?php } ?>

    </div>

    <!-- ================= LIST MAHASISWA ================= -->
    <table class="table table-hover align-middle">

        <thead>
            <tr>
                <th>No</th>
                <th>Mahasiswa</th>
                <th>NIM</th>
                <th>Total MK</th>
                <th>Pending</th>
                <th>Disetujui</th>
                <th>Ditolak</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>

        <?php
        $no = 1;

        while($mhs =
        mysqli_fetch_assoc($list_mahasiswa)){
        ?>

            <tr>

                <td><?= $no++ ?></td>

                <td>
                    <strong>
                        <?= $mhs['nama'] ?>
                    </strong>
                </td>

                <td>
                    <?= $mhs['nim'] ?>
                </td>

                <td>
                    <?= $mhs['total_krs'] ?? 0 ?>
                </td>

                <td>
                    <span class="badge bg-warning text-dark">
                        <?= $mhs['total_pending'] ?? 0 ?>
                    </span>
                </td>

                <td>
                    <span class="badge bg-success">
                        <?= $mhs['total_acc'] ?? 0 ?>
                    </span>
                </td>

                <td>
                    <span class="badge bg-danger">
                        <?= $mhs['total_tolak'] ?? 0 ?>
                    </span>
                </td>

                <td>

                    <?php
                    $pending =
                    $mhs['total_pending'];

                    $acc =
                    $mhs['total_acc'];

                    $tolak =
                    $mhs['total_tolak'];
                    ?>

                    <?php if($pending > 0){ ?>

                        <span class="badge bg-warning text-dark">
                            Menunggu Validasi
                        </span>

                    <?php } elseif($acc > 0 && $tolak == 0){ ?>

                        <span class="badge bg-success">
                            Sudah ACC
                        </span>

                    <?php } elseif($tolak > 0){ ?>

                        <span class="badge bg-danger">
                            Ada Penolakan
                        </span>

                    <?php } else { ?>

                        <span class="badge bg-secondary">
                            Belum Ambil KRS
                        </span>

                    <?php } ?>

                </td>

                <td>

                    <a href="detail_krs.php?id_mahasiswa=<?= $mhs['id_mahasiswa'] ?>"
class="btn btn-primary btn-sm">

                        <i class="fas fa-eye"></i>
                        Lihat KRS

                    </a>

                </td>

            </tr>

        <?php } ?>

        </tbody>

        </table>

    </div>

</div>

</body>
</html>