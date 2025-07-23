<?php
date_default_timezone_set('Asia/Jakarta');
// config/database.php

// Variabel konfigurasi database
$db_host = 'db';             // Host database Anda, default Laragon adalah 'localhost'
$db_name = 'face_attendance';    // Ganti dengan NAMA DATABASE Anda yang sebenarnya di phpMyAdmin
$db_user = 'root';                  // Username database Anda, default Laragon adalah 'root'
$db_pass = 'root_password';                      // Password database Anda, default Laragon adalah KOSONG

try {
    // Membuat instance PDO (PHP Data Objects) untuk koneksi ke MySQL
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);

    // Mengatur atribut PDO untuk penanganan error dan mode pengambilan data
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Mengaktifkan mode error yang akan melempar PDOException
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Mengatur default fetch mode ke associative array
    $pdo->exec("SET time_zone = '+07:00'");
} catch (PDOException $e) {
    // Menangani error koneksi database
    error_log("Database connection failed in config/database.php: " . $e->getMessage());
    die("Koneksi database gagal. Mohon coba lagi nanti.");
}
?>