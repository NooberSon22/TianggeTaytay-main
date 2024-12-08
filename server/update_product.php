<?php
// Include the database connection file
include_once 'connect.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the JSON data from the request body
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate if required fields are present
    if (isset($data['id'], $data['name'], $data['price'], $data['description'])) {
        // Assign values from the request
        $productId = $data['id'];
        $productName = $data['name'];
        $productPrice = $data['price'];
        $productDescription = $data['description'];

        try {
            // Prepare the SQL query to update the product
            $stmt = $conn->prepare("
                UPDATE producttb 
                SET product_name = :name, 
                    price = :price, 
                    description = :description 
                WHERE productid = :productid
            ");

            // Bind parameters to the query
            $stmt->bindParam(':name', $productName);
            $stmt->bindParam(':price', $productPrice);
            $stmt->bindParam(':description', $productDescription);
            $stmt->bindParam(':productid', $productId, PDO::PARAM_INT);

            // Execute the query
            if ($stmt->execute()) {
                // If successful, respond with a success message
                echo json_encode([
                    'success' => true, 
                    'message' => 'Product updated successfully.'
                ]);
            } else {
                // If failed, respond with an error message
                echo json_encode([
                    'success' => false, 
                    'message' => 'Failed to update product.'
                ]);
            }
        } catch (PDOException $e) {
            // Catch any PDO errors and respond with an error message
            echo json_encode([
                'success' => false, 
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    } else {
        // Respond with an error message if required data is missing
        echo json_encode([
            'success' => false, 
            'message' => 'Invalid input data. Required fields are missing.'
        ]);
    }
} else {
    // Respond with an error message if the request method is not POST
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid request method.'
    ]);
}
