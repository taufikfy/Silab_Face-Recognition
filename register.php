<?php
require_once __DIR__ . '/includes/functions.php';

$error = '';
$success = '';

// Cek jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nim = $_POST['nim'] ?? '';
    $name = $_POST['name'] ?? '';
    $faceData = $_POST['face_data'] ?? '';

    if (empty($nim) || empty($name) || empty($faceData)) {
        $error = "Semua data wajib diisi, termasuk foto wajah.";
    } else {
        // Cek apakah NIM sudah terdaftar
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE nim = ?");
        $stmt->execute([$nim]);
        if ($stmt->fetchColumn() > 0) {
            $error = "NIM sudah terdaftar. Silakan gunakan NIM lain.";
        } else {
            // Proses dan simpan foto ke folder /faces/
            // Format nama file: NIM_NamaLengkap.jpg
            $cleanName = preg_replace('/\s+/', '_', $name);
            $photoFilename = $nim . '_' . $cleanName . '.jpg';
            $photoPath = __DIR__ . '/faces/' . $photoFilename;

            // Hapus header base64 dari data gambar
            $faceData = str_replace('data:image/jpeg;base64,', '', $faceData);
            $faceData = str_replace(' ', '+', $faceData);
            
            // Simpan file foto
            if (file_put_contents($photoPath, base64_decode($faceData))) {
                // Panggil fungsi untuk menyimpan data ke database
                if (registerStudent($nim, $name, $photoFilename)) {
                    $success = "Mahasiswa '$name' dengan NIM '$nim' berhasil diregistrasi!";
                } else {
                    $error = "Gagal menyimpan data ke database.";
                    // Hapus foto jika gagal simpan ke DB untuk konsistensi
                    unlink($photoPath);
                }
            } else {
                $error = "Gagal menyimpan file foto. Periksa izin folder /faces/.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi Mahasiswa - SILAB</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">SILAB TIF UNISSULA</a>
        </div>
    </nav>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Registrasi Mahasiswa Baru</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                        <?php endif; ?>
                        
                        <form method="post" id="registration-form">
                            <div class="mb-3">
                                <label for="nim" class="form-label">NIM</label>
                                <input type="text" class="form-control" id="nim" name="nim" required>
                            </div>
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Pendaftaran Wajah</label>
                                <div class="video-container mb-2">
                                    <video id="video" autoplay muted playsinline></video>
                                    <canvas id="canvas" class="d-none"></canvas> </div>
                                <div id="detection-status" class="alert alert-info text-center">Arahkan wajah ke kamera...</div>
                                <button type="button" id="capture-btn" class="btn btn-secondary w-100"><i class="bi bi-camera"></i> Ambil Foto Wajah</button>
                            </div>
                            <input type="hidden" id="face_data" name="face_data">
                            <button type="submit" id="register-btn" class="btn btn-primary w-100 mt-3" disabled>Registrasi Mahasiswa</button>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <a href="index.php">Kembali ke Halaman Presensi</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const detectionStatus = document.getElementById('detection-status');
            const captureBtn = document.getElementById('capture-btn');
            const faceDataInput = document.getElementById('face_data');
            const registerBtn = document.getElementById('register-btn');

            async function setupCamera() {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                    video.srcObject = stream;
                    detectionStatus.textContent = "Kamera siap. Silakan klik tombol 'Ambil Foto Wajah'.";
                    detectionStatus.className = "alert alert-success text-center";
                } catch (err) {
                    console.error("Error accessing camera: ", err);
                    detectionStatus.textContent = "Tidak dapat mengakses kamera. Pastikan Anda memberikan izin.";
                    detectionStatus.className = "alert alert-danger text-center";
                }
            }

            captureBtn.addEventListener('click', function() {
                // Pastikan video sudah memiliki ukuran
                if (video.videoWidth > 0 && video.videoHeight > 0) {
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    const context = canvas.getContext('2d');
                    
                    // Balikkan gambar secara horizontal sebelum mengambil foto
                    context.translate(video.videoWidth, 0);
                    context.scale(-1, 1);
                    context.drawImage(video, 0, 0, canvas.width, canvas.height);
                    
                    const imageData = canvas.toDataURL('image/jpeg');
                    faceDataInput.value = imageData;
                    
                    detectionStatus.textContent = "Foto wajah berhasil diambil!";
                    detectionStatus.className = "alert alert-success text-center";
                    registerBtn.disabled = false; // Aktifkan tombol registrasi
                } else {
                    detectionStatus.textContent = "Kamera belum siap sepenuhnya. Mohon tunggu sebentar.";
                    detectionStatus.className = "alert alert-warning text-center";
                }
            });

            setupCamera();
        });
    </script>
</body>
</html>