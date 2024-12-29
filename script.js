document.addEventListener("DOMContentLoaded", () => {
    /**
     * Highlight Selected Answers
     * - Correct answers: Green
     * - Incorrect answers: Red
     */
    const options = document.querySelectorAll('input[type="radio"]');
    options.forEach((option) => {
        option.addEventListener("change", (event) => {
            const parent = event.target.closest("label");
            const allOptions = parent.closest(".question").querySelectorAll("label");

            // Reset all options' background
            allOptions.forEach((label) => {
                label.style.backgroundColor = "";
            });

            // Highlight the selected option
            if (event.target.dataset.correct === "true") {
                parent.style.backgroundColor = "lightgreen";
            } else {
                parent.style.backgroundColor = "lightcoral";
            }
        });
    });

    /**
     * Timer for Quiz/Test
     * - Counts down from a predefined time
     * - Submits the form when the timer reaches zero
     */
    const timerElement = document.getElementById("timer");
    if (timerElement) {
        let timeLeft = 300; // 5 minutes in seconds

        const updateTimer = () => {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerElement.textContent = `${minutes}:${seconds < 10 ? "0" : ""}${seconds}`;

            if (timeLeft > 0) {
                timeLeft--;
            } else {
                clearInterval(timerInterval);
                alert("Time is up! Submitting your answers.");
                document.querySelector("form").submit();
            }
        };

        const timerInterval = setInterval(updateTimer, 1000);
        updateTimer();
    }

    /**
     * Smooth Scrolling for Question Navigation
     * - Allows users to jump to specific questions
     */
    const links = document.querySelectorAll("#question-navigation a");
    links.forEach((link) => {
        link.addEventListener("click", (event) => {
            event.preventDefault();
            const targetId = link.getAttribute("href").substring(1);
            const targetElement = document.getElementById(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({ behavior: "smooth" });
            }
        });
    });
});
