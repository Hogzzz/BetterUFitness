<!-- filepath: c:\xampp\htdocs\BetterUFitness\Pages\Calendar.php -->
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: SignIn.php"); // Redirect to sign-in page if not logged in
    exit();
}

include '../css-php/header.php'; // Include the header
include '../JS/connect.php'; // Include the database connection

$userId = $_SESSION['user_id']; // Get the logged-in user's ID

// Set the default time zone to match your database's time zone
date_default_timezone_set('America/New_York'); // Set to your desired timezone

// Debugging: Log the current time in UTC and America/New_York
error_log("UTC Time: " . gmdate('Y-m-d H:i:s'));
error_log("New York Time: " . date('Y-m-d H:i:s'));

// Fetch the requested month from the query string or default to the current month
$currentMonth = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
$monthName = date('F Y', strtotime($currentMonth . '-01')); // Get the full month name and year

$stmt = $conn->prepare("SELECT workout_type, DATE(log_date) AS log_date FROM workout_log WHERE user_id = ? AND log_date LIKE ?");
$monthPattern = $currentMonth . '%';
$stmt->bind_param("is", $userId, $monthPattern);
$stmt->execute();
$result = $stmt->get_result();

$workouts = [];
while ($row = $result->fetch_assoc()) {
    $date = $row['log_date']; // Extract the date part only
    $workouts[$date][] = $row['workout_type']; // Group workouts by date
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workout Calendar</title>
    <link rel="stylesheet" href="../css-php/styles.css"> <!-- Shared styles -->
    <link rel="stylesheet" href="../css-php/calendar.css"> <!-- Calendar-specific styles -->
</head>
<body>
    <div class="wrapper">
        <div class="main-content">
            <header class="dashboard-header">
                <h1><?php echo $monthName; ?></h1> <!-- Display the current month's name -->
            </header>

            <div class="calendar-container">
                <?php
                // Generate the calendar for the current month
                $daysInMonth = date('t'); // Total days in the current month
                $firstDayOfMonth = date('N', strtotime($currentMonth . '-01')); // Day of the week for the 1st (1 = Monday, 7 = Sunday)
                $totalCells = $daysInMonth + $firstDayOfMonth - 1; // Total cells needed (days + empty cells at the start)
                $rows = ceil($totalCells / 7); // Calculate the number of rows needed

                echo '<div class="calendar">';
                echo '<div class="calendar-body">';

                // Add empty cells for days before the 1st
                for ($i = 1; $i < $firstDayOfMonth; $i++) {
                    echo '<div class="calendar-cell empty"></div>';
                }

                // Add cells for each day of the month
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $date = $currentMonth . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
                    $class = ($date === date('Y-m-d')) ? 'calendar-cell today' : 'calendar-cell';
                    echo "<div class='$class'>";
                    echo "<div class='date'>$day</div>";

                    // Display workouts for the day
                    if (isset($workouts[$date])) {
                        echo '<ul class="workout-list">';
                        foreach ($workouts[$date] as $workout) {
                            echo "<li>$workout</li>";
                        }
                        echo '</ul>';
                    }

                    echo '</div>';
                }

                echo '</div>'; // Close calendar-body
                echo '</div>'; // Close calendar
                ?>
            </div>

            <div class="calendar-navigation">
                <a href="?month=<?php echo date('Y-m', strtotime($currentMonth . ' -1 month')); ?>" class="prev-button">&larr; Previous</a>
                <a href="?month=<?php echo date('Y-m', strtotime($currentMonth . ' +1 month')); ?>" class="next-button">Next &rarr;</a>
            </div>
        </div> <!-- Close main-content -->
    </div> <!-- Close wrapper -->
    <footer>
        <p>&copy; 2025 BetterUFitness. All rights reserved.</p>
    </footer>
    <script src="../JS/darkModeToggle.js"></script>
</body>
</html>