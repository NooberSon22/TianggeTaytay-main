<?php 

$sellerHTML = include('../server/fetchstore.php');

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>e-Tiangge Taytay</title>
    <link rel="stylesheet" href="../style/store.css">
    <link rel="stylesheet" href="../style/navandfoot.css">
</head>

<style>
.path {
    padding: 30px 100px;
    width: 100%;
    display: flex;
    align-items: center;
}

.path a {
    color: #2c2c2c;
    margin-right: 10px;
    font-size: 17px !important;
    font-weight: 400;
    text-decoration: none;
}

.store {
    padding: 0 200px 0 250px;
    display: grid;
    margin-top: 20px;
    margin-bottom: 150px;
    gap: 5rem;
    grid-template-columns: repeat(4, 1fr);
}
</style>

<body>

    <div class="register">
        <p>Become a Seller? <a href="register.php">Register Now</a></p>
    </div>

    <!-- Navbar Section -->

    <?php
    include("../components/nav.php");
    ?>

    <div class="main-store">

        <div class="path">
            <a href="">Home</a>
            <img src="../assets/arrowrightblack.png" alt="">
            <a href="">Store</a>
        </div>

        <div class="store">
            <?php 

            echo $sellerHTML;

            ?>
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

</body>

</html>