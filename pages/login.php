<?php
$error = isset($_GET['error']) ? $_GET['error'] : null;

include_once '../server/connect.php';

$sql = "SELECT systemlogo, TC, PP FROM systeminfo WHERE id = 1";
$stmt = $conn->prepare($sql);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC)
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>e-Tiangge Taytay</title>
    <link rel="stylesheet" href="../style/Login.css">
    <link rel="stylesheet" href="../style/navandfoot.css">
</head>

<body>


    <!-- back button to -->
    <div class="return-home">
        <a href="home.php" class="back-button">
            <img style="transform: rotate(180deg);" src="../assets/arrowrightblack.png" alt="Return to Homepage" />
            Return to Homepage
        </a>
    </div>

    <div class="container-center">
        <div class="container">
            <div class="login-image">
            <img src="data:image/png;base64,<?= base64_encode($data['systemlogo']) ?>" alt="System Logo">
            </div>
            <div class="login-wrapper">
                <h3>Welcome</h3>
                <p class="subheading">Log in to discover exclusive products</p>

                <form action="../server/fetchlogincredentials.php" method="POST">
                    <label>Username</label>
                    <input type="text" name="username" required>
                    <label>Password</label>
                    <input type="password" name="password" id="password" required>

                    <div class="show-password">
                        <label for="show-password-checkbox">
                            <input type="checkbox" id="show-password-checkbox" onclick="togglePassword()"> Show Password
                        </label>
                    </div>

                    <!-- Error Message -->
                    <?php if ($error): ?>
                    <div class="error-message-container">
                        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
                    </div>
                    <?php endif; ?>

                    <button type="submit">Log In</button>
                </form>


                <div class="register-now">
                    <p>Want to become a seller?</p>
                    <a href="register.php">Register Now</a>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <div class="top-footer">
            <div class="footer-logo">
                <img src="../assets/tianggeportal.png" alt="">
                <p>Find quality clothes and<br> garments in Taytay Tiangge<br> anytime and anywhere you are!</p>
            </div>

            <div class="footer-info">
                <h4 class="first-category">Information</h4>
                <ul>
                    <li><a href="about.php">About</a></li>
                    <li><a href="terms.php">Terms & Conditions</a></li>
                    <li><a href="privacy.php">Privacy Policy</a></li>
                </ul>
            </div>
            <div class="footer-info">
                <h4 class="second-category">Categories</h4>
                <ul>
                    <li><a href="products.php">Men's Fashion</a></li>
                    <li><a href="products.php">Women's Fashion</a></li>
                    <li><a href="products.php">Kid's</a></li>
                </ul>
                <div class="footer-products-shortcut">
                    <a style="color: #029f6f;" href="products.php">Find More</a> <img src="../assets/greenright.png"
                        alt="">
                </div>
            </div>
            <div class="footer-info">
                <h4 class="third-category">Help & Support</h4>
                <ul>
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
            </div>
        </div>
        <div class="bottom-footer">
            <p>e-Tiangge Portal © 2024.<br>
                All Rights Reserved.</p>
            <img src="../assets/municipalitylogo.png" alt="">
            <img src="../assets/smiletaytay.png" alt="">
        </div>
    </footer>

    <script>
    function togglePassword() {
        const passwordField = document.getElementById('password');
        const checkbox = document.getElementById('show-password-checkbox');
        passwordField.type = checkbox.checked ? 'text' : 'password';
    }
    </script>
</body>

</html>