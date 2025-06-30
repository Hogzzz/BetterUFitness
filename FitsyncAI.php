<!-- filepath: c:\xampp\htdocs\BetterUFitness\Pages\FitsyncAI.php -->
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: SignIn.php");
    exit();
}
include '../css-php/header.php'; // Include the header
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitSync AI</title>
    <link rel="stylesheet" href="../css-php/styles.css"> <!-- Shared styles -->
    <link rel="stylesheet" href="../css-php/FitsyncAI.css"> <!-- AI-specific styles -->
</head>
<body>
    <header class="dashboard-header">
        <h1>FitSync AI</h1>
    </header>

    <div class="wrapper">
        <div class="main-content">
            <main class="fitsync-ai">
                <section class="ai-options">
                    <h2>Choose Your AI Assistant</h2>
                    <div class="ai-buttons">
                        <button id="workout-ai-button">Workout AI Helper</button>
                        <button id="nutrition-ai-button">Food & Nutrition AI</button>
                    </div>
                </section>

                <!-- Workout AI Section -->
                <section id="workout-ai-section" style="display: none;">
                    <h2>Workout AI Helper</h2>
                    <p>Get personalized workout recommendations and guidance.</p>
                    <div id="workout-ai-content">
                        <!-- Content dynamically loaded via JavaScript -->
                    </div>
                </section>

                <!-- Nutrition AI Section -->
                <section id="nutrition-ai-section" style="display: none;">
                    <h2>Food & Nutrition AI</h2>
                    <p>Get meal recommendations, calorie estimations, and nutritional analysis.</p>
                    <div id="nutrition-ai-content">
                        <!-- Content dynamically loaded via JavaScript -->
                    </div>
                </section>

                <!-- Chatbot Section -->
                <section id="chatbot-section" style="display: none;">
                    <h2 id="chatbot-title">Chat with FitSync AI</h2>
                    <div id="chat-container">
                        <div id="chat-messages"></div>
                        <input type="text" id="chat-input" placeholder="Ask me anything..." autocomplete="off">
                        <button id="chat-send">Send</button>
                    </div>
                </section>
            </main>
        </div> <!-- Close wrapper -->
    </div>
    <footer>
        <!-- Footer content -->
        <p>&copy; 2025 BetterUFitness. All rights reserved.</p>
    </footer>

    <script src="../JS/fitsyncAI.js"></script>
    <script src="../JS/darkModeToggle.js"></script>
</body>
</html>