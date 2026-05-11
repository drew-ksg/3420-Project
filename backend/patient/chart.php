<?php
header("Content-Type: application/json");

require_once("../dbConnection.php");
$conn = connect();

if ($conn === NULL) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

$patientID = $_GET["patientID"] ?? "";

if ($patientID === "") {
    echo json_encode(["status" => "error", "message" => "Missing patientID"]);
    exit;
}

try {
    $patientStmt = $conn->prepare("
        SELECT patientID, firstName, lastName, dob, gender, email, phone
        FROM Patient
        WHERE patientID = ?
    ");
    $patientStmt->execute([$patientID]);
    $patient = $patientStmt->fetch(PDO::FETCH_ASSOC);

    if (!$patient) {
        echo json_encode(["status" => "error", "message" => "Patient not found"]);
        exit;
    }

    $chartStmt = $conn->prepare("
        SELECT chartID, patientID, address, emergencyContactName, emergencyContactPhone,
               allergies, medications, medicalHistory, progressNotes
        FROM PatientChart
        WHERE patientID = ?
    ");
    $chartStmt->execute([$patientID]);
    $chart = $chartStmt->fetch(PDO::FETCH_ASSOC);

    $insuranceStmt = $conn->prepare("
        SELECT insuranceID, patientID, providerName, idNumber, groupNumber, coverageType
        FROM Insurance
        WHERE patientID = ?
        ORDER BY insuranceID DESC
        LIMIT 1
    ");
    $insuranceStmt->execute([$patientID]);
    $insurance = $insuranceStmt->fetch(PDO::FETCH_ASSOC);

    $userStmt = $conn->prepare("
        SELECT userID, username, role, patientID
        FROM UserAccount
        WHERE patientID = ?
          AND role = 'patient'
        LIMIT 1
    ");
    $userStmt->execute([$patientID]);
    $userAccount = $userStmt->fetch(PDO::FETCH_ASSOC);

    $appointmentsStmt = $conn->prepare("
        SELECT appointmentID, patientID, providerID, medAssistantID, apptDate, reasonForVisit, status
        FROM Appointment
        WHERE patientID = ?
        ORDER BY apptDate DESC
    ");
    $appointmentsStmt->execute([$patientID]);
    $appointments = $appointmentsStmt->fetchAll(PDO::FETCH_ASSOC);

    $notesStmt = $conn->prepare("
        SELECT
            s.noteID,
            s.appointmentID,
            a.apptDate,
            a.reasonForVisit AS chiefComplaint,
            a.reasonForVisit,
            s.subjective,
            s.objective,
            s.assessment,
            s.plan
        FROM SOAPNote s
        JOIN Appointment a ON s.appointmentID = a.appointmentID
        WHERE a.patientID = ?
        ORDER BY a.apptDate DESC
    ");
    $notesStmt->execute([$patientID]);
    $notes = $notesStmt->fetchAll(PDO::FETCH_ASSOC);

    $documentsStmt = $conn->prepare("
        SELECT documentID, patientID, filePath, uploadedAt, documentType
        FROM Document
        WHERE patientID = ?
        ORDER BY uploadedAt DESC
    ");
    $documentsStmt->execute([$patientID]);
    $documents = $documentsStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "patient" => $patient,
        "chart" => $chart,
        "insurance" => $insurance,
        "appointments" => $appointments,
        "notes" => $notes,
        "userAccount" => $userAccount,
        "documents" => $documents
    ]);
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Could not load patient chart",
        "details" => $e->getMessage()
    ]);
}
?>
