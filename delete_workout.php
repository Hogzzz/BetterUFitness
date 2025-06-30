<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "error" => "User not logged in."]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION['user_id'];
    $workoutType = $_POST['workout_type'];
    $logDate = $_POST['log_date'];

    // Delete the workout from the workout_log table
    $stmt = $conn->prepare("DELETE FROM workout_log WHERE user_id = ? AND workout_type = ? AND log_date = ?");
    $stmt->bind_param("iss", $userId, $workoutType, $logDate);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
