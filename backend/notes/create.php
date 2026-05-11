<?php
header("Content-Type: application/json");

require_once("../dbConnection.php");
$conn = connect();

if ($conn === NULL) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

$appointmentID = $_POST["appointmentID"] ?? "";
$subjective = $_POST["subjective"] ?? "";
$objective = $_POST["objective"] ?? "";
$assessment = $_POST["assessment"] ?? "";
$plan = $_POST["plan"] ?? "";

if ($appointmentID === "") {
    echo json_encode(["status" => "error", "message" => "Missing appointmentID"]);
    exit;
}

try {
    $checkStmt = $conn->prepare("
        SELECT noteID
        FROM SOAPNote
        WHERE appointmentID = ?
    ");
    $checkStmt->execute([$appointmentID]);
    $existingNote = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if ($existingNote) {
        echo json_encode([
            "status" => "error",
            "message" => "A progress note already exists for this appointment. Use edit instead."
        ]);
        exit;
    }

    $stmt = $conn->prepare("
        INSERT INTO SOAPNote (
            appointmentID,
            subjective,
            objective,
            assessment,
            plan
        )
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $appointmentID,
        $subjective,
        $objective,
        $assessment,
        $plan
    ]);

    echo json_encode([
        "status" => "success",
        "noteID" => $conn->lastInsertId()
    ]);
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Could not create progress note",
        "details" => $e->getMessage()
    ]);
}
?>
