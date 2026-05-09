<?php
header("Content-Type: application/json");

require_once("../dbConnection.php");
$conn = connect();

if ($conn === NULL) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

$stmt = $conn->prepare("
    UPDATE Appointment
    SET checkOutTime = NOW(),
        status = 'Checked Out'
    WHERE appointmentID = ?
      AND checkInTime IS NOT NULL
");

$stmt->execute([$_POST["appointmentID"]]);

echo json_encode(["status" => "success"]);
?>
