<?php
include_once "connect.php";

try {
    $query_archived = "SELECT * FROM archived_categories";
    $stmt_archived = $conn->prepare($query_archived);
    $stmt_archived->execute();
    $archived_categories = $stmt_archived->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $archived_categories = [];
    $errorMessage = "Error fetching archived categories: " . $e->getMessage();
}
?>
