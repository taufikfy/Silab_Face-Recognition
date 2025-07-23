<?php
// config/migrate.php
require 'database.php';

echo "Memulai proses migrasi...\n\n";

// Pastikan table log sudah ada
$pdo->exec("CREATE TABLE IF NOT EXISTS migrations_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    migrated_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$migrations = glob(__DIR__ . '/migrations/*.php');
sort($migrations); // Pastikan urutan file benar

foreach ($migrations as $migration) {
    $filename = basename($migration);

    // Cek apakah migrasi ini sudah pernah dijalankan
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM migrations_log WHERE filename = ?");
    $stmt->execute([$filename]);

    if ($stmt->fetchColumn() > 0) {
        echo "⏭  Lewati: $filename (sudah dijalankan)\n";
        continue;
    }

    echo "⚙️  Menjalankan: $filename\n";
    try {
        $query = require $migration;
        if (isset($query['up'])) {
            $pdo->exec($query['up']);

            // Catat ke log
            $log = $pdo->prepare("INSERT INTO migrations_log (filename) VALUES (?)");
            $log->execute([$filename]);
            
            echo "✅  Sukses\n";
        } else {
            echo "⚠️  Peringatan: Tidak ada query 'up' di file $filename\n";
        }
    } catch (PDOException $e) {
        echo "❌  Error: " . $e->getMessage() . "\n";
    }
}

echo "\nProses migrasi selesai.\n";
?>