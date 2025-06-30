<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session only if none is active
}

// Check if the user is logged in, if not, redirect to the sign-in page
if (!isset($_SESSION['user_id'])) {
    header("Location: SignIn.php");
    exit();
}

include '../JS/connect.php'; // Include the database connection
include '../JS/calorie_calculator.php'; // Include the calorie calculator file

$userId = $_SESSION['user_id']; // Get the logged-in user's ID
$username = $_SESSION['username']; // Get the logged-in user's username

// Query each field individually
$age = null;
$height = null;
$weight = null;
$dob = null;
$calorieGoal = null;
$gender = null;

$stmt = $conn->prepare("SELECT age FROM users WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($age);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare("SELECT height FROM users WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($height);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare("SELECT weight FROM users WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($weight);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare("SELECT dob FROM users WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($dob);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare("SELECT calorie_goal FROM users WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($calorieGoal);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare("SELECT gender FROM users WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($gender);
$stmt->fetch();
$stmt->close();

$calculatedCalorieGoal = null; // Initialize variable to store the calculated calorie goal

// Handle form submission to update user details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['field']) && isset($_POST['value'])) {
        $field = $_POST['field'];
        $value = $_POST['value'];

        // Validate the field and value
        if (in_array($field, ['age', 'height', 'weight', 'dob', 'calorie_goal', 'gender'])) {
            $stmt = $conn->prepare("UPDATE users SET $field = ? WHERE user_id = ?");
            $stmt->bind_param("si", $value, $userId);

            if (!$stmt->execute()) {
                die("Error updating database: " . $stmt->error);
            }

            $stmt->close();

            // Refresh the page to display updated data
            header("Location: ProfilePage.php");
            exit();
        }
    }

    // Handle calorie estimator form submission
    if (isset($_POST['activity_level']) && isset($_POST['weight_goal'])) {
        $activityLevel = $_POST['activity_level'];
        $weightGoal = $_POST['weight_goal'];

        // Calculate the calorie goal using the external function
        $calculatedCalorieGoal = calculateCalorieGoal($conn, $userId, $activityLevel, $weightGoal);

        // Update the user's calorie goal in the users table
        $stmt = $conn->prepare("UPDATE users SET calorie_goal = ? WHERE user_id = ?");
        $stmt->bind_param("di", $calculatedCalorieGoal, $userId);
        $stmt->execute();
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <link rel="stylesheet" href="../css-php/styles.css">
    <link rel="stylesheet" href="../css-php/Profile.css">
    <script src="../JS/darkModeToggle.js" defer></script>
    <script>
        function editField(field) {
            const textElement = document.getElementById(`${field}-text`);
            const inputElement = document.getElementById(`${field}-input`);
            const saveButton = document.getElementById(`${field}-save`);
            const cancelButton = document.getElementById(`${field}-cancel`);
            const editButton = document.getElementById(`${field}-edit`);

            // Show input and Save/Cancel buttons, hide text and Edit button
            textElement.style.display = 'none';
            inputElement.style.display = 'inline-block';
            saveButton.style.display = 'inline-block';
            cancelButton.style.display = 'inline-block';
            editButton.style.display = 'none';
        }

        function cancelEdit(field) {
            const textElement = document.getElementById(`${field}-text`);
            const inputElement = document.getElementById(`${field}-input`);
            const saveButton = document.getElementById(`${field}-save`);
            const cancelButton = document.getElementById(`${field}-cancel`);
            const editButton = document.getElementById(`${field}-edit`);

            // Hide input and Save/Cancel buttons, show text and Edit button
            textElement.style.display = 'inline-block';
            inputElement.style.display = 'none';
            saveButton.style.display = 'none';
            cancelButton.style.display = 'none';
            editButton.style.display = 'inline-block';
        }
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const calorieEstimatorForm = document.querySelector(".calorie-estimator form");
            const calorieGoalText = document.getElementById("calorie_goal-text");

            if (calorieEstimatorForm) {
                calorieEstimatorForm.addEventListener("submit", function (event) {
                    event.preventDefault(); // Prevent form submission

                    const formData = new FormData(calorieEstimatorForm);
                    fetch("ProfilePage.php", {
                        method: "POST",
                        body: formData
                    })
                    .then(response => response.text())
                    .then(() => {
                        const activityLevel = formData.get("activity_level");
                        const weightGoal = formData.get("weight_goal");

                        // Fetch the updated calorie goal from the server
                        fetch(`../JS/get_calorie_goal.php?activity_level=${activityLevel}&weight_goal=${weightGoal}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.calorieGoal) {
                                    calorieGoalText.textContent = `${data.calorieGoal} kcal`; // Update the display
                                }
                            })
                            .catch(error => console.error("Error fetching updated calorie goal:", error));
                    })
                    .catch(error => console.error("Error submitting form:", error));
                });
            }
        });
    </script>
</head>
<body>
    <?php include '../css-php/header.php'; ?>

    <div class="wrapper">
        <div class="main-content">
            <main class="profile-page">
                <h1>Hello, <?php echo htmlspecialchars($username); ?>!</h1>

                <!-- Dark Mode Toggle -->
                <div class="dark-mode-toggle">
                    <label for="dark-mode-checkbox">Dark Mode</label>
                    <input type="checkbox" id="dark-mode-checkbox">
                </div>

                <section class="profile-info">
                    <h2>Your Profile Information</h2>
                    <table>
                        <tr>
                            <td><strong>Age:</strong></td>
                            <td>
                                <span id="age-text"><?php echo $age !== null ? htmlspecialchars($age) . " yrs" : 'Please enter age'; ?></span>
                                <form action="ProfilePage.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="field" value="age">
                                    <input type="number" name="value" id="age-input" value="<?php echo htmlspecialchars($age ?? ''); ?>" style="display: none;">
                                    <button type="submit" id="age-save" style="display: none;">Save</button>
                                </form>
                            </td>
                            <td class="edit-column">
                                <a href="javascript:void(0);" id="age-edit" onclick="editField('age')">Edit</a>
                                <a href="javascript:void(0);" id="age-cancel" onclick="cancelEdit('age')" style="display: none;">Cancel</a>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Height (in):</strong></td>
                            <td>
                                <span id="height-text"><?php echo $height !== null ? htmlspecialchars($height) . " in" : 'Please enter height'; ?></span>
                                <form action="ProfilePage.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="field" value="height">
                                    <input type="number" step="0.1" name="value" id="height-input" value="<?php echo htmlspecialchars($height ?? ''); ?>" style="display: none;">
                                    <button type="submit" id="height-save" style="display: none;">Save</button>
                                </form>
                            </td>
                            <td class="edit-column">
                                <a href="javascript:void(0);" id="height-edit" onclick="editField('height')">Edit</a>
                                <a href="javascript:void(0);" id="height-cancel" onclick="cancelEdit('height')" style="display: none;">Cancel</a>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Weight (lbs):</strong></td>
                            <td>
                                <span id="weight-text"><?php echo $weight !== null ? htmlspecialchars($weight) . " lbs" : 'Please enter weight'; ?></span>
                                <form action="ProfilePage.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="field" value="weight">
                                    <input type="number" step="0.1" name="value" id="weight-input" value="<?php echo htmlspecialchars($weight ?? ''); ?>" style="display: none;">
                                    <button type="submit" id="weight-save" style="display: none;">Save</button>
                                </form>
                            </td>
                            <td class="edit-column">
                                <a href="javascript:void(0);" id="weight-edit" onclick="editField('weight')">Edit</a>
                                <a href="javascript:void(0);" id="weight-cancel" onclick="cancelEdit('weight')" style="display: none;">Cancel</a>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Date of Birth:</strong></td>
                            <td>
                                <span id="dob-text"><?php echo $dob !== null ? htmlspecialchars($dob) : 'Please enter date of birth'; ?></span>
                                <form action="ProfilePage.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="field" value="dob">
                                    <input type="date" name="value" id="dob-input" value="<?php echo htmlspecialchars($dob ?? ''); ?>" style="display: none;">
                                    <button type="submit" id="dob-save" style="display: none;">Save</button>
                                </form>
                            </td>
                            <td class="edit-column">
                                <a href="javascript:void(0);" id="dob-edit" onclick="editField('dob')">Edit</a>
                                <a href="javascript:void(0);" id="dob-cancel" onclick="cancelEdit('dob')" style="display: none;">Cancel</a>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Calorie Goal:</strong></td>
                            <td>
                                <span id="calorie_goal-text"><?php echo $calorieGoal !== null ? htmlspecialchars($calorieGoal) . " kcal" : 'Please set your goal'; ?></span>
                                <form action="ProfilePage.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="field" value="calorie_goal">
                                    <input type="number" name="value" id="calorie_goal-input" value="<?php echo htmlspecialchars($calorieGoal ?? ''); ?>" style="display: none;">
                                    <button type="submit" id="calorie_goal-save" style="display: none;">Save</button>
                                </form>
                            </td>
                            <td class="edit-column">
                                <a href="javascript:void(0);" id="calorie_goal-edit" onclick="editField('calorie_goal')">Edit</a>
                                <a href="javascript:void(0);" id="calorie_goal-cancel" onclick="cancelEdit('calorie_goal')" style="display: none;">Cancel</a>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Gender:</strong></td>
                            <td>
                                <span id="gender-text"><?php echo $gender !== null ? htmlspecialchars($gender) : 'Please select gender'; ?></span>
                                <form action="ProfilePage.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="field" value="gender">
                                    <select name="value" id="gender-input" style="display: none;">
                                        <option value="Male" <?php echo $gender === 'Male' ? 'selected' : ''; ?>>Male</option>
                                        <option value="Female" <?php echo $gender === 'Female' ? 'selected' : ''; ?>>Female</option>
                                        <option value="Other" <?php echo $gender === 'Other' ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                    <button type="submit" id="gender-save" style="display: none;">Save</button>
                                </form>
                            </td>
                            <td class="edit-column">
                                <a href="javascript:void(0);" id="gender-edit" onclick="editField('gender')">Edit</a>
                                <a href="javascript:void(0);" id="gender-cancel" onclick="cancelEdit('gender')" style="display: none;">Cancel</a>
                            </td>
                        </tr>
                    </table>
                </section>
                <section class="calorie-estimator">
                    <h2>Estimate Your Calorie Goal Here</h2>
                    <form action="ProfilePage.php" method="POST" style="display: flex; flex-direction: column; gap: 10px; max-width: 400px; margin: 0 auto;">
                        <label for="activity-level">Activity Level:</label>
                        <select name="activity_level" id="activity-level" required>
                            <option value="sedentary">Sedentary (little or no exercise)</option>
                            <option value="light">Lightly active (light exercise/sports 1-3 days/week)</option>
                            <option value="moderate">Moderately active (moderate exercise/sports 3-5 days/week)</option>
                            <option value="active">Active (hard exercise/sports 6-7 days a week)</option>
                            <option value="very-active">Very active (very hard exercise/physical job)</option>
                        </select>

                        <label for="weight-goal">Weight Goal:</label>
                        <select name="weight_goal" id="weight-goal" required>
                            <option value="maintain">Maintain weight</option>
                            <option value="lose">Lose weight</option>
                            <option value="gain">Gain weight</option>
                        </select>

                        <button type="submit" class="button button-primary">Estimate Calorie Goal</button>
                    </form>

                    <p id="calorie-goal-message" style="margin-top: 20px; font-weight: bold; color: green;">
                        Your estimated calorie goal is: <span id="dynamic-calorie-goal"><?php echo htmlspecialchars($calorieGoal ?? 'N/A'); ?></span> kcal.
                    </p>
                </section>
                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        const calorieEstimatorForm = document.querySelector(".calorie-estimator form");
                        const dynamicCalorieGoal = document.getElementById("dynamic-calorie-goal");

                        if (calorieEstimatorForm) {
                            calorieEstimatorForm.addEventListener("submit", function (event) {
                                event.preventDefault(); // Prevent form submission

                                const formData = new FormData(calorieEstimatorForm);
                                fetch("ProfilePage.php", {
                                    method: "POST",
                                    body: formData
                                })
                                .then(response => response.text())
                                .then(() => {
                                    const activityLevel = formData.get("activity_level");
                                    const weightGoal = formData.get("weight_goal");

                                    // Fetch the updated calorie goal from the server
                                    fetch(`../JS/get_calorie_goal.php?activity_level=${activityLevel}&weight_goal=${weightGoal}`)
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.calorieGoal) {
                                                dynamicCalorieGoal.textContent = `${data.calorieGoal}`; // Update the display
                                            }
                                        })
                                        .catch(error => console.error("Error fetching updated calorie goal:", error));
                                })
                                .catch(error => console.error("Error submitting form:", error));
                            });
                        }
                    });
                </script>
            </main>
        </div>
    </div> <!-- Close wrapper -->
    <footer>
        <!-- Footer content -->
        <p>&copy; 2025 BetterUFitness. All rights reserved.</p>
    </footer>
    <script src="../JS/darkModeToggle.js"></script>
</body>
</html>