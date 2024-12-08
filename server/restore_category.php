<?php
include_once "connect.php";

if (isset($_POST['categoryid'])) {
    $categoryid = $_POST['categoryid'];

    try {
        $conn->beginTransaction();

        // Step 1: Fetch the archived category
        $query_fetch = "SELECT * FROM archived_categories WHERE categoryid = :categoryid";
        $stmt_fetch = $conn->prepare($query_fetch);
        $stmt_fetch->bindParam(':categoryid', $categoryid, PDO::PARAM_INT);
        $stmt_fetch->execute();
        $category = $stmt_fetch->fetch(PDO::FETCH_ASSOC);

        if ($category) {
            // Step 2: Insert back into categorytb
            $query_restore = "INSERT INTO categorytb (categoryid, category_name) VALUES (:categoryid, :category_name)";
            $stmt_restore = $conn->prepare($query_restore);
            $stmt_restore->bindParam(':categoryid', $category['categoryid'], PDO::PARAM_INT);
            $stmt_restore->bindParam(':category_name', $category['category_name'], PDO::PARAM_STR);
            $stmt_restore->execute();

            // Step 3: Delete from archived_categories
            $query_delete = "DELETE FROM archived_categories WHERE categoryid = :categoryid";
            $stmt_delete = $conn->prepare($query_delete);
            $stmt_delete->bindParam(':categoryid', $categoryid, PDO::PARAM_INT);
            $stmt_delete->execute();

            $conn->commit();
            header('Location: ../pages/settings.php?section=archive&success=Category restored successfully');
        } else {
            echo "Archived category not found.";
        }
    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Error restoring category: " . $e->getMessage();
    }
} else {
    echo "No category ID provided.";
}
?>
