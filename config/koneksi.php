<?php

$host = "localhost";
$user = "root";
$pass = "";
$db   = "siakad2"; 

$koneksi = mysqli_connect($host, $user, $pass, $db);

// cek koneksi
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// set timezone Indonesia
date_default_timezone_set('Asia/Jakarta');

?>