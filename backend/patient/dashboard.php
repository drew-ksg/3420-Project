<?php
#Will contain all infromation for the patient dashboard
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
    $stmt = $conn->prepare("SELECT * FROM view_PatientProfile WHERE patientID = ?");
     $stmt->execute([$patientID]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt2 = $conn->prepare("SELECT * FROM view_PatientAppointments WHERE patientID = ?");
    $stmt2->execute([$patientID]);
    $appointments = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    $stmt3 = $conn->prepare("SELECT * FROM view_OrderSummary WHERE patientID = ?");
    $stmt3->execute([$patientID]);
    $orders = $stmt3->fetchAll(PDO::FETCH_ASSOC);


    echo json_encode([
        "status" => "success",
        "data" => $patient,
        "appointments" => $appointments,
        "orders" => $orders
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);

    
}
?>