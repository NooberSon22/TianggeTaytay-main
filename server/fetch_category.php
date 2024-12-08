<?php

include_once 'connect.php';
// Example of fetching categories from a database and returning JSON
header('Content-Type: application/json'); // Set the response type to JSON

try {
    $conn = new PDO('mysql:host=localhost;dbname=tianggedb', 'root', ''); // Adjust credentials
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = 'SELECT categoryid, category_name FROM categorytb';
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return categories as JSON
    echo json_encode($categories);
} catch (Exception $e) {
    echo json_encode(['error' => 'Failed to fetch categories: ' . $e->getMessage()]);
}
?>
