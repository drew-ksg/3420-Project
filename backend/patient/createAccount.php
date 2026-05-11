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
    $patientStmt = $conn->prepare("
        SELECT patientID, firstName, lastName
        FROM Patient
        WHERE patientID = ?
    ");
    $patientStmt->execute([$patientID]);
    $patient = $patientStmt->fetch(PDO::FETCH_ASSOC);

    if (!$patient) {
        echo json_encode(["status" => "error", "message" => "Patient not found"]);
        exit;
    }

    $existingStmt = $conn->prepare("
        SELECT userID, username
        FROM UserAccount
        WHERE patientID = ?
          AND role = 'patient'
        LIMIT 1
    ");
    $existingStmt->execute([$patientID]);
    $existing = $existingStmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        echo json_encode([
            "status" => "error",
            "message" => "This patient already has an account.",
            "username" => $existing["username"]
        ]);
        exit;
    }

    $baseUsername = strtolower($patient["firstName"] . "." . $patient["lastName"] . $patientID);
    $baseUsername = preg_replace("/[^a-z0-9.]/", "", $baseUsername);

    $username = $baseUsername;
    $counter = 1;

    while (true) {
        $checkUsernameStmt = $conn->prepare("
            SELECT userID
            FROM UserAccount
            WHERE username = ?
        ");
        $checkUsernameStmt->execute([$username]);

        if (!$checkUsernameStmt->fetch(PDO::FETCH_ASSOC)) {
            break;
        }

        $username = $baseUsername . $counter;
        $counter++;
    }

    $randomPassword = bin2hex(random_bytes(4));

    $passwordToStore = $randomPassword;

    $insertStmt = $conn->prepare("
        INSERT INTO UserAccount (
            username,
            passwordHash,
            role,
            patientID,
            providerID,
            medAssistantID
        )
        VALUES (?, ?, 'patient', ?, NULL, NULL)
    ");

    $insertStmt->execute([
        $username,
        $passwordToStore,
        $patientID
    ]);

    echo json_encode([
        "status" => "success",
        "username" => $username,
        "password" => $randomPassword
    ]);
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Could not create patient account",
        "details" => $e->getMessage()
    ]);
}
?>
