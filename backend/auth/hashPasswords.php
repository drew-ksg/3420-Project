<?php
require("../dbConnection.php");

$conn = connect();

if ($conn === NULL) {
    die("Database connection failed");
}

try {

    $stmt = $conn->prepare("
        SELECT userID, passwordHash
        FROM UserAccount
    ");

    $stmt->execute();

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($users as $user) {

        $userID = $user["userID"];
        $plainPassword = $user["passwordHash"];

        if (password_get_info($plainPassword)["algo"] !== 0) {
            continue;
        }

        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

        $updateStmt = $conn->prepare("
            UPDATE UserAccount
            SET passwordHash = ?
            WHERE userID = ?
        ");

        $updateStmt->execute([
            $hashedPassword,
            $userID
        ]);

        echo "Updated userID: $userID<br>";
    }

    echo "<br>Migration complete.";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>