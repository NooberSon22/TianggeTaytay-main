<?php

header('Content-Type: application/json');
include_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form inputs
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $firstName = $_POST['first_name'];
    $middleName = $_POST['middle_name'] ?? null; // Optional
    $lastName = $_POST['last_name'];
    $contact = $_POST['contact'];
    $birthday = $_POST['birthday'];
    $age = $_POST['age'];
    $province = $_POST['province'];
    $municipality = $_POST['municipality'];
    $baranggay = $_POST['barangay'];
    $houseno = $_POST['houseno'];
    $lazada = isset($_POST['lazada']) ? $_POST['lazada'] : null;
    $shopee = isset($_POST['shopee']) ? $_POST['shopee'] : null;
    $stallName = $_POST['stall_name'];
    $storeName = $_POST['store_name'];
    $updatedAt = date('M d, Y');

    if (isset($_FILES['permit']) && $_FILES['permit']['error'] === UPLOAD_ERR_OK) {
        // Get the file content
        $permit = file_get_contents($_FILES['permit']['tmp_name']);
    } else {
        $permit = null; // No permit file uploaded
    }

    // Default image file
    $imagePath = '../assets/storepic.png';

    // Check if the file exists and read its content
    if (file_exists($imagePath)) {
        $imageData = file_get_contents($imagePath);
    } else {
        die(json_encode(['status' => 'error', 'message' => 'Default image file not found.']));
    }

    // Validate password and confirm password
    if ($password !== $confirmPassword) {
        die(json_encode(['status' => 'error', 'message' => 'Passwords do not match.']));
    }

    try {
        // Ensure storename, username, and email are not empty
        if (empty($username) || empty($email) || empty($storeName)) {
            echo json_encode(['status' => 'error', 'errors' => ['general' => 'All fields must be filled out.']]);
            exit;
        }
    
        // Check if username exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM sellertb WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $usernameCount = $stmt->fetchColumn();
    
        // Check if email exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM sellertb WHERE seller_email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $emailCount = $stmt->fetchColumn(); 
    
        // Check if store exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM storetb WHERE storename = :storename");
        $stmt->bindParam(':storename', $storeName);
        $stmt->execute();
        $storeCount = $stmt->fetchColumn();
    
        // Return error if username exists
        if ($usernameCount > 0) {
            echo json_encode(['status' => 'error', 'errors' => ['username' => 'Username already exists.']]);
            exit;
        }
    
        // Return error if email exists
        if ($emailCount > 0) {
            echo json_encode(['status' => 'error', 'errors' => ['email' => 'Email already exists.']]);
            exit;
        }
    
        // Return error if store already exists
        if ($storeCount > 0) {
            echo json_encode(['status' => 'error', 'errors' => ['storename' => 'Store already exists.']]);
            exit;
        }
    
    
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Begin transaction
        $conn->beginTransaction();

        // Insert into sellertb
        $sellerQuery = "INSERT INTO sellertb (username, password, seller_email, first_name, middle_name, last_name, seller_contact, birthday, age, province, municipality, baranggay, houseno, updated_at, permit) 
                        VALUES (:username, :password, :seller_email, :first_name, :middle_name, :last_name, :seller_contact, :birthday, :age, :province, :municipality, :baranggay, :houseno, :updated_at, :permit)";
        $sellerStmt = $conn->prepare($sellerQuery);
        $sellerStmt->bindParam(':username', $username, PDO::PARAM_STR);
        $sellerStmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        $sellerStmt->bindParam(':seller_email', $email, PDO::PARAM_STR);
        $sellerStmt->bindParam(':first_name', $firstName, PDO::PARAM_STR);
        $sellerStmt->bindParam(':middle_name', $middleName, PDO::PARAM_STR);
        $sellerStmt->bindParam(':last_name', $lastName, PDO::PARAM_STR);
        $sellerStmt->bindParam(':seller_contact', $contact, PDO::PARAM_STR);
        $sellerStmt->bindParam(':birthday', $birthday, PDO::PARAM_STR);
        $sellerStmt->bindParam(':age', $age, PDO::PARAM_INT);
        $sellerStmt->bindParam(':province', $province, PDO::PARAM_STR);
        $sellerStmt->bindParam(':municipality', $municipality, PDO::PARAM_STR);
        $sellerStmt->bindParam(':baranggay', $baranggay, PDO::PARAM_STR);
        $sellerStmt->bindParam(':houseno', $houseno, PDO::PARAM_STR);
        $sellerStmt->bindParam(':updated_at', $updatedAt, PDO::PARAM_STR);
        $sellerStmt->bindParam(':permit', $permit, PDO::PARAM_LOB); // Insert permit as LOB
        $sellerStmt->execute();

        // Get the last inserted seller_id
        $sellerId = $conn->lastInsertId();

        // Insert into storetb
        $storeQuery = "INSERT INTO storetb (sellerid, storename, img, lazada, shopee) 
                       VALUES (:sellerid, :storename, :img, :lazada, :shopee)";
        $storeStmt = $conn->prepare($storeQuery);
        $storeStmt->bindParam(':sellerid', $sellerId, PDO::PARAM_INT);
        $storeStmt->bindParam(':storename', $storeName, PDO::PARAM_STR);
        $storeStmt->bindParam(':img', $imageData, PDO::PARAM_LOB); // Bind as LOB
        $storeStmt->bindParam(':lazada', $lazada, PDO::PARAM_STR);
        $storeStmt->bindParam(':shopee', $shopee, PDO::PARAM_STR);
        $storeStmt->execute();

        // Insert into stalltb
        $stallQuery = "INSERT INTO stalltb (stallnumber, storename) 
                       VALUES (:stallnumber, :storename)";
        $stallStmt = $conn->prepare($stallQuery);
        $stallStmt->bindParam(':stallnumber', $stallName, PDO::PARAM_INT);
        $stallStmt->bindParam(':storename', $storeName, PDO::PARAM_STR);
        $stallStmt->execute();

        // Commit transaction
        $conn->commit();

        // Send a JSON success response
        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        // Rollback on error
        $conn->rollBack();
        die(json_encode(['status' => 'error', 'message' => $e->getMessage()]));
    }
} else {
    die(json_encode(['status' => 'error', 'message' => 'Invalid request method.']));
}
