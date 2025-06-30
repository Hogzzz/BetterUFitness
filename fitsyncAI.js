document.addEventListener("DOMContentLoaded", function () {
    const workoutButton = document.getElementById("workout-ai-button");
    const nutritionButton = document.getElementById("nutrition-ai-button");
    const chatbotSection = document.getElementById("chatbot-section");
    const chatbotTitle = document.getElementById("chatbot-title");
    const chatMessages = document.getElementById("chat-messages");
    const chatInput = document.getElementById("chat-input");
    const chatSend = document.getElementById("chat-send");

    let selectedAI = ""; // Track which AI the user selected

    const today = new Date().toLocaleDateString('en-CA'); // Ensure date is in EST format

    // Show Chatbot for Workout AI
    workoutButton.addEventListener("click", function () {
        selectedAI = "workout";
        chatbotTitle.textContent = "Chat with Workout AI";
        chatbotSection.style.display = "block";
    });

    // Show Chatbot for Nutrition AI
    nutritionButton.addEventListener("click", function () {
        selectedAI = "nutrition";
        chatbotTitle.textContent = "Chat with Nutrition AI";
        chatbotSection.style.display = "block";
    });

    // Handle Chat Messages
    chatSend.addEventListener("click", function () {
        const userMessage = chatInput.value.trim();
        if (userMessage === "") return;

        // Display user message in the chat
        const userMessageElement = document.createElement("div");
        userMessageElement.className = "chat-message user";
        userMessageElement.textContent = `You: ${userMessage}`;
        chatMessages.appendChild(userMessageElement);

        // Clear the input field
        chatInput.value = "";

        // Send the message to the server
        fetch("../API/chatAI.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ message: userMessage, aiType: selectedAI }),
        })
            .then(response => response.json())
            .then(data => {
                // Display AI response in the chat
                const aiMessageElement = document.createElement("div");
                aiMessageElement.className = "chat-message ai";
                aiMessageElement.textContent = `AI: ${data.response}`;
                chatMessages.appendChild(aiMessageElement);
            })
            .catch(error => {
                console.error("Error:", error);
                const errorMessageElement = document.createElement("div");
                errorMessageElement.className = "chat-message error";
                errorMessageElement.textContent = "Error: Unable to get a response from the AI.";
                chatMessages.appendChild(errorMessageElement);
            });
    });
});