<?php
session_start();
include '../config/koneksi.php';

// ==========================
// PROTEKSI MAHASISWA
// ==========================
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: ../auth/login.php");
    exit;
}

$id_mahasiswa = $_SESSION['id_ref'];

// ==========================
// HAPUS KRS
// ==========================
if(isset($_GET['hapus'])){

    $id_krs = mysqli_real_escape_string($koneksi, $_GET['hapus']);

    mysqli_query($koneksi,"
        DELETE FROM krs
        WHERE id_krs='$id_krs'
        AND id_mahasiswa='$id_mahasiswa'
    ");

    echo "<script>
        alert('Mata kuliah berhasil dihapus!');
        window.location='krs.php';
    </script>";
    exit;
}

// ==========================
// SIMPAN KRS (FIX FINAL STABIL)
// ==========================
if(isset($_POST['ambil_krs'])){

    if(isset($_POST['mk']) && count($_POST['mk']) > 0){

        foreach($_POST['mk'] as $id_mk){

            $id_mk = mysqli_real_escape_string($koneksi, $id_mk);

            // ambil kelas dari mata kuliah
            $q = mysqli_query($koneksi,"
                SELECT id_kelas 
                FROM mata_kuliah 
                WHERE id_mk='$id_mk'
                LIMIT 1
            ");

            $d = mysqli_fetch_assoc($q);
            $id_kelas = $d['id_kelas'] ?? 0;

            // cek duplikat
            $cek = mysqli_query($koneksi,"
                SELECT id_krs 
                FROM krs
                WHERE id_mahasiswa='$id_mahasiswa'
                AND id_mk='$id_mk'
            ");

            if(mysqli_num_rows($cek) == 0){

                mysqli_query($koneksi,"
                    INSERT INTO krs (
                        id_mahasiswa,
                        id_mk,
                        id_kelas,
                        semester_akademik,
                        status
                    ) VALUES (
                        '$id_mahasiswa',
                        '$id_mk',
                        " . ($id_kelas ? "'$id_kelas'" : "NULL") . ",
                        'Genap 2025/2026',
                        'Pending'
                    )
                ");
            }
        }

        echo "<script>
            alert('KRS berhasil disimpan!');
            window.location='krs.php';
        </script>";
        exit;
    }
}

// ==========================
// DATA MAHASISWA
// ==========================
$q_mhs = mysqli_query($koneksi,"
    SELECT m.*, d.nama AS nama_pa
    FROM mahasiswa m
    LEFT JOIN dosen d ON m.id_dosen_pa = d.id_dosen
    WHERE m.id_mahasiswa='$id_mahasiswa'
");
$mhs = mysqli_fetch_assoc($q_mhs);

// ==========================
// MATA KULIAH
// ==========================
$matkul = mysqli_query($koneksi,"
    SELECT 
        mk.*,
        k.nama_kelas,
        COALESCE(d.nama, mk.nama_dosen, '-') AS nama_dosen

    FROM mata_kuliah mk

    LEFT JOIN kelas k
        ON mk.id_kelas = k.id_kelas

    LEFT JOIN dosen d
        ON mk.id_dosen = d.id_dosen

    ORDER BY mk.semester ASC
");

// ==========================
// DATA KRS
// ==========================
$data_krs = mysqli_query($koneksi,"
    SELECT
        krs.*,
        mk.kode_mk,
        mk.nama_mk,
        mk.sks,
        k.nama_kelas,
        COALESCE(d.nama, mk.nama_dosen, '-') AS nama_dosen

    FROM krs

    JOIN mata_kuliah mk
        ON krs.id_mk = mk.id_mk

    LEFT JOIN kelas k
        ON krs.id_kelas = k.id_kelas

    LEFT JOIN dosen d
        ON mk.id_dosen = d.id_dosen

    WHERE krs.id_mahasiswa='$id_mahasiswa'
");

// ==========================
// ARRAY MATKUL TERPILIH
// ==========================
$mk_terpilih = [];

$q_selected = mysqli_query($koneksi,"
    SELECT id_mk, status
    FROM krs
    WHERE id_mahasiswa='$id_mahasiswa'
");

while($s = mysqli_fetch_assoc($q_selected)){
    $mk_terpilih[$s['id_mk']] = $s['status'];
}

// ==========================
// TOTAL SKS
// ==========================
$total_sks = mysqli_fetch_assoc(mysqli_query($koneksi,"
    SELECT COALESCE(SUM(mk.sks),0) AS total
    FROM krs k
    JOIN mata_kuliah mk ON k.id_mk = mk.id_mk
    WHERE k.id_mahasiswa='$id_mahasiswa'
"));
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>KRS Mahasiswa</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
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

/* CARD */
.card-custom{
    background:white;
    border-radius:20px;
    padding:25px;
    box-shadow:0 10px 30px rgba(0,0,0,.06);
    margin-bottom:25px;
}

.table th{
    background:#eff6ff;
    color:#1e3a8a;
}

.badge{
    border-radius:10px;
    padding:8px 12px;
}

.btn-primary{
    border:none;
    border-radius:12px;
    padding:12px 20px;
}

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

        <a href="dashboard.php">
            <i class="fas fa-house"></i> Dashboard
        </a>

        <a href="krs.php" class="active">
            <i class="fas fa-book-open"></i> KRS
        </a>

        <a href="khs.php">
            <i class="fas fa-chart-line"></i> KHS / Nilai
        </a>

        <a href="jadwal.php">
            <i class="fas fa-calendar-days"></i> Jadwal
        </a>

        <a href="profil.php">
            <i class="fas fa-user"></i> Profil
        </a>

        <a href="ganti_password.php">
            <i class="fas fa-key"></i> Ganti Password
        </a>

        <a href="logout.php">
            <i class="fas fa-right-from-bracket"></i> Logout
        </a>

    </div>

</div>

<div class="content">

    <div class="topbar">
        <h3>Kartu Rencana Studi (KRS)</h3>
        <p>Halo, <?= $mhs['nama']; ?></p>
<p>Dosen Pembimbing Akademik : <?= $mhs['nama_pa'] ?? 'Belum ada PA'; ?></p>
    </div>

    <div class="card-custom">

        <h5 class="mb-4">
            Pilih Mata Kuliah
        </h5>

        <form method="POST">

            <table class="table table-hover">

                <thead>
                    <tr>
                        <th>Pilih</th>
                        <th>Kode</th>
                        <th>Mata Kuliah</th>
                        <th>Kelas</th>
                        <th>SKS</th>
                        <th>Semester</th>
                        <th>Dosen</th>
                    </tr>
                </thead>
                <tbody>

<?php while($mk = mysqli_fetch_assoc($matkul)) { ?>

<tr>
    <td>
        <?php
$checked = isset($mk_terpilih[$mk['id_mk']]);
$status_mk = $mk_terpilih[$mk['id_mk']] ?? '';
?>

<input
type="checkbox"
name="mk[]"
value="<?= $mk['id_mk']; ?>"

<?= $checked ? 'checked' : '' ?>

<?= ($status_mk == 'disetujui')
    ? 'disabled'
    : '' ?>
>
    </td>

    <td><?= $mk['kode_mk']; ?></td>

    <td><?= $mk['nama_mk']; ?></td>

    <td>
        <?= !empty($mk['nama_kelas']) 
            ? $mk['nama_kelas'] 
            : 'Belum ada kelas'; ?>
    </td>

    <td><?= $mk['sks']; ?></td>

    <td><?= $mk['semester']; ?></td>

    <td><?= $mk['nama_dosen']; ?></td>
</tr>

<?php } ?>

</tbody>

            </table>

            <button type="submit"
            name="ambil_krs"
            class="btn btn-primary">

                <i class="fas fa-save"></i>
                Simpan KRS

            </button>

        </form>

    </div>

    <div class="card-custom">

        <div class="d-flex justify-content-between mb-3">

            <h5>KRS Saya</h5>

            <h5>
                Total SKS :
                <span class="text-primary">
                    <?= $total_sks['total'] ?? 0 ?>
                </span>
            </h5>

        </div>

        <table class="table table-hover">

    <thead>
        <tr>
            <th>No</th>
            <th>Kode MK</th>
            <th>Mata Kuliah</th>
            <th>Kelas</th>
            <th>SKS</th>
            <th>Dosen</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>

    <tbody>

<?php
$no = 1;
while($row = mysqli_fetch_assoc($data_krs)) {
?>

<tr>
    <td><?= $no++; ?></td>

    <td><?= $row['kode_mk']; ?></td>

    <td><?= $row['nama_mk']; ?></td>

    <td>
        <?= !empty($row['nama_kelas']) 
            ? $row['nama_kelas'] 
            : 'Belum ada kelas'; ?>
    </td>

    <td><?= $row['sks']; ?></td>

    <td><?= $row['nama_dosen']; ?></td>

    <!-- STATUS -->
    <td>
        <?php if(strtolower($row['status']) == "pending"){ ?>

            <span class="badge bg-warning text-dark">
                Pending
            </span>

        <?php } elseif(strtolower($row['status']) == "disetujui"){ ?>

            <span class="badge bg-success">
                Disetujui
            </span>

        <?php } elseif(strtolower($row['status']) == "ditolak"){ ?>

            <span class="badge bg-danger">
                Ditolak
            </span>

        <?php } ?>
    </td>

    <!-- AKSI -->
    <td>

        <?php if(
            strtolower($row['status']) == "pending" ||
            strtolower($row['status']) == "ditolak"
        ){ ?>

            <a href="krs.php?hapus=<?= $row['id_krs']; ?>"
            class="btn btn-danger btn-sm"
            onclick="return confirm('Yakin ingin menghapus mata kuliah ini?')">

                <i class="fas fa-trash"></i>
                Hapus

            </a>

        <?php } elseif(strtolower($row['status']) == "disetujui"){ ?>

            <span class="text-success fw-bold">
                Sudah ACC
            </span>

        <?php } ?>

    </td>

</tr>

<?php } ?>

</tbody>

</table>
    