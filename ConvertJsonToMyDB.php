<?php
include 'db.php';

// Function to process the JSON file and save to the database
function processJsonFileAndSaveToDB($filePath, $lectureName) {
    global $conn;

    // Check if the file exists
    if (!file_exists($filePath)) {
        echo "File does not exist.";
        return;
    }

    // Read the JSON file content
    $jsonContent = file_get_contents($filePath);

    // Decode JSON into PHP array
    $data = json_decode($jsonContent, true);

    if ($data === null) {
        echo "Invalid JSON format.";
        return;
    }

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Insert the lecture into the database
        $stmtLecture = $conn->prepare("INSERT INTO lectures (lecture_name) VALUES (?)");
        $stmtLecture->bind_param("s", $lectureName);
        $stmtLecture->execute();
        $lectureId = $conn->insert_id; // Get the inserted lecture's ID

        // Insert each question and its options
        foreach ($data as $item) {
            $questionText = $item['Question'];
            $codeOption = isset($item['CodeOption']) ? $item['CodeOption'] : null;

            // Insert question
            $stmtQuestion = $conn->prepare("INSERT INTO questions (lecture_id, question_text, code_option) VALUES (?, ?, ?)");
            $stmtQuestion->bind_param("iss", $lectureId, $questionText, $codeOption);
            $stmtQuestion->execute();
            $questionId = $conn->insert_id; // Get the inserted question's ID

            // Insert options
            foreach ($item['Options'] as $option) {
                $isCorrect = ($option === $item['Answer']) ? 1 : 0;

                $stmtOption = $conn->prepare("INSERT INTO options (question_id, option_text, is_correct) VALUES (?, ?, ?)");
                $stmtOption->bind_param("isi", $questionId, $option, $isCorrect);
                $stmtOption->execute();
            }
        }

        // Commit transaction
        $conn->commit();

        echo "<h3>JSON data successfully saved to the database.</h3>";

        // Display lecture and questions
        displayLectureAndQuestions($lectureId);

    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}

// Function to display lecture and questions
function displayLectureAndQuestions($lectureId) {
    global $conn;

    // Get the lecture details
    $resultLecture = $conn->query("SELECT * FROM lectures WHERE lecture_id = $lectureId");
    $lecture = $resultLecture->fetch_assoc();

    echo "<h4>Lecture Name: " . htmlspecialchars($lecture['lecture_name']) . "</h4>";

    // Get questions for the lecture
    $resultQuestions = $conn->query("SELECT * FROM questions WHERE lecture_id = $lectureId");
    echo "<ul>";
    while ($question = $resultQuestions->fetch_assoc()) {
        echo "<li><strong>Question:</strong> " . htmlspecialchars($question['question_text']) . "</li>";

        // Get options for the question
        $questionId = $question['question_id'];
        $resultOptions = $conn->query("SELECT * FROM options WHERE question_id = $questionId");

        echo "<ul>";
        while ($option = $resultOptions->fetch_assoc()) {
            $isCorrect = $option['is_correct'] ? "(Correct)" : "";
            echo "<li>" . htmlspecialchars($option['option_text']) . " $isCorrect</li>";
        }
        echo "</ul>";
    }
    echo "</ul>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JSON to Database</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        form {
            margin-bottom: 20px;
        }
        input, button {
            padding: 10px;
            margin: 5px 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <h1>Upload JSON to Database</h1>
    <form action="" method="POST">
        <label for="lectureName">Lecture Name:</label>
        <input type="text" name="lectureName" id="lectureName" required>

        <label for="filePath">JSON File Path:</label>
        <input type="text" name="filePath" id="filePath" required>

        <button type="submit" name="submit">Submit</button>
    </form>

    <?php
    // Handle form submission
    if (isset($_POST['submit'])) {
        $filePath = $_POST['filePath'];
        $lectureName = $_POST['lectureName'];
        processJsonFileAndSaveToDB($filePath, $lectureName);
    }
    ?>
</body>
</html>
