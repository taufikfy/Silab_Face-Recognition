<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login.php");
    exit;
}

$totalStudents = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$todayAttendance = $pdo->query("SELECT COUNT(*) FROM attendance WHERE DATE(attended_at) = CURDATE()")->fetchColumn();
$totalRecords = $pdo->query("SELECT COUNT(*) FROM attendance")->fetchColumn();

$todayDetails = $pdo->query("
    SELECT s.nim, s.name, a.attended_at, a.check_out
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
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <!-- Header & Logout -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Dashboard Admin</h3>
        <a href="logout.php" class="btn btn-outline-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>

    <!-- Statistik -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-success shadow-sm">
                <div class="card-body text-center">
                    <h5>Total Mahasiswa</h5>
                    <h3 class="text-success"><?= $totalStudents ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-info shadow-sm">
                <div class="card-body text-center">
                    <h5>Presensi Hari Ini</h5>
                    <h3 class="text-info"><?= $todayAttendance ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-primary shadow-sm">
                <div class="card-body text-center">
                    <h5>Total Presensi</h5>
                    <h3 class="text-primary"><?= $totalRecords ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Aksi Cepat -->
    <div class="mb-4 d-flex justify-content-between flex-wrap gap-2">
        <div>
            <a href="students.php" class="btn btn-primary me-2"><i class="bi bi-people"></i> Kelola Mahasiswa</a>
            <a href="records.php" class="btn btn-secondary"><i class="bi bi-clock-history"></i> Riwayat Presensi</a>
        </div>
        <a href="../index.php" class="btn btn-outline-dark"><i class="bi bi-house-door"></i> Halaman Utama</a>
    </div>

    <!-- Tabel Presensi Hari Ini -->
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
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($todayDetails) === 0): ?>
                            <tr><td colspan="4" class="text-center text-muted py-3">Belum ada presensi hari ini</td></tr>
                        <?php else: ?>
                            <?php foreach ($todayDetails as $record): ?>
                                <tr>
                                    <td><?= htmlspecialchars($record['nim']) ?></td>
                                    <td><?= htmlspecialchars($record['name']) ?></td>
                                    <td><?= date('Y-m-d H:i', strtotime($record['attended_at'])) ?></td>
                                    <td><?= $record['check_out'] ? date('Y-m-d H:i', strtotime($record['check_out'])) : '-' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
