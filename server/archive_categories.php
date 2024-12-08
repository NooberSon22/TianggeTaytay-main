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

if (isset($_POST['categoryid'])) {
    $categoryid = $_POST['categoryid'];

    try {
        // Begin a transaction
        $conn->beginTransaction();

        // Step 1: Select the record to be archived
        $query_select = "SELECT * FROM categorytb WHERE categoryid = :categoryid";
        $stmt_select = $conn->prepare($query_select);
        $stmt_select->bindParam(':categoryid', $categoryid, PDO::PARAM_INT);
        $stmt_select->execute();
        $category = $stmt_select->fetch(PDO::FETCH_ASSOC);

        if ($category) {
            // Step 2: Insert the record into archived_categories
            $query_archive = "INSERT INTO archived_categories (categoryid, category_name) VALUES (:categoryid, :category_name)";
            $stmt_archive = $conn->prepare($query_archive);
            $stmt_archive->bindParam(':categoryid', $category['categoryid'], PDO::PARAM_INT);
            $stmt_archive->bindParam(':category_name', $category['category_name'], PDO::PARAM_STR);
            $stmt_archive->execute();

            // Step 3: Delete the record from categorytb
            $query_delete = "DELETE FROM categorytb WHERE categoryid = :categoryid";
            $stmt_delete = $conn->prepare($query_delete);
            $stmt_delete->bindParam(':categoryid', $categoryid, PDO::PARAM_INT);
            $stmt_delete->execute();

            $action = $adminUsername . " archived a category " . $category['category_name'];
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
            header("Location: ../pages/admin-settings.php?section=categories&success=Category archived successfully");
        } else {
            echo "Category not found.";
        }
    } catch (PDOException $e) {
        // Rollback the transaction if something goes wrong
        $conn->rollBack();
        echo "Error archiving category: " . $e->getMessage();
    }
} else {
    echo "No category ID provided.";
}
?>
