<?php
#List one patient by patientID
require("../dbConnection.php");

$conn = connect();

if (!isset($_GET["patientID"])) {
    echo json_encode([
        "status" => "error",
        "message" => "patientID missing"
    ]);
    exit;
}

try {

    $stmt = $conn->prepare("
        SELECT *
        FROM view_PatientProfile
        WHERE patientID = ?
    ");

    $stmt->execute([$_GET["patientID"]]);

    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "data" => $patient
    ]);

} catch(PDOException $e) {

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>