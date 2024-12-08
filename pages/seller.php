<?php
session_start(); // Start session
include_once '../server/connect.php';
include_once '../server/fetchproduct.php';

// Check if the seller is logged in
if (!isset($_SESSION['seller_id']) || $_SESSION['role'] !== 'seller') {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Use the seller_id from the session
$seller_id = $_SESSION['seller_id'];

// Fetch store details from the database
$stmt = $conn->prepare("SELECT storename, img FROM storetb WHERE sellerid = :sellerid");
$stmt->execute(['sellerid' => $seller_id]);
$store = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle cases where no store is found
$store_name = $store['storename'] ?? 'No Store Found';
$store_img = isset($store['img']) ? 'data:image/png;base64,' . base64_encode($store['img']) : '../assets/storepic.png';

// Include category and product fetch logic

list($categoryHTML, $categories) = include_once '../server/fetchcategory.php';
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
<style>
    .store-name {
        border-bottom: 1px solid #cccccc;
        border-radius: 0 !important;
    }

    .dropdown-menu {
        text-align: center;
    }

    .dropdown-menu .store-name {
        font-weight: 600;
    }

    .dropdown-menu a:hover {
        background-color: #fff !important;
    }
</style>

<body>


    <nav class="navbar">
        <div class="left-side">
            <a href="seller.php"><img src="../assets/shoppingbag.png" alt=""></a>
            <div class="input-with-icon">
                <img class="search-icon" src="../assets/Vector.png" alt="">
                <input type="text" placeholder="Search for Products...">
            </div>
        </div>
        <div class="right-side">
            <ul>
                <li class="selected"><a href="#">Home</a></li>
                <li><a href="about.php" target="_blank">About</a></li>
                <li><a href="products.php" target="_blank">Products</a></li>
                <li><a href="store.php" target="_blank">Store</a></li>
                <li><a href="contact.php" target="_blank">Contact us</a></li>
            </ul>
        </div>


        <div class="dropdown-container" id="dropdown">
            <!-- Store Image -->
            <img style="border-radius: 50px; width: 60px; height: 60px;" src="<?php echo $store_img; ?>"
                alt="Store Image">
            <!-- Arrow Icon -->
            <img id="arrow" style="width: 20px; height: 20px; transform: rotate(90deg);"
                src="../assets/arrowrightblack.png" alt="">
        </div>

        <!-- Dropdown Menu -->
        <div class="dropdown-menu" id="dropdown-menu">
            <a class="store-name" href="#"><?php echo $store_name; ?></a>
            <a href="seller-info.php">Manage Account</a>
            <a href="store-info.php">Manage Store</a>
            <a style="color: red;" href="logout.php">Logout</a>
        </div>
    </nav>

    <!-- Blue Container for Feature Descriptions -->
    <main>
        <div class="main-left-side">
            <div class="hero">
                <img src="../assets/tianggeportal.png" alt="">
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
            <img style="width: 100%; height: 100%;" src="../assets/girlinayellow.png" alt="" />
            <div class="products-shortcut">
                <a href="product.php">See All Products</a> <img src="../assets/arrowright.png" alt="">
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
<script>
    <?php if (isset($_SESSION['username'])): ?>
        alert("Welcome, <?php echo $_SESSION['username']; ?>!");
    <?php endif; ?>
</script>
<script src="../script/drop-down.js"></script>

</html>