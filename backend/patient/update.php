<?php
#Updates patient info for now its only email
require("../dbConnection.php");

$conn = connect();

$stmt = $conn->prepare("CALL UpdatePatientEmail(?, ?)");
$stmt->execute([
    $_POST['patientID'],
    $_POST['email']
]);

echo json_encode(["status" => "updated"]);

?>