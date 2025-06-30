<!-- filepath: c:\xampp\htdocs\BetterUFitness\Pages\submit_contact.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Submitted - BetterUFitness</title>
    <link rel="stylesheet" href="../css-php/styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <?php include '../css-php/header.php'; ?> <!-- Include header -->

    <div class="submit-container">
        <?php
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $name = htmlspecialchars($_POST['name']);
            $email = htmlspecialchars($_POST['email']);
            $rating = htmlspecialchars($_POST['rating']);
            $message = htmlspecialchars($_POST['message']);

            // Display a thank-you message
            echo "<h1>Thank You, $name!</h1>";
            echo "<p>Your feedback has been received.</p>";
            echo "<p><strong>Email:</strong> $email</p>";
            echo "<p><strong>Rating:</strong> $rating/5</p>";
            echo "<p><strong>Message:</strong> $message</p>";
        } else {
            echo "<h1>Invalid Request</h1>";
            echo "<p>It seems like you accessed this page incorrectly.</p>";
        }
        ?>
        <a href="../Pages/Contact.php" class="back-button">Go Back to Contact Page</a>
    </div>

    <?php include '../css-php/footer.php'; ?> <!-- Include footer -->
</body>
</html>