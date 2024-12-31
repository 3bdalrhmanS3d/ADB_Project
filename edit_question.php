<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_name']) || $_SESSION['user_name'] !== 'fox76459@gmail.com') {
    header("Location: register.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $question_id = $_POST['question_id'];
    $lecture_id = $_POST['lecture_id'];
    $question_text = $_POST['question_text'];
    $code_option = $_POST['code_option'];

    if ($action === 'add') {
        $stmt = $conn->prepare("CALL AddQuestion(?, ?, ?)");
        $stmt->bind_param("iss", $lecture_id, $question_text, $code_option);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $question_id = $row['question_id'];

        if (isset($_POST['options'])) {
            foreach ($_POST['options'] as $index => $option_text) {
                $is_correct = isset($_POST['is_correct'][$index]) ? 1 : 0;
                $stmt = $conn->prepare("INSERT INTO options (question_id, option_text, is_correct) VALUES (?, ?, ?)");
                $stmt->bind_param("isi", $question_id, $option_text, $is_correct);
                $stmt->execute();
            }
        }
    } elseif ($action === 'edit') {
        $stmt = $conn->prepare("CALL EditQuestion(?, ?, ?)");
        $stmt->bind_param("iss", $question_id, $question_text, $code_option);
        $stmt->execute();

        $stmt = $conn->prepare("DELETE FROM options WHERE question_id = ?");
        $stmt->bind_param("i", $question_id);
        $stmt->execute();

        if (isset($_POST['options'])) {
            foreach ($_POST['options'] as $index => $option_text) {
                $is_correct = isset($_POST['is_correct'][$index]) ? 1 : 0;
                $stmt = $conn->prepare("INSERT INTO options (question_id, option_text, is_correct) VALUES (?, ?, ?)");
                $stmt->bind_param("isi", $question_id, $option_text, $is_correct);
                $stmt->execute();
            }
        }
    } elseif ($action === 'delete') {
        $stmt = $conn->prepare("CALL DeleteQuestion(?)");
        $stmt->bind_param("i", $question_id);
        $stmt->execute();
        echo "<script>alert('Question deleted successfully');</script>";
    }
}

$result = $conn->query("SELECT q.*, l.lecture_name FROM questions q JOIN lectures l ON q.lecture_id = l.lecture_id");
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
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h1 {
            color: #333;
        }

        form {
            width: 80%;
            background-color: #fff;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }

        input[type="number"],
        textarea,
        button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
        }

        button {
            background-color: #007BFF;
            color: #fff;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #0056b3;
        }

        table {
            width: 80%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th,
        td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #007BFF;
            color: #fff;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        form[style="display:inline;"] {
            display: inline;
        }

        @media (max-width: 768px) {
            form, table {
                width: 100%;
            }

            th, td {
                font-size: 14px;
            }
        }

    </style>
</head>
<body>
    <h1>Manage Questions</h1>
    <form method="POST">
        <input type="hidden" name="action" value="add">
        <label for="lecture_id">Lecture:</label>
        <select name="lecture_id" required>
            <?php
            $lectures = $conn->query("SELECT * FROM lectures");
            while ($lecture = $lectures->fetch_assoc()): ?>
                <option value="<?php echo $lecture['lecture_id']; ?>">
                    <?php echo $lecture['lecture_name']; ?>
                </option>
            <?php endwhile; ?>
        </select>
        <label for="question_text">Question Text:</label>
        <textarea name="question_text" required></textarea>
        <label for="code_option">Code Option:</label>
        <textarea name="code_option"></textarea>
        <label>Options:</label>
        <div id="options-container">
            <div>
                <input type="text" name="options[]" placeholder="Option Text" required>
                <input type="checkbox" name="is_correct[]"> Correct
            </div>
        </div>
        <button type="button" onclick="addOption()">Add Option</button>
        <button type="submit">Add Question</button>
    </form>
    <a href="lectures.php" style="display: inline-block; margin-top: 20px; padding: 10px 20px; border: 1px solid #007bff; border-radius: 4px; background-color: #f8f9fa; text-decoration: none; color: #007bff; transition: background-color 0.3s, color 0.3s;">Back to Home</a>

    <table>
        <thead>
            <tr>
                <th>Question ID</th>
                <th>Lecture</th>
                <th>Question Text</th>
                <th>Code Option</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['question_id']; ?></td>
                    <td><?php echo $row['lecture_name']; ?></td>
                    <td><?php echo $row['question_text']; ?></td>
                    <td><?php echo $row['code_option']; ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="question_id" value="<?php echo $row['question_id']; ?>">
                            <input type="hidden" name="lecture_id" value="<?php echo $row['lecture_id']; ?>">
                            <input type="hidden" name="question_text" value="<?php echo $row['question_text']; ?>">
                            <input type="hidden" name="code_option" value="<?php echo $row['code_option']; ?>">
                            <button type="submit">Edit</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="question_id" value="<?php echo $row['question_id']; ?>">
                            <button type="submit" onclick="return confirm('Are you sure you want to delete this question?');">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <script>
        function addOption() {
            const container = document.getElementById('options-container');
            const optionDiv = document.createElement('div');
            optionDiv.innerHTML = `
                <input type="text" name="options[]" placeholder="Option Text" required>
                <input type="checkbox" name="is_correct[]"> Correct
            `;
            container.appendChild(optionDiv);
        }
    </script>
</body>
</html>
