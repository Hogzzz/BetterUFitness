<?php
session_start();

// Unset session variables explicitly
unset($_SESSION['email']);
unset($_SESSION['username']);

// Destroy the session
session_destroy();

// Redirect to signin page
header("Location: ../Pages/SignIn.php");
exit();
?>