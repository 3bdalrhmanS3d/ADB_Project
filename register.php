<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_name = $_POST['user_name'];

    $stmt = $conn->prepare("CALL register_user(?, @user_id)");
    $stmt->bind_param("s", $user_name);
    $stmt->execute();
    
    if ($stmt->error) {
        echo "Error: " . $stmt->error;
    }
    
    $result = $conn->query("SELECT @user_id AS user_id");
    $row = $result->fetch_assoc();
    $user_id = $row['user_id'];

    session_start();
    $_SESSION['user_id'] = $user_id;

    header("Location: lectures.php");
    exit();
}
?>
