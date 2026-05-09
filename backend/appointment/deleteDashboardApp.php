<?php
header("Content-Type: application/json");

require_once("../dbConnection.php");
$conn = connect();

if ($conn === NULL) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

$stmt = $conn->prepare("
    DELETE FROM Appointment
    WHERE appointmentID = ?
");

$stmt->execute([$_POST["appointmentID"]]);

echo json_encode(["status" => "success"]);
?>
