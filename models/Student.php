<?php
require_once __DIR__ . '/../config/database.php';

class Student
{
    // Tambah mahasiswa baru
    public static function create($nim, $name, $photo)
    {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO students (nim, name, photo) VALUES (?, ?, ?)");
        return $stmt->execute([$nim, $name, $photo]);
    }

    // Ambil data mahasiswa berdasarkan NIM
    public static function getByNim($nim)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM students WHERE nim = ?");
        $stmt->execute([$nim]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Ambil data mahasiswa berdasarkan ID
    public static function getById($id)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Ambil semua mahasiswa
    public static function getAll()
    {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM students ORDER BY registered_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>