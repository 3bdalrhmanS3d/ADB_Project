<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_name']) || $_SESSION['user_name'] !== 'fox76459@gmail.com') {
    header("Location: register.html");
    exit();
}

$result = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Users</title>
    
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

        table {
            width: 80%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
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

        td {
            border-bottom: 1px solid #ddd;
        }

        @media (max-width: 768px) {
            table {
                width: 100%;
            }

            th, td {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <h1>All Users</h1>
    <a href="lectures.php" style="display: inline-block; margin-top: 20px; padding: 10px 20px; border: 1px solid #007bff; border-radius: 4px; background-color: #f8f9fa; text-decoration: none; color: #007bff; transition: background-color 0.3s, color 0.3s;">Back to Home</a>
    <table>
        <thead>
            <tr>
                <th>User ID</th>
                <th>User Name</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['user_id']; ?></td>
                    <td><?php echo $row['user_name']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
