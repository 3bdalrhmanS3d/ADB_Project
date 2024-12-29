<?php
include 'db.php';

if (isset($_POST['add_question'])) {
    $lecture_id = $_POST['lecture_id'];
    $question_text = $_POST['question_text'];
    $code_option = !empty($_POST['code_option']) ? $_POST['code_option'] : null;

    $stmt = $conn->prepare("CALL AddQuestion(?, ?, ?)");
    $stmt->bind_param("iss", $lecture_id, $question_text, $code_option);
    $stmt->execute();
    header("Location: manage_questions.php?lecture_id=$lecture_id");
    exit();
}

if (isset($_GET['delete_question_id'])) {
    $question_id = $_GET['delete_question_id'];
    $lecture_id = $_GET['lecture_id'];

    $stmt = $conn->prepare("CALL DeleteQuestion(?)");
    $stmt->bind_param("i", $question_id);
    $stmt->execute();
    header("Location: manage_questions.php?lecture_id=$lecture_id");
    exit();
}

$lectures_result = $conn->query("CALL AvailableLectures()");

$questions = [];
$selected_lecture_id = isset($_GET['lecture_id']) ? (int)$_GET['lecture_id'] : 0;
if ($selected_lecture_id > 0) {
    $stmt = $conn->prepare("CALL GetQuestionsByLecture(?)");
    $stmt->bind_param("i", $selected_lecture_id);
    $stmt->execute();
    $questions_result = $stmt->get_result();
    while ($row = $questions_result->fetch_assoc()) {
        $questions[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Questions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f9f9f9;
        }

        h1 {
            text-align: center;
        }

        form, table {
            margin: 20px auto;
            width: 80%;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        form label, form select, form textarea, form button {
            display: block;
            width: 100%;
            margin-bottom: 10px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        button, a {
            display: inline-block;
            padding: 10px 15px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
        }

        button:hover, a:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Manage Questions</h1>

    <!-- Form to add a question -->
    <form method="POST">
        <label for="lecture_id">Select Lecture:</label>
        <select name="lecture_id" id="lecture_id" required>
            <option value="">-- Select Lecture --</option>
            <?php while ($lecture = $lectures_result->fetch_assoc()): ?>
                <option value="<?php echo $lecture['lecture_id']; ?>" <?php echo $lecture['lecture_id'] == $selected_lecture_id ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($lecture['lecture_name']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="question_text">Question Text:</label>
        <textarea name="question_text" id="question_text" rows="4" required></textarea>

        <label for="code_option">Code Option (Optional):</label>
        <textarea name="code_option" id="code_option" rows="4"></textarea>

        <button type="submit" name="add_question">Add Question</button>
    </form>

    <?php if ($selected_lecture_id > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Question</th>
                    <th>Code Option</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($questions) > 0): ?>
                    <?php foreach ($questions as $index => $question): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($question['question_text']); ?></td>
                            <td><?php echo htmlspecialchars($question['code_option']); ?></td>
                            <td>
                                <a href="edit_question.php?question_id=<?php echo $question['question_id']; ?>&lecture_id=<?php echo $selected_lecture_id; ?>">Edit</a>
                                <a href="manage_questions.php?delete_question_id=<?php echo $question['question_id']; ?>&lecture_id=<?php echo $selected_lecture_id; ?>" onclick="return confirm('Are you sure you want to delete this question?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No questions found for this lecture.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
