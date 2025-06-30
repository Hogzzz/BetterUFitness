<?php
function calculateCalorieGoal($conn, $userId, $activityLevel, $weightGoal) {
    // Fetch user's age, weight, height, and gender from the users table
    $stmt = $conn->prepare("SELECT age, weight, height, gender FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($userAge, $userWeightLbs, $userHeightInches, $userGender);
    $stmt->fetch();
    $stmt->close();

    // Convert user's weight from lbs to kgs and height from inches to cm for comparison with bmr_dataset
    $userWeightKg = $userWeightLbs / 2.20462;
    $userHeightCm = $userHeightInches * 2.54;

    // Find the closest BMR match in the bmr_dataset table
    $stmt = $conn->prepare("
        SELECT BMR 
        FROM bmr_dataset 
        WHERE gender = ? 
        ORDER BY ABS(age - ?) + ABS(weight - ?) + ABS(height - ?) ASC 
        LIMIT 1
    ");
    $stmt->bind_param("siii", $userGender, $userAge, $userWeightKg, $userHeightCm);
    $stmt->execute();
    $stmt->bind_result($matchedBMR);
    $stmt->fetch();
    $stmt->close();

    // Calculate calorie goal based on activity level
    $activityMultiplier = match ($activityLevel) {
        'sedentary' => 1.2,
        'light' => 1.375,
        'moderate' => 1.55,
        'active' => 1.725,
        'very-active' => 1.9,
        default => 1.2,
    };
    $calorieGoal = $matchedBMR * $activityMultiplier;

    // Adjust calorie goal based on weight goal
    if ($weightGoal === 'lose') {
        $calorieGoal -= 1000;
    } elseif ($weightGoal === 'gain') {
        $calorieGoal += 1000;
    }

    return round($calorieGoal);
}
?>
