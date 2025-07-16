<?php
require_once __DIR__ . '/../config/database.php';

function registerStudent($nim, $name, $photoFilename) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO students (nim, name, photo) VALUES (?, ?, ?)");
    return $stmt->execute([$nim, $name, $photoFilename]);
}

function checkIn($studentId) {
    global $pdo;
    // Cek apakah sudah check-in hari ini
    $stmt = $pdo->prepare("SELECT * FROM attendance WHERE student_id = ? AND DATE(attended_at) = CURDATE()");
    $stmt->execute([$studentId]);
    if ($stmt->fetch()) {
        return ['status' => 'info', 'message' => 'Sudah melakukan check-in hari ini.'];
    }

    // Insert check-in
    $stmt = $pdo->prepare("INSERT INTO attendance (student_id, attended_at) VALUES (?, NOW())");
    $stmt->execute([$studentId]);
    return ['status' => 'success', 'message' => 'Berhasil presensi masuk.'];
}

function checkOut($studentId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM attendance WHERE student_id = ? AND DATE(attended_at) = CURDATE()");
    $stmt->execute([$studentId]);
    $record = $stmt->fetch();

    if (!$record) {
        return ['status' => 'error', 'message' => 'Belum melakukan check-in hari ini.'];
    }

    if ($record['check_out']) {
        return ['status' => 'info', 'message' => 'Sudah melakukan check-out hari ini.'];
    }

    $stmt = $pdo->prepare("UPDATE attendance SET check_out = NOW() WHERE id = ?");
    $stmt->execute([$record['id']]);
    return ['status' => 'success', 'message' => 'Berhasil presensi keluar.'];
}

function adminLogin($username, $password)
{
    // User dan password default (hardcoded)
    $default_username = 'admin';
    $default_password = 'admin123';

    if ($username === $default_username && $password === $default_password) {
        return ['username' => $username];
    }

    return false;
}

function getAttendanceRecords($filter = []) {
    global $pdo;
    $date = $filter['attended_at'] ?? date('Y-m-d');
    $stmt = $pdo->prepare("
        SELECT a.id, a.student_id, a.attended_at, a.check_out, s.name, s.nim
        FROM attendance a
        JOIN students s ON a.student_id = s.id
        WHERE DATE(a.attended_at) = ?
        ORDER BY a.attended_at ASC
    ");
    $stmt->execute([$date]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getStudentByNIM($nim) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM students WHERE nim = ?");
    $stmt->execute([$nim]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
