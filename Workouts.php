<!-- filepath: c:\xampp\htdocs\BetterUFitness\Pages\Workouts.php -->
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: SignIn.php"); // Redirect to sign-in page if not logged in
    exit();
}
include '../css-php/header.php'; // Include the header
include '../JS/connect.php'; // Include the database connection

// Set the default timezone to your desired timezone
date_default_timezone_set('America/New_York'); // Set timezone to EST

$currentDay = date("l, F j, Y");
$logDate = date('Y-m-d'); // Ensure correct date format
$userId = $_SESSION['user_id']; // Assuming the user ID is stored in the session

// Fetch saved workouts for the current day, including calories burned
$savedWorkouts = [];
$stmt = $conn->prepare("SELECT workout_type, time, calories_burned FROM workout_log WHERE user_id = ? AND log_date = ?");
$stmt->bind_param("is", $userId, $logDate);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $savedWorkouts[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workout Tracker</title>
    <link rel="stylesheet" href="../css-php/styles.css"> <!-- Shared styles -->
    <link rel="stylesheet" href="../css-php/Workout.css"> <!-- Workouts-specific styles -->
</head>
<body>
    <div class="wrapper">
        <header class="dashboard-header">
            <h1>Workout Tracker</h1>
        </header>
        <div class="main-content">
            <p class="date-display">Today is: <strong><?php echo $currentDay; ?></strong></p>

            <form action="../JS/log_workout.php" method="post" class="nutrition-form">
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($userId); ?>"> <!-- Pass user ID -->
                <input type="hidden" name="log_date" value="<?php echo htmlspecialchars($logDate); ?>"> <!-- Ensure correct date format -->

                <div class="meal-section section-container">
                    <h2>Workouts</h2>
                    <ul id="workout-list" class="meal-list">
                        <?php if (!empty($savedWorkouts)): ?>
                            <?php foreach ($savedWorkouts as $workout): ?>
                                <li>
                                    <?php echo htmlspecialchars($workout['workout_type']) . ' - ' . htmlspecialchars($workout['time']) . ' minutes'; ?>
                                    <?php if (isset($workout['calories_burned'])): ?>
                                        (<?php echo round($workout['calories_burned'], 2); ?> calories burned)
                                    <?php endif; ?>
                                    <button type="button" class="button button-danger delete-item-button" 
                                            data-workout-type="<?php echo htmlspecialchars($workout['workout_type']); ?>" 
                                            data-log-date="<?php echo htmlspecialchars($logDate); ?>">🗑️</button>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li id="no-workouts-message">No workouts logged today.</li>
                        <?php endif; ?>
                    </ul>
                    <button type="button" class="button button-success add-item-button" data-meal="workout">Add Workout</button>
                    <div class="add-item-form" id="workout-form" style="display: none;">
                        <input type="text" class="workout-type" name="workout_type" placeholder="Workout Type" required>
                        <input type="number" class="workout-time" name="duration" placeholder="Time (minutes)" required>
                        <button type="submit" class="button button-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <footer>
        <p>&copy; 2025 BetterUFitness. All rights reserved.</p>
    </footer>
    <script src="../JS/workouts.js"></script>
    <script src="../JS/darkModeToggle.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const deleteButtons = document.querySelectorAll(".delete-item-button");

        deleteButtons.forEach(button => {
            button.addEventListener("click", () => {
                const workoutType = button.getAttribute("data-workout-type");
                const logDate = button.getAttribute("data-log-date");

                // Ensure only one confirmation dialog is displayed
                if (confirm(`Are you sure you want to delete the workout: ${workoutType}?`)) {
                    fetch("../JS/delete_workout.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded",
                        },
                        body: `workout_type=${encodeURIComponent(workoutType)}&log_date=${encodeURIComponent(logDate)}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            button.parentElement.remove(); // Remove the workout from the list
                            if (document.querySelectorAll("#workout-list li").length === 0) {
                                document.getElementById("workout-list").innerHTML = '<li id="no-workouts-message">No workouts logged today.</li>';
                            }
                        } else {
                            alert("Error deleting workout: " + (data.error || "Unknown error"));
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        alert("An error occurred while deleting the workout.");
                    });
                }
            });
        });
    });
    </script>
</body>
</html>