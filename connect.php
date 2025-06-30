<?php
// Set the default timezone
date_default_timezone_set('America/New_York'); // Set timezone to EST

$servername = "localhost";
$user = "root";
$password = "";
$dbname = "betterufitness";

//create connection
$conn = mysqli_connect($servername, $user, $password, $dbname);

//check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
} 

?>