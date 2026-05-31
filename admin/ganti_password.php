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
// AMBIL DATA USER LOGIN
// ==========================
$username = $_SESSION['username'];

$queryUser = mysqli_query($koneksi, "
    SELECT *
    FROM users
    WHERE username = '$username'
");

if (!$queryUser) {
    die("Query Error: " . mysqli_error($koneksi));
}

$user = mysqli_fetch_assoc($queryUser);

// ==========================
// PROSES GANTI PASSWORD
// ==========================
$notif = '';
$notif_type = '';

if (isset($_POST['ganti_password'])) {

    $password_lama = trim($_POST['password_lama']);
    $password_baru = trim($_POST['password_baru']);
    $konfirmasi    = trim($_POST['konfirmasi_password']);

    // VALIDASI KOSONG
    if (
        empty($password_lama) ||
        empty($password_baru) ||
        empty($konfirmasi)
    ) {

        $notif = "Semua field wajib diisi!";
        $notif_type = "danger";
    }

    // VALIDASI PASSWORD LAMA
    else {

        $passwordValid = false;

        // jika password hash
        if (password_verify($password_lama, $user['password'])) {
            $passwordValid = true;
        }

        // jika password biasa (plain text)
        if ($password_lama === $user['password']) {
            $passwordValid = true;
        }

        if (!$passwordValid) {

            $notif = "Password lama tidak sesuai!";
            $notif_type = "danger";

        } elseif ($password_baru != $konfirmasi) {

            $notif = "Konfirmasi password tidak cocok!";
            $notif_type = "warning";

        } elseif (strlen($password_baru) < 6) {

            $notif = "Password minimal 6 karakter!";
            $notif_type = "warning";

        } else {

            // HASH PASSWORD BARU
            $hashPassword = password_hash(
                $password_baru,
                PASSWORD_DEFAULT
            );

            // UPDATE PASSWORD
            $update = mysqli_query($koneksi, "
                UPDATE users
                SET password = '$hashPassword'
                WHERE username = '$username'
            ");

            if ($update) {

                echo "
                <script>
                    alert('Password berhasil diganti! Silakan login ulang.');
                    window.location='../auth/logout.php';
                </script>
                ";

                exit;

            } else {

                $notif = "Gagal mengganti password!";
                $notif_type = "danger";
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

<title>Ganti Password</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

body{
    min-height:100vh;
    background:
    linear-gradient(135deg,#0f172a,#1e293b,#334155);
    overflow-x:hidden;
    position:relative;
}

/* ORNAMEN */
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
    0%{transform:translateY(0);}
    50%{transform:translateY(20px);}
    100%{transform:translateY(0);}
}

.wrapper{
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:30px;
    position:relative;
    z-index:2;
}

.form-card{
    width:100%;
    max-width:600px;
    background:#fff;
    border-radius:28px;
    overflow:hidden;
    box-shadow:0 20px 50px rgba(0,0,0,.2);
}

.header-card{
    background:linear-gradient(135deg,#2563eb,#0ea5e9);
    padding:35px;
    text-align:center;
    color:white;
}

.icon-box{
    width:70px;
    height:70px;
    background:rgba(255,255,255,.15);
    border-radius:20px;
    margin:auto;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:28px;
    margin-bottom:15px;
}

.form-body{
    padding:35px;
}

.form-label{
    font-weight:700;
    color:#1e293b;
}

.password-box{
    position:relative;
}

.form-control{
    height:55px;
    border-radius:16px;
    border:1.5px solid #dbeafe;
    background:#f8fafc;
    padding-right:55px;
}

.form-control:focus{
    border-color:#2563eb;
    box-shadow:0 0 0 4px rgba(37,99,235,.15);
}

.toggle-password{
    position:absolute;
    top:50%;
    right:18px;
    transform:translateY(-50%);
    cursor:pointer;
    color:#64748b;
}

.toggle-password:hover{
    color:#2563eb;
}

.btn-save{
    border:none;
    border-radius:16px;
    padding:14px 30px;
    font-weight:700;
    color:white;
    background:linear-gradient(
        135deg,
        #2563eb,
        #38bdf8
    );
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
    border-radius:16px;
}

</style>
</head>

<body>

<div class="wrapper">

<div class="form-card">

    <div class="header-card">

        <div class="icon-box">
            <i class="fas fa-key"></i>
        </div>

        <h2>Ganti Password</h2>
        <p>Keamanan akun administrator</p>

    </div>

    <div class="form-body">

        <?php if($notif != '') : ?>
            <div class="alert alert-<?= $notif_type ?>">
                <?= $notif ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <!-- PASSWORD LAMA -->
            <div class="mb-3">
                <label class="form-label">
                    Password Lama
                </label>

                <div class="password-box">
                    <input
                    type="password"
                    name="password_lama"
                    id="password_lama"
                    class="form-control"
                    required>

                    <span class="toggle-password"
                    onclick="togglePassword('password_lama', this)">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
            </div>

            <!-- PASSWORD BARU -->
            <div class="mb-3">
                <label class="form-label">
                    Password Baru
                </label>

                <div class="password-box">
                    <input
                    type="password"
                    name="password_baru"
                    id="password_baru"
                    class="form-control"
                    required>

                    <span class="toggle-password"
                    onclick="togglePassword('password_baru', this)">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
            </div>

            <!-- KONFIRMASI -->
            <div class="mb-4">
                <label class="form-label">
                    Konfirmasi Password
                </label>

                <div class="password-box">
                    <input
                    type="password"
                    name="konfirmasi_password"
                    id="konfirmasi_password"
                    class="form-control"
                    required>

                    <span class="toggle-password"
                    onclick="togglePassword('konfirmasi_password', this)">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-3">

                <a href="dashboard.php" class="btn-back">
                    Kembali
                </a>

                <button
                type="submit"
                name="ganti_password"
                class="btn-save">

                    <i class="fas fa-save me-2"></i>
                    Simpan

                </button>

            </div>

        </form>

    </div>

</div>

</div>

<script>
function togglePassword(id, element){

    let input = document.getElementById(id);
    let icon = element.querySelector("i");

    if(input.type === "password"){
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}
</script>

</body>
</html>