<?php
    // database connection
    $hostname = "localhost";
    $username = "root";
    $password = "";
    $database = "pharmacy_management_system";

    $conn = mysqli_connect($hostname, $username, $password, $database);

    if(!$conn){
        die("Connection failed: " . mysqli_connect_error());
    }
?>