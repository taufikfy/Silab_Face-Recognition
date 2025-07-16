<?php
require_once __DIR__ . '/includes/functions.php';

$students = [];
$attendance = [];

$students = $pdo->query("SELECT * FROM students ORDER BY name")->fetchAll();
$attendance = getAttendanceRecords(['attended_at' => date('Y-m-d')]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>SILAB TIF UNISSULA</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
  <link href="assets/css/style.css" rel="stylesheet"/>
  <style>
    body { background: #f8f9fa; }
    .video-container {
      position: relative;
      width: 100%;
      height: 300px;
      border: 1px solid #dee2e6;
      border-radius: 0.5rem;
      overflow: hidden;
      background: #000;
    }
    video, canvas {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    canvas {
      position: absolute;
      top: 0;
      left: 0;
      pointer-events: none;
    }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
      <a class="navbar-brand" href="#">SILAB TIF UNISSULA</a>
      <a href="login.php" class="btn btn-outline-light ms-auto">Admin Login</a>
    </div>
  </nav>

  <div class="container py-4">
    <div class="row g-4">
      <div class="col-lg-6">
        <div class="card shadow-sm">
          <div class="card-header bg-primary text-white">
            <ul class="nav nav-pills nav-justified bg-light p-1 rounded-3 shadow-sm" role="tablist" style="gap: 10px;">
            <li class="nav-item" role="presentation">
                <button class="nav-link active text-dark fw-semibold" data-bs-toggle="tab" data-bs-target="#checkin" type="button" role="tab">
                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link text-dark fw-semibold" data-bs-toggle="tab" data-bs-target="#checkout" type="button" role="tab">
                <i class="bi bi-box-arrow-right me-1"></i> Keluar
                </button>
            </li>
            </ul>
          </div>
          <div class="card-body tab-content">
            <!-- Presensi Masuk -->
            <div class="tab-pane fade show active" id="checkin" role="tabpanel">
              <h5 class="text-center mb-3">Presensi Masuk</h5>
              <div class="video-container mb-3">
                <video id="video-in" autoplay muted playsinline></video>
                <canvas id="canvas-in"></canvas>
              </div>
              <div id="status-in" class="alert alert-info text-center">Camera is loading...</div>
              <button id="btn-checkin" class="btn btn-success w-100 mb-2"><i class="bi bi-box-arrow-in-right"></i> Presensi Masuk</button>
              <div id="result-in" class="text-center mt-2"></div>
            </div>
            <!-- Presensi Keluar -->
            <div class="tab-pane fade" id="checkout" role="tabpanel">
              <h5 class="text-center mb-3">Presensi Keluar</h5>
              <div class="video-container mb-3">
                <video id="video-out" autoplay muted playsinline></video>
                <canvas id="canvas-out"></canvas>
              </div>
              <div id="status-out" class="alert alert-info text-center">Camera is loading...</div>
              <button id="btn-checkout" class="btn btn-danger w-100 mb-2"><i class="bi bi-box-arrow-right"></i> Presensi Keluar</button>
              <div id="result-out" class="text-center mt-2"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Data Registrasi dan Presensi Hari Ini -->
      <div class="col-lg-6">
        <div class="card shadow-sm mb-4">
          <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Registrasi Mahasiswa</h5>
          </div>
          <div class="card-body">
            <a href="register.php" class="btn btn-primary w-100"><i class="bi bi-person-plus"></i> Tambah Mahasiswa</a>
          </div>
        </div>

        <div class="card shadow-sm">
          <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Presensi Hari Ini</h5>
            <form method="get" class="d-inline">
              <button type="submit" name="search" class="btn btn-sm btn-light"><i class="bi bi-arrow-clockwise"></i></button>
            </form>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($attendance as $record): ?>
                    <tr>
                      <td><?= htmlspecialchars($record['nim']) ?></td>
                      <td><?= htmlspecialchars($record['name']) ?></td>
                      <td><span class="badge bg-success"><?= date('Y-m-d H:i', strtotime($record['attended_at'])) ?></span></td>
                      <td><?= isset($record['check_out']) ? '<span class="badge bg-danger">'.date('Y-m-d H:i', strtotime($record['check_out'])).'</span>' : '<span class="text-muted">-</span>' ?></td>
                    </tr>
                  <?php endforeach; ?>
                  <?php if (count($attendance) === 0): ?>
                    <tr><td colspan="4" class="text-center text-muted py-3">Belum ada presensi hari ini</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.js"></script>
  <script>
    async function loadModels() {
      await faceapi.nets.tinyFaceDetector.loadFromUri('https://raw.githubusercontent.com/justadudewhohacks/face-api.js/refs/heads/master/weights');
    }

    async function setupCamera(videoEl, statusEl, btnEl, canvasEl) {
      try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        videoEl.srcObject = stream;
        statusEl.textContent = "Camera ready.";
        statusEl.className = "alert alert-success";
        btnEl.disabled = false;

        videoEl.onloadedmetadata = () => {
          runLiveDetection(videoEl, canvasEl);
        };
      } catch {
        statusEl.textContent = "Kamera tidak bisa diakses.";
        statusEl.className = "alert alert-danger";
        btnEl.disabled = true;
      }
    }

    async function runLiveDetection(videoEl, canvasEl) {
      const displaySize = { width: videoEl.videoWidth, height: videoEl.videoHeight };
      faceapi.matchDimensions(canvasEl, displaySize);

      setInterval(async () => {
        const detections = await faceapi.detectAllFaces(videoEl, new faceapi.TinyFaceDetectorOptions());
        const resizedDetections = faceapi.resizeResults(detections, displaySize);

        const ctx = canvasEl.getContext('2d');
        ctx.clearRect(0, 0, canvasEl.width, canvasEl.height);
        faceapi.draw.drawDetections(canvasEl, resizedDetections);
      }, 100);
    }

    async function detectAndSend(videoEl, canvasEl, statusEl, resultEl, endpoint) {
      statusEl.textContent = "Mendeteksi wajah...";
      const ctx = canvasEl.getContext('2d');
      canvasEl.width = videoEl.videoWidth;
      canvasEl.height = videoEl.videoHeight;
      ctx.drawImage(videoEl, 0, 0);
      const imageData = canvasEl.toDataURL('image/jpeg');

      try {
        const verifyRes = await fetch('http://localhost:5000/verify', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ image: imageData.split(',')[1] })
        });
        const data = await verifyRes.json();
        if (data.status === "success") {
          statusEl.textContent = "Wajah dikenali!";
          resultEl.innerHTML = "Identitas: <b>" + data.identity + "</b>";
          if (data.framed_image) {
            resultEl.innerHTML += `<br><img src="data:image/jpeg;base64,${data.framed_image}" style="width:100%;margin-top:10px;">`;
          }

          const attendanceRes = await fetch('attendance.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'identity=' + encodeURIComponent(data.identity) + '&action=' + endpoint
          });
          const attendance = await attendanceRes.json();
          resultEl.innerHTML += "<br><span class='text-" + (attendance.status === "success" ? "success" : (attendance.status === "info" ? "info" : "danger")) + "'>" + attendance.message + "</span>";
        } else {
          statusEl.textContent = "Wajah tidak dikenali.";
          resultEl.innerHTML = "";
        }
      } catch {
        statusEl.textContent = "Gagal mengirim ke server.";
        resultEl.innerHTML = "";
      }
    }

    window.onload = async function () {
      const videoIn = document.getElementById('video-in');
      const canvasIn = document.getElementById('canvas-in');
      const statusIn = document.getElementById('status-in');
      const resultIn = document.getElementById('result-in');
      const btnCheckin = document.getElementById('btn-checkin');

      const videoOut = document.getElementById('video-out');
      const canvasOut = document.getElementById('canvas-out');
      const statusOut = document.getElementById('status-out');
      const resultOut = document.getElementById('result-out');
      const btnCheckout = document.getElementById('btn-checkout');

      await loadModels();
      setupCamera(videoIn, statusIn, btnCheckin, canvasIn);
      setupCamera(videoOut, statusOut, btnCheckout, canvasOut);

      btnCheckin.addEventListener('click', () => {
        detectAndSend(videoIn, canvasIn, statusIn, resultIn, "checkin");
      });

      btnCheckout.addEventListener('click', () => {
        detectAndSend(videoOut, canvasOut, statusOut, resultOut, "checkout");
      });
    };
  </script>
</body>
</html>
