<!-- filepath: c:\xampp\htdocs\BetterUFitness\Pages\Contact.php -->
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: SignIn.php"); // Redirect to sign-in page if not logged in
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - BetterUFitness</title>
    <link rel="stylesheet" href="../css-php/styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <?php include '../css-php/header.php'; ?> <!-- Include header -->

    <div class="wrapper">
        <div class="main-content">
            <div class="contact-container">
                <h1>Contact Us</h1>
                <p>If you have any questions, feedback, or concerns, feel free to reach out to us!</p>
                <p><strong>Email:</strong> betterufitness@gmail.com</p>
                <p><strong>Phone:</strong> +1 (555) 123-4567</p>

                <h2>Leave Us a Message</h2>
                <form action="../JS/submit_contact.php" method="POST" class="contact-form">
                    <label for="name">Your Name:</label>
                    <input type="text" id="name" name="name" placeholder="Enter your name" required>

                    <label for="email">Your Email:</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>

                    <label for="rating">Rate Us:</label>
                    <select id="rating" name="rating" required>
                        <option value="" disabled selected>Select a rating</option>
                        <option value="5">5 - Excellent</option>
                        <option value="4">4 - Good</option>
                        <option value="3">3 - Average</option>
                        <option value="2">2 - Poor</option>
                        <option value="1">1 - Very Poor</option>
                    </select>

                    <label for="message">Your Message:</label>
                    <textarea id="message" name="message" rows="5" placeholder="Leave your message here..." required></textarea>

                    <button type="submit">Submit</button>
                </form>
            </div>
        </div>
    </div> <!-- Close wrapper -->
    <footer>
        <!-- Footer content -->
        <p>&copy; 2025 BetterUFitness. All rights reserved.</p>
    </footer>
    <script src="../JS/darkModeToggle.js"></script>
</body>
</html>