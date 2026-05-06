<?php
#Creates a new appointment in the database. There is no procudure or trigger for this so we just insert the data directly into the table
require("../dbConnection.php");

$conn = connect();

$required = ["patientID", "providerID", "medAssistantID", "apptDate", "reasonForVisit", "status"];

foreach ($required as $field) {
    if (!isset($_POST[$field])) {
        echo json_encode(["error" => "$field missing"]);
        exit;
    }
}

$stmt = $conn->prepare("
    INSERT INTO Appointment (patientID, providerID, medAssistantID, apptDate, reasonForVisit, status)
    VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->execute([
    $_POST["patientID"],
    $_POST["providerID"],
    $_POST["medAssistantID"],
    $_POST["apptDate"],
    $_POST["reasonForVisit"],
    $_POST["status"]
]);

echo json_encode(["status" => "created"]);
?>