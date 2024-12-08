<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include("connect.php");

    // Retrieve form values
    $username = $_POST['newusername'];
    $password = $_POST['newpassword'];
    $seller_email = $_POST['seller_email'];
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'] ?? null;
    $lastname = $_POST['lastname'];
    $contact = $_POST['contact'];
    $birthday = $_POST['birthday'];
    $age = $_POST['age'];
    $province = $_POST['province'];
    $municipality = $_POST['municipality'];
    $baranggay = $_POST['baranggay'];
    $houseno = $_POST['houseno'];
    $current_username = $_POST['current_username'];
    $current_password = $_POST['currentpassword'];

    // Step 1: Validate the current password if provided
    try {
        if (!empty($current_password)) {
            // Check the current password in the database
            $stmt = $conn->prepare("SELECT password FROM sellertb WHERE username = :current_username");
            $stmt->bindParam(':current_username', $current_username);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row || !password_verify($current_password, $row['password'])) {
                header("Location: ../pages/seller-info.php?error=currentpassword");
                exit;
            }
        }
    } catch (PDOException $e) {
        echo "Error validating current password: " . $e->getMessage();
        exit;
    }

    // Step 2: Check if the new username or email already exists
    try {
        // Check if username exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM sellertb WHERE username = :username AND username != :current_username");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':current_username', $current_username);
        $stmt->execute();
        $usernameCount = $stmt->fetchColumn();

        // Check if email exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM sellertb WHERE seller_email = :seller_email AND username != :current_username");
        $stmt->bindParam(':seller_email', $seller_email);
        $stmt->bindParam(':current_username', $current_username);
        $stmt->execute();
        $emailCount = $stmt->fetchColumn();

        if ($usernameCount > 0) {
            header("Location: ../pages/seller-info.php?error=username");
            exit;
        }

        if ($emailCount > 0) {
            header("Location: ../pages/seller-info.php?error=email");
            exit;
        }
    } catch (PDOException $e) {
        echo "Error checking username/email: " . $e->getMessage();
        exit;
    }

    // Step 3: Validate the new password length (if provided)
    if (!empty($password) && strlen($password) < 8) {
        header("Location: ../pages/seller-info.php?error=newpassword");
        exit;
    }

    // Step 4: Prepare update query
    $passwordQuery = "";
    $params = [
        ':newusername' => $username,
        ':seller_email' => $seller_email,
        ':firstname' => $firstname,
        ':middlename' => $middlename,
        ':lastname' => $lastname,
        ':seller_contact' => $contact,
        ':birthday' => $birthday,
        ':age' => $age,
        ':province' => $province,
        ':municipality' => $municipality,
        ':baranggay' => $baranggay,
        ':houseno' => $houseno,
        ':current_username' => $current_username
    ];

    // If password is provided, hash it and add it to the query
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $passwordQuery = "password = :newpassword,";
        $params[':newpassword'] = $hashedPassword;
    }

    // Prepare the update query
    $updateQuery = "UPDATE sellertb 
                    SET
                        username = :newusername,
                        $passwordQuery
                        seller_email = :seller_email,
                        first_name = :firstname, 
                        middle_name = :middlename, 
                        last_name = :lastname, 
                        seller_contact = :seller_contact, 
                        birthday = :birthday, 
                        age = :age, 
                        province = :province, 
                        municipality = :municipality, 
                        baranggay = :baranggay, 
                        houseno = :houseno 
                    WHERE username = :current_username";

    try {
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->execute($params);
        
        header("Location: ../pages/seller-info.php?success=update"); // Redirect on success
        exit;
    } catch (PDOException $e) {
        echo "Error updating seller info: " . $e->getMessage();
    }
}
?>
