<?php
// Database connection details
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'tianggedb';

try {
    // Create a PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Sample image for the logo (this can be any valid image file)
    $imagePath = './assets/e-logo.png'; // Replace with the correct path
    $imageData = file_get_contents($imagePath); // Read image file into binary data

    // Sample Terms and Conditions and Privacy Policy text
    $termsAndConditions = "This is the Terms and Conditions of the system.";
    $privacyPolicy = "This is the Privacy Policy of the system.";

    // Insert SQL query
    $sql = "INSERT INTO systeminfo (systemlogo, TC, PP) VALUES (:systemlogo, :tc, :pp)";
    
    // Prepare the statement
    $stmt = $pdo->prepare($sql);
    
    // Bind values to the prepared statement
    $stmt->bindParam(':systemlogo', $imageData, PDO::PARAM_LOB);
    $stmt->bindParam(':tc', $termsAndConditions, PDO::PARAM_STR);
    $stmt->bindParam(':pp', $privacyPolicy, PDO::PARAM_STR);

    // Execute the insert query
    $stmt->execute();

    echo "Sample data inserted successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
