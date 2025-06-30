document.addEventListener("DOMContentLoaded", () => {
    const body = document.body;

    // Load dark mode preference from localStorage
    const isDarkMode = localStorage.getItem("darkMode") === "true";
    if (isDarkMode) {
        body.classList.add("dark-mode");
    }

    // If the dark mode toggle exists (on pages with the toggle), add functionality
    const darkModeCheckbox = document.getElementById("dark-mode-checkbox");
    if (darkModeCheckbox) {
        darkModeCheckbox.checked = isDarkMode;

        darkModeCheckbox.addEventListener("change", () => {
            if (darkModeCheckbox.checked) {
                body.classList.add("dark-mode");
                localStorage.setItem("darkMode", "true");
            } else {
                body.classList.remove("dark-mode");
                localStorage.setItem("darkMode", "false");
            }
        });
    }
});
