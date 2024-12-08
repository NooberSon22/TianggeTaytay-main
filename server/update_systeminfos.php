<?php
include '../server/connect.php'; // Include the database connection

session_start();

$userid = $_SESSION['userid'];

// Fetch store details from the database
$stmt = $conn->prepare("SELECT userid, username, password, first_name, middle_name, surname, email, role, img FROM admintb WHERE userid = :userid");
$stmt->execute(['userid' => $userid]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if ($admin) {
    $admin_id = $admin['userid'];
    $adminUsername = $admin['username']; 
    $adminRole = $admin['role'];
    $adminEmail = $admin['email'];
} 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => 'No action performed.'];

    try {
        if (isset($_FILES['systemlogo']) && $_FILES['systemlogo']['error'] === UPLOAD_ERR_OK) {
            $logoData = file_get_contents($_FILES['systemlogo']['tmp_name']);
            $sql = "UPDATE systeminfo SET systemlogo = :systemlogo WHERE id = 1";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':systemlogo', $logoData, PDO::PARAM_LOB);
            $stmt->execute();

            $action = $adminUsername . " updated the system logo";
            $logSql = "INSERT INTO actlogtb (usertype, email, action) 
                       VALUES (:usertype, :email, :action)";

            $logStmt = $conn->prepare($logSql);
            $logStmt->bindParam(':usertype', $adminRole);
            $logStmt->bindParam(':email', $adminEmail);
            $logStmt->bindParam(':action', $action);

            // Execute the log query
            $logStmt->execute();

            header("Location: ../pages/admin-settings.php?section=general");
        }

        if (!empty($_POST['terms'])) {
            $terms = $_POST['terms'];
            $sql = "UPDATE systeminfo SET TC = :TC WHERE id = 1";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':TC', $terms);
            $stmt->execute();

            $action = $adminUsername . " updated the Terms and Condition";
            $logSql = "INSERT INTO actlogtb (usertype, email, action) 
                       VALUES (:usertype, :email, :action)";

            $logStmt = $conn->prepare($logSql);
            $logStmt->bindParam(':usertype', $adminRole);
            $logStmt->bindParam(':email', $adminEmail);
            $logStmt->bindParam(':action', $action);

            // Execute the log query
            $logStmt->execute();

            header("Location: ../pages/admin-settings.php?section=general");
        }

        if (!empty($_POST['privacy'])) {
            $privacy = $_POST['privacy'];
            $sql = "UPDATE systeminfo SET PP = :PP WHERE id = 1";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':PP', $privacy);
            $stmt->execute();

            $action = $adminUsername . " updated the Pricavy Policy";
            $logSql = "INSERT INTO actlogtb (usertype, email, action) 
                       VALUES (:usertype, :email, :action)";

            $logStmt = $conn->prepare($logSql);
            $logStmt->bindParam(':usertype', $adminRole);
            $logStmt->bindParam(':email', $adminEmail);
            $logStmt->bindParam(':action', $action);

            // Execute the log query
            $logStmt->execute();

            header("Location: ../pages/admin-settings.php?section=general");
        }
    } catch (Exception $e) {
        $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
