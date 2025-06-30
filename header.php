<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session only if none is active
}

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    include '../JS/connect.php'; // Include the database connection

    $userId = $_SESSION['user_id']; // Get the logged-in user's ID

    // Fetch the profile picture path from the database
    /*
    $stmt = $conn->prepare("SELECT profile_picture FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();
    $stmt->close();

    // Use the uploaded profile picture if it exists, otherwise use the default image
    $profilePicture = !empty($userData['profile_picture']) ? $userData['profile_picture'] : '../images/BlankUser.png';

    $conn->close();
    */

    // Set to default profile picture
    $profilePicture = '../images/BlankUser.png';
} else {
    $profilePicture = '../images/BlankUser.png'; // Default image for non-logged-in users
}
?>

<html>
    <body>
        <header class="dashboard-header">
            <div class="logo">
                <img src="../images/logo.jpg" alt="Fitness Logo">
                <h1>Better U Fitness</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="Nutrition.php">Nutrition</a></li>
                    <li><a href="Workouts.php">Workouts</a></li>
                    <li><a href="FitsyncAI.php">FitSync AI</a></li>
                    <li><a href="Calendar.php">Calendar</a></li>
                    <li><a href="Contact.php">Contact</a></li>
                </ul>
            </nav>
            <!-- User Profile Area -->
            <div class="user-profile">
                <img src="<?php echo htmlspecialchars($profilePicture); ?>" alt="User Profile Picture" class="profile-pic">
                <a href="ProfilePage.php" class="settings-button">Settings</a>
                <a href="../JS/signout.php" class="logout-button">Logout</a>
            </div>
        </header>
    </body>
</html>
