<?php
session_start();
include '../config/koneksi.php';

// PROTEKSI DOSEN
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'dosen') {
    header("Location: ../auth/login.php");
    exit;
}

$id_dosen = $_SESSION['id_ref'];

// AMBIL DOSEN (TETAP DIPERTAHANKAN)
$dosen = mysqli_fetch_assoc(mysqli_query($koneksi,"
    SELECT * FROM dosen WHERE id_dosen='$id_dosen'
"));

// AMBIL MAHASISWA PA (TETAP DIPERTAHANKAN)
$query = mysqli_query($koneksi,"
    SELECT m.*
    FROM mahasiswa m
    WHERE m.id_dosen_pa='$id_dosen'
    ORDER BY m.nama ASC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Mahasiswa PA</title>

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
    background: linear-gradient(135deg,#eef4ff,#dbeafe);
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

/* HEADER */
.topbar{
    background:white;
    padding:25px;
    border-radius:22px;
    box-shadow:0 15px 40px rgba(0,0,0,.08);
    margin-bottom:25px;
}

.topbar h3{
    font-weight:800;
}

/* TABLE CARD */
.table-box{
    background:white;
    padding:25px;
    border-radius:22px;
    box-shadow:0 10px 30px rgba(0,0,0,.08);
}

/* FOTO */
.foto{
    width:55px;
    height:55px;
    border-radius:12px;
    object-fit:cover;
    border:2px solid #3b82f6;
}

/* TABLE */
.table th{
    background:#eff6ff;
    color:#1e3a8a;
    font-size:13px;
    text-align:center;
    vertical-align:middle;
}

.table td{
    font-size:13px;
    text-align:center;
    vertical-align:middle;
}

/* BADGE */
.badge-nim{
    background:#dbeafe;
    padding:5px 10px;
    border-radius:10px;
    font-size:12px;
    font-weight:600;
    color:#1d4ed8;
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">

    <div class="brand">
        <i class="fas fa-chalkboard-teacher"></i>
        <h3>SIAKAD</h3>
        <small>Dosen Panel</small>
    </div>

    <div class="menu">
        <a href="dashboard.php"><i class="fas fa-house"></i> Dashboard</a>
        <a href="mahasiswa_pa.php" class="active"><i class="fas fa-users"></i> Mahasiswa PA</a>
        <a href="acc_krs.php"><i class="fas fa-file-signature"></i> ACC KRS</a>
        <a href="input_nilai.php"><i class="fas fa-pen"></i> Input Nilai</a>
        <a href="mahasiswa_ajar.php"><i class="fas fa-user-graduate"></i> Daftar Mahasiswa</a>
        <a href="jadwal.php"><i class="fas fa-calendar-days"></i> Jadwal Mengajar</a>
        <a href="profil.php"><i class="fas fa-user"></i> Profil</a>
        <a href="ganti_password.php"><i class="fas fa-key"></i> Ganti Password</a>
        <a href="../auth/logout.php"><i class="fas fa-right-from-bracket"></i> Logout</a>
    </div>
</div>

<!-- CONTENT -->
<div class="content">

    <div class="topbar">
        <h3>Mahasiswa Bimbingan Akademik</h3>
        <p>Daftar lengkap mahasiswa PA Anda</p>
    </div>

    <div class="table-box">

        <table class="table table-hover table-bordered align-middle">

            <thead>
                <tr>
                    <th>No</th>
                    <th>Foto</th>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>JK</th>
                    <th>Prodi</th>
                    <th>Jurusan</th>
                    <th>TTL</th>
                    <th>Agama</th>
                    <th>Status</th>
                    <th>Email</th>
                    <th>Alamat</th>
                </tr>
            </thead>

            <tbody>

            <?php $no=1; while($row=mysqli_fetch_assoc($query)):

                $foto = !empty($row['foto']) && file_exists("../assets/uploads/mahasiswa/".$row['foto'])
                    ? "../assets/uploads/mahasiswa/".$row['foto']
                    : "https://ui-avatars.com/api/?name=".urlencode($row['nama']);

            ?>

                <tr>
                    <td><?= $no++ ?></td>

                    <td>
                        <img src="<?= $foto ?>" class="foto">
                    </td>

                    <td><span class="badge-nim"><?= $row['nim'] ?? '-' ?></span></td>
                    <td><?= $row['nama'] ?? '-' ?></td>
                    <td><?= $row['jenis_kelamin'] ?? '-' ?></td>
                    <td><?= $row['prodi'] ?? '-' ?></td>
                    <td><?= $row['jurusan'] ?? '-' ?></td>

                    <td>
                        <?= ($row['tempat_lahir'] ?? '-') . ', ' . ($row['tanggal_lahir'] ?? '-') ?>
                    </td>

                    <td><?= $row['agama'] ?? '-' ?></td>
                    <td><?= $row['status'] ?? '-' ?></td>
                    <td><?= $row['email'] ?? '-' ?></td>
                    <td><?= $row['alamat'] ?? '-' ?></td>
                </tr>

            <?php endwhile; ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>