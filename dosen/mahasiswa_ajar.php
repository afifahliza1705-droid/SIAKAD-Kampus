<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'dosen') {
    header("Location: ../auth/login.php");
    exit;
}

$id_dosen = $_SESSION['id_ref'];

// DOSEN
$dosen = mysqli_fetch_assoc(mysqli_query($koneksi,"
    SELECT * FROM dosen WHERE id_dosen='$id_dosen'
"));

// MAHASISWA
$sql = "
SELECT DISTINCT
    m.id_mahasiswa,
    m.nim,
    m.nama,
    m.jenis_kelamin,
    m.jurusan,
    m.prodi,
    m.email,
    m.alamat,
    m.foto,
    mk.nama_mk
FROM krs k
JOIN mahasiswa m ON k.id_mahasiswa = m.id_mahasiswa
JOIN mata_kuliah mk ON k.id_mk = mk.id_mk
WHERE mk.id_dosen='$id_dosen'
";

$sql .= " GROUP BY m.id_mahasiswa ORDER BY m.nama ASC";

$data = mysqli_query($koneksi, $sql);

if (!$data) {
    die("SQL ERROR: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Mahasiswa Diajar</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

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
    overflow-y:auto;
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
    padding:14px;
    color:#cbd5e1;
    text-decoration:none;
    border-radius:14px;
    margin-bottom:8px;
    transition:.3s;
    font-weight:500;
    font-size:14px;
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
    margin:0;
}

.topbar p{
    color:#64748b;
    margin-top:5px;
}

/* FILTER */
.filter-box{
    background:white;
    padding:20px;
    border-radius:20px;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
    margin-bottom:20px;
}

/* TABLE */
.card-box{
    background:white;
    padding:25px;
    border-radius:25px;
    box-shadow:0 15px 35px rgba(0,0,0,.08);
}

.table thead{
    background:#1e3a8a;
    color:white;
}

/* FOTO */
.foto{
    width:50px;
    height:50px;
    border-radius:12px;
    object-fit:cover;
    border:3px solid #dbeafe;
}

/* ===================== FIX UTAMA (MATA KULIAH TIDAK NUMPUK) ===================== */
td{
    vertical-align:middle;
}

/* kolom MK biar tidak sempit & bisa turun baris */
td:nth-child(9){
    white-space:normal !important;
    min-width:180px;
}

/* badge MK biar rapi dan tidak numpuk */
.badge-mk{
    display:inline-block;
    background:linear-gradient(135deg,#7c3aed,#2563eb);
    color:white;
    padding:6px 10px;
    border-radius:10px;
    font-size:12px;
    margin:2px;
}

/* kalau nanti ada banyak MK dalam 1 cell */
.mk-wrapper{
    display:flex;
    flex-wrap:wrap;
    gap:5px;
}

</style>
</head>

<body>

<div class="sidebar">

    <div class="brand">
        <i class="fas fa-chalkboard-teacher"></i>
        <h3>SIAKAD</h3>
        <small>Dosen Panel</small>
    </div>

    <div class="menu">
        <a href="dashboard.php"><i class="fas fa-house"></i> Dashboard</a>
        <a href="mahasiswa_pa.php"><i class="fas fa-users"></i> Mahasiswa PA</a>
        <a href="acc_krs.php"><i class="fas fa-file-signature"></i> ACC KRS</a>
        <a href="input_nilai.php"><i class="fas fa-pen"></i> Input Nilai</a>
        <a href="mahasiswa_ajar.php" class="active"><i class="fas fa-user-graduate"></i> Daftar Mahasiswa</a>
        <a href="jadwal.php"><i class="fas fa-calendar-days"></i> Jadwal Mengajar</a>
        <a href="profil.php"><i class="fas fa-user"></i> Profil</a>
        <a href="ganti_password.php"><i class="fas fa-key"></i> Ganti Password</a>
        <a href="../auth/logout.php"><i class="fas fa-right-from-bracket"></i> Logout</a>
    </div>
</div>

<div class="content">

    <div class="topbar">
        <div>
            <h3>Mahasiswa Diajar</h3>
            <p>Data mahasiswa sesuai mata kuliah Anda</p>
        </div>
    </div>

    
    <div class="card-box">

        <table class="table table-hover align-middle">

            <thead>
                <tr>
                    <th>Foto</th>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>JK</th>
                    <th>Jurusan</th>
                    <th>Prodi</th>
                    <th>Email</th>
                    <th>Alamat</th>
                    <th>Mata Kuliah</th>
                </tr>
            </thead>

            <tbody>

            <?php while($row = mysqli_fetch_assoc($data)){

                $foto = !empty($row['foto'])
                    ? "../assets/uploads/mahasiswa/".$row['foto']
                    : "https://cdn-icons-png.flaticon.com/512/3135/3135715.png";
            ?>

                <tr>
                    <td><img src="<?= $foto ?>" class="foto"></td>
                    <td><?= $row['nim'] ?></td>
                    <td><?= $row['nama'] ?></td>
                    <td><?= $row['jenis_kelamin'] ?></td>
                    <td><?= $row['jurusan'] ?></td>
                    <td><?= $row['prodi'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td><?= $row['alamat'] ?></td>

                    <td>
                        <span class="badge-mk"><?= $row['nama_mk'] ?></span>
                    </td>
                </tr>

            <?php } ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>