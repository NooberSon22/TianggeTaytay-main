<?php
// Include database connection
include_once "connect.php";

// Initialize the $types variable to ensure it is defined
$types = [];

try {
    // Query to fetch all types
    $query_all = "SELECT * FROM producttypetb";  
    $stmt_all = $conn->prepare($query_all);
    $stmt_all->execute();

    // Process all types (to store in types array)
    if ($stmt_all->rowCount() > 0) {
        while ($row = $stmt_all->fetch(PDO::FETCH_ASSOC)) {
            // Collect all type data into the $types array
            $types[] = [
                'typeid' => $row['typeid'],
                'typename' => $row['typename']
            ];
        }
    } else {
        // If no types are found, keep $types as an empty array
        $types = [];
    }
} catch (PDOException $e) {
    // In case of an error, initialize an empty array for $types
    $types = [];
    $errorMessage = "Error fetching types: " . $e->getMessage();
    echo $errorMessage; // Display the error message
}
?>
