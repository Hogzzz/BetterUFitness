// filepath: c:\xampp\htdocs\BetterUFitness\JS\nutrition.js
document.addEventListener("DOMContentLoaded", function () {
    const circleCircumference = 880;

    function updateCalorieRing() {
        const calorieGoalElement = document.getElementById("calorie-goal");
        const caloriesConsumedElement = document.getElementById("calories-consumed");
        const caloriesConsumedRing = document.querySelector('.calories-consumed');

        // Ensure all required elements exist before proceeding
        if (!calorieGoalElement || !caloriesConsumedElement || !caloriesConsumedRing) {
            console.warn("One or more elements required for updating the calorie ring are missing.");
            return;
        }

        fetch("../JS/get_total_calories.php")
            .then(response => response.json())
            .then(data => {
                const totalCaloriesConsumed = data.totalCalories || 0;
                const calorieGoal = parseInt(calorieGoalElement.textContent) || 2000;

                const consumedProgress = Math.min((totalCaloriesConsumed / calorieGoal) * circleCircumference, circleCircumference);
                caloriesConsumedRing.style.strokeDashoffset = circleCircumference - consumedProgress;
                caloriesConsumedElement.textContent = totalCaloriesConsumed;
            })
            .catch(error => console.error("Error updating calorie ring:", error));
    }

    // Dropdown autocomplete for food name
    document.querySelectorAll(".food-name").forEach(input => {
        input.addEventListener("input", function () {
            const query = this.value.trim();
            const autocompleteList = this.nextElementSibling;

            if (query.length > 2) {
                // Fetch matching food items from the server
                fetch(`../JS/search_food.php?query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        autocompleteList.innerHTML = ""; // Clear previous suggestions
                        data.forEach(item => {
                            const suggestion = document.createElement("li");
                            suggestion.textContent = `${item.FoodItem} (${item.Cals_per100grams} cal/100g)`;
                            suggestion.setAttribute("data-calories", item.Cals_per100grams);
                            suggestion.addEventListener("click", () => {
                                this.value = item.FoodItem;
                                this.setAttribute("data-calories", item.Cals_per100grams);
                                autocompleteList.innerHTML = ""; // Clear suggestions
                            });
                            autocompleteList.appendChild(suggestion);
                        });
                    })
                    .catch(error => console.error("Error fetching food suggestions:", error));
            } else {
                autocompleteList.innerHTML = ""; // Clear suggestions if query is too short
            }
        });
    });

    // Show the add-item-form when the "Add [Meal]" button is clicked
    document.querySelectorAll(".add-item-button").forEach(button => {
        button.addEventListener("click", function () {
            const mealType = this.getAttribute("data-meal");
            const form = document.getElementById(`${mealType}-form`);
            form.style.display = form.style.display === "none" ? "block" : "none";
        });
    });

    // Handle the "Submit" button in the add-item-form
    document.querySelectorAll(".submit-item-button").forEach(button => {
        button.addEventListener("click", function () {
            const mealType = this.getAttribute("data-meal");
            const form = document.getElementById(`${mealType}-form`);
            const foodNameInput = form.querySelector(".food-name");
            const foodServingsInput = form.querySelector(".food-servings");

            const foodName = foodNameInput.value.trim();
            const servings = parseFloat(foodServingsInput.value.trim());
            const caloriesPer100g = parseFloat(foodNameInput.getAttribute("data-calories"));

            if (foodName && servings && caloriesPer100g) {
                const totalCalories = (servings / 100) * caloriesPer100g;

                // Debugging: Log the data being sent
                console.log("Submitting food:", { foodName, servings, mealType });

                // Send the new food item to the server
                fetch("../JS/log_food.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        food_name: foodName,
                        serving_amount: servings,
                        meal_type: mealType
                    }),
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            updateCalorieRing(); // Update the ring after successful addition
                        } else {
                            alert(data.error || "Failed to log food.");
                        }
                    })
                    .catch(error => console.error("Error logging food:", error));

                // Add the new item to the meal list
                const mealList = document.getElementById(`${mealType}-list`);
                const listItem = document.createElement("li");
                listItem.setAttribute("data-servings", servings); // Set the serving amount
                listItem.setAttribute("data-calories", totalCalories); // Set the calculated calories
                listItem.innerHTML = `
                    ${foodName} - ${servings} grams (${totalCalories.toFixed(2)} calories)
                    <button class="button button-danger delete-item-button" data-food-name="${foodName}">🗑️</button>
                `;
                mealList.appendChild(listItem);

                // Clear the form inputs
                foodNameInput.value = "";
                foodServingsInput.value = "";
                foodNameInput.removeAttribute("data-calories");

                // Hide the form
                form.style.display = "none";

                // Reattach delete functionality to the new item
                attachDeleteHandlers();
            } else {
                alert("Please fill out all fields and select a valid food item.");
            }
        });
    });

    // Attach delete functionality to all delete-item-buttons
    function attachDeleteHandlers() {
        document.querySelectorAll(".delete-item-button").forEach(button => {
            button.addEventListener("click", function (event) {
                event.preventDefault(); // Prevent the default behavior (e.g., form submission or navigation)

                const listItem = this.parentElement;
                const foodName = this.getAttribute("data-food-name");

                // Ensure the log date is set to the correct timezone and format
                const logDateInput = document.querySelector("input[name='log_date']");
                const logDate = logDateInput.value; // Use the value directly from the hidden input field

                // Debugging: Log the food name and log date
                console.log("Deleting food item:", foodName, "on date:", logDate);

                if (confirm(`Are you sure you want to delete "${foodName}"?`)) {
                    // Send a request to the server to delete the item
                    fetch(`../JS/delete_food.php?food_name=${encodeURIComponent(foodName)}&log_date=${encodeURIComponent(logDate)}`, {
                        method: "GET",
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                listItem.remove(); // Remove the item from the DOM
                                alert("Food item deleted successfully.");
                                updateCalorieRing(); // Update the ring after successful deletion
                            } else {
                                alert(data.error || "Failed to delete the food item.");
                            }
                        })
                        .catch(error => console.error("Error deleting food item:", error));
                }
            });
        });
    }

    // Call this function initially to attach delete handlers to existing items
    attachDeleteHandlers();

    // Ensure the correct date is passed when submitting the form
    const logDateInput = document.querySelector("input[name='log_date']");
    const now = new Date();
    const offset = now.getTimezoneOffset() * 60000; // Offset in milliseconds
    const localDate = new Date(now.getTime() - offset).toISOString().split('T')[0]; // Format as YYYY-MM-DD
    logDateInput.value = localDate;

    updateCalorieRing(); // Initial call to set the ring
});