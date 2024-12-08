<?php
require_once 'connect.php';

$errorMessage = '';
$successMessage = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve the form data
    $firstName = $_POST['first_name'] ?? '';
    $middleName = $_POST['middle_name'] ?? '';
    $surname = $_POST['surname'] ?? '';
    $email = $_POST['email'] ?? '';
    $username = $_POST['username'] ?? '';
    $currentPassword = $_POST['password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';

    try {
        // Validate inputs
        if (empty($username)) {
            throw new Exception("Username is required.");
        }
        if (empty($email)) {
            throw new Exception("Email is required.");
        }

        // Fetch the current password for validation
        $stmt = $conn->prepare("SELECT password FROM admintb WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin) {
            throw new Exception("Admin not found.");
        }

        // Check if the current password matches
        if (!empty($currentPassword) && !password_verify($currentPassword, $admin['password'])) {
        header('Location: ../pages/settings.php?section=account&error=Wrong Password');
        exit();
        }

        // Hash the new password if provided
        $hashedPassword = $admin['password']; // Keep current password if no new password is provided
        if (!empty($newPassword)) {
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        }

        // Update the admin's data in the database
        $stmt = $conn->prepare("
            UPDATE admintb 
            SET first_name = :first_name, 
                middle_name = :middle_name, 
                surname = :surname, 
                password = :password 
            WHERE username = :username
        ");
        $stmt->execute([
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'surname' => $surname,
            'password' => $hashedPassword,
            'username' => $username
        ]);

        header('Location: ../pages/settings.php?section=account&success=Updated Successfully');
    } catch (Exception $e) {
        header('Location: ../pages/settings.php?section=account&error=Wrong Password');
    }
}
?>