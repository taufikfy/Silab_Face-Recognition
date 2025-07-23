<?php
// config/migrations/003_create_admins_table.php

// Hash untuk password 'admin123'
$passwordHash = '$2y$10$fA1.eW5qJ.sU2r.Y8w.fIuGZkdNiA4p3J1fG9E/8lK3eH5nO0c7dG';

return [
    'up' => "
        CREATE TABLE IF NOT EXISTS admins (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;

        INSERT INTO admins (username, password) VALUES ('admin', '{$passwordHash}');
    ",
    'down' => "DROP TABLE IF EXISTS admins;"
];
?>