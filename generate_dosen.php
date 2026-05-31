<?php
include 'config/koneksi.php';

// ambil semua dosen
$query = mysqli_query($koneksi, "SELECT * FROM dosen");

while ($d = mysqli_fetch_assoc($query)) {

    $nip = $d['nip'];

    // password = nip
    $password = password_hash($nip, PASSWORD_DEFAULT);

    // cek apakah user sudah ada
    $cek = mysqli_query($koneksi, "
        SELECT * FROM users 
        WHERE username='$nip'
    ");

    if(mysqli_num_rows($cek) == 0){

        mysqli_query($koneksi, "
            INSERT INTO users (
                username,
                password,
                role,
                id_ref,
                created_at
            ) VALUES (
                '$nip',
                '$password',
                'dosen',
                '".$d['id_dosen']."',
                NOW()
            )
        ");

        echo "Akun dosen <b>".$d['nama']."</b> berhasil dibuat <br>";

    } else {
        echo "Akun <b>".$d['nama']."</b> sudah ada <br>";
    }
}

echo "<br><h2>SELESAI 🔥</h2>";
?>