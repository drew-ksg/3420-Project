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
    SET patientID = ?,
        providerID = ?,
        medAssistantID = ?,
        apptDate = ?,
        reasonForVisit = ?
    WHERE appointmentID = ?
");

$stmt->execute([
    $_POST["patientID"],
    $_POST["providerID"],
    $_POST["medAssistantID"] !== "" ? $_POST["medAssistantID"] : null,
    $_POST["apptDate"],
    $_POST["reasonForVisit"],
    $_POST["appointmentID"]
]);

echo json_encode(["status" => "success"]);
?>
