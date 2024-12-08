<?php
list($categoryHTML, $categories) = include_once '../server/fetchcategory.php';
include_once '../server/fetchproduct.php';
include_once '../server/connect.php';

$sql = "SELECT systemlogo, TC, PP FROM systeminfo WHERE id = 1";
$stmt = $conn->prepare($sql);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/home.css">
    <link rel="stylesheet" href="../style/navandfoot.css">
    <title>e-Tiangge Taytay</title>
</head>

<body>
    <!-- Register Seller Section -->
    <div class="register">
        <p>Become a Seller? <a href="register.php">Register Now</a></p>
    </div>

    <!-- Navbar Section -->
    <?php
    include("../components/nav.php");
    ?>

    <!-- Main Content Section -->
    <main>
        <div class="main-left-side">
            <div class="hero">
            <img src="data:image/png;base64,<?= base64_encode($data['systemlogo']) ?>" alt="System Logo">
                <div class="text-hero">
                    <h1>CONNECTING</h1>
                    <h1>COMMUNITIES</h1>
                    <h1>EMPOWERING LOCAL</h1>
                    <h1>BUSINESSES</h1>
                </div>
            </div>
            <p>Discover, shop, and support Taytay's finest local products <br>
                —all in one convenient digital marketplace.</p>
        </div>
        <div class="main-right-side">
            <div class="slider">
                <div class="image-slider">
                    <img src="../assets/slider1.png" alt="" />
                    <img src="../assets/slider2.png" alt="" />
                    <img src="../assets/slider3.png" alt="" />
                    <img src="../assets/slider1.png" alt="" />
                </div>
            </div>
            <div class="products-shortcut">
                <a href="products.php">See All Products</a> <img src="../assets/arrowright.png" alt="">
            </div>
        </div>
    </main>

    <!-- Blue Container for Feature Descriptions -->
    <div class="blue-container">
        <div class="content"><img src="../assets/shopping-basket.png" alt="">
            <div class="sub-content">
                <h3>Product Browsing</h3>
                <p>A catalog with categories, detailed product descriptions, and high-quality images.</p>
            </div>
        </div>
        <div class="content"><img src="../assets/shop.png" alt="">
            <div class="sub-content">
                <h3>Store Directory</h3>
                <p>A list of registered sellers or stores, each with a profile showcasing their offerings and contact
                    information.</p>
            </div>
        </div>
        <div class="content"><img src="../assets/search-alternate.png" alt="">
            <div class="sub-content">
                <h3>Product Search</h3>
                <p>Search functionality with filters for category, price, or keywords.</p>
            </div>
        </div>
        <div class="content"><img src="../assets/smart-phone.png" alt="">
            <div class="sub-content">
                <h3>Contact Seller</h3>
                <p>A feature allowing buyers to directly message sellers for inquiries or reservations</p>
            </div>
        </div>
    </div>

    <div class="category-container">
        <h1>CATEGORIES</h1>
        <div class="category-item">
            <?php echo $categoryHTML; ?>
        </div>
        <a href="products.php"><button>Browse Products</button></a>
    </div>

    <div class="divider">
        <div></div>
    </div>

    <div class="product-container">
        <h1>NEW ARRIVALS</h1>
        <div class="product-item">
            <?php
            // Assuming $product_details is your array of products
            $new_arrivals = fetchProducts("NEW_ARRIVALS"); // Get first 4 products

            foreach ($new_arrivals as $product): ?>
                <div class="product-card">
                    <!-- Display the first image of the product -->
                    <img src="<?php echo isset($product['first_image']) ? 'data:image/jpeg;base64,' . base64_encode($product['first_image']) : '../assets/default-product.png'; ?>"
                        alt="Product Image">

                    <!-- Product Name and Price -->
                    <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                    <p>₱<?php echo htmlspecialchars($product['price']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        <a href="products.php"><button class="view">View All</button></a>
    </div>

    <div class="divider">
        <div></div>
    </div>

    <div class="product-container last-product-container">
        <h1>MOST VIEWED</h1>
        <div class="product-item">
            <?php
            // Get last 4 products (assuming they are sorted by views or some other criteria)
            $most_viewed = fetchProducts("MOST_VIEWED");

            foreach ($most_viewed as $product): ?>
                <div class="product-card">

                    <img src="<?php echo isset($product['first_image']) ? 'data:image/jpeg;base64,' . base64_encode($product['first_image']) : '../assets/default-product.png'; ?>"
                        alt="Product Image">

                    <!-- Product Name and Price -->
                    <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                    <p>₱<?php echo htmlspecialchars($product['price']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        <a href="products.php"><button class="view">View All</button></a>
    </div>

    <footer>
        <div class="top-footer">
            <div class="footer-logo">
            <img src="data:image/png;base64,<?= base64_encode($data['systemlogo']) ?>" alt="System Logo">
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