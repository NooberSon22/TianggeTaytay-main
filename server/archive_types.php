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

if (isset($_POST['typeid'])) {
    $typeid = $_POST['typeid'];

    try {
        // Begin transaction
        $conn->beginTransaction();

        // Step 1: Fetch the type to be archived
        $query_select = "SELECT * FROM producttypetb WHERE typeid = :typeid";
        $stmt_select = $conn->prepare($query_select);
        $stmt_select->bindParam(':typeid', $typeid, PDO::PARAM_INT);
        $stmt_select->execute();
        $type = $stmt_select->fetch(PDO::FETCH_ASSOC);

        if ($type) {
            // Step 2: Insert the record into archived_productypetb
            $query_archive = "INSERT INTO archived_productypetb (typeid, typename) VALUES (:typeid, :typename)";
            $stmt_archive = $conn->prepare($query_archive);
            $stmt_archive->bindParam(':typeid', $type['typeid'], PDO::PARAM_INT);
            $stmt_archive->bindParam(':typename', $type['typename'], PDO::PARAM_STR);
            $stmt_archive->execute();

            // Step 3: Delete the record from productypetb
            $query_delete = "DELETE FROM producttypetb WHERE typeid = :typeid";
            $stmt_delete = $conn->prepare($query_delete);
            $stmt_delete->bindParam(':typeid', $typeid, PDO::PARAM_INT);
            $stmt_delete->execute();

            $action = $adminUsername . " archived a product type " . $type['typename'];
            $logSql = "INSERT INTO actlogtb (usertype, email, action) 
                       VALUES (:usertype, :email, :action)";

            $logStmt = $conn->prepare($logSql);
            $logStmt->bindParam(':usertype', $adminRole);
            $logStmt->bindParam(':email', $adminEmail);
            $logStmt->bindParam(':action', $action);

            // Execute the log query
            $logStmt->execute();

            // Commit transaction
            $conn->commit();
            header("Location: ../pages/admin-settings.php?section=type&success=Type archived successfully.");
        } else {
            header("Location: ../pages/admin-settings.php?section=type&error=Type not found.");
        }
    } catch (PDOException $e) {
        // Rollback the transaction in case of error
        $conn->rollBack();
        echo "Error archiving type: " . $e->getMessage();
    }
} else {
    echo "No type ID provided.";
}
?>
