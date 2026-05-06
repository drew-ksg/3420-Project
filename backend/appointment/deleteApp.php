<?php
require("../../dbConnection.php");

$conn = connect();

if (!isset($_POST["appointmentID"])) {
    echo json_encode(["error" => "appointmentID missing"]);
    exit;
}

$stmt = $conn->prepare("CALL DeleteAppointment(?)");
$stmt->execute([$_POST["appointmentID"]]);

echo json_encode(["status" => "deleted"]);
?>