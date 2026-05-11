<?php
require("../dbConnection.php");

$conn = connect();

$stmt = $conn->query("SELECT * FROM view_PatientAppointments");
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data);

?>
