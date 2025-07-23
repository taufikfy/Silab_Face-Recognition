<?php
return [
    'up' => "
        CREATE TABLE IF NOT EXISTS migrations_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            filename VARCHAR(255) NOT NULL,
            migrated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    ",
    'down' => "DROP TABLE IF EXISTS migrations_log;"
];
