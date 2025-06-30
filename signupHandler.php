<?php
include 'connect.php'; // Ensure this file connects to your database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if the email already exists in the database
    $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // If the email already exists, show an error
        $error_message = "An account with this email already exists.";
    } else {
        // Insert the new user into the database
        $stmt = $conn->prepare("INSERT INTO users (email, username, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $username, $password);

        if ($stmt->execute()) {
            // After successful sign-up, don't log them in automatically.
            // Instead, redirect them to the sign-in page
            header("Location: ../Pages/signin.php?signup=success");
            exit();
        } else {
            // Error creating the account
            $error_message = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
    $conn->close();
}
?>
