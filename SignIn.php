<?php
session_start();
include '../JS/connect.php'; 

// Check if the user has just signed up
if (isset($_GET['signup']) && $_GET['signup'] == 'success') {
    echo "<p>Account created successfully! Please log in.</p>";
}

// Error Message handling
if (isset($_GET['error'])) {
    $errorMessage = '';
    switch ($_GET['error']) {
        case 'emptyfields':
            $errorMessage = 'Please fill in all fields.';
            break;
        case 'invalidpassword':
            $errorMessage = 'Incorrect password.';
            break;
        case 'noaccount':
            $errorMessage = 'No account found with that email or username.';
            break;
    }
    echo "<p class='error'>$errorMessage</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - BetterUFitness</title>
    <link rel="stylesheet" href="../css-php/styles.css"> <!-- Shared styles -->
    <link rel="stylesheet" href="../css-php/signin.css"> <!-- Sign-in specific styles -->
</head>
<body>
    <?php include '../css-php/header.php'; ?>

    <div class="wrapper">
        <div class="main-content">
            <div class="signin-container">
                <h2>Welcome Back to BetterUFitness</h2>
                <p>Log in to continue your fitness journey</p>

                <form action="../JS/loginhandler.php" method="POST">
                    <label for="email_or_username">Email or Username</label>
                    <input type="text" id="email_or_username" name="email_or_username" placeholder="Enter your email or username" required>

                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>

                    <button type="submit">Sign In</button>

                    <p class="signup-link">Don't have an account? <a href="SignUp.php">Sign Up</a></p>
                </form>
            </div>
        </div>
    </div> <!-- Close wrapper -->
    <footer>
        <!-- Footer content -->
        <p>&copy; 2025 BetterUFitness. All rights reserved.</p>
    </footer>
</body>
</html>
