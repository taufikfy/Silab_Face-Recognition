<?php
// filepath: c:\xampp\htdocs\Silab-Face Recognition\index.php

require_once __DIR__ . '/includes/functions.php';

// Inisialisasi koneksi PDO jika belum ada di functions.php
// Pastikan kredensial ini sesuai dengan pengaturan database Anda
if (!isset($pdo) || !$pdo instanceof PDO) {
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=silab_db;charset=utf8mb4', 'root', ''); // <-- SESUAIKAN DENGAN KREDENSIAL DATABASE ANDA
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Koneksi database gagal: " . $e->getMessage());
    }
}

// Mengambil data statistik dari database
$totalStudents = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$todayAttendance = $pdo->query("SELECT COUNT(DISTINCT student_id) FROM attendance WHERE DATE(attended_at) = CURDATE()")->fetchColumn();
$totalRecords = $pdo->query("SELECT COUNT(DISTINCT student_id) FROM attendance WHERE DATE(attended_at) = CURDATE()")->fetchColumn();

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
</head>
<body>
<?php
echo "<p>Waktu Server PHP Saat Ini: " . date('Y-m-d H:i:s') . "</p>";
?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">SILAB TIF UNISSULA</a>
            <a href="login.php" class="btn btn-outline-light ms-auto">Admin Login</a>
        </div>
    </nav>

    <div class="container py-4">
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

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white p-0">
                        <ul class="nav nav-pills nav-justified bg-light p-2 rounded-top-3 shadow-sm" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active text-dark fw-semibold" id="checkin-tab" data-bs-toggle="tab" data-bs-target="#checkin" type="button" role="tab" aria-controls="checkin" aria-selected="true">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link text-dark fw-semibold" id="checkout-tab" data-bs-toggle="tab" data-bs-target="#checkout" type="button" role="tab" aria-controls="checkout" aria-selected="false">
                                    <i class="bi bi-box-arrow-right me-1"></i> Keluar
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body tab-content">
                        <div class="tab-pane fade show active" id="checkin" role="tabpanel" aria-labelledby="checkin-tab">
                            <h5 class="text-center mb-3">Presensi Masuk</h5>
                            <div class="video-container mb-3">
                                <video id="video-in" autoplay muted playsinline></video>
                                <canvas id="canvas-in"></canvas>
                            </div>
                            <div id="status-in" class="alert alert-info text-center">Camera is loading...</div>
                            <button id="btn-checkin" class="btn btn-success w-100 mb-2" disabled><i class="bi bi-box-arrow-in-right"></i> Presensi Masuk</button>
                            <div id="result-in" class="text-center mt-2"></div>
                        </div>
                        <div class="tab-pane fade" id="checkout" role="tabpanel" aria-labelledby="checkout-tab">
                            <h5 class="text-center mb-3">Presensi Keluar</h5>
                            <div class="video-container mb-3">
                                <video id="video-out" autoplay muted playsinline></video>
                                <canvas id="canvas-out"></canvas>
                            </div>
                            <div id="status-out" class="alert alert-info text-center">Camera is loading...</div>
                            <button id="btn-checkout" class="btn btn-danger w-100 mb-2" disabled><i class="bi bi-box-arrow-right"></i> Presensi Keluar</button>
                            <div id="result-out" class="text-center mt-2"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Registrasi Mahasiswa</h5>
                    </div>
                    <div class="card-body">
                        <a href="register.php" class="btn btn-primary w-100"><i class="bi bi-person-plus"></i> Tambah Mahasiswa</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="confirmationModalLabel">Konfirmasi Presensi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-3">
                        <div class="detail-item">
                            <span class="detail-label">NIM:</span>
                            <span id="studentNIMConfirm" class="detail-value fw-bold"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Nama:</span>
                            <span id="studentNameConfirm" class="detail-value fw-bold"></span>
                        </div>
                    </div>
                    
                    <img id="framedImageConfirm" src="" alt="Framed Face" class="img-fluid rounded mb-3" style="display: none;">
                    
                    <p class="mt-3">Apakah data di atas sudah benar?</p>
                </div>
                <div class="modal-footer d-flex justify-content-around">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle me-1"></i> Batal</button>
                    <button type="button" class="btn btn-primary" id="confirmAttendanceBtn"><i class="bi bi-check-circle me-1"></i> Konfirmasi</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.js"></script>
    
    <script>
        // Variabel global untuk menyimpan data sementara saat konfirmasi
        let currentIdentity = null;     // Akan berisi NIM dari Flask API
        let currentStudentName = null;  // Akan berisi nama mahasiswa dari Flask API
        let currentEndpoint = null;     // Akan berisi "checkin" atau "checkout"
        let currentResultEl = null;     // Referensi ke elemen div hasil (misal result-in atau result-out)
        let currentStatusEl = null;     // Referensi ke elemen div status (misal status-in atau status-out)

        // Variabel global untuk menyimpan stream kamera aktif
        let currentCheckinStream = null;
        let currentCheckoutStream = null;


        /**
         * Memuat model Face-API.js yang diperlukan untuk deteksi wajah.
         * Menggunakan TinyFaceDetector untuk performa yang lebih baik.
         */
        async function loadModels() {
            try {
                // Memuat model detektor wajah (TinyFaceDetector) dari GitHub
                await faceapi.nets.tinyFaceDetector.loadFromUri('https://raw.githubusercontent.com/justadudewhohacks/face-api.js/refs/heads/master/weights');
                console.log("Face-API models loaded successfully.");
            } catch (error) {
                console.error("Failed to load Face-API models:", error);
                alert("Gagal memuat model Face-API. Presensi mungkin tidak berfungsi. Periksa koneksi internet atau path model.");
            }
        }

        /**
         * Menginisialisasi kamera dan stream video ke elemen <video>.
         * Mengatur ukuran video dan canvas agar sesuai dengan container 1:1.
         * @param {HTMLVideoElement} videoEl - Elemen video.
         * @param {HTMLElement} statusEl - Elemen untuk menampilkan status kamera.
         * @param {HTMLButtonElement} btnEl - Tombol yang akan diaktifkan setelah kamera siap.
         * @param {HTMLCanvasElement} canvasEl - Elemen canvas untuk menggambar deteksi.
         * @returns {MediaStream|null} - Mengembalikan objek MediaStream jika berhasil, null jika gagal.
         */
        async function setupCamera(videoEl, statusEl, btnEl, canvasEl) {
            console.log(`Attempting to setup camera for: ${videoEl.id}`); 
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                videoEl.srcObject = stream;
                statusEl.textContent = "Camera ready.";
                statusEl.className = "alert alert-success";
                btnEl.disabled = false; // Aktifkan tombol presensi
                console.log(`Camera ${videoEl.id} started successfully.`);

                // Ketika metadata video sudah dimuat (resolusi, durasi, dll.)
                videoEl.onloadedmetadata = () => {
                    console.log(`Metadata loaded for ${videoEl.id}`);
                    const videoContainer = videoEl.closest('.video-container');
                    const containerWidth = videoContainer.offsetWidth;
                    const containerHeight = videoContainer.offsetHeight; // Akan sama dengan containerWidth karena CSS

                    videoEl.width = containerWidth;
                    videoEl.height = containerHeight;
                    canvasEl.width = containerWidth;
                    canvasEl.height = containerHeight;
                    
                    runLiveDetection(videoEl, canvasEl);
                };
                return stream; // Penting: kembalikan stream agar bisa dihentikan nanti
            } catch (error) {
                console.error(`Error accessing camera ${videoEl.id}:`, error);
                statusEl.textContent = "Kamera tidak bisa diakses. Pastikan browser memiliki izin atau tidak ada aplikasi lain yang menggunakan kamera.";
                statusEl.className = "alert alert-danger";
                btnEl.disabled = true; // Nonaktifkan tombol presensi jika kamera gagal
                return null;
            }
        }

        /**
         * Menghentikan stream kamera dan membersihkan interval deteksi.
         * @param {HTMLVideoElement} videoEl - Elemen video yang stream-nya akan dihentikan.
         * @param {HTMLCanvasElement} canvasEl - Elemen canvas yang akan dibersihkan.
         */
        function stopCamera(videoEl, canvasEl) {
            console.log(`Stopping camera for: ${videoEl.id}`);
            // Hentikan semua track di dalam stream
            if (videoEl.srcObject) {
                videoEl.srcObject.getTracks().forEach(track => track.stop());
                videoEl.srcObject = null; // Hapus referensi stream dari elemen video
            }
            // Hentikan interval deteksi wajah
            if (videoEl._detectionInterval) {
                clearInterval(videoEl._detectionInterval);
                videoEl._detectionInterval = null;
            }
            // Bersihkan canvas
            const ctx = canvasEl.getContext('2d');
            ctx.clearRect(0, 0, canvasEl.width, canvasEl.height);
            console.log(`Camera ${videoEl.id} stopped and canvas cleared.`);
        }


        /**
         * Menjalankan deteksi wajah secara periodik pada stream video
         * dan menggambar kotak deteksi di canvas.
         * @param {HTMLVideoElement} videoEl - Elemen video.
         * @param {HTMLCanvasElement} canvasEl - Elemen canvas untuk menggambar deteksi.
         */
        async function runLiveDetection(videoEl, canvasEl) {
            console.log(`Starting live detection for: ${videoEl.id}`); 
            const displaySize = { width: videoEl.width, height: videoEl.height };
            faceapi.matchDimensions(canvasEl, displaySize); 

            // Hapus interval sebelumnya untuk elemen ini jika ada, untuk menghindari duplikasi
            if (videoEl._detectionInterval) {
                clearInterval(videoEl._detectionInterval);
                console.log(`Cleared previous interval for ${videoEl.id}`); 
            }

            // Simpan ID interval ke properti elemen video agar bisa di-clear
            videoEl._detectionInterval = setInterval(async () => {
                if (!videoEl.srcObject || videoEl.paused || videoEl.ended) {
                    // console.log(`Detection paused/ended for ${videoEl.id}`); 
                    const ctx = canvasEl.getContext('2d');
                    ctx.clearRect(0, 0, canvasEl.width, canvasEl.height); 
                    return;
                }

                // console.log(`Performing detection for ${videoEl.id}`); // Debugging intensif
                const detections = await faceapi.detectAllFaces(videoEl, new faceapi.TinyFaceDetectorOptions());
                const resizedDetections = faceapi.resizeResults(detections, displaySize);

                const ctx = canvasEl.getContext('2d');
                ctx.clearRect(0, 0, canvasEl.width, canvasEl.height);
                faceapi.draw.drawDetections(canvasEl, resizedDetections);
            }, 100);
        }

        /**
         * Mengambil frame dari video, mengirimnya ke Flask API untuk verifikasi,
         * dan menampilkan modal konfirmasi jika wajah dikenali.
         * @param {HTMLVideoElement} videoEl - Elemen video dari kamera.
         * @param {HTMLCanvasElement} canvasEl - Elemen canvas.
         * @param {HTMLElement} statusEl - Elemen untuk menampilkan status.
         * @param {HTMLElement} resultEl - Elemen untuk menampilkan hasil.
         * @param {string} endpoint - Jenis aksi ('checkin' atau 'checkout').
         */
        async function detectAndSend(videoEl, canvasEl, statusEl, resultEl, endpoint) {
            statusEl.textContent = "Mendeteksi dan memverifikasi wajah...";
            resultEl.innerHTML = ""; // Bersihkan hasil sebelumnya

            // Ambil frame dari video
            const ctx = canvasEl.getContext('2d');
            canvasEl.width = videoEl.width; 
            canvasEl.height = videoEl.height; 
            ctx.drawImage(videoEl, 0, 0, canvasEl.width, canvasEl.height);
            const imageData = canvasEl.toDataURL('image/jpeg'); // Konversi frame ke Base64 JPEG

            try {
                // Mengirim gambar ke Flask API untuk verifikasi
                const verifyRes = await fetch('http://localhost:5000/verify', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    // Mengirim hanya data Base64 (tanpa 'data:image/jpeg;base64,')
                    body: JSON.stringify({ image: imageData.split(',')[1] }) 
                });
                const data = await verifyRes.json(); // Parse respons JSON dari Flask

                if (data.status === "success") {
                    // Simpan data untuk digunakan di modal konfirmasi
                    currentIdentity = data.identity; 
                    currentStudentName = data.name || "Nama tidak tersedia"; 
                    currentEndpoint = endpoint;
                    currentResultEl = resultEl;
                    currentStatusEl = statusEl;

                    // Mengisi modal konfirmasi dengan NIM dan Nama mahasiswa
                    document.getElementById('studentNIMConfirm').textContent = currentIdentity;
                    document.getElementById('studentNameConfirm').textContent = currentStudentName;

                    // Menampilkan gambar wajah yang terbingkai di modal
                    if (data.framed_image) {
                        document.getElementById('framedImageConfirm').src = `data:image/jpeg;base64,${data.framed_image}`;
                        document.getElementById('framedImageConfirm').style.display = 'block';
                    } else {
                        document.getElementById('framedImageConfirm').style.display = 'none';
                    }
                    
                    // Tampilkan modal konfirmasi
                    const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
                    confirmationModal.show();

                    statusEl.textContent = "Wajah dikenali. Menunggu konfirmasi...";
                } else {
                    // Wajah tidak dikenali atau error dari Flask
                    statusEl.textContent = "Wajah tidak dikenali.";
                    resultEl.innerHTML = `<span class='text-danger'>${data.message || "Wajah tidak terdaftar atau tidak jelas."}</span>`;
                }
            } catch (error) {
                console.error("Error during detection or sending to Flask server:", error);
                statusEl.textContent = "Gagal mengirim ke server.";
                resultEl.innerHTML = "<span class='text-danger'>Terjadi kesalahan koneksi atau server Flask. Pastikan Flask API berjalan.</span>";
            }
        }

        /**
         * Mengirimkan catatan presensi (check-in/check-out) ke server PHP.
         * Fungsi ini dipanggil setelah pengguna mengkonfirmasi di modal.
         */
        async function sendAttendanceRecord() {
            // Pastikan semua data yang diperlukan tersedia
            if (!currentIdentity || !currentEndpoint || !currentResultEl || !currentStatusEl) {
                console.error("Data presensi tidak lengkap untuk dikirim.");
                return;
            }

            const formData = new FormData();
            formData.append('identity', currentIdentity); 
            formData.append('action', currentEndpoint); 

            try {
                // Mengirim data presensi ke attendance.php (script PHP Anda)
                const attendanceRes = await fetch('attendance.php', {
                    method: 'POST',
                    body: formData // FormData secara otomatis mengatur Content-Type
                });
                const attendance = await attendanceRes.json(); // Parse respons JSON dari PHP

                // Tampilkan hasil presensi dari server PHP
                let alertClass = "text-danger"; // Default: merah
                if (attendance.status === "success") {
                    alertClass = "text-success";
                } else if (attendance.status === "info") {
                    alertClass = "text-info";
                }
                currentResultEl.innerHTML = `<span class='${alertClass}'>${attendance.message}</span>`;
                currentStatusEl.textContent = "Selesai.";

            } catch (error) {
                console.error("Error sending attendance record to PHP:", error);
                currentResultEl.innerHTML = "<span class='text-danger'>Terjadi kesalahan jaringan atau server PHP saat mengirim data presensi.</span>";
                currentStatusEl.textContent = "Error!";
            } finally {
                // Reset variabel setelah pengiriman, baik berhasil maupun gagal
                currentIdentity = null;
                currentStudentName = null;
                currentEndpoint = null;
                currentResultEl = null;
                currentStatusEl = null;
            }
        }

        // Fungsi yang dijalankan saat halaman dimuat sepenuhnya
        window.onload = async function () {
            // Dapatkan referensi elemen DOM untuk tab Check-in
            const videoIn = document.getElementById('video-in');
            const canvasIn = document.getElementById('canvas-in');
            const statusIn = document.getElementById('status-in');
            const resultIn = document.getElementById('result-in');
            const btnCheckin = document.getElementById('btn-checkin');

            // Dapatkan referensi elemen DOM untuk tab Check-out
            const videoOut = document.getElementById('video-out');
            const canvasOut = document.getElementById('canvas-out');
            const statusOut = document.getElementById('status-out');
            const resultOut = document.getElementById('result-out');
            const btnCheckout = document.getElementById('btn-checkout');

            await loadModels();
            
            // Inisialisasi hanya kamera check-in saat halaman dimuat (karena tab check-in aktif secara default)
            currentCheckinStream = await setupCamera(videoIn, statusIn, btnCheckin, canvasIn);

            btnCheckin.addEventListener('click', () => {
                detectAndSend(videoIn, canvasIn, statusIn, resultIn, "checkin");
            });

            btnCheckout.addEventListener('click', () => {
                detectAndSend(videoOut, canvasOut, statusOut, resultOut, "checkout");
            });

            document.getElementById('confirmAttendanceBtn').addEventListener('click', () => {
                sendAttendanceRecord();
                const confirmationModal = bootstrap.Modal.getInstance(document.getElementById('confirmationModal'));
                if (confirmationModal) {
                    confirmationModal.hide();
                }
            });

            // Event listener untuk mengelola kamera saat tab diaktifkan/dinonaktifkan
            const checkinTabTrigger = document.getElementById('checkin-tab');
            const checkoutTabTrigger = document.getElementById('checkout-tab');

            if (checkinTabTrigger) {
                checkinTabTrigger.addEventListener('shown.bs.tab', async () => {
                    console.log("Check-in tab activated.");
                    // Jika kamera checkout aktif, hentikan
                    if (currentCheckoutStream) {
                        stopCamera(videoOut, canvasOut);
                        currentCheckoutStream = null;
                    }
                    // Mulai kamera check-in jika belum aktif
                    if (!currentCheckinStream || !currentCheckinStream.active) {
                        currentCheckinStream = await setupCamera(videoIn, statusIn, btnCheckin, canvasIn);
                    }
                    resultIn.innerHTML = "";
                    statusIn.textContent = "Camera ready.";
                    statusIn.className = "alert alert-success";
                });
            }
            if (checkoutTabTrigger) {
                checkoutTabTrigger.addEventListener('shown.bs.tab', async () => {
                    console.log("Check-out tab activated.");
                    // Jika kamera checkin aktif, hentikan
                    if (currentCheckinStream) {
                        stopCamera(videoIn, canvasIn);
                        currentCheckinStream = null;
                    }
                    // Mulai kamera checkout jika belum aktif
                    if (!currentCheckoutStream || !currentCheckoutStream.active) {
                        currentCheckoutStream = await setupCamera(videoOut, statusOut, btnCheckout, canvasOut);
                    }
                    resultOut.innerHTML = "";
                    statusOut.textContent = "Camera ready.";
                    statusOut.className = "alert alert-success";
                });
            }

            // Opsional: Hentikan semua kamera saat halaman ditutup/dipindahkan
            window.addEventListener('beforeunload', () => {
                if (currentCheckinStream) {
                    stopCamera(videoIn, canvasIn);
                }
                if (currentCheckoutStream) {
                    stopCamera(videoOut, canvasOut);
                }
            });
        };
    </script>
</body>
</html>