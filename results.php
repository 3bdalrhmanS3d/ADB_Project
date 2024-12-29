<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: register.html");
    exit();
}

$total_score = $_GET['score'];
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Results</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                text-align: center;
                background-color: #f8f9fa;
                padding: 20px;
                color: #333;
            }

            h1 {
                font-size: 2em;
                margin-bottom: 20px;
                color: #007bff;
            }

            p {
                font-size: 1.5em;
                margin: 20px 0;
            }

            .feedback {
                font-size: 1.2em;
                margin-top: 10px;
                color: #555;
            }

            a {
                display: inline-block;
                margin-top: 20px;
                padding: 10px 20px;
                border: 1px solid #007bff;
                border-radius: 4px;
                background-color: #f8f9fa;
                text-decoration: none;
                color: #007bff;
                transition: background-color 0.3s, color 0.3s;
            }

            a:hover {
                background-color: #007bff;
                color: #fff;
            }
        </style>
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const score = <?php echo $total_score; ?>;
                const feedbackElement = document.getElementById("feedback");

                if (score >= 80) {
                    feedbackElement.textContent = "Excellent work! Keep it up!";
                } else if (score >= 50) {
                    feedbackElement.textContent = "Good job! But there's room for improvement.";
                } else {
                    feedbackElement.textContent = "Don't worry! Keep practicing to improve your score.";
                }
            });
        </script>
    </head>
    <body>
        <h1>Your Results</h1>
        <p>Total Score: <?php echo $total_score; ?></p>
        <p id="feedback" class="feedback"></p>
        <a href="lectures.php">Back to Lectures</a>
    </body>
</html>
