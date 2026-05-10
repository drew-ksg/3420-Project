<?php
header("Content-Type: application/json");

require_once("../dbConnection.php");
$conn = connect();

if ($conn === NULL) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

$patientID = $_POST["patientID"] ?? "";

if ($patientID === "") {
    echo json_encode(["status" => "error", "message" => "Missing patientID"]);
    exit;
}

try {
    $conn->beginTransaction();

    $patientStmt = $conn->prepare("
        UPDATE Patient
        SET firstName = ?,
            lastName = ?,
            dob = ?,
            gender = ?,
            email = ?,
            phone = ?
        WHERE patientID = ?
    ");

    $patientStmt->execute([
        $_POST["firstName"] ?? "",
        $_POST["lastName"] ?? "",
        $_POST["dob"] ?? null,
        $_POST["gender"] ?? "",
        $_POST["email"] ?? "",
        $_POST["phone"] ?? "",
        $patientID
    ]);

    $chartStmt = $conn->prepare("
        UPDATE PatientChart
        SET address = ?,
            emergencyContactName = ?,
            emergencyContactPhone = ?
        WHERE patientID = ?
    ");

    $chartStmt->execute([
        $_POST["address"] ?? "",
        $_POST["emergencyContactName"] ?? "",
        $_POST["emergencyContactPhone"] ?? "",
        $patientID
    ]);

    $insuranceCheck = $conn->prepare("
        SELECT insuranceID
        FROM Insurance
        WHERE patientID = ?
        ORDER BY insuranceID DESC
        LIMIT 1
    ");
    $insuranceCheck->execute([$patientID]);
    $insurance = $insuranceCheck->fetch(PDO::FETCH_ASSOC);

    if ($insurance) {
        $insuranceStmt = $conn->prepare("
            UPDATE Insurance
            SET providerName = ?,
                idNumber = ?
            WHERE insuranceID = ?
        ");

        $insuranceStmt->execute([
            $_POST["insuranceProvider"] ?? "",
            $_POST["insuranceID"] ?? "",
            $insurance["insuranceID"]
        ]);
    } else {
        $insuranceStmt = $conn->prepare("
            INSERT INTO Insurance (patientID, providerName, idNumber, groupNumber, coverageType)
            VALUES (?, ?, ?, '', '')
        ");

        $insuranceStmt->execute([
            $patientID,
            $_POST["insuranceProvider"] ?? "",
            $_POST["insuranceID"] ?? ""
        ]);
    }

    $conn->commit();

    echo json_encode(["status" => "success"]);
} catch (Exception $e) {
    $conn->rollBack();

    echo json_encode([
        "status" => "error",
        "message" => "Could not update demographics",
        "details" => $e->getMessage()
    ]);
}
?>
