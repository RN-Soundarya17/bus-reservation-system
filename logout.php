<?php

session_start();

// Remove all admin session data
unset($_SESSION["admin_id"]);
unset($_SESSION["admin_name"]);
unset($_SESSION["admin_email"]);

// Destroy the session
session_destroy();

// Redirect to admin login
header("Location: login.php");
exit();

?>