<?php
header("Content-Type: application/json");

require_once("../dbConnection.php");
$conn = connect();

if ($conn === NULL) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

$stmt = $conn->prepare("
    INSERT INTO Appointment (
        patientID, providerID, medAssistantID, apptDate, reasonForVisit, status
    )
    VALUES (?, ?, ?, ?, ?, 'Scheduled')
");

$stmt->execute([
    $_POST["patientID"],
    $_POST["providerID"],
    $_POST["medAssistantID"] !== "" ? $_POST["medAssistantID"] : null,
    $_POST["apptDate"],
    $_POST["reasonForVisit"]
]);

echo json_encode([
    "status" => "success",
    "appointmentID" => $conn->lastInsertId()
]);
?>
