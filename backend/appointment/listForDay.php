<?php
header("Content-Type: application/json");

require_once("../dbConnection.php");
$conn = connect();

if ($conn === NULL) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

$date = $_GET["date"] ?? date("Y-m-d");

$stmt = $conn->prepare("
    SELECT
        a.appointmentID,
        a.patientID,
        a.providerID,
        a.medAssistantID,
        a.apptDate,
        a.reasonForVisit,
        a.status,
        a.checkInTime,
        a.checkOutTime,
        p.firstName,
        p.lastName,
        p.dob
    FROM Appointment a
    JOIN Patient p ON a.patientID = p.patientID
    WHERE DATE(a.apptDate) = ?
    ORDER BY a.apptDate
");

$stmt->execute([$date]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
