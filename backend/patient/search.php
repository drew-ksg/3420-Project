<?php
header("Content-Type: application/json");

require_once("../dbConnection.php");
$conn = connect();

if ($conn === NULL) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

$term = $_GET["term"] ?? "";

if ($term === "") {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("
    SELECT patientID, firstName, lastName, dob, email, phone
    FROM Patient
    WHERE firstName LIKE ?
       OR lastName LIKE ?
       OR patientID LIKE ?
       OR dob LIKE ?
    ORDER BY lastName, firstName
    LIMIT 10
");

$searchTerm = "%" . $term . "%";
$stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
