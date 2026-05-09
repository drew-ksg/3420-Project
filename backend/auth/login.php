<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once("../dbConnection.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method"
    ]);
    exit;
}

$username = $_POST["username"] ?? "";
$password = $_POST["password"] ?? "";

if (empty($username) || empty($password)) {
    echo json_encode([
        "status" => "error",
        "message" => "Username and password are required"
    ]);
    exit;
}

$stmt = $conn->prepare("
    SELECT userID, username, passwordHash, role, providerID, medAssistantID, patientID
    FROM UserAccount
    WHERE username = ?
");

$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid username or password"
    ]);
    exit;
}

if ($password !== $user["passwordHash"]) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid username or password"
    ]);
    exit;
}

echo json_encode([
    "status" => "success",
    "message" => "Login successful",
    "userID" => $user["userID"],
    "username" => $user["username"],
    "role" => $user["role"],
    "providerID" => $user["providerID"],
    "medAssistantID" => $user["medAssistantID"],
    "patientID" => $user["patientID"]
]);
?>