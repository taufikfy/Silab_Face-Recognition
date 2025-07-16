<?php
session_start();
session_unset();  // Hapus semua session
session_destroy();
header("Location: ../login.php");
exit;
