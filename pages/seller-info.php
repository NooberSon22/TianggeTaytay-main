<?php
include("../server/fetchinformation.php");
include("../server/fetchstoreinfo.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>e-Tiangge Taytay</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../style/navandfoot.css">
    <link rel="stylesheet" href="../style/store-info.css">
    <link rel="stylesheet" href="../style/seller-info.css">
</head>

<style>
.store-name {
    border-bottom: 1px solid #dddddd !important;
    border-radius: 0 !important;
}

.dropdown-menu {
    text-align: center;
}

.dropdown-menu .store-name {
    font-weight: 600;
}

.dropdown-menu .store-name:hover {
    background-color: #fff !important;
}

.hidden {
    display: none;
}

.basic-info {
    display: flex;
    width: 100%;
    justify-content: space-between;
}

.basic-info input {
    width: 100%;
}

.basic-info div {
    display: flex;
    width: 32%;
    flex-direction: column;
}

.account-row {
    display: flex;
    width: 68%;
}

.account-row div {
    margin-right: 20px;
    width: 100%;
}

.edit-container img {
    width: 15px;
    height: 15px;
}

#updateInfo h2 {
    font-size: 22px;
    margin-bottom: 16px;
    color: #0033a0;
    text-align: left !important;
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
            <a href="#" class="store-name"><?php echo $store_name; ?></a>
            <a href="seller-info.php">Manage Account</a>
            <a href="store-info.php">Manage Store</a>
            <a style="color: red;" href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="main">
        <div class="sidebar">
            <a href="#" class="active">Manage Account</a>
            <a href="store-info.php">Manage Store</a>
        </div>
        <div class="">
            <div class="rounded-box" id="accountInfo">
                <div class="section">
                    <div class="section-header">
                        <h2>Account Information</h2>
                        
                        <button class="edit-btn">
                            <img src="../assets/pencil.svg" alt="Edit">
                            Edit
                        </button>
                    </div>
                    <div class="info-row">
                        <div class="info-group">
                            <div><strong>Username</strong></div>
                            <p><?php echo $seller_username?></p>
                        </div>
                        <div class="info-group">
                            <div><strong>Email</strong></div>
                            <p><?php echo $seller_email?></p>
                        </div>
                    </div>
                    <div class="info-group">
                        <div><strong>Password</strong></div>
                        <p>
                            <?php 
                        $password = $seller_password['password'] ?? 'N/A';
                        $maskedPassword = str_repeat('•••', strlen($password));
                        echo htmlspecialchars($maskedPassword);
                        ?>
                        </p>
                        <div>
                        <?php if (isset($_GET['error']) && $_GET['error'] == 'username'): ?>
                            <p style="color: red;">Username already exists.</p>
                            <?php endif; ?>
                            <?php if (isset($_GET['error']) && $_GET['error'] == 'email'): ?>
                            <p style="color: red;">Email already exists.</p>
                            <?php endif; ?>
                            <?php if (isset($_GET['error']) && $_GET['error'] == 'currentpassword'): ?>
                            <p style="color: red;">Current password is incorrect.</p>
                            <?php endif; ?>
                            <?php if (isset($_GET['error']) && $_GET['error'] == 'newpassword'): ?>
                            <p style="color: red;">New password must be at least 8 characters long.</p>
                            <?php endif; ?>
                            <?php if (isset($_GET['success']) && $_GET['success'] == 'update'): ?>
                            <p style="color: green;">Updated Successfully</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div id="personalInfo" class="rounded-box">
                <div class="section">
                    <div class="section-header">
                        <h2>Personal Information</h2>
                        <button class="edit-btn">
                            <img src="../assets/pencil.svg" alt="Edit">
                            Edit
                        </button>
                    </div>
                    <div class="info-row">
                        <div class="info-group">
                            <div><strong>First Name</strong></div>
                            <p><?php echo $seller_fname ?></p>
                        </div>
                        <div class="info-group">
                            <div><strong>Last Name</strong></div>
                            <p><?php echo $seller_lname ?></p>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-group">
                            <div><strong>Middle Name</strong></div>
                            <p><?php echo $seller_mname ?></p>
                        </div>
                        <div class="info-group">
                            <div><strong>Contact Number</strong></div>
                            <p><?php echo $seller_contact ?></p>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-group">
                            <div><strong>Birthday</strong></div>
                            <p><?php echo $seller_birthday ?></p>
                        </div>
                        <div class="info-group">
                            <div><strong>Age</strong></div>
                            <p><?php echo $seller_age ?></p>
                        </div>
                    </div>
                    <div class="info-group">
                        <div><strong>Address</strong></div>
                        <p><?php echo $seller_houseno . " " . $seller_baranggay . " " . $seller_municipality . ", " . $seller_province;  ?>
                        </p>
                        </p>

                    </div>
                </div>
            </div>

            <form class="hidden" id="updateInfo" method="POST" action="../server/updateSeller.php">
                <input type="hidden" name="current_username" value="<?php echo $seller_username; ?>">

                <div class="section-header">
                    <h2>Account Information</h2>
                </div>
                <div class="account-row">
                    <div style="margin-right: 20px">
                        <div class="info-group">
                            <label for="newusername">Username</label>
                            <input type="text" name="newusername" id="newusername"
                                value="<?php echo $seller_username ?>" required>
                            
                        </div>
                        <div class="info-group">
                            <label for="seller_email">Email</label>
                            <input type="text" name="seller_email" value="<?php echo $seller_email ?>" required>
                            
                        </div>
                    </div>
                    <div>
                        <div class="info-group">
                            <label for="currentpassword">Current Password</label>
                            <input type="password" name="currentpassword" id="currentpassword"
                                placeholder="Current Password">
                            
                        </div>
                        <div class="info-group">
                            <label for="newpassword">New Password</label>
                            <input type="password" name="newpassword" id="newpassword" placeholder="New Password">
                            
                        </div>
                    </div>
                </div>

                <!-- Personal Information Section -->
                <h2>Personal Information</h2>
                <div class="basic-info">
                    <div>
                        <label for="firstname">First Name</label>
                        <input type="text" name="firstname" id="firstname" value="<?php echo $seller_fname ?>" required>
                    </div>
                    <div>
                        <label for="middlename">Middle Name (Optional)</label>
                        <input type="text" name="middlename" id="middlename" value="<?php echo $seller_mname ?>">
                    </div>
                    <div>
                        <label for="lastname">Surname</label>
                        <input type="text" name="lastname" id="lastname" value="<?php echo $seller_lname ?>" required>
                    </div>
                </div>
                <div class="basic-info">
                    <div>
                        <label for="contact">Contact</label>
                        <input type="text" name="contact" id="contact" value="<?php echo $seller_contact ?>" required>
                    </div>
                    <div>
                        <label for="birthday">Birthday</label>
                        <input type="date" name="birthday" id="birthday" value="<?php echo $seller_birthday ?>"
                            required>
                    </div>
                    <div>
                        <label for="age">Age</label>
                        <input type="number" name="age" id="age" value="<?php echo $seller_age?>" required>
                    </div>
                </div>
                <div class="basic-info">
                    <div>
                        <label for="province">Province</label>
                        <input type="text" name="province" id="province" value="<?php echo $seller_province ?>"
                            required>
                    </div>
                    <div>
                        <label for="municipality">Municipality</label>
                        <input type="text" name="municipality" id="municipality"
                            value="<?php echo $seller_municipality ?>" required>
                    </div>
                    <div>
                        <label for="baranggay">Baranggay</label>
                        <input type="text" name="baranggay" id="baranggay" value="<?php echo $seller_baranggay ?>"
                            required>
                    </div>
                </div>
                <div>
                    <label for="houseno">House No.</label>
                    <input type="text" name="houseno" id="houseno" value="<?php echo $seller_houseno ?>" required>
                </div>

                <div class="add-button">
                    <button style="margin-right: 20px;" type="button" id="cancelButton">Cancel</button>
                    <button type="submit" id="submitBtn">Save</button>
                </div>
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
    <script src="../script/form-show.js"></script>


    <!-- Add this to include the JavaScript -->
    <script>
    document.getElementById('updateInfo').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirmpassword').value;
        const errorContainer = document.querySelector('#error-container');
        let isValid = true;

        // Clear any previous errors
        errorContainer.innerHTML = '';


        if (password.length > 0 || confirmPassword.length > 0) {
            if (password !== confirmPassword) {
                e.preventDefault();
                const errorMsg = document.createElement('p');
                errorMsg.textContent = 'Passwords do not match.';
                errorMsg.style.color = 'red';
                errorContainer.appendChild(errorMsg);
                isValid = false;
            }

            // Check if password is at least 8 characters long
            if (password.length < 8) {
                e.preventDefault();
                const errorMsg = document.createElement('p');
                errorMsg.textContent = 'Password must be at least 8 characters long.';
                errorMsg.style.color = 'red';
                errorContainer.appendChild(errorMsg);
                isValid = false;
            }
        }
        // Check if password and confirm password match


        // Only submit the form if all validations pass
        if (!isValid) {
            e.preventDefault(); // Prevent form submission
        }
    });
    </script>

</body>

</html>