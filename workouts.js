document.addEventListener("DOMContentLoaded", function () {
    // Handle "Add Workout" button click
    const addItemButton = document.querySelector(".add-item-button");
    if (addItemButton) {
        addItemButton.addEventListener("click", function () {
            const form = document.getElementById("workout-form");
            if (form) {
                form.style.display = form.style.display === "none" ? "block" : "none";
            }
        });
    }

    // Ensure the correct date is passed when submitting the form
    const logDateInput = document.querySelector("input[name='log_date']");
    const now = new Date();
    const offset = now.getTimezoneOffset() * 60000; // Offset in milliseconds
    const localDate = new Date(now.getTime() - offset).toISOString().split('T')[0]; // Format as YYYY-MM-DD
    logDateInput.value = localDate;

    // Function to toggle the "No workouts logged today" message
    function toggleNoWorkoutsMessage() {
        const list = document.getElementById("workout-list");
        const noWorkoutsMessage = document.querySelector("#no-workouts-message");
        if (list && noWorkoutsMessage) {
            if (list.children.length === 0) {
                noWorkoutsMessage.style.display = "block";
            } else {
                noWorkoutsMessage.style.display = "none";
            }
        }
    }

    // Handle delete button clicks
    function attachDeleteHandlers() {
        document.querySelectorAll(".delete-item-button").forEach(button => {
            button.addEventListener("click", function () {
                const workoutType = this.getAttribute("data-workout-type");
                const listItem = this.closest("li");

                if (confirm("Are you sure you want to delete this workout?")) {
                    // Send AJAX request to delete the workout
                    fetch(`../JS/delete_workout.php?workout_type=${encodeURIComponent(workoutType)}`, {
                        method: "GET",
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                listItem?.remove(); // Remove the item from the UI
                                toggleNoWorkoutsMessage(); // Update the message visibility
                            } else {
                                alert("Failed to delete the workout. Please try again.");
                            }
                        })
                        .catch(error => console.error("Error deleting workout:", error));
                }
            });
        });
    }

    attachDeleteHandlers(); // Initial call to attach delete handlers
    toggleNoWorkoutsMessage(); // Initial call to set the correct message visibility
});