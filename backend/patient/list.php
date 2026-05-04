<?php
#List all patient info using the patientView
#Maybe add so that it grabs specfic patients not all

require("../dbConnection.php");

$conn = connect();

$stmt = $conn->query("SELECT * FROM view_PatientProfile");
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data);
?>