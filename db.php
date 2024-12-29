<?php   
    $host = 'localhost';
    $user = 'root';
    $pass = 'rootroot';
    $db = 'lecture_system';

    $conn = new mysqli($host, $user, $pass, $db);

    if($conn->connect_error){
        die('Database connection error: ' . $conn->connect_error);
    } else {
        //echo 'Database connection successful';
        
    }


?> 