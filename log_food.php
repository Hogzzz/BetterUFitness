<?php
session_start(); // Start the session

include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "error" => "User not logged in."]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    // Debugging: Log the received data
    error_log("Received data: " . print_r($data, true));

    // Validate input data
    if (empty($data['food_name']) || empty($data['serving_amount']) || empty($data['meal_type'])) {
        echo json_encode(["success" => false, "error" => "Invalid input data."]);
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $food_name = $data['food_name'];
    $serving_amount = floatval($data['serving_amount']);
    $meal_type = $data['meal_type'];
    $log_date = date("Y-m-d"); // Use today's date

    // Fetch calories per 100g from the database
    $stmt = $conn->prepare("SELECT Cals_per100grams FROM calories WHERE FoodItem = ?");
    $stmt->bind_param("s", $food_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(["success" => false, "error" => "Food item not found in database."]);
        exit();
    }

    $row = $result->fetch_assoc();
    $calories_per_100g = floatval($row['Cals_per100grams']);

    // Calculate total calories
    $calories = ($serving_amount / 100) * $calories_per_100g;

    // Debugging: Log the calculated calories
    error_log("Calculated calories: $calories");

    // Insert the meal into the nutrition_log table, including serving_amount
    $stmt = $conn->prepare("INSERT INTO nutrition_log (user_id, food_name, serving_amount, calories, log_date, meal_type) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isidss", $user_id, $food_name, $serving_amount, $calories, $log_date, $meal_type);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        // Debugging: Log the SQL error
        error_log("SQL Error: " . $stmt->error);
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>

