<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lectures List</title>
    <link rel="stylesheet" href="styles.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #007bff;
            color: #fff;
            padding: 20px;
            text-align: center;
        }

        header h1 {
            margin: 0;
            font-size: 2em;
        }

        .container {
            padding: 20px;
            text-align: center;
        }

        ul {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
            padding: 0;
            list-style: none;
        }

        li {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 250px;
            text-align: center;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        li:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        a {
            text-decoration: none;
            color: #007bff;
            font-size: 1.2em;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

        footer {
            margin-top: 20px;
            text-align: center;
            padding: 10px;
            background-color: #f1f1f1;
        }

        footer a {
            text-decoration: none;
            color: #007bff;
            font-size: 1em;
        }

        footer a:hover {
            text-decoration: underline;
        }


    </style>
</head>
<body>
    <header>
        <h1>Available Lectures</h1>
    </header>
    <div class="container">
        <ul>
            <?php
                include 'db.php';
                $result = $conn->query("CALL AvailableLectures()");
                while ($row = $result->fetch_assoc()) {
                    echo "<li><a href='questions.php?lecture_id=" . $row['lecture_id'] . "'>" . $row['lecture_name'] . "</a></li>";
                }
            ?>
        </ul>
    </div>

    <a href="ConvertJsonToMyDB.php" class="upload-button">
        Upload JSON to Database
    </a>
</body>

</html>
