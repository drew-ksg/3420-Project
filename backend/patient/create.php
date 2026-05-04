<?php
#Creates record in the patient table

require("../dbConnection.php");

$conn = connect();

$stmt = $conn->prepare("CALL AddPatient(?,?,?,?,?,?,?,?,?,?,?)");
$stmt->execute([
    $_POST["firstName"],
    $_POST["lastName"],
    $_POST["dob"],
    $_POST["gender"],
    $_POST["email"],
    $_POST["phone"],
    $_POST["insuranceProvider"],
    $_POST["coverageType"],
    $_POST["allergies"],
    $_POST["medications"],
    $_POST["medicalHistory"]
]);
echo json_encode(["status" => "success"]);

?>