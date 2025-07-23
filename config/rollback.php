<?php
require 'database.php';

// Ambil migrasi terakhir dari log
$stmt = $pdo->query("SELECT * FROM migrations_log ORDER BY id DESC LIMIT 1");
$last = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$last) {
    echo "📭 Tidak ada migrasi yang bisa di-rollback.\n";
    exit;
}

$filename = $last['filename'];
$filepath = __DIR__ . "/migrations/" . $filename;

if (!file_exists($filepath)) {
    echo "❌ File migrasi tidak ditemukan: $filename\n";
    exit;
}

echo "🔁 Rolling back: $filename\n";
try {
    $query = require $filepath;
    $pdo->exec($query['down']);

    // Hapus dari log
    $del = $pdo->prepare("DELETE FROM migrations_log WHERE id = ?");
    $del->execute([$last['id']]);

    echo "✅ Rollback berhasil\n";
} catch (PDOException $e) {
    echo "❌ Rollback gagal: " . $e->getMessage() . "\n";
}
