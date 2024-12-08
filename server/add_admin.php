<?php
// Include database connection file
require_once('connect.php');

session_start();

$userid = $_SESSION['userid'];

// Fetch store details from the database
$stmt = $conn->prepare("SELECT userid, username, password, first_name, middle_name, surname, email, role, img FROM admintb WHERE userid = :userid");
$stmt->execute(['userid' => $userid]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if ($admin) {
    $admin_id = $admin['userid'];
    $adminUsername = $admin['username']; 
    $adminRole = $admin['role'];
    $adminEmail = $admin['email'];
} 

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $firstName = $_POST['first_name'];
    $middleName = $_POST['middle_name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Check if the username or email already exists
        $checkQuery = "SELECT * FROM admintb WHERE username = :username OR email = :email";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // If the username or email already exists, redirect with an error message
            header('Location: ../pages/settings.php?error=Username or email already exists');
        } else {
            // Prepare the insert query for the new admin
            $sql = "INSERT INTO admintb (first_name, middle_name, surname, email, username, password) 
                    VALUES (:first_name, :middle_name, :surname, :email, :username, :password)";

            // Prepare the statement
            $stmt = $conn->prepare($sql);

            // Bind the parameters to the query
            $stmt->bindParam(':first_name', $firstName);
            $stmt->bindParam(':middle_name', $middleName);
            $stmt->bindParam(':surname', $surname);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hashedPassword);

            // Execute the query for the admin
            if ($stmt->execute()) {
                // Log the action in actlogtb
                $action = $adminUsername . " added a new admin";
                $logSql = "INSERT INTO actlogtb (usertype, email, action) 
                           VALUES (:usertype, :email, :action)";

                $logStmt = $conn->prepare($logSql);
                $logStmt->bindParam(':usertype', $adminRole);
                $logStmt->bindParam(':email', $adminEmail);
                $logStmt->bindParam(':action', $action);

                // Execute the log query
                $logStmt->execute();

                // Redirect with a success message
                header('Location: ../pages/settings.php?success=Admin added successfully');
            } else {
                // If there was an error adding the admin
                header('Location: ../pages/settings.php?error=Error adding admin');
            }
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

// Function to send the email
function verify($email, $username, $password) {
    $mail = new PHPMailer(true);
    
    try {
        $mail->SMTPDebug = 0;  // Disable debug mode for production
        $mail->isSMTP();
        $mail->Host     = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'bantajio22@gmail.com';
        $mail->Password = 'mgqx cwpm dlrv ujxr';  // Use an application-specific password
        $mail->SMTPSecure = 'tls';
        $mail->Port     = 587;
    
        $mail->setFrom('e-tiangge@gmail.com', 'E-Tiangge Portal');
        $mail->addAddress($email);
    
        $mail->isHTML(true);
        $mail->Body = "
            Username: $username <br>
            Password: $password <br>
            <br>
            You can now proceed to the system portal by clicking the link below: <br>
            <a href='http://localhost/ETianggeTaytay/pages/login.php'>Login</a>
        ";

        $mail->send();
        echo "Mail has been sent successfully!";
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}


                // Send verification email after successfully adding admin
                verify($email, $username, $password);
?>
