<?php
$host   = "localhost";
$user   = "root";
$pass   = "";
$dbname = "kenanginkopi";

$mysqli = new mysqli($host, $user, $pass, $dbname);


if ($mysqli->connect_errno) {
    die('Koneksi Database Gagal: ' . $mysqli->connect_error);
}


$mysqli->set_charset('utf8mb4');
?>