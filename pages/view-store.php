<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/reset.css">
    <link rel="stylesheet" href="../style/store-view.css">
    <link rel="stylesheet" href="../style/navandfoot.css">
    <link rel="stylesheet" href="../style/custom-select.css">
    <title>e-Tiangge Taytay</title>
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
            <div>
                <div class="breadcrumbs">
                    <span>Home</span>
                    <img src="../assets/arrowrightblack.png" alt="">
                    <span>Stores</span>
                    <img src="../assets/arrowrightblack.png" alt="">
                    <span class="name selected">STYL E.BOSS</span>
                </div>

                <div class="store-container">
                    <div class="store-info">
                        <div class="store-img">
                            <img class="store-logo" src="../assets/storepic.png" alt="">
                            <p class="store-name"></p>
                        </div>
                        <div>
                            <div class="info-container">
                                <img src="../assets/shipment-box.png" alt="">
                                <p>Products: <span class="product-count"></span></p>
                            </div>
                            <div class="info-container">
                                <img src="../assets/joined.png" alt="">
                                <p>Joined: <span class="store-join-date"></span></p>
                            </div>
                        </div>
                        <div>
                            <div class="info-container">
                                <img src="../assets/telephone.png" alt="">
                                <p>Contact: <span class="store-contact"></span></p>
                            </div>
                            <div class="info-container">
                                <img src="../assets/thread.png" alt="">
                                <p>Email: <span class="store-email"></span></p>
                            </div>
                        </div>
                        <div>
                            <div class="info-container">
                                <img src="../assets/stall.png" alt="">
                                <p>Stall No: <span class="store-stall"></span></p>
                            </div>
                        </div>
                    </div>

                    <div class="store-description">
                        <div class="store-description-text">
                        </div>

                        <div class="linked-accounts">
                            <p>Linked Accounts</p>
                            <div class="account-logos accounts">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="store-div"></div>

            <div class="store-products">
                <div class="store-products-header">
                    <p>from the shop</p>

                    <div>
                        <select class="custom-select categories-select">
                            <option value="All Products">All Categories</option>
                        </select>

                        <select class="custom-select types-select">
                            <option value="All Products">All Products</option>
                        </select>

                        <button class="reset-button">&#128472;</button>
                    </div>
                </div>

                <div class="products">
                </div>
            </div>

            <div class="pagination">
                <p class="results"></p>

                <div class="pages">
                </div>
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
<script src="../script/view-store.js"></script>
<script src="../script/view-store-ui.js"></script>

</html>