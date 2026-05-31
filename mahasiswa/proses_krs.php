<?php
session_start();
include '../config/koneksi.php';

// ==========================
// PROTEKSI MAHASISWA
// ==========================
if (
    !isset($_SESSION['role']) ||
    $_SESSION['role'] != 'mahasiswa'
) {
    header("Location: ../auth/login.php");
    exit;
}

$id_mahasiswa = $_SESSION['id_ref'];

// ==========================
// VALIDASI SUBMIT
// ==========================
if (!isset($_POST['ambil_krs'])) {
    header("Location: krs.php");
    exit;
}

// ==========================
// VALIDASI PILIH MK
// ==========================
if (
    !isset($_POST['mk']) ||
    empty($_POST['mk'])
) {

    echo "
    <script>
        alert('Pilih minimal 1 mata kuliah!');
        window.location='krs.php';
    </script>
    ";

    exit;
}

// ==========================
// LOOP SIMPAN KRS
// ==========================
foreach ($_POST['mk'] as $id_mk) {

    // sanitasi input
    $id_mk = mysqli_real_escape_string(
        $koneksi,
        $id_mk
    );

    // ==========================
    // AMBIL DATA MATA KULIAH
    // ==========================
    $q_mk = mysqli_query($koneksi, "
        SELECT
            id_kelas
        FROM mata_kuliah
        WHERE id_mk='$id_mk'
        LIMIT 1
    ");

    // jika mk tidak ditemukan
    if (mysqli_num_rows($q_mk) == 0) {
        continue;
    }

    $data_mk = mysqli_fetch_assoc($q_mk);

    $id_kelas =
        $data_mk['id_kelas']
        ?? NULL;

    // ==========================
    // CEK DUPLIKAT KRS
    // ==========================
    $cek = mysqli_query($koneksi, "
        SELECT *
        FROM krs
        WHERE id_mahasiswa='$id_mahasiswa'
        AND id_mk='$id_mk'
    ");

    // jika belum pernah ambil
    if (mysqli_num_rows($cek) == 0) {

        // ==========================
        // INSERT KRS
        // STATUS HARUS LOWERCASE
        // ==========================
        mysqli_query($koneksi, "
            INSERT INTO krs (
                id_mahasiswa,
                id_mk,
                id_kelas,
                semester_akademik,
                status
            )
            VALUES (
                '$id_mahasiswa',
                '$id_mk',
                " . ($id_kelas ? "'$id_kelas'" : "NULL") . ",
                'Genap 2025/2026',
                'pending'
            )
        ");
    }
}

// ==========================
// REDIRECT SUCCESS
// ==========================
echo "
<script>
    alert('KRS berhasil disimpan!');
    window.location='krs.php';
</script>
";

exit;
?>