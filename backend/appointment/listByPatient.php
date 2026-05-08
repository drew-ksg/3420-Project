<?php
require("../dbConnection.php");

$conn = connect();


if (!isset($_GET["patientID"])) {
    echo json_encode([
        "status" => "error",
        "message" => "patientID missing"
    ]);
    exit;
}
$patientID = $_GET["patientID"];

try {

    $stmt = $conn->prepare("
        SELECT * 
        FROM view_PatientAppointments 
        WHERE patientID = ?
    ");

    $stmt->execute([$patientID]);

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "data" => $data
    ]);

} catch (PDOException $e) {

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>