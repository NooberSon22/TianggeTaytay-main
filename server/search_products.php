<?php

include "connect.php";

try {
    // Get JSON input and decode
    $input = json_decode(file_get_contents("php://input"), true);
    $search_term = isset($input['search_term']) ? $input['search_term'] : '';

    // Prepare SQL statement with LIKE operator
    $stmt = $conn->prepare("SELECT * FROM producttb WHERE product_name LIKE :search_term LIMIT 10 ");
    $stmt->bindValue(':search_term', '%' . $search_term . '%', PDO::PARAM_STR);
    $stmt->execute();

    // Fetch results
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return JSON response
    echo json_encode($products);
} catch (PDOException $e) {
    // Handle errors
    echo json_encode(["error" => $e->getMessage()]);
}

?>
