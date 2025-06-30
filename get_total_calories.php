<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["totalCalories" => 0]);
    exit();
}

$userId = $_SESSION['user_id'];
$logDate = date('Y-m-d');

$stmt = $conn->prepare("SELECT SUM(calories) AS totalCalories FROM nutrition_log WHERE user_id = ? AND log_date = ?");
$stmt->bind_param("is", $userId, $logDate);
$stmt->execute();
$stmt->bind_result($totalCalories);
$stmt->fetch();
$stmt->close();
$conn->close();

echo json_encode(["totalCalories" => $totalCalories ?? 0]);
?>
