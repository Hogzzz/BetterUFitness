<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Pages/SignIn.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION['user_id'];
    $workoutType = $_POST['workout_type'];
    $duration = intval($_POST['duration']);
    
    if (isset($_POST['log_date']) && !empty($_POST['log_date'])) {
        $logDate = $_POST['log_date'];
    } else {
        $logDate = date('Y-m-d'); // Fallback to current date if not provided
    }

    // Fetch user details
    $stmt = $conn->prepare("SELECT gender, age, height, weight FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($gender, $age, $height, $weight);
    $stmt->fetch();
    $stmt->close();

    // Find the best match in the calories_burnt table
    $stmt = $conn->prepare("
        SELECT duration, calories 
        FROM calories_burnt 
        WHERE gender = ? 
        ORDER BY ABS(age - ?) + ABS(height - ?) + ABS(weight - ?), duration DESC 
        LIMIT 1
    ");
    $stmt->bind_param("siii", $gender, $age, $height, $weight);
    $stmt->execute();
    $stmt->bind_result($bestMatchDuration, $bestMatchCalories);
    $stmt->fetch();
    $stmt->close();

    if ($bestMatchDuration && $bestMatchCalories) {
        // Calculate calories burned based on the ratio of input duration to best match duration
        $caloriesBurned = ($duration / $bestMatchDuration) * $bestMatchCalories;
    } else {
        $caloriesBurned = 0; // Default to 0 if no match is found
    }

    // Insert the workout into the workout_log table, including calories burned
    $stmt = $conn->prepare("
        INSERT INTO workout_log (user_id, workout_type, time, log_date, calories_burned) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("isisd", $userId, $workoutType, $duration, $logDate, $caloriesBurned);

    if (!$stmt->execute()) {
        error_log("Error inserting workout: " . $stmt->error); // Log any errors
    }

    $stmt->close();
    $conn->close();
}

// Redirect back to the Workouts page
header("Location: ../Pages/Workouts.php");
exit();
?>
