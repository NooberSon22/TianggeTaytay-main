<?php
// Include database connection
include('connect.php');

// Check if type ID is provided
if (isset($_POST['typeid']) && !empty($_POST['typeid'])) {
    $typeid = $_POST['typeid'];

    // Delete the type from the database
    try {
        // Check if the type exists before trying to delete
        $stmt = $conn->prepare("SELECT * FROM producttypetb WHERE typeid = :typeid");
        $stmt->bindParam(':typeid', $typeid, PDO::PARAM_INT);
        $stmt->execute();
        $type = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($type) {
            // Type exists, delete it
            $stmt = $conn->prepare("DELETE FROM producttypetb WHERE typeid = :typeid");
            $stmt->bindParam(':typeid', $typeid, PDO::PARAM_INT);
            $stmt->execute();

            // Redirect with success message
            header('Location: ../pages/admin-settings.php?section=type&success=Type deleted successfully');
            exit();
        } else {
            // Type does not exist
            header('Location: ../pages/admin-settings.php?section=type&error=Type not found');
            exit();
        }
    } catch (PDOException $e) {
        // Handle any errors
        header('Location: ../pages/admin-settings.php?section=type&error=Failed to delete type: ' . $e->getMessage());
        exit();
    }
} else {
    // Type ID is missing or invalid
    header('Location: ../pages/admin-settings.php?section=type&error=Invalid type ID');
    exit();
}
?>
