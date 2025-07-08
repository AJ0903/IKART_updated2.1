<?php
session_start();
session_unset();
session_destroy();

// Determine where to redirect based on the previous session
if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'admin') !== false) {
    header("Location: admin_login.php");
} else {
    header("Location: login.html");
}
exit();
?>
