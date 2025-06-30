<?php
session_start(); // Start the session

date_default_timezone_set('America/New_York'); // Ensure timezone is set

include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "error" => "User not logged in."]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Validate input data
    $food_name = $_GET['food_name'] ?? '';
    $log_date = $_GET['log_date'] ?? '';

    // Debugging: Log the received log_date before processing
    error_log("Received Log Date: $log_date");

    // Ensure the log date is interpreted in the correct timezone
    $log_date = date('Y-m-d', strtotime($log_date));

    if (empty($food_name) || empty($log_date)) {
        echo json_encode(["success" => false, "error" => "Invalid input data."]);
        exit();
    }

    $user_id = $_SESSION['user_id'];

    // Debugging: Log the processed log_date
    error_log("Processed Log Date: $log_date");

    // Delete the food item from the database
    $stmt = $conn->prepare("DELETE FROM nutrition_log WHERE user_id = ? AND food_name = ? AND log_date = ?");
    $stmt->bind_param("iss", $user_id, $food_name, $log_date);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(["success" => true]);
        } else {
            // Debugging: Log if no rows were affected
            error_log("No rows affected. Food item not found.");
            echo json_encode(["success" => false, "error" => "Food item not found."]);
        }
    } else {
        // Debugging: Log the SQL error
        error_log("SQL Error: " . $stmt->error);
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>