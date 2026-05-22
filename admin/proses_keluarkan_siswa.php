<?php
session_start();
require '../login/koneksi.php';

if($_SESSION['role'] != 'admin' || !isset($_GET['id'])) {
    header("Location: mataPelajaran.php");
    exit;
}

$id_siswa = mysqli_real_escape_string($koneksi, $_GET['id']);
$kelas    = mysqli_real_escape_string($koneksi, $_GET['kelas']);

// Mengosongkan status kelas siswa di database
$query_clear = "UPDATE siswa SET Kelas = '' WHERE IDSiswa = '$id_siswa'";

if(mysqli_query($koneksi, $query_clear)) {
    header("Location: mataPelajaran.php?status=sukses_keluar");
    exit;
} else {
    echo "Gagal memproses data: " . mysqli_error($koneksi);
}
?>