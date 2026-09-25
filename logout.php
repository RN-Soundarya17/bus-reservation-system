<?php

session_start();

unset($_SESSION["user_id"]);
unset($_SESSION["user_name"]);
unset($_SESSION["user_email"]);

session_destroy();

session_start();

$_SESSION["logout_message"] = "Logged out successfully!";

header("Location: index.php");
exit();

?>