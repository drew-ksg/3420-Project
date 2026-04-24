<?php
require(__DIR__ . "/dbConnection.php");
$conn = connect();

if ($conn) {
    echo "Database is connected!\n";
}
