<?php
// Include the database connection
include_once 'connect.php';

// Ensure the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read the raw input (JSON payload)
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['id'])) {
        $productId = $data['id'];

        try {
            // Prepare and execute the delete statement
            $stmt = $conn->prepare("DELETE FROM producttb WHERE productid = :productid");
            $stmt->bindParam(':productid', $productId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                // Respond with success
                echo json_encode(['success' => true, 'message' => 'Product deleted successfully.']);
            } else {
                // Respond with error
                echo json_encode(['success' => false, 'message' => 'Failed to delete product.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No product ID provided.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
