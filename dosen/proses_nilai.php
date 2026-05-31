<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'dosen') {
    header("Location: ../auth/login.php");
    exit;
}

/*
==========================
VALIDASI INPUT
==========================
*/
if (
    empty($_POST['id_mahasiswa']) ||
    empty($_POST['id_mk'])
) {
    die("ERROR: Data mahasiswa atau mata kuliah tidak dikirim dari form.");
}

$id_mahasiswa = mysqli_real_escape_string($koneksi, $_POST['id_mahasiswa']);
$id_mk        = mysqli_real_escape_string($koneksi, $_POST['id_mk']);

/*
==========================
AMBIL & VALIDASI NILAI
==========================
*/
$tugas = isset($_POST['tugas']) ? floatval($_POST['tugas']) : 0;
$uts   = isset($_POST['uts']) ? floatval($_POST['uts']) : 0;
$uas   = isset($_POST['uas']) ? floatval($_POST['uas']) : 0;

/*
HINDARI NILAI KOSONG / NEGATIF / >100
*/
if ($tugas < 0 || $tugas > 100) $tugas = 0;
if ($uts < 0 || $uts > 100) $uts = 0;
if ($uas < 0 || $uas > 100) $uas = 0;

/*
==========================
VALIDASI FOREIGN KEY (LEBIH RINGAN)
==========================
*/
$cek_mhs = mysqli_query($koneksi, "SELECT 1 FROM mahasiswa WHERE id_mahasiswa='$id_mahasiswa' LIMIT 1");
if (mysqli_num_rows($cek_mhs) == 0) {
    die("ERROR: Mahasiswa tidak ditemukan.");
}

$cek_mk = mysqli_query($koneksi, "SELECT 1 FROM mata_kuliah WHERE id_mk='$id_mk' LIMIT 1");
if (mysqli_num_rows($cek_mk) == 0) {
    die("ERROR: Mata kuliah tidak ditemukan.");
}

/*
==========================
HITUNG NILAI AKHIR
==========================
*/
$nilai_akhir = ($tugas * 0.3) + ($uts * 0.3) + ($uas * 0.4);

/*
==========================
GRADE
==========================
*/
if ($nilai_akhir >= 85) {
    $grade = "A";
} elseif ($nilai_akhir >= 75) {
    $grade = "B";
} elseif ($nilai_akhir >= 65) {
    $grade = "C";
} elseif ($nilai_akhir >= 50) {
    $grade = "D";
} else {
    $grade = "E";
}

/*
==========================
CEK DATA EXIST
==========================
*/
$cek = mysqli_query($koneksi, "
    SELECT id_nilai 
    FROM nilai 
    WHERE id_mahasiswa='$id_mahasiswa' 
    AND id_mk='$id_mk'
    LIMIT 1
");

/*
==========================
INSERT / UPDATE
==========================
*/
if (mysqli_num_rows($cek) > 0) {

    $update = mysqli_query($koneksi, "
        UPDATE nilai SET
            tugas='$tugas',
            uts='$uts',
            uas='$uas',
            nilai_akhir='$nilai_akhir',
            grade='$grade'
        WHERE id_mahasiswa='$id_mahasiswa'
        AND id_mk='$id_mk'
    ");

    if (!$update) {
        die("UPDATE ERROR: " . mysqli_error($koneksi));
    }

} else {

    $insert = mysqli_query($koneksi, "
        INSERT INTO nilai (
            id_mahasiswa,
            id_mk,
            tugas,
            uts,
            uas,
            nilai_akhir,
            grade
        ) VALUES (
            '$id_mahasiswa',
            '$id_mk',
            '$tugas',
            '$uts',
            '$uas',
            '$nilai_akhir',
            '$grade'
        )
    ");

    if (!$insert) {
        die("INSERT ERROR: " . mysqli_error($koneksi));
    }
}

/*
==========================
SUCCESS
==========================
*/
echo "<script>
alert('Nilai berhasil disimpan');
window.location='input_nilai.php';
</script>";
?>