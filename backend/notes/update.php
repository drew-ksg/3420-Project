<?php
header("Content-Type: application/json");

require_once("../dbConnection.php");
$conn = connect();

if ($conn === NULL) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

$noteID = $_POST["noteID"] ?? "";
$subjective = $_POST["subjective"] ?? "";
$objective = $_POST["objective"] ?? "";
$assessment = $_POST["assessment"] ?? "";
$plan = $_POST["plan"] ?? "";

if ($noteID === "") {
    echo json_encode(["status" => "error", "message" => "Missing noteID"]);
    exit;
}

try {
    $stmt = $conn->prepare("
        UPDATE SOAPNote
        SET subjective = ?,
            objective = ?,
            assessment = ?,
            plan = ?
        WHERE noteID = ?
    ");

    $stmt->execute([
        $subjective,
        $objective,
        $assessment,
        $plan,
        $noteID
    ]);

    echo json_encode(["status" => "success"]);
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Could not update progress note",
        "details" => $e->getMessage()
    ]);
}
?>
