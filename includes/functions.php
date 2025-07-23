<?php
// includes/functions.php
require_once __DIR__ . '/../config/database.php';

function registerStudent($nim, $name, $photoFilename) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO students (nim, name, photo) VALUES (?, ?, ?)");
        return $stmt->execute([$nim, $name, $photoFilename]);
    } catch (PDOException $e) {
        error_log("Error registering student: " . $e->getMessage());
        return false;
    }
}

function checkIn($studentId) {
    global $pdo;
    $current_date = date('Y-m-d');
    $current_time = date('Y-m-d H:i:s'); // Mengambil waktu dari PHP yang sudah benar

    // Cek apakah mahasiswa sudah memiliki record hari ini
    $stmt_check = $pdo->prepare("SELECT id, check_out FROM attendance WHERE student_id = ? AND DATE(attended_at) = ?");
    $stmt_check->execute([$studentId, $current_date]);
    $existing_record = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if ($existing_record) {
        if ($existing_record['check_out'] === null) {
            return ['status' => 'info', 'message' => 'Anda sudah presensi masuk hari ini dan belum keluar.'];
        } else {
            return ['status' => 'info', 'message' => 'Anda sudah presensi masuk dan keluar hari ini.'];
        }
    }

    try {
        // Mengirim waktu (attended_at) langsung dari PHP
        $stmt = $pdo->prepare("INSERT INTO attendance (student_id, attended_at, status) VALUES (?, ?, ?)");

        if ($stmt->execute([$studentId, $current_time, 'masuk'])) {
            $student_data = getStudentById($studentId);
            $student_name = $student_data ? $student_data['name'] : 'Mahasiswa';
            return ['status' => 'success', 'message' => 'Presensi masuk berhasil! Selamat datang, ' . htmlspecialchars($student_name) . '.'];
        } else {
            return ['status' => 'error', 'message' => 'Gagal menyimpan presensi masuk.'];
        }
    } catch (PDOException $e) {
        // DIUBAH: Tampilkan pesan error SQL yang detail untuk debugging
        return ['status' => 'error', 'message' => 'Database Error: ' . $e->getMessage()];
    }
}

function checkOut($studentId) {
    global $pdo;
    $current_date = date('Y-m-d');
    $current_time = date('Y-m-d H:i:s');

    // Cari record presensi masuk hari ini yang belum diisi waktu keluar (check_out IS NULL)
    $stmt = $pdo->prepare("SELECT id, attended_at, check_out FROM attendance WHERE student_id = ? AND DATE(attended_at) = ? AND check_out IS NULL ORDER BY attended_at DESC LIMIT 1");
    $stmt->execute([$studentId, $current_date]);
    $record_to_update = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$record_to_update) {
        // Tidak ada record masuk hari ini yang belum di-check-out
        $stmt_check_any_today = $pdo->prepare("SELECT id, check_out FROM attendance WHERE student_id = ? AND DATE(attended_at) = ? ORDER BY attended_at DESC LIMIT 1");
        $stmt_check_any_today->execute([$studentId, $current_date]);
        $any_record_today = $stmt_check_any_today->fetch(PDO::FETCH_ASSOC);

        if (!$any_record_today) {
            return ['status' => 'info', 'message' => 'Anda belum presensi masuk hari ini.'];
        } elseif ($any_record_today['check_out'] !== null) {
            return ['status' => 'info', 'message' => 'Anda sudah presensi keluar hari ini.'];
        } else {
            return ['status' => 'error', 'message' => 'Tidak dapat melakukan presensi keluar.'];
        }
    }

    try {
        $stmt = $pdo->prepare("UPDATE attendance SET check_out = ?, status = ? WHERE id = ?");
        if ($stmt->execute([$current_time, 'keluar', $record_to_update['id']])) {
            $student_data = getStudentById($studentId);
            $student_name = $student_data ? $student_data['name'] : 'Mahasiswa';
            return ['status' => 'success', 'message' => 'Presensi keluar berhasil! Sampai jumpa lagi, ' . htmlspecialchars($student_name) . '.'];
        } else {
            return ['status' => 'error', 'message' => 'Gagal menyimpan presensi keluar.'];
        }
    } catch (PDOException $e) {
        // DIUBAH: Tampilkan pesan error SQL yang detail untuk debugging
        return ['status' => 'error', 'message' => 'Database Error: ' . $e->getMessage()];
    }
}

function adminLogin($username, $password) {
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
        SELECT a.id, a.student_id, a.attended_at, a.check_out, s.name, s.nim, a.status
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
    $stmt = $pdo->prepare("SELECT id, name FROM students WHERE nim = ?");
    $stmt->execute([$nim]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getStudentById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id, name FROM students WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>