<?php
require("../dbConnection.php");

$conn = connect();

if (!isset($_POST["patientID"])) {
    echo json_encode(["status" => "error", "message" => "patientID missing"]);
    exit;
}

try {

    $stmt = $conn->prepare("DELETE FROM Patient WHERE patientID = ?");
    $stmt->execute([$_POST["patientID"]]);

    echo json_encode(["status" => "success"]);

} catch(PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
