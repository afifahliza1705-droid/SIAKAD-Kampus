<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - SIAKAD</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<!-- FONT PREMIUM -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Inter',sans-serif;
}

body{
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    overflow:hidden;

    background:
        radial-gradient(circle at 20% 20%, #38bdf8, transparent 35%),
        radial-gradient(circle at 80% 30%, #1d4ed8, transparent 40%),
        radial-gradient(circle at 50% 80%, #0f172a, transparent 50%),
        #0b1220;
}

/* floating light */
.blob{
    position:absolute;
    border-radius:50%;
    filter:blur(90px);
    opacity:.45;
    animation:float 8s ease-in-out infinite;
}

.blob1{
    width:320px;
    height:320px;
    background:#38bdf8;
    top:-120px;
    left:-120px;
}

.blob2{
    width:360px;
    height:360px;
    background:#2563eb;
    bottom:-140px;
    right:-140px;
}

@keyframes float{
    0%,100%{transform:translateY(0);}
    50%{transform:translateY(25px);}
}

/* MAIN WRAPPER */
.wrapper{
    width:1000px;
    display:flex;
    border-radius:28px;
    overflow:hidden;

    background:rgba(255,255,255,0.06);
    backdrop-filter:blur(22px);

    box-shadow:0 35px 90px rgba(0,0,0,.55);
}

/* LEFT HERO */
.left{
    width:45%;
    padding:70px 55px;
    color:white;

    background:linear-gradient(180deg,#0f172a,#1e293b);
    display:flex;
    flex-direction:column;
    justify-content:center;
    text-align:center;
}

.left i{
    font-size:90px;
    color:#38bdf8;
    margin-bottom:20px;
    text-shadow:0 0 30px rgba(56,189,248,.5);
    animation:icon 3s ease-in-out infinite;
}

@keyframes icon{
    0%,100%{transform:translateY(0);}
    50%{transform:translateY(-10px);}
}

.left h1{
    font-size:34px;
    font-weight:800;
}

.left h3{
    color:#38bdf8;
    margin-top:5px;
    font-weight:600;
}

.left p{
    margin-top:15px;
    font-size:14px;
    opacity:.8;
    line-height:1.7;
}

/* RIGHT */
.right{
    width:55%;
    padding:65px 60px;
}

/* TITLE */
.title{
    font-size:28px;
    font-weight:800;
    color:white;
}

.subtitle{
    color:#cbd5e1;
    margin-bottom:25px;
    font-size:14px;
}

/* ALERT */
.alert{
    border-radius:14px;
}

/* INPUT */
.form-label{
    color:#e2e8f0;
    font-weight:600;
    font-size:13px;
}

.input-group{
    margin-bottom:18px;
    border-radius:14px;
    overflow:hidden;
    transition:.3s;
    border:1px solid rgba(255,255,255,.1);
}

.input-group:focus-within{
    border-color:#38bdf8;
    box-shadow:0 0 20px rgba(56,189,248,.25);
    transform:translateY(-2px);
}

.input-group-text{
    background:#0f172a;
    border:none;
    color:#38bdf8;
    width:50px;
    justify-content:center;
}

.form-control{
    height:52px;
    border:none;
    background:rgba(255,255,255,0.08);
    color:white;
}

.form-control::placeholder{
    color:#94a3b8;
}

/* BUTTON */
.btn-login{
    width:100%;
    height:52px;
    border:none;
    border-radius:14px;

    background:linear-gradient(135deg,#1d4ed8,#38bdf8);
    color:white;

    font-weight:800;
    letter-spacing:1px;

    transition:.3s;
    margin-top:10px;

    box-shadow:0 15px 40px rgba(56,189,248,.25);
}

.btn-login:hover{
    transform:translateY(-3px);
    box-shadow:0 25px 60px rgba(56,189,248,.35);
}

/* FOOTER */
.footer{
    text-align:center;
    margin-top:20px;
    font-size:12px;
    color:#94a3b8;
}

/* RESPONSIVE */
@media(max-width:900px){
    .wrapper{
        flex-direction:column;
        width:95%;
    }

    .left,.right{
        width:100%;
    }
}

</style>

</head>

<body>

<div class="blob blob1"></div>
<div class="blob blob2"></div>

<div class="wrapper">

    <!-- LEFT -->
    <div class="left">
        <i class="fa-solid fa-graduation-cap"></i>

        <h1>SIAKAD</h1>
        <h3>Universitas System</h3>

        <p>
            Sistem Informasi Akademik modern untuk pengelolaan data mahasiswa, dosen, mata kuliah,
            dan administrasi universitas secara terintegrasi dan efisien.
        </p>
    </div>

    <!-- RIGHT -->
    <div class="right">

        <div class="title">Login Portal</div>
        <div class="subtitle">Masuk untuk mengakses sistem akademik</div>

        <?php if(isset($_GET['error'])) : ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i>
                Username atau Password salah!
            </div>
        <?php endif; ?>

        <form action="proses_login.php" method="POST">

            <label class="form-label">Username</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
            </div>

            <label class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>

                <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password" required>

                <span class="input-group-text" onclick="togglePassword()" style="cursor:pointer;">
                    <i id="eyeIcon" class="fa-solid fa-eye"></i>
                </span>
            </div>

            <button type="submit" class="btn-login">
                LOGIN
            </button>

        </form>

        <div class="footer">
            © 2026 SIAKAD University System
        </div>

    </div>

</div>

<script>
function togglePassword(){
    let pass = document.getElementById("password");
    let icon = document.getElementById("eyeIcon");

    if(pass.type === "password"){
        pass.type = "text";
        icon.classList.replace("fa-eye","fa-eye-slash");
    }else{
        pass.type = "password";
        icon.classList.replace("fa-eye-slash","fa-eye");
    }
}
</script>

</body>
</html>