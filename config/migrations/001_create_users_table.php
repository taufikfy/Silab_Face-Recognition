<?php
return [
    'up' => "
        CREATE TABLE IF NOT EXISTS students (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nim VARCHAR(32) NOT NULL,
            name VARCHAR(100) NOT NULL,
            photo TEXT NOT NULL,
            ragister_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    ",
    'down' => "DROP TABLE IF EXISTS student;"
];
