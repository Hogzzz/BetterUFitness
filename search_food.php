<?php
// filepath: c:\xampp\htdocs\BetterUFitness\css-php\search_food.php

// Database connection
$servername = "localhost";
$username = "root"; // Replace with your DB username
$password = ""; // Replace with your DB password
$dbname = "betterufitness"; // Replace with your DB name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the search term from the AJAX request
$searchTerm = $_GET['query'] ?? '';

if (!empty($searchTerm)) {
    // Query the database for matching food items
    $sql = "SELECT FoodItem, Cals_per100grams FROM calories WHERE FoodItem LIKE ? LIMIT 10";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%" . $searchTerm . "%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch results and return as JSON
    $foodItems = [];
    while ($row = $result->fetch_assoc()) {
        $foodItems[] = $row;
    }

    echo json_encode($foodItems);

    $stmt->close();
}
$conn->close();
?>