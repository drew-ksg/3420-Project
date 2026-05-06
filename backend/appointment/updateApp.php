<?php
require("../../dbConnection.php");

$conn = connect();

$required = ["appointmentID", "apptDate", "status"];

foreach ($required as $field) {
    if (!isset($_POST[$field])) {
        echo json_encode(["error" => "$field missing"]);
        exit;
    }
}

$stmt = $conn->prepare("
    UPDATE Appointment
    SET apptDate = ?, status = ?
    WHERE appointmentID = ?
");

$stmt->execute([
    $_POST["apptDate"],
    $_POST["status"],
    $_POST["appointmentID"]
]);

echo json_encode(["status" => "updated"]);
?>