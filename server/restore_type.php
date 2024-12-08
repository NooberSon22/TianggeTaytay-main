<?php
include_once "connect.php";

if (isset($_POST['typeid'])) {
    $typeid = $_POST['typeid'];

    try {
        $conn->beginTransaction();

        // Step 1: Fetch the archived category
        $query_fetch = "SELECT * FROM archived_productypetb WHERE typeid = :typeid";
        $stmt_fetch = $conn->prepare($query_fetch);
        $stmt_fetch->bindParam(':typeid', $typeid, PDO::PARAM_INT);
        $stmt_fetch->execute();
        $type = $stmt_fetch->fetch(PDO::FETCH_ASSOC);

        if ($type) {
            // Step 2: Insert back into categorytb
            $query_restore = "INSERT INTO producttypetb (typeid, typename) VALUES (:typeid, :typename)";
            $stmt_restore = $conn->prepare($query_restore);
            $stmt_restore->bindParam(':typeid', $type['typeid'], PDO::PARAM_INT);
            $stmt_restore->bindParam(':typename', $type['typename'], PDO::PARAM_STR);
            $stmt_restore->execute();

            // Step 3: Delete from archived_categories
            $query_delete = "DELETE FROM archived_productypetb WHERE typeid = :typeid";
            $stmt_delete = $conn->prepare($query_delete);
            $stmt_delete->bindParam(':typeid', $typeid, PDO::PARAM_INT);
            $stmt_delete->execute();

            $conn->commit();
            header('Location: ../pages/settings.php?section=archive&success=Product Type restored successfully');
exit; // Ensure no further code is executed

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