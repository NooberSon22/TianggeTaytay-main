<?php
// Include database connection
include_once "connect.php";

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

if (isset($_POST['userid'])) {
    $userid = $_POST['userid'];

    try {
        // Begin a transaction
        $conn->beginTransaction();

        // Step 1: Select the record to be archived
        $query_select = "SELECT * FROM admintb WHERE userid = :userid";
        $stmt_select = $conn->prepare($query_select);
        $stmt_select->bindParam(':userid', $userid, PDO::PARAM_INT);
        $stmt_select->execute();
        $category = $stmt_select->fetch(PDO::FETCH_ASSOC);

        if ($category) {
            // Step 2: Insert the record into archived_categories
            $query_archive = "INSERT INTO archived_admintb (admin_id, username, password, first_name, middle_name, surname, email, role) VALUES (:admin_id, :username, :password, :first_name, :middle_name, :surname, :email, :role)";
            $stmt_archive = $conn->prepare($query_archive);

            $stmt_archive->bindParam(':admin_id', $category['admin_id'], PDO::PARAM_INT);
            $stmt_archive->bindParam(':username', $category['username'], PDO::PARAM_STR);
            $stmt_archive->bindParam(':password', $hashed_password, PDO::PARAM_STR); // Bind hashed password
            $stmt_archive->bindParam(':first_name', $category['first_name'], PDO::PARAM_STR);
            $stmt_archive->bindParam(':middle_name', $category['middle_name'], PDO::PARAM_STR);
            $stmt_archive->bindParam(':surname', $category['surname'], PDO::PARAM_STR);
            $stmt_archive->bindParam(':email', $category['email'], PDO::PARAM_STR);
            $stmt_archive->bindParam(':role', $category['role'], PDO::PARAM_STR);

            // Hash the password
            $hashed_password = password_hash($category['password'], PASSWORD_DEFAULT);


            $stmt_archive->execute();

            // Step 3: Delete the record from categorytb
            $query_delete = "DELETE FROM admintb WHERE userid = :userid";
            $stmt_delete = $conn->prepare($query_delete);
            $stmt_delete->bindParam(':userid', $userid, PDO::PARAM_INT);
            $stmt_delete->execute();

            $action = $adminUsername . " archived an admin " . $category['username'];
                $logSql = "INSERT INTO actlogtb (usertype, email, action) 
                           VALUES (:usertype, :email, :action)";

                $logStmt = $conn->prepare($logSql);
                $logStmt->bindParam(':usertype', $adminRole);
                $logStmt->bindParam(':email', $adminEmail);
                $logStmt->bindParam(':action', $action);

                // Execute the log query
                $logStmt->execute();

            // Commit the transaction
            $conn->commit();
            header("Location: ../pages/users.php?section=categories&success=Admin archived successfully");
        } else {
            echo "Admin not found.";
        }
    } catch (PDOException $e) {
        // Rollback the transaction if something goes wrong
        $conn->rollBack();
        echo "Error archiving Admin: " . $e->getMessage();
    }
} else {
    echo "No Admin ID provided.";
}
?>