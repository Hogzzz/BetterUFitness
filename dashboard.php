<?php
session_start(); // Start the session

// Check if the user is logged in, if not, redirect to the sign-in page
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Pages/SignIn.php"); // Redirect to sign-in page if not logged in
    exit();
}

include '../JS/connect.php'; // Include the database connection

$userId = $_SESSION['user_id']; // Get the logged-in user's ID
$logDate = date('Y-m-d'); // Get today's date

// Fetch today's activities from the database
$activities = [];
$stmt = $conn->prepare("SELECT workout_type, time FROM workout_log WHERE user_id = ? AND log_date = ?");
$stmt->bind_param("is", $userId, $logDate);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $activities[] = $row; // Store each activity in the array
}

$stmt->close();

$stmt = $conn->prepare("SELECT calorie_goal FROM users WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($calorieGoal);
$stmt->fetch();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BetterUFitness Dashboard</title>
    <link rel="stylesheet" href="../css-php/styles.css">
    <link rel="stylesheet" href= "../css-php/dashboard.css">
</head>
<body>

    <?php include '../css-php/header.php'; ?> <!-- Include Header -->

    <div class="wrapper">
        <div class="main-content">
            <main class="dashboard">

                <!-- Calories Section -->
                <section class="calories">
                    <h2>Calories Summary</h2>
                    <p id="total-calories-text" class="centered-text">Total Calorie Goal: <span id="total-calories"><?php echo htmlspecialchars($calorieGoal ?? 2000); ?></span> kcal</p> <!-- Total calorie goal -->
                    <p id="remaining-calories-text" class="centered-text">Remaining Calories: <span id="remaining-calories">0</span> kcal</p> <!-- Centered text -->
                    <div class="calorie-box">
                        <svg class="progress-ring" width="300" height="300">
                            <!-- Define shadow filter -->
                            <defs>
                                <filter id="ring-shadow" x="-50%" y="-50%" width="200%" height="200%">
                                    <feDropShadow dx="0" dy="4" stdDeviation="4" flood-color="rgba(0, 0, 0, 0.3)" />
                                </filter>
                            </defs>
                            <circle class="progress-ring-background" cx="150" cy="150" r="140" style="stroke: #9BEC00;" /> <!-- Light green (empty) -->
                            <circle class="progress-ring-bar calories-consumed" cx="150" cy="150" r="140" style="stroke: #059212;" /> <!-- Dark green (consumed) -->
                            <circle class="progress-ring-bar calories-burned" cx="150" cy="150" r="140" style="stroke: #FF4D4D;" /> <!-- Red (burned) -->
                        </svg>
                    </div>
                    <div class="calorie-text">
                        <p style="color: red;">Burned: <span id="calories-burned">0</span> kcal</p>
                        <p style="color: green;">Consumed: <span id="calories-consumed">0</span> kcal</p>
                    </div>
                    <div id="calorie-goal" style="display: none;"><?php echo htmlspecialchars($calorieGoal ?? 2000); ?></div>
                    <div id="calories-consumed" style="display: none;">0</div> <!-- Add this hidden element to initialize the consumed calories -->
                    <div id="calories-burned-hidden" style="display: none;">0</div> <!-- Hidden element for calories burned -->
                </section>


                <!-- Other Sections Below Calories Box -->
                <div class="other-sections">
                    <section class="macros">
                        <h2>Macronutrient Breakdown</h2>
                        <div class="macro-box">
                            <p>🥩 Protein: <span id="protein">0g</span></p>
                            <p>🍞 Carbs: <span id="carbs">0g</span></p>
                            <p>🧈 Fats: <span id="fats">0g</span></p>
                        </div>
                    </section>

                    <section class="activity">
                        <h2>Activity Tracker</h2>
                        <div class="activity-box">
                            <p>🏃 Steps Taken: <span id="steps">0</span></p>
                            <p>💪 Workouts Completed: <span id="workouts"><?php echo count($activities); ?></span></p>
                            <ul class="activity-list">
                                <?php if (!empty($activities)): ?>
                                    <?php foreach ($activities as $activity): ?>
                                        <li>
                                            <?php echo htmlspecialchars($activity['workout_type']) . ' - ' . htmlspecialchars($activity['time']) . ' minutes'; ?>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li>No activities logged today.</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </section>

                    <section class="health-stats">
                        <h2>Health Overview</h2>
                        <div class="stats-box">
                            <p>💧 Water Intake: <span id="water-intake">0</span> L</p>
                            <p>🛌 Sleep Hours: <span id="sleep-hours">0</span> hrs</p>
                        </div>
                    </section>
                </div>
            </main>
        </div> <!-- Close wrapper -->
    </div>
    <footer>
        <!-- Footer content -->
        <p>&copy; 2025 BetterUFitness. All rights reserved.</p>
    </footer>

    <script src="../JS/dashboard.js"></script>
    <script src="../JS/darkModeToggle.js"></script>
</body>
</html>

