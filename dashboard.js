document.addEventListener("DOMContentLoaded", function () {
    const circleCircumference = 880;

    function updateCalorieRing() {
        const calorieGoal = Math.round(parseInt(document.getElementById("calorie-goal").textContent) || 2000);
        const caloriesConsumedElement = document.getElementById("calories-consumed");
        const caloriesBurnedElement = document.getElementById("calories-burned");
        const caloriesConsumedRing = document.querySelector('.calories-consumed');
        const caloriesBurnedRing = document.querySelector('.calories-burned');

        Promise.all([
            fetch("../JS/get_total_calories.php").then(response => response.json()),
            fetch("../JS/get_total_calories_burned.php").then(response => response.json())
        ])
        .then(([consumedData, burnedData]) => {
            const totalCaloriesConsumed = Math.round(consumedData.totalCalories || 0);
            const totalCaloriesBurned = Math.round(burnedData.totalCaloriesBurned || 0);

            // Calculate progress for calories consumed
            const consumedProgress = Math.min((totalCaloriesConsumed / calorieGoal) * circleCircumference, circleCircumference);
            caloriesConsumedRing.style.strokeDashoffset = circleCircumference - consumedProgress;
            caloriesConsumedElement.textContent = totalCaloriesConsumed;

            // Calculate progress for calories burned
            const burnedProgress = Math.min((totalCaloriesBurned / calorieGoal) * circleCircumference, consumedProgress); // Ensure burned does not exceed consumed
            const burnedStartOffset = circleCircumference - consumedProgress; // Start at the tail of consumed
            caloriesBurnedRing.style.strokeDashoffset = burnedStartOffset + burnedProgress; // Add burned progress to start offset
            caloriesBurnedElement.textContent = totalCaloriesBurned;

            // Calculate and display remaining calories
            const remainingCalories = Math.max(calorieGoal - totalCaloriesConsumed + totalCaloriesBurned, 0);
            document.getElementById("remaining-calories").textContent = remainingCalories;
        })
        .catch(error => console.error("Error updating calorie ring:", error));
    }

    updateCalorieRing(); // Initial call to set the ring
});