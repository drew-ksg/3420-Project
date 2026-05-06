<?php
require("../../dbConnection.php");

$conn = connect();

$stmt = $conn->query("CALL AvgAppointmentsPerProvider()");
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data);
?>