<?php
session_start();
include 'connect.php'; // Ensure this file connects to your database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the user input
    $emailOrUsername = $_POST['email_or_username']; // User can enter either email or username
    $password = $_POST['password'];

    // Prevent empty inputs
    if (empty($emailOrUsername) || empty($password)) {
        header("Location: ../Pages/SignIn.php?error=emptyfields");
        exit();
    }

    // Check if the input is an email or username and prepare the query accordingly
    if (filter_var($emailOrUsername, FILTER_VALIDATE_EMAIL)) {
        // The input is an email, search the database using email
        $stmt = $conn->prepare("SELECT user_id, email, username, password FROM users WHERE email = ?");
    } else {
        // The input is a username, search the database using username
        $stmt = $conn->prepare("SELECT user_id, email, username, password FROM users WHERE username = ?");
    }

    // Bind the parameter and execute the query
    $stmt->bind_param("s", $emailOrUsername);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($userId, $dbEmail, $dbUsername, $dbPassword);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $dbPassword)) {
            // Regenerate session ID to prevent session fixation
            session_regenerate_id();

            // Store session variables
            $_SESSION['user_id'] = $userId; // Store the user ID in the session
            $_SESSION['email'] = $dbEmail;
            $_SESSION['username'] = $dbUsername;

            // Redirect to the dashboard after login
            header("Location: ../Pages/dashboard.php");
            exit();
        } else {
            // Password is incorrect
            header("Location: ../Pages/SignIn.php?error=invalidpassword");
            exit();
        }
    } else {
        // No account found for the provided email/username
        header("Location: ../Pages/SignIn.php?error=noaccount");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
