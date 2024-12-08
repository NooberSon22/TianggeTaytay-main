<?php
include_once "connect.php";

try {
    $query_archived = "SELECT userid, email, CONCAT(first_name,' ', middle_name ,' ', surname) AS fullname, role, status FROM admintb";
    $stmt_archived = $conn->prepare($query_archived);
    $stmt_archived->execute();
    $admins = $stmt_archived->fetchAll(PDO::FETCH_ASSOC);

    $query= "SELECT admin_id, email, username, role, status FROM archived_admintb";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $archiveAdmins = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $admins = [];
    $errorMessage = "Error fetching archived categories: " . $e->getMessage();
}
?>
