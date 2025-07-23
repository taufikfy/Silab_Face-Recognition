<?php
header('Content-Type: application/json');
require_once __DIR__ . '/includes/functions.php';

$response = ['status' => 'error', 'message' => 'Request tidak valid.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Di JavaScript kita menggunakan FormData, jadi data ada di $_POST
    $nim = $_POST['identity'] ?? null;
    $action = $_POST['action'] ?? null;

    if ($nim && $action) {
        $student = getStudentByNIM($nim);
        if ($student) {
            if ($action === 'checkin') {
                $response = checkIn($student['id']);
            } elseif ($action === 'checkout') {
                $response = checkOut($student['id']);
            } else {
                $response = ['status' => 'error', 'message' => 'Aksi tidak valid.'];
            }
        } else {
            $response = ['status' => 'error', 'message' => 'Mahasiswa tidak ditemukan.'];
        }
    } else {
        $response = ['status' => 'error', 'message' => 'Data identitas atau aksi tidak lengkap.'];
    }
}

echo json_encode($response);
?>