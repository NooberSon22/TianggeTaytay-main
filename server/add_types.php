<?php
// Include database connection
include('connect.php');

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

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['typename'])) {
    $type_name = $_POST['typename'];

    // Check if type name is empty
    if (empty($type_name)) {
        header('Location: ../pages/admin-settings.php?section=type&rror=Type name cannot be empty');
        exit();
    }

    // Insert the new type into the database
    try {
        $stmt = $conn->prepare("INSERT INTO producttypetb (typename) VALUES (:typename)");
        $stmt->bindParam(':typename', $type_name, PDO::PARAM_STR);
        $stmt->execute();

        $action = $adminUsername . " added a new category";
                $logSql = "INSERT INTO actlogtb (usertype, email, action) 
                           VALUES (:usertype, :email, :action)";

                $logStmt = $conn->prepare($logSql);
                $logStmt->bindParam(':usertype', $adminRole);
                $logStmt->bindParam(':email', $adminEmail);
                $logStmt->bindParam(':action', $action);

                // Execute the log query
                $logStmt->execute();

        // Redirect back with success message
        header('Location: ../pages/admin-settings.php?section=type&success=Type added successfully');
        exit();
    } catch (PDOException $e) {
        // Handle any errors
        header('Location: ../pages/admin-settings.php?section=type&error=Failed to add type: ' . $e->getMessage());
        exit();
    }
} else {
    // If the form is not submitted, redirect back
    header('Location: ../pages/admin-settings.php?section=type&error=Invalid request');
    exit();
}
?>
