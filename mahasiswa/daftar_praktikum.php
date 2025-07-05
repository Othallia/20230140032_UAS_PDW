<?php
session_start();
require_once '../config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'mahasiswa') { header("Location: ../login.php"); exit(); }
if (!isset($_GET['id']) || empty($_GET['id'])) { header("Location: ../katalog.php?status=gagal&pesan=ID Praktikum tidak valid."); exit(); }
$mahasiswa_id = $_SESSION['user_id'];
$praktikum_id = $_GET['id'];
$sql_cek = "SELECT id FROM pendaftaran_praktikum WHERE mahasiswa_id = ? AND mata_praktikum_id = ?";
$stmt_cek = $conn->prepare($sql_cek);
$stmt_cek->bind_param("ii", $mahasiswa_id, $praktikum_id);
$stmt_cek->execute();
$stmt_cek->store_result();
if ($stmt_cek->num_rows > 0) {
    header("Location: ../katalog.php?status=gagal&pesan=Anda sudah terdaftar pada mata praktikum ini.");
} else {
    $sql_insert = "INSERT INTO pendaftaran_praktikum (mahasiswa_id, mata_praktikum_id) VALUES (?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("ii", $mahasiswa_id, $praktikum_id);
    if ($stmt_insert->execute()) { header("Location: praktikum_saya.php?status=sukses&pesan=Pendaftaran berhasil!"); } 
    else { header("Location: ../katalog.php?status=gagal&pesan=Terjadi kesalahan saat mendaftar."); }
    $stmt_insert->close();
}
$stmt_cek->close();
$conn->close();
exit();
?>