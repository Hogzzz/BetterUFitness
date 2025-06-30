<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["totalCaloriesBurned" => 0]);
    exit();
}

$userId = $_SESSION['user_id'];
$logDate = date('Y-m-d');

$stmt = $conn->prepare("SELECT SUM(calories_burned) AS totalCaloriesBurned FROM workout_log WHERE user_id = ? AND log_date = ?");
$stmt->bind_param("is", $userId, $logDate);
$stmt->execute();
$stmt->bind_result($totalCaloriesBurned);
$stmt->fetch();
$stmt->close();
$conn->close();

echo json_encode(["totalCaloriesBurned" => $totalCaloriesBurned ?? 0]);
?>
