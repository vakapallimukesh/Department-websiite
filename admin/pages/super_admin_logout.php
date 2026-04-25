<?php
session_start();

// Destroy all session variables related to super admin
unset($_SESSION['superadmin_logged_in']);
unset($_SESSION['superadmin_username']);
unset($_SESSION['username']);
unset($_SESSION['last_activity']);
unset($_SESSION['expire_time']);

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: login.php");
exit();
?>