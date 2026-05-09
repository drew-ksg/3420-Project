<?php
header("Content-Type: application/json");

require_once("../dbConnection.php");
$conn = connect();

if ($conn === NULL) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

try {
    $conn->beginTransaction();

    $stmt = $conn->prepare("
        INSERT INTO Patient (firstName, lastName, dob, gender, email, phone)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $_POST["firstName"] ?? "",
        $_POST["lastName"] ?? "",
        $_POST["dob"] ?? null,
        $_POST["gender"] ?? "",
        $_POST["email"] ?? "",
        $_POST["phone"] ?? ""
    ]);

    $patientID = $conn->lastInsertId();

    $chartStmt = $conn->prepare("
        INSERT INTO PatientChart (
            patientID, address, allergies, medications, medicalHistory
        )
        VALUES (?, ?, ?, ?, ?)
    ");

    $chartStmt->execute([
        $patientID,
        $_POST["address"] ?? "",
        $_POST["allergies"] ?? "",
        $_POST["medications"] ?? "",
        $_POST["medicalHistory"] ?? ""
    ]);

    $insuranceStmt = $conn->prepare("
        INSERT INTO Insurance (
            patientID, providerName, idNumber, groupNumber, coverageType
        )
        VALUES (?, ?, ?, ?, ?)
    ");

    $insuranceStmt->execute([
        $patientID,
        $_POST["insuranceProvider"] ?? "",
        $_POST["insuranceID"] ?? "",
        $_POST["groupNumber"] ?? "",
        $_POST["coverageType"] ?? ""
    ]);

    $conn->commit();

    echo json_encode([
        "status" => "success",
        "patientID" => $patientID
    ]);
} catch (Exception $e) {
    $conn->rollBack();

    echo json_encode([
        "status" => "error",
        "message" => "Could not add patient",
        "details" => $e->getMessage()
    ]);
}
?>
