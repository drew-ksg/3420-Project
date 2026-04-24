<?php
require("secrets.php");



function connect(){

    $servername = "localhost";
    $username = "username";
    $password = "password";
    $dbname = "mydb";
    
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Connected successfully";
    }
    catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
}




ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>