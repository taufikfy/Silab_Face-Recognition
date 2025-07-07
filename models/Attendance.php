<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/Student.php';
require_once __DIR__ . '/models/Attendance.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['identity'])) {
    $nim = $_POST['identity'];
    $student = Student::getByNim($nim);

    if ($student) {
        Attendance::record($student['id']);
        echo json_encode([
            'status' => 'success',
            'message' => 'Absensi berhasil dicatat.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Mahasiswa tidak ditemukan.'
        ]);
    }
    exit;
}

echo json_encode([
    'status' => 'error',
    'message' => 'Request tidak valid.'
]);