<?php
// Include database connection
include_once "connect.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['categoryid'])) {
    // Get the category ID from POST request
    $category_id = $_POST['categoryid'];

    try {
        // Prepare the delete query
        $query = "DELETE FROM categorytb WHERE categoryid = :categoryid";
        $stmt = $conn->prepare($query);

        // Bind the category ID
        $stmt->bindParam(':categoryid', $category_id, PDO::PARAM_INT);

        // Execute the query
        if ($stmt->execute()) {
            // Redirect back to the categories page after deletion
            header("Location: ../pages/settings.php?section=categories&success=Category deleted successfully");
            exit();
        } else {
            echo "Error: Unable to delete category.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
?>
