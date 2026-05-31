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

// DOSEN
$dosen = mysqli_fetch_assoc(mysqli_query($koneksi,"
    SELECT * FROM dosen WHERE id_dosen='$id_dosen'
"));

// ==========================
// QUERY JADWAL (SUDAH + KELAS)
// ==========================
$sql = "
SELECT 
    jk.*,
    mk.nama_mk,
    mk.sks,
    mk.semester,
    k.nama_kelas
FROM jadwal_kuliah jk
JOIN mata_kuliah mk ON jk.id_mk = mk.id_mk
LEFT JOIN kelas k ON jk.id_kelas = k.id_kelas
WHERE jk.id_dosen='$id_dosen'
";

$sql .= " ORDER BY FIELD(jk.hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'), jk.jam_mulai ASC";

$jadwal = mysqli_query($koneksi, $sql);

// FORMAT JAM
function jam($t){
    return str_replace(":",".",substr($t,0,5));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Jadwal Mengajar</title>

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

body{background:#eef4ff;}

.sidebar{
    width:280px;
    height:100vh;
    position:fixed;
    background:linear-gradient(180deg,#0f172a,#172554);
    padding:30px 20px;
    color:white;
}

.brand{text-align:center;margin-bottom:35px;}
.brand i{font-size:55px;color:#60a5fa;}
.brand h3{margin-top:10px;font-weight:800;}
.brand small{color:#cbd5e1;}

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

.content{
    margin-left:280px;
    padding:30px;
}

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

.topbar h3{font-weight:800;}
.topbar p{color:#64748b;}

.filter-box{
    background:white;
    padding:20px;
    border-radius:20px;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
    margin-bottom:20px;
}

.card-box{
    background:white;
    border-radius:25px;
    padding:25px;
    box-shadow:0 15px 35px rgba(0,0,0,.08);
}

.badge-day{
    background:linear-gradient(135deg,#2563eb,#7c3aed);
    color:white;
    padding:6px 12px;
    border-radius:12px;
    font-size:12px;
    font-weight:600;
}

.table th,
.table td{
    text-align:center !important;
    vertical-align:middle !important;
}

.table thead{
    background:#1e3a8a;
    color:white;
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
        <a href="mahasiswa_ajar.php"><i class="fas fa-user-graduate"></i> Daftar Mahasiswa</a>
        <a href="jadwal.php" class="active"><i class="fas fa-calendar-days"></i> Jadwal Mengajar</a>
        <a href="profil.php"><i class="fas fa-user"></i> Profil</a>
        <a href="ganti_password.php"><i class="fas fa-key"></i> Ganti Password</a>
        <a href="../auth/logout.php"><i class="fas fa-right-from-bracket"></i> Logout</a>
    </div>
</div>

<div class="content">

    <div class="topbar">
        <div>
            <h3>Jadwal Mengajar</h3>
            <p>Daftar jadwal perkuliahan Anda</p>
        </div>
    </div>

    <div class="card-box">

        <table class="table table-hover align-middle">

            <thead>
                <tr>
                    <th>No</th>
                    <th>Hari</th>
                    <th>Kelas</th> <!-- 🔥 TAMBAHAN -->
                    <th>Mata Kuliah</th>
                    <th>Semester</th>
                    <th>SKS</th>
                    <th>Jam Mulai</th>
                    <th>Jam Selesai</th>
                    <th>Ruangan</th>
                </tr>
            </thead>

            <tbody>

            <?php $no=1; while($row=mysqli_fetch_assoc($jadwal)){ ?>

                <tr>
                    <td><?= $no++ ?></td>
                    <td><span class="badge-day"><?= $row['hari'] ?></span></td>

                    <!-- 🔥 KELAS BARU -->
                    <td><?= $row['nama_kelas'] ?? '-' ?></td>

                    <td><?= $row['nama_mk'] ?></td>
                    <td><?= $row['semester'] ?></td>
                    <td><?= $row['sks'] ?></td>
                    <td><?= substr($row['jam_mulai'],0,5) ?></td>
                    <td><?= substr($row['jam_selesai'],0,5) ?></td>
                    <td><?= $row['ruangan'] ?></td>
                </tr>

            <?php } ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>