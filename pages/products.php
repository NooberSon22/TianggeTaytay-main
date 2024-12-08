<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/product1.css">
    <link rel="stylesheet" href="../style/navandfoot.css">
    <title>Document</title>
</head>

<body>
    <div class="register">
        <p>Become a Seller? <a href="register.php">Register Now</a></p>
    </div>
    <?php
        include("../components/nav.php");
    ?>

    <main class="container">
        <div class="content">
            <div class="filter-bar">
                <div class="filter-title">
                    <p>Filters</p>
                    <!-- <img src="../assets/filter-icon.png" alt="" class="reset-button"> -->
                </div>

                <div class="div-bar"></div>

                <div class="filter-type">
                    <!-- <div>
                        <p>T-shirts</p>
                        <img src="../assets/arrow-icon.png" alt="">
                    </div>
                    <div>
                        <p>Shorts</p>
                        <img src="../assets/arrow-icon.png" alt="">
                    </div>
                    <div>
                        <p>Shirts</p>
                        <img src="../assets/arrow-icon.png" alt="">
                    </div>
                    <div>
                        <p>Hoodie</p>
                        <img src="../assets/arrow-icon.png" alt="">
                    </div>
                    <div>
                        <p>Jeans</p>
                        <img src="../assets/arrow-icon.png" alt="">
                    </div> -->
                </div>

                <div class="div-bar"></div>

                <div class="filter-price">
                    <div>
                        <p>Price</p>
                        <img src="../assets/arrow-icon.png" alt="">
                    </div>

                    <div>
                        <input type="range" name="" id="">
                        <div class="price-range">
                            <p class="min-price"></p>
                            <p class="max-price"></p>
                        </div>
                    </div>
                </div>

                <div class="div-bar"></div>

                <div class="filter-categories">
                    <div class="title">
                        <p>Categories</p>
                        <img src="../assets/arrow-icon.png" alt="">
                    </div>

                    <div class="categories">
                    </div>
                </div>
                <div class="apply-filter reset-button">
                    Reset Filter
                </div>
            </div>

            <div class="products-container">
                <div class="breadcrumbs">
                    <span>Home</span>
                    <img src="../assets/arrow-icon.png" alt="">
                    <span>Products</span>
                    <img src="../assets/arrow-icon.png" alt="">
                    <span class="category"></span>
                </div>

                <div class="products" data-page="<?php echo isset($_GET['page']) ? $_GET['page'] : 1; ?>" data-type="<?php echo isset($_GET['type']) ? $_GET['type'] : null; ?>" data-category="<?php echo isset($_GET['category']) ? $_GET['category'] : null; ?>">
                </div>
            </div>
        </div>
        </div>


        <div class="pagination">
            <p class="results"></p>

            <div class="pages">
                <!-- <div class="button back-page">
                    <img src="../assets/pagination-right.png" alt="">
                </div>
                <div class="button next-page">
                    <img src="../assets/pagination-next.png" alt="">
                </div> -->
            </div>
        </div>
    </main>

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
                    <a style="color: #029f6f;" href="products.php">Find More</a> <img src="../assets/greenright.png" alt="">
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
<script src="../script/products.js"></script>
<script src="../script/products-fetch-ui.js"></script>

</html>