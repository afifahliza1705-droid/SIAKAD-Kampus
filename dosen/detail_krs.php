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
// VALIDASI MAHASISWA
// ==========================
if(!isset($_GET['id_mahasiswa'])){
    header("Location: acc_krs.php");
    exit;
}

$id_mahasiswa = mysqli_real_escape_string(
    $koneksi,
    $_GET['id_mahasiswa']
);

// ==========================
// DATA MAHASISWA
// ==========================
$q_mhs = mysqli_query($koneksi,"

    SELECT *
    FROM mahasiswa
    WHERE id_mahasiswa='$id_mahasiswa'
    AND id_dosen_pa='$id_dosen'
    LIMIT 1

");

$mahasiswa = mysqli_fetch_assoc($q_mhs);

if(!$mahasiswa){
    header("Location: acc_krs.php");
    exit;
}

// ==========================
// ACTION ACC
// ==========================
if(isset($_GET['acc'])){

    $id_krs = mysqli_real_escape_string(
        $koneksi,
        $_GET['acc']
    );

    mysqli_query($koneksi,"
        UPDATE krs
        SET status='disetujui'
        WHERE id_krs='$id_krs'
    ");

    header("Location: detail_krs.php?id_mahasiswa=".$id_mahasiswa);
    exit;
}

// ==========================
// ACTION TOLAK
// ==========================
if(isset($_GET['tolak'])){

    $id_krs = mysqli_real_escape_string(
        $koneksi,
        $_GET['tolak']
    );

    mysqli_query($koneksi,"
        UPDATE krs
        SET status='ditolak'
        WHERE id_krs='$id_krs'
    ");

    header("Location: detail_krs.php?id_mahasiswa=".$id_mahasiswa);
    exit;
}

// ==========================
// ACTION BATAL
// ==========================
if(isset($_GET['batal'])){

    $id_krs = mysqli_real_escape_string(
        $koneksi,
        $_GET['batal']
    );

    mysqli_query($koneksi,"
        UPDATE krs
        SET status='pending'
        WHERE id_krs='$id_krs'
    ");

    header("Location: detail_krs.php?id_mahasiswa=".$id_mahasiswa);
    exit;
}

// ==========================
// DATA KRS
// ==========================
$query = mysqli_query($koneksi,"

    SELECT
        krs.*,
        mk.kode_mk,
        mk.nama_mk,
        mk.sks,
        mk.semester,

        COALESCE(k.nama_kelas,'-')
        AS nama_kelas,

        COALESCE(d.nama,mk.nama_dosen,'-')
        AS nama_dosen

    FROM krs

    JOIN mata_kuliah mk
        ON krs.id_mk = mk.id_mk

    LEFT JOIN kelas k
        ON krs.id_kelas = k.id_kelas

    LEFT JOIN dosen d
        ON mk.id_dosen = d.id_dosen

    WHERE krs.id_mahasiswa='$id_mahasiswa'

    ORDER BY mk.semester ASC

");

// ==========================
// TOTAL SKS
// ==========================
$total_sks = mysqli_fetch_assoc(mysqli_query($koneksi,"
    SELECT COALESCE(
        SUM(mk.sks),0
    ) AS total

    FROM krs k
    JOIN mata_kuliah mk
    ON k.id_mk = mk.id_mk

    WHERE k.id_mahasiswa='$id_mahasiswa'
"));

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Detail KRS Mahasiswa</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
rel="stylesheet">

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Plus Jakarta Sans',sans-serif;
}

body{
    background:
    linear-gradient(
        135deg,
        #edf4ff 0%,
        #f8fbff 100%
    );
    color:#1e293b;
}

/* =========================
CONTENT
========================= */

.content{
    max-width:1280px;
    margin:auto;
    padding:30px 22px;
}

/* =========================
MAIN CARD
========================= */

.main-card{

    background:
    rgba(255,255,255,.88);

    backdrop-filter:
    blur(18px);

    border:
    1px solid rgba(255,255,255,.6);

    border-radius:34px;

    padding:30px;

    box-shadow:
    0 20px 45px
    rgba(59,130,246,.08);
}

/* =========================
HEADER
========================= */

.header-box{

    display:flex;
    justify-content:space-between;
    align-items:center;

    margin-bottom:28px;
}

.user-box{

    display:flex;
    align-items:center;
    gap:18px;
}

.avatar{

    width:75px;
    height:75px;

    border-radius:24px;

    background:
    linear-gradient(
        135deg,
        #2563eb,
        #60a5fa
    );

    display:flex;
    justify-content:center;
    align-items:center;

    color:white;
    font-size:28px;

    box-shadow:
    0 10px 25px
    rgba(37,99,235,.25);
}

.user-info h2{

    font-size:25px;
    font-weight:800;

    color:#0f172a;

    margin-bottom:4px;
}

.user-info p{

    margin:0;
    color:#64748b;
    font-size:14px;
}

/* MINI INFO */

.info-mini{

    display:flex;
    gap:10px;
    margin-top:12px;
}

.soft-box{

    background:#f8fbff;

    border:
    1px solid #dbeafe;

    border-radius:18px;

    padding:12px 18px;

    font-size:14px;
    color:#475569;
}

.soft-box strong{
    color:#2563eb;
}

/* =========================
BUTTON BACK
========================= */

.btn-back{

    background:
    linear-gradient(
        135deg,
        #2563eb,
        #3b82f6
    );

    color:white;
    text-decoration:none;

    padding:13px 22px;

    border-radius:18px;

    font-size:14px;
    font-weight:700;

    transition:.25s;

    box-shadow:
    0 8px 20px
    rgba(37,99,235,.2);
}

.btn-back:hover{

    transform:
    translateY(-2px);

    color:white;
}

/* =========================
TABLE HEADER
========================= */

.table-header{

    display:flex;
    justify-content:space-between;
    align-items:center;

    margin-bottom:22px;
}

.table-header h4{

    font-size:22px;
    font-weight:800;

    color:#0f172a;
}

.table-header span{

    font-size:14px;
    color:#64748b;
}

/* =========================
TABLE WRAPPER
========================= */

.table-wrapper{

    overflow:hidden;

    border-radius:28px;

    border:
    1px solid #dbeafe;

    background:white;
}

/* =========================
TABLE
========================= */

.table{
    margin:0;
}

.table thead th{

    background:
    linear-gradient(
        135deg,
        #2563eb,
        #3b82f6
    );

    color:white;

    border:none;

    text-align:center;

    padding:18px 14px;

    font-size:14px;
    font-weight:700;
}

.table tbody td{

    text-align:center;

    vertical-align:middle;

    padding:18px 12px;

    border-bottom:
    1px solid #eff6ff;

    font-size:14px;

    color:#334155;
}

.table tbody tr{

    transition:.25s;
}

.table tbody tr:hover{

    background:#f8fbff;
}

/* =========================
STATUS
========================= */

.badge-pending{

    background:#fff7d6;
    color:#b45309;

    padding:9px 18px;

    border-radius:999px;

    font-size:13px;
    font-weight:700;
}

.badge-acc{

    background:#dcfce7;
    color:#166534;

    padding:9px 18px;

    border-radius:999px;

    font-size:13px;
    font-weight:700;
}

.badge-tolak{

    background:#fee2e2;
    color:#b91c1c;

    padding:9px 18px;

    border-radius:999px;

    font-size:13px;
    font-weight:700;
}

/* =========================
BUTTON ACTION
========================= */

.btn-action{

    width:42px;
    height:42px;

    border:none;

    border-radius:14px;

    display:inline-flex;
    justify-content:center;
    align-items:center;

    text-decoration:none;

    transition:.25s;

    margin:0 2px;
}

.btn-action:hover{

    transform:
    translateY(-2px)
    scale(1.04);
}

/* BUTTON */

.btn-acc{
    background:#dcfce7;
    color:#16a34a;
}

.btn-tolak{
    background:#fee2e2;
    color:#dc2626;
}

.btn-batal{
    background:#dbeafe;
    color:#2563eb;
}

/* =========================
RESPONSIVE
========================= */

@media(max-width:768px){

    .header-box{
        flex-direction:column;
        align-items:flex-start;
        gap:18px;
    }

    .table{
        min-width:950px;
    }

    .content{
        padding:15px;
    }
}

</style>

</head>

<body>

<div class="content">

    <div class="main-card">

        <!-- =========================
        HEADER
        ========================= -->

        <div class="header-box">

            <div class="user-box">

                <div class="avatar">
                    <i class="fas fa-user-graduate"></i>
                </div>

                <div class="user-info">

                    <h2>
                        <?= $mahasiswa['nama']; ?>
                    </h2>

                    <p>
                        NIM :
                        <?= $mahasiswa['nim']; ?>
                    </p>

                    <div class="info-mini">

                        <div class="soft-box">

                            Total SKS

                            <strong>
                                <?= $total_sks['total']; ?>
                            </strong>

                        </div>

                        <div class="soft-box">

                            Status

                            <strong>
                                Validasi KRS
                            </strong>

                        </div>

                    </div>

                </div>

            </div>

            <a href="acc_krs.php"
            class="btn-back">

                <i class="fas fa-arrow-left"></i>
                Kembali

            </a>

        </div>


        <!-- =========================
        TITLE
        ========================= -->

        <div class="table-header">

            <div>

                <h4>
                    Detail KRS Mahasiswa
                </h4>

                <span>
                    Daftar mata kuliah yang dipilih mahasiswa
                </span>

            </div>

        </div>


        <!-- =========================
        TABLE
        ========================= -->

        <div class="table-wrapper">

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>

                        <tr>

                            <th>No</th>
                            <th>Kode MK</th>
                            <th>Mata Kuliah</th>
                            <th>Kelas</th>
                            <th>SKS</th>
                            <th>Semester</th>
                            <th>Dosen</th>
                            <th>Status</th>
                            <th>Aksi</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php
                    $no = 1;

                    while(
                    $row =
                    mysqli_fetch_assoc($query)
                    ){
                    ?>

                    <tr>

                        <td>
                            <?= $no++ ?>
                        </td>

                        <td>

                            <strong>
                                <?= $row['kode_mk'] ?>
                            </strong>

                        </td>

                        <td>

                            <?= $row['nama_mk'] ?>

                        </td>

                        <td>

                            <?= $row['nama_kelas'] ?>

                        </td>

                        <td>

                            <?= $row['sks'] ?>

                        </td>

                        <td>

                            Semester
                            <?= $row['semester'] ?>

                        </td>

                        <td>

                            <?= $row['nama_dosen'] ?>

                        </td>

                        <td>

                            <?php
                            if(
                            $row['status']
                            == 'pending'
                            ){
                            ?>

                            <span
                            class="badge-pending">

                                Pending

                            </span>

                            <?php
                            }
                            elseif(
                            $row['status']
                            == 'disetujui'
                            ){
                            ?>

                            <span
                            class="badge-acc">

                                Disetujui

                            </span>

                            <?php
                            }
                            else{
                            ?>

                            <span
                            class="badge-tolak">

                                Ditolak

                            </span>

                            <?php } ?>

                        </td>

                        <td>

                            <?php
                            if(
                            $row['status']
                            == 'pending'
                            ){
                            ?>

                            <!-- ACC -->

                            <a href="
                            ?id_mahasiswa=
                            <?= $id_mahasiswa ?>
                            &acc=
                            <?= $row['id_krs'] ?>
                            "

                            class="
                            btn-action
                            btn-acc
                            "

                            onclick="
                            return confirm(
                            'Setujui KRS ini?'
                            )
                            ">

                                <i class="
                                fas fa-check
                                "></i>

                            </a>

                            <!-- TOLAK -->

                            <a href="
                            ?id_mahasiswa=
                            <?= $id_mahasiswa ?>
                            &tolak=
                            <?= $row['id_krs'] ?>
                            "

                            class="
                            btn-action
                            btn-tolak
                            "

                            onclick="
                            return confirm(
                            'Tolak KRS ini?'
                            )
                            ">

                                <i class="
                                fas fa-times
                                "></i>

                            </a>

                            <?php
                            } else {
                            ?>

                            <!-- BATAL -->

                            <a href="
                            ?id_mahasiswa=
                            <?= $id_mahasiswa ?>
                            &batal=
                            <?= $row['id_krs'] ?>
                            "

                            class="
                            btn-action
                            btn-batal
                            "

                            onclick="
                            return confirm(
                            'Batalkan status KRS ini?'
                            )
                            ">

                                <i class="
                                fas fa-rotate-left
                                "></i>

                            </a>

                            <?php } ?>

                        </td>

                    </tr>

                    <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

</body>
</html>