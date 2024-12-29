<?php
include 'db.php';

// Get lecture_id from URL
$lecture_id = isset($_GET['lecture_id']) ? (int)$_GET['lecture_id'] : 0;
if ($lecture_id === 0) {
    echo "Invalid lecture ID.";
    exit;
}

// Fetch all questions for the lecture
$result = $conn->query("SELECT * FROM questions WHERE lecture_id = $lecture_id");
if ($result === false) {
    echo "Error: " . $conn->error;
    exit;
}

// Store questions in an array for easier navigation
$questions = [];
while ($row = $result->fetch_assoc()) {
    $questions[] = $row;
}

// Get the total number of questions
$totalQuestions = count($questions);

// Get the current question index from the query string
$currentQuestionIndex = isset($_GET['question']) ? (int)$_GET['question'] : 0;
if ($currentQuestionIndex < 0 || $currentQuestionIndex >= $totalQuestions) {
    echo "Invalid question number.";
    exit;
}

// Get the current question
$currentQuestion = $questions[$currentQuestionIndex];

// Fetch options for the current question
$options = $conn->query("SELECT * FROM options WHERE question_id = " . $currentQuestion['question_id']);
if ($options === false) {
    echo "Error: " . $conn->error;
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecture Questions</title>
    <style>
        .question {
            background: #fff;
            margin: 15px auto;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: left;
            width: 80%;
        }

        input[type="radio"] {
            margin-right: 10px;
        }

        label {
            display: block;
            margin: 5px 0;
            font-size: 1em;
        }

        .navigation {
            margin: 20px 0;
            text-align: center;
        }

        button {
            padding: 10px 20px;
            margin: 5px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }

        .result {
            margin-top: 20px;
            font-size: 1.2em;
            color: #fff;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }

        .correct {
            background-color: #4caf50;
        }

        .incorrect {
            background-color: #f44336;
        }
        .progress-container {
            width: 80%;
            margin: 20px auto;
            background-color: #f1f1f1;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .progress-text {
            text-align: center;
            margin-top: 10px;
            font-size: 1.5em;
        }

        .go-to {
            margin: 20px 0;
            text-align: center;
        }

        .go-to input {
            padding: 10px;
            width: 60px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .go-to button {
            padding: 10px 20px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .go-to button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Lecture Questions</h1>

    <!-- Progress Bar -->
    <div class="progress-container">
        <div class="progress-bar"></div>
    </div>
    <p class="progress-text">Question <?php echo $currentQuestionIndex + 1; ?> of <?php echo $totalQuestions; ?></p>

    <form id="quizForm">
        <div class="question">
            <p><strong>Question <?php echo $currentQuestionIndex + 1; ?>:</strong> <?php echo htmlspecialchars($currentQuestion['question_text']); ?></p>
            <?php while ($option = $options->fetch_assoc()): ?>
                <label>
                    <input type="radio" name="answer" value="<?php echo $option['option_id']; ?>" data-correct="<?php echo $option['is_correct']; ?>">
                    <?php echo htmlspecialchars($option['option_text']); ?>
                </label>
            <?php endwhile; ?>
        </div>

        <div class="navigation">
            <button type="button" onclick="checkAnswer()">Check Answer</button>
            <?php if ($currentQuestionIndex > 0): ?>
                <button type="button" onclick="navigateToQuestion(<?php echo $currentQuestionIndex - 1; ?>)">Previous</button>
            <?php endif; ?>
            <?php if ($currentQuestionIndex < $totalQuestions - 1): ?>
                <button type="button" onclick="navigateToQuestion(<?php echo $currentQuestionIndex + 1; ?>)">Next</button>
            <?php else: ?>
                <button type="button" onclick="submitQuiz()">Submit Quiz</button>
            <?php endif; ?>
        </div>
        <div id="result" class="result" style="display: none;"></div>
    </form>

    <div class="progress-container">
        <div class="progress-bar"></div>
    </div>

    <!-- Go to Question -->
    <div class="go-to">
        <label for="goToQuestion">Go to Question:</label>
        <input type="number" id="goToQuestion" min="1" max="<?php echo $totalQuestions; ?>" value="<?php echo $currentQuestionIndex + 1; ?>">
        <button onclick="goToQuestion()">Go</button>
    </div>

    <a href="lectures.php" style="display: inline-block; margin-top: 20px; padding: 10px 20px; border: 1px solid #007bff; border-radius: 4px; background-color: #f8f9fa; text-decoration: none; color: #007bff; transition: background-color 0.3s, color 0.3s;">Back to Home</a>

    <script>
        function checkAnswer() {
            const selectedOption = document.querySelector('input[name="answer"]:checked');
            const resultElement = document.getElementById('result');

            if (!selectedOption) {
                resultElement.style.display = 'block';
                resultElement.textContent = 'Please select an answer.';
                resultElement.className = 'result incorrect';
                return;
            }

            const isCorrect = selectedOption.getAttribute('data-correct') === '1';
            resultElement.style.display = 'block';
            resultElement.textContent = isCorrect ? 'Correct Answer!' : 'Incorrect Answer.';
            resultElement.className = isCorrect ? 'result correct' : 'result incorrect';
        }

        function navigateToQuestion(questionIndex) {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('question', questionIndex);
            window.location.search = urlParams.toString();
        }

        function submitQuiz() {
            const form = document.getElementById('quizForm');
            form.action = 'submit_answers.php';
            form.method = 'POST';
            form.submit();
        }

        function navigateToQuestion(questionIndex) {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('question', questionIndex);
            window.location.search = urlParams.toString();
        }

        function navigateToQuestion(questionIndex) {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('question', questionIndex);
            window.location.search = urlParams.toString();
        }

        function goToQuestion() {
            const questionNumber = document.getElementById('goToQuestion').value;
            if (questionNumber >= 1 && questionNumber <= <?php echo $totalQuestions; ?>) {
                navigateToQuestion(questionNumber - 1);
            } else {
                alert('Invalid question number.');
            }
        }
    </script>
</body>
</html>
