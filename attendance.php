<?php
require_once __DIR__ . '/includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['identity']) && isset($_POST['action'])) {
    $nim = $_POST['identity'];
    $action = $_POST['action'];

    $student = getStudentByNIM($nim);
    if (!$student) {
        echo json_encode(['status' => 'error', 'message' => 'Mahasiswa tidak ditemukan.']);
        exit;
    }

    if ($action === 'checkin') {
        $result = checkIn($student['id']);
        echo json_encode($result);
        exit;
    } elseif ($action === 'checkout') {
        $result = checkOut($student['id']);
        echo json_encode($result);
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Aksi tidak valid.']);
        exit;
    }
}

echo json_encode(['status' => 'error', 'message' => 'Request tidak valid.']);
