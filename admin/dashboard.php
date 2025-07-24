<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

// auth.php sudah menjalankan session_start() dan proteksi
// jadi tidak perlu diulang di sini

$totalStudents = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$todayAttendance = $pdo->query("SELECT COUNT(DISTINCT student_id) FROM attendance WHERE DATE(attended_at) = CURDATE()")->fetchColumn();
$totalRecords = $pdo->query("SELECT COUNT(*) FROM attendance")->fetchColumn();

$todayDetails = $pdo->query("
    SELECT s.nim, s.name, a.attended_at, a.check_out, a.status
    FROM attendance a
    JOIN students s ON s.id = a.student_id
    WHERE DATE(a.attended_at) = CURDATE()
    ORDER BY a.attended_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - SILAB</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Dashboard Admin</h3>
        <a href="logout.php" class="btn btn-outline-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>

    <div class="mb-4 d-flex justify-content-between flex-wrap gap-2">
        <div>
            <a href="students.php" class="btn btn-primary me-2"><i class="bi bi-people"></i> Kelola Mahasiswa</a>
            <a href="records.php" class="btn btn-secondary"><i class="bi bi-clock-history"></i> Riwayat Presensi</a>
        </div>
        <a href="../index.php" class="btn btn-outline-dark"><i class="bi bi-house-door"></i> Halaman Utama</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            Presensi Hari Ini
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>NIM</th>
                            <th>Nama</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($todayDetails)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-3">Belum ada presensi hari ini</td></tr>
                        <?php else: ?>
                            <?php foreach ($todayDetails as $record): ?>
                                <tr>
                                    <td><?= htmlspecialchars($record['nim']) ?></td>
                                    <td><?= htmlspecialchars($record['name']) ?></td>
                                    <td><?= date('H:i:s', strtotime($record['attended_at'])) ?></td>
                                    <td><?= $record['check_out'] ? date('H:i:s', strtotime($record['check_out'])) : '-' ?></td>
                                    <td>
                                        <span class="badge bg-<?= $record['status'] == 'masuk' ? 'success' : 'secondary' ?>">
                                            <?= htmlspecialchars($record['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>