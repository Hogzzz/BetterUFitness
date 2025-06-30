<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session only if not already active
}

if (!isset($_SESSION['user_id'])) {
    header("Location: SignIn.php"); // Redirect to sign-in page if not logged in
    exit();
}

include '../css-php/header.php'; // Include the header
include '../JS/connect.php'; // Corrected path to connect.php

// Set the default timezone
date_default_timezone_set('America/New_York'); // Set timezone to EST

// Debugging: Log the current time in UTC and America/New_York
error_log("UTC Time: " . gmdate('Y-m-d H:i:s'));
error_log("New York Time: " . date('Y-m-d H:i:s'));

// Correctly calculate today's date and retrieve the user ID
date_default_timezone_set('America/New_York');
error_log("PHP Timezone: " . date_default_timezone_get());
error_log("Current Date: " . date('Y-m-d H:i:s'));
$currentDay = date("l, F j, Y");
$logDate = date('Y-m-d');
error_log("Log Date: " . $logDate);

$userId = $_SESSION['user_id']; // Align with Workouts.php

// Fetch saved meals for the current day
$savedMeals = [];
$stmt = $conn->prepare("SELECT meal_type, food_name, serving_amount, calories FROM nutrition_log WHERE user_id = ? AND log_date = ?");
$stmt->bind_param("is", $userId, $logDate);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $savedMeals[$row['meal_type']][] = $row;
}

$totalCaloriesConsumed = 0;

foreach ($savedMeals as $mealType => $meals) {
    foreach ($meals as $meal) {
        $totalCaloriesConsumed += $meal['calories'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nutrition Tracker</title>
    <link rel="stylesheet" href="../css-php/styles.css"> <!-- Shared styles -->
    <link rel="stylesheet" href="../css-php/Nutrition.css"> <!-- Nutrition-specific styles -->
    <script>
        let totalCaloriesConsumed = <?php echo round($totalCaloriesConsumed); ?>;
        let calorieGoal = <?php echo htmlspecialchars($calorieGoal ?? 2000); ?>;

        function updateCalorieRing() {
            const circleCircumference = 880;
            const consumedProgress = Math.min((totalCaloriesConsumed / calorieGoal) * circleCircumference, circleCircumference);

            document.querySelector('.calories-consumed').style.strokeDashoffset = circleCircumference - consumedProgress;
            document.getElementById('calories-consumed').textContent = totalCaloriesConsumed;
        }

        document.addEventListener("DOMContentLoaded", function () {
            updateCalorieRing();

            document.querySelectorAll(".submit-item-button").forEach(button => {
                button.addEventListener("click", function () {
                    const mealType = this.getAttribute("data-meal");
                    const form = document.getElementById(`${mealType}-form`);
                    const foodServingsInput = form.querySelector(".food-servings");
                    const caloriesPer100g = parseFloat(form.querySelector(".food-name").getAttribute("data-calories"));
                    const servings = parseFloat(foodServingsInput.value.trim());

                    if (servings && caloriesPer100g) {
                        const totalCalories = Math.round((servings / 100) * caloriesPer100g);
                        totalCaloriesConsumed += totalCalories;
                        updateCalorieRing();
                    }
                });
            });
        });
    </script>
</head>
<body>
    <div class="wrapper">
        <header class="dashboard-header">
            <h1>Nutrition Tracker</h1>
        </header>
        <div class="main-content">
            <p class="date-display">Today is: <strong><?php echo $currentDay; ?></strong></p>

            <form action="../JS/log_food.php" method="post" class="nutrition-form">
                <input type="hidden" name="log_date" value="<?php echo date('Y-m-d'); ?>"> <!-- Ensure correct date format -->

                <?php foreach (['breakfast', 'lunch', 'dinner'] as $mealType): ?>
                <div class="meal-section section-container">
                    <h2><?php echo ucfirst($mealType); ?></h2>
                    <ul id="<?php echo $mealType; ?>-list" class="meal-list">
                        <?php if (!empty($savedMeals[$mealType])): ?>
                            <?php foreach ($savedMeals[$mealType] as $meal): ?>
                                <li>
                                    <?php echo htmlspecialchars($meal['food_name']) . ' - ' . htmlspecialchars($meal['serving_amount']) . ' grams (' . htmlspecialchars($meal['calories']) . ' calories)'; ?>
                                    <button type="button" class="button button-danger delete-item-button" data-food-name="<?php echo htmlspecialchars($meal['food_name']); ?>">🗑️</button>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>No items logged for <?php echo ucfirst($mealType); ?>.</li>
                        <?php endif; ?>
                    </ul>
                    <button type="button" class="button button-success add-item-button" data-meal="<?php echo $mealType; ?>">Add <?php echo ucfirst($mealType); ?></button>
                    <div class="add-item-form" id="<?php echo $mealType; ?>-form" style="display: none;">
                        <input type="text" class="food-name" placeholder="Food Name" data-meal="<?php echo $mealType; ?>">
                        <ul class="autocomplete-list"></ul> <!-- Dropdown suggestions will appear here -->
                        <input type="number" class="food-servings" placeholder="Servings (grams)" data-meal="<?php echo $mealType; ?>">
                        <button type="button" class="button button-primary submit-item-button" data-meal="<?php echo $mealType; ?>">Submit</button>
                    </div>
                </div>
                <?php endforeach; ?>

                <!-- Removed the Save Meals button -->
            </form>

            <div id="calorie-goal" style="display: none;"><?php echo htmlspecialchars($calorieGoal ?? 2000); ?></div>
            <div id="calories-consumed" style="display: none;">0</div> <!-- Ensure this element exists -->

            <script src="../JS/nutrition.js"></script>
        </div>
    </div>
    <footer>
        <p>&copy; 2025 BetterUFitness. All rights reserved.</p>
    </footer>
    <script src="../JS/darkModeToggle.js"></script>
</body>
</html>
