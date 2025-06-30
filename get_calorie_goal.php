<?php
session_start();
include 'connect.php';
include 'calorie_calculator.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['activity_level']) || !isset($_GET['weight_goal'])) {
    echo json_encode(["calorieGoal" => null]);
    exit();
}

$userId = $_SESSION['user_id'];
$activityLevel = $_GET['activity_level'];
$weightGoal = $_GET['weight_goal'];

$calorieGoal = calculateCalorieGoal($conn, $userId, $activityLevel, $weightGoal);
echo json_encode(["calorieGoal" => $calorieGoal]);
$conn->close();
?>
