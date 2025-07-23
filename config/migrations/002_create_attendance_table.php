<?php
// config/migrations/002_create_attendance_table.php

return [
    'up' => "
        CREATE TABLE IF NOT EXISTS attendance (
            id INT AUTO_INCREMENT PRIMARY KEY,
            student_id INT NOT NULL,
            attended_at DATETIME NOT NULL,
            check_out DATETIME DEFAULT NULL,
            status ENUM('masuk', 'keluar') NOT NULL,
            FOREIGN KEY (student_id) REFERENCES students(id)
                ON DELETE CASCADE
        ) ENGINE=InnoDB;
    ",
    'down' => "DROP TABLE IF EXISTS attendance;"
];

?>