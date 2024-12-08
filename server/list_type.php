<?php
include_once "connect.php";

try {
    $query_archived = "SELECT * FROM archived_productypetb";
    $stmt_archived = $conn->prepare($query_archived);
    $stmt_archived->execute();
    $archived_types = $stmt_archived->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $archived_types = [];
    $errorMessage = "Error fetching archived types: " . $e->getMessage();
}
?>
