<?php
// Start session
session_start();
include_once '../server/connect.php';
include_once '../server/fetchstoreinfo.php';
// Ensure the user is a seller



$storeDescription = htmlspecialchars($store['description'] ?? 'N/A');
$storeName = htmlspecialchars($store['storename'] ?? 'N/A');
$storeContact = htmlspecialchars($seller['store_contact'] ?? 'N/A');
$storeEmail = htmlspecialchars($store['email'] ?? 'N/A');

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>e-Tiangge Taytay</title>
    <link rel="stylesheet" href="../style/navandfoot.css">
    <link rel="stylesheet" href="../style/store-info.css">
</head>

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
            <a href="#" class="store-name"><?php echo $store_name; ?></a>
            <a href="seller-info.php">Manage Account</a>
            <a href="store-info.php">Manage Store</a>
            <a style="color: red;" href="logout.php">Logout</a>
        </div>
    </nav>

    <div id="productFormContainer" class="form-hidden">
        <div class="product-form-container">
            <form class="product-form" id="addingForm" action="../server/add_product.php" method="POST"
                enctype="multipart/form-data">
                <!-- Close Button -->
                <img src="../assets/close.png" id="closeFormButton" class="close-btn" alt="Close">

                <label>Product Name</label>
                <input type="text" name="product_name" required>

                <label>Product Description</label>
                <textarea class="description" name="description" id="" placeholder="Description"></textarea>

                <label>Price</label>
                <input type="text" name="price" required>

                <label>Category</label>
                <select name="category" id="category" class="form-category">
                    <?php foreach ($store_categories as $category): ?>
                        <option value="<?php echo $category['value']; ?>">
                            <?php echo htmlspecialchars($category['category_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="product-categories form-labels">
                </div>

                <label>Type</label>
                <select name="type" id="type" class="form-category">
                    <?php foreach ($product_types_store as $type): ?>
                        <option value="<?php echo $type['value']; ?>"><?php echo htmlspecialchars($type['typename']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="product-types form-labels">
                </div>

                <div id="image-container" class="img-container">
                    <label for="file-upload" class="img-file-upload">Choose Files</label>
                    <input type="file" id="file-upload" name="product_imgs[]" accept="image/*" multiple
                        style="opacity: 0%;" />
                </div>

                <label>Links</label>
                <div class="link-container">
                    <?php
                    foreach ($linkedstore_links as $link) {
                        echo "
                            <div>
                                <p>{$link["platformtype"]}:</p>
                                <input type=\"text\" name=\"links[{$link["idstore_linkstb"]}]\"  >
                            </div>
                        ";
                    }
                    ?>
                </div>


                <div class="add-button">
                    <button type="submit">Add Product</button>
                </div>
            </form>
        </div>
    </div>

    <div class="main-content">
        <div class="sidebar">
            <a href="seller-info.php">Manage Account</a>
            <a href="#" class="active">Manage Store</a>
        </div>

        <div class="store-info-main-container">
            <div class="account-container">
                <div class="edit-container">
                    <button class="edt-btn" id="editStoreBtn">
                        <img src="../assets/pencil.svg" alt="Edit">Edit Store Information
                    </button>
                </div>

                <div id="storeInfo" class="account-info">
                    <div class="store-info-card">
                        <img style="border-radius: 50px; width: 100px; height: 100px;" src="<?php echo $store_img; ?>"
                            alt="Store Image">
                        <p style="font-weight: 600;"><?php echo $store_name; ?></p>
                    </div>

                    <div class="info-card middle-info-card">
                        <div class="info"><img src="../assets/shipment-box.png" alt="">
                            <p>Products: <strong><?php echo $product_count; ?></strong></p>
                        </div>
                        <div class="info"><img src="../assets/joined.png" alt="">
                            <p>Created At: <strong><?php echo $created_at; ?></strong></p>
                        </div>
                        <div class="info"><img src="../assets/stall.png" alt="">
                            <p>Stall No: <strong><?php echo htmlspecialchars(implode(' ', $stallNumbers)); ?></strong>
                            </p>
                        </div>
                    </div>

                    <div class="info-card">
                        <div class="info"><img src="../assets/telephone.png" alt="">
                            <p>Contact: <strong><?php echo $store_contact; ?></strong></p>
                        </div>
                        <div class="info"><img src="../assets/thread.png" alt="">
                            <p>Email: <strong><?php echo $store_email; ?></strong></p>
                        </div>
                    </div>
                </div>

                <div id="divider" class="divider">
                    <div></div>
                </div>

                <div id="mainProducts" class="main-products-container">
                    <div class="child-container">
                        <div class="header-container">
                            <h2>MY PRODUCTS</h2>

                            <div class="filterbar">
                                <select class="custom-select categories-select">
                                    <option value="-">All Categories</option>
                                </select>

                                <select class="custom-select types-select">
                                    <option value="All Products">All Products</option>
                                </select>
                                <button id="showFormButton">Add Product</button>
                            </div>
                        </div>



                        <!-- Pagination Controls -->

                        <div class="products" data-storename="<?php echo $store_name; ?>" data-storeid="<?php echo $store_id; ?> ">
                        </div>
                    </div>
                </div>
            </div>


            <form id="updateInfo" method="POST" action="../server/updateStore.php"
                enctype="multipart/form-data" class="edit-store hidden">
                <div class="basic-info rounded-box">
                    <h2>Store Icon</h2>
                    <div>
                        <div id="file-container" class="file-container">
                            <label for="file-upload" class="custom-file-upload">Choose File</label>
                            <input type="file" id="file-upload" name="img" accept="image/*" onchange="renderFile(event)"
                                style="opacity: 0%;" />
                        </div>
                    </div>
                </div>

                <div class="basic-info rounded-box">
                    <h2>About</h2>
                    <div>
                        <textarea name="description" id="description"><?php echo $store_description ?></textarea>
                    </div>
                </div>

                <div class="basic-info rounded-box">
                    <h2>Store Details</h2>
                    <div class="store-information">
                        <div class="first-child">
                            <div>
                                <label for="stallnumber">Stall No.</label>
                                <input type="text" name="stallnumber" id="stallnumber"
                                    value="<?php echo htmlspecialchars(implode(' ', $stallNumbers)); ?>" />
                            </div>
                            <div>
                                <label for="contact">Contact No.</label>
                                <input type="text" name="contact" id="contact"
                                    value="<?php echo htmlspecialchars($store_contact); ?>" />
                            </div>
                            <div>
                                <label for="firstname">Business Permit</label>
                                <p style="color: green; font-weight: 600;">
                                    <?php
                                    // Assume $status is fetched from sellertb
                                    echo htmlspecialchars($status ?? 'Unknown');
                                    ?>
                                </p>
                            </div>
                        </div>
                        <div>
                            <div>
                                <label for="storename">Store Name</label>
                                <input type="text" name="storename" id="storename" value="<?php echo $store_name ?>"
                                    readonly required />
                            </div>
                            <div>
                                <label for="email">Email</label>
                                <input type="text" name="email" id="email"
                                    value="<?php echo htmlspecialchars($store_email); ?>" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="basic-info rounded-box">
                    <div class="store-categories-header">
                        <h2>Store Categories</h2>
                        <button type="button" id="addCategoryButton">Add New Category</button>
                    </div>

                    <div>
                        <div class="store-categories" id="store-categories" data-storecategories='<?= json_encode($store_only_categories) ?>'>
                        </div>

                        <div class="hidden new-category" id="new-category">
                            <input type="text" name="newcategory" id="newcategory" placeholder="New Category">
                            <button type="button" id="saveCategoryButton">Save</button>
                        </div>
                    </div>
                </div>

                <div class="basic-info rounded-box">
                    <div class="linked-account"
                        style="display: flex; justify-content: space-between; align-items: center;">
                        <h2>Linked Accounts</h2>
                        <div class="edit-button-container">
                            <button type="button" id="editAccountsButton" class="edit-button">Edit</button>
                            <button type="button" id="addAccountsButton" class="hidden">Add Account</button>
                        </div>
                    </div>

                    <div id="linkedAccountsView" class="linked-accounts-view">
                        <?php
                        foreach ($store_links as $link) {
                            $platform = $link['platformtype'];
                            $mainlink = $link['link'];
                            $platformLabel = strtoupper($platform[0]) . strtolower(substr($platform, 1));

                            echo "
                        <div style=\"display: flex;\">
                            <label for=\"firstname\">$platformLabel:</label>";

                            // Conditional rendering outside the echo string
                            if (!empty($mainlink)) {
                                echo "<p style=\"color: green; font-weight: 600;\">YES</p>";
                            } else {
                                echo "<p style=\"color: red; font-weight: 600;\">NO</p>";
                            }

                            echo "</div>";
                        }
                        ?>
                    </div>

                    <div id="linkedAccountsEdit" class="linked-accounts-edit hidden">
                        <div class="platforms-select hidden" id="platforms-select">
                            <div class="platforms">
                                <label for="platforms">Platforms not in Store:</label>
                                <select class="custom-select" id="platform-selector">
                                    <option value="0">Link a platform</option>
                                    <?php
                                    foreach ($platforms_notin_store as $platform) {
                                        echo "<option value='" . $platform['platformid'] . "'>" . $platform['platformtype'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- <button>
                                Add platform
                            </button> -->
                        </div>

                        <div id="platform-links-container" data-storelinks='<?php echo json_encode($store_links); ?>'>
                        </div>

                        <div class="placeholder hidden">
                            <label for="platform" class="platform-label">Platform</label>
                            <input type="text" name="links[$id]"
                                value=""
                                placeholder="Enter platform account link" />
                        </div>
                    </div>
                </div>


                <div class="add-button">
                    <button style="margin-right: 20px;" type="button" id="cancelButton">Cancel</button>
                    <button type="submit">Save</button>
                </div>
            </form>
        </div>
    </div>

    <div class="pagination">
        <p class="results"></p>
        <div class="pages">
        </div>
    </div>

    <div id="editModal" class="modal form-hidden">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>x
            <h2>Edit Product</h2>
            <form id="editProductForm">
                <input type="text" id="editName" placeholder="Product Name" required />
                <input type="number" id="editPrice" placeholder="Price" required />
                <textarea id="editDescription" placeholder="Description"></textarea>
                <button type="submit">Save Changes</button>
                <img src="../assets/close.png" id="closeEditFormButton" class="close-btn" alt="Close">
            </form>
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

    <script src="../script/drop-down.js"></script>
    <script src="../script/adding-product.js"></script>
    <script src="../script/adding-form.js"></script>
    <script src="../script/platform-management.js"></script>
    <script src="../script/store-categories-management.js"></script>
    <script src="../script/store-info.js"></script>
</body>

</html>