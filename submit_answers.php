<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: register.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$lecture_id = $_POST['lecture_id'];
$total_score = 0;

foreach ($_POST as $key => $value) {
    if (strpos($key, 'question_') === 0) {
        $question_id = str_replace('question_', '', $key);
        $selected_option_id = $value;

        $stmt = $conn->prepare("CALL insert_user_answer(?, ?, ?, @is_correct)");
        $stmt->bind_param("iii", $user_id, $question_id, $selected_option_id);
        $stmt->execute();
    }
}

$result = $conn->query("CALL calculate_total_score($user_id, @total_score)");
$row = $result->fetch_assoc();
$total_score = $row['total_score'];

header("Location: results.php?score=" . $total_score);
exit();
?>
