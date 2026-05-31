<?php
session_start();
include '../config/koneksi.php';

// ==========================
// PROTEKSI ADMIN
// ==========================
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// ==========================
// AMBIL DATA DOSEN
// ==========================
$dosen = mysqli_query($koneksi, "
    SELECT id_dosen, nama, nip
    FROM dosen
    ORDER BY nama ASC
");

// ==========================
// KELAS (TAMBAHAN BARU)
// ==========================
$kelas = mysqli_query($koneksi, "
    SELECT id_kelas, nama_kelas
    FROM kelas
    ORDER BY nama_kelas ASC
");

// ==========================
// PROSES TAMBAH
// ==========================
$notif = '';
$notif_type = '';

if (isset($_POST['simpan'])) {

    $kode_mk  = trim(mysqli_real_escape_string($koneksi, $_POST['kode_mk']));
    $nama_mk  = trim(mysqli_real_escape_string($koneksi, $_POST['nama_mk']));
    $sks      = trim(mysqli_real_escape_string($koneksi, $_POST['sks']));
    $semester = trim(mysqli_real_escape_string($koneksi, $_POST['semester']));
    $id_dosen = mysqli_real_escape_string($koneksi, $_POST['id_dosen']);
    $id_kelas = mysqli_real_escape_string($koneksi, $_POST['id_kelas']);

    if (
        $kode_mk == '' ||
        $nama_mk == '' ||
        $sks == '' ||
        $semester == '' ||
        $id_dosen == '' ||
        $id_kelas == ''
    ) {
        $notif = "Semua field wajib diisi!";
        $notif_type = "danger";
    } else {

        // ==========================
        // VALIDASI KOSONG (DUPLIKAT BLOK ASLI)
        // ==========================
        if (
            $kode_mk == '' ||
            $nama_mk == '' ||
            $sks == '' ||
            $semester == '' ||
            $id_dosen == ''
        ) {

            $notif = "Semua field wajib diisi!";
            $notif_type = "danger";

        } else {

            // ==========================
            // AMBIL NAMA DOSEN
            // ==========================
            $queryDosen = mysqli_query($koneksi, "
                SELECT nama
                FROM dosen
                WHERE id_dosen = '$id_dosen'
            ");

            if (mysqli_num_rows($queryDosen) > 0) {
                $dataDosen = mysqli_fetch_assoc($queryDosen);
                $nama_dosen = $dataDosen['nama'];
            } else {
                $nama_dosen = '';
            }

            // ==========================
            // CEK DUPLIKAT KODE MK
            // ==========================
            $cek = mysqli_query($koneksi, "
                SELECT *
                FROM mata_kuliah
                WHERE kode_mk = '$kode_mk'
            ");

            if (mysqli_num_rows($cek) > 0) {

                $notif = "Kode mata kuliah sudah digunakan!";
                $notif_type = "warning";

            } else {

                // ==========================
                // INSERT DATA
                // ==========================
                // VALIDASI KELAS
if ($id_kelas == '' || $id_dosen == '') {
    die("ERROR: kelas atau dosen tidak terkirim");
}

$insert = mysqli_query($koneksi, "
    INSERT INTO mata_kuliah (
        kode_mk,
        nama_mk,
        sks,
        semester,
        id_dosen,
        nama_dosen,
        id_kelas,
        created_at
    )
    VALUES (
        '$kode_mk',
        '$nama_mk',
        '$sks',
        '$semester',
        '$id_dosen',
        '$nama_dosen',
        '$id_kelas',
        NOW()
    )
");

if (!$insert) {
    die("ERROR INSERT: " . mysqli_error($koneksi));
}

                $notif = "Mata kuliah berhasil ditambahkan!";
                $notif_type = "success";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Tambah Mata Kuliah - SIAKAD</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
/* ===== STYLE TETAP SAMA (TIDAK DIUBAH) ===== */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

body{
    min-height:100vh;
    overflow-x:hidden;
    background:linear-gradient(135deg,#0f172a,#1e293b,#334155);
    position:relative;
}

body::before,
body::after{
    content:'';
    position:absolute;
    border-radius:50%;
    filter:blur(80px);
    animation: float 8s infinite ease-in-out;
}

body::before{
    width:300px;
    height:300px;
    background:#2563eb;
    top:-100px;
    left:-100px;
}

body::after{
    width:350px;
    height:350px;
    background:#38bdf8;
    bottom:-120px;
    right:-120px;
}

@keyframes float{
    0%{transform:translateY(0px);}
    50%{transform:translateY(20px);}
    100%{transform:translateY(0px);}
}

.wrapper{
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:40px 20px;
    position:relative;
    z-index:2;
}

.form-card{
    width:100%;
    max-width:900px;
    border-radius:30px;
    overflow:hidden;
    background:rgba(255,255,255,0.96);
    backdrop-filter:blur(20px);
    box-shadow:0 25px 50px rgba(0,0,0,.25);
}

.header-card{
    background:linear-gradient(135deg,#2563eb,#0ea5e9);
    padding:35px;
    text-align:center;
    color:white;
}

.icon-box{
    width:75px;
    height:75px;
    margin:auto;
    margin-bottom:15px;
    border-radius:22px;
    background:rgba(255,255,255,.15);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:30px;
}

.header-card h2{font-weight:800;margin-bottom:8px;}
.header-card p{margin:0;opacity:.9;}

.form-body{padding:40px;}

.form-label{
    font-weight:700;
    color:#1e293b;
    margin-bottom:8px;
}

.form-control,
.form-select{
    height:55px;
    border-radius:16px;
    border:1.8px solid #dbeafe;
    background:#f8fafc;
}

.btn-save{
    border:none;
    border-radius:16px;
    padding:14px 30px;
    color:white;
    font-weight:700;
    background:linear-gradient(135deg,#2563eb,#38bdf8);
}

.btn-back{
    text-decoration:none;
    padding:14px 30px;
    border-radius:16px;
    background:#e2e8f0;
    color:#1e293b;
    font-weight:700;
}

.alert{
    border-radius:18px;
    font-weight:600;
}
</style>

</head>

<body>

<div class="wrapper">
<div class="form-card">

    <div class="header-card">
        <div class="icon-box">
            <i class="fas fa-book-open"></i>
        </div>
        <h2>Tambah Mata Kuliah</h2>
        <p>Sistem Informasi Akademik Universitas</p>
    </div>

    <div class="form-body">

        <?php if($notif != '') : ?>
            <div class="alert alert-<?= $notif_type ?>">
                <?= $notif ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="row">

                <div class="col-md-6 mb-4">
                    <label class="form-label">Kode Mata Kuliah</label>
                    <input type="text" name="kode_mk" class="form-control" required>
                </div>

                <div class="col-md-6 mb-4">
                    <label class="form-label">Nama Mata Kuliah</label>
                    <input type="text" name="nama_mk" class="form-control" required>
                </div>

                <div class="col-md-4 mb-4">
                    <label class="form-label">Jumlah SKS</label>
                    <input type="number" name="sks" class="form-control" min="1" max="6" required>
                </div>

                <div class="col-md-4 mb-4">
                    <label class="form-label">Semester</label>
                    <select name="semester" class="form-select" required>
                        <option value="">-- Pilih Semester --</option>
                        <?php for($i=1; $i<=8; $i++) : ?>
                            <option value="<?= $i ?>">Semester <?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="col-md-4 mb-4">
                    <label class="form-label">Dosen Pengampu</label>
                    <select name="id_dosen" class="form-select" required>
                        <option value="">-- Pilih Dosen --</option>
                        <?php while($d = mysqli_fetch_assoc($dosen)) : ?>
                            <option value="<?= $d['id_dosen'] ?>">
                                <?= $d['nama'] ?> - <?= $d['nip'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-4 mb-4">
                    <label class="form-label">Kelas</label>
                    <select name="id_kelas" class="form-select" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php while($k = mysqli_fetch_assoc($kelas)) : ?>
                            <option value="<?= $k['id_kelas'] ?>">
                                <?= $k['nama_kelas'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

            </div>

            <div class="d-flex justify-content-end gap-3 mt-3">
                <a href="mata_kuliah.php" class="btn-back">Kembali</a>
                <button type="submit" name="simpan" class="btn-save">
                    <i class="fas fa-save me-2"></i> Simpan Data
                </button>
            </div>

        </form>

    </div>

</div>
</div>

</body>
</html>