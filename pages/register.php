<?php 
include_once "../server/connect.php";

$stmt = $conn->prepare("SELECT platformid, platformtype FROM platformtb");
$stmt->execute();
$platforms = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/registerr.css">
    <link rel="stylesheet" href="../style/navandfoot.css">
    <title>e-Tiangge Taytay</title>
</head>

<body>

    <style>
    .link-form {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: #fdfdfd;
        border-radius: 8px;
        padding: 35px 20px 20px 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        width: 412px;
        max-height: 500px;
    }

    .link-form label {
        margin: 0px !important;
        font-weight: 600;
    }

    .link-form input,
    .link-form select {
        width: 100%;
        height: 40px;
        font-size: 17px;
        padding-left: 10px;
        margin: 10px 0 20px 0 !important;
        border: 1px solid #dddddf;
        outline: none;
        border-radius: 7px;
    }

    .add-button button {
        background-color: #0033a0;
        width: 114px;
        color: white;
        height: 40px;
        border: none;
        border-radius: 8px;
        outline: none;
        cursor: pointer;
        font-size: 15px;
        font-weight: 600;
    }

    .close-btn {
        position: absolute;
        top: 10px;
        right: 10px;
    }

    .img-container {
        height: 200px;
        display: flex;
        border: 1px solid #cccccc;
        align-items: center;
        justify-content: center;
        border-radius: 7px;
    }

    .progress-container {
        padding: 30px 300px;
        height: 180px;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-around;
        flex-direction: column;
    }

    .progress-sub-container {
        width: 100%;
        display: flex;
        justify-content: space-around;
    }

    .progress-number {
        display: flex;
        flex-direction: column;
        align-items: center;
        height: 100px;
        justify-content: space-around;
    }

    .progress-number div {
        height: 50px;
        width: 50px;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: white;
        border-radius: 35px;
        border: 1px solid #0033a0;
    }

    .progress-number div p {
        font-weight: 600 !important;
        font-size: 17px;
    }

    .progress-number p {
        font-size: 17px;
        font-weight: 450;
        letter-spacing: -0.5px;
        color: #0033a0;
    }

    .progress-bar {
        width: 100%;
        height: 10px;
        background-color: #e5e5e5;
        border-radius: 10px;
    }

    .error-message { 
    display: flex;
    width: 100%;
    height: 30px;
    border-radius: 10px;
    padding: 20px;
    font-weight: 600;
    color: #f84545;
    border: 1px solid #f84545;
    align-items: center;
    justify-content: center;
    background-color: #fff3f3;
    }
    </style>

    <div class="progress-container">
        <div class="progress-sub-container">
            <div class="progress-number">
                <div>
                    <p>1</p>
                </div>
                <p>Account Credentials</p>
            </div>
            <div class="progress-number">
                <div>
                    <p>2</p>
                </div>
                <p>Personal Information</p>
            </div>
            <div class="progress-number">
                <div>
                    <p>3</p>
                </div>
                <p>Store Details</p>
            </div>
            <div class="progress-number">
                <div>
                    <p>4</p>
                </div>
                <p>Agreement Section</p>
            </div>
        </div>
        <div class="progress-bar">
            <div class="progress-bar-fill"></div>
        </div>
    </div>

    <div id="linkFormContainer" class="hidden link-form">
        <form action="addinglink.php" method="post">
            <div>
                <img src="../assets/close.png" id="linkcloseFormButton" class="close-btn" alt="Close">
            </div>

            <div style="text-align: center; margin-bottom: 20px; font-weight:600; color: #0033a0;">
                <p>Link your Accounts +</p>
            </div>

            <div>
                <label>Choose Platform</label>
                <select name="category" id="category">
                    <?php foreach ($platforms as $platform): ?>
                    <option value="<?php echo $platform['platformid']; ?>">
                        <?php echo htmlspecialchars($platform['platformtype']); ?></option>
                    <?php endforeach; ?>
                </select>

                <label>Link</label>
                <input type="text" id="linkInput" placeholder="Enter your link">
            </div>

            <div style="display: flex; justify-content: flex-end;" class="add-button">
                <button type="submit">Add Account</button>
            </div>
        </form>
    </div>

    <!-- Single Hero Text Section -->
    <div class="hero-text active" id="heroText">
        <h1 id="hero-title">Register</h1>
        <p id="hero-description" style="text-align: center;">Create your seller account to post and market your
            products!</p>
    </div>

    <div class="container">
        <form id="registrationForm" action="../server/save_registration.php" method="POST"
            enctype="multipart/form-data">
            <!-- Step 1 -->
            <div class="form-step active" id="step1">
                <h2>Account Information</h2>
                <label>Username</label>
                <input type="text" name="username" required>
                <label>Email</label>
                <input type="email" name="email" required>
                <label>Password</label>
                <input type="password" id="password" name="password" required>
                <label>Confirm Password</label>
                <input type="password" id="confirmPassword" name="confirm_password" required>
                <div style="display: flex; justify-content: flex-end;" class="form-buttons">
                    <button type="button" onclick="nextStep(1)">Next</button>
                </div>
            </div>

            <!-- Step 2: Personal Information -->
            <div class="form-step" id="step2">
                <h2>Personal Information</h2>
                <label>First Name</label>
                <input type="text" name="first_name" required>

                <label>Middle Name (Optional)</label>
                <input type="text" name="middle_name">

                <label>Last Name</label>
                <input type="text" name="last_name" required>

                <label>Contact Number</label>
                <input type="text" name="contact" required>

                <div style="display: flex; justify-content: space-between;">
                    <div style="width: 50%;">
                        <label>Birthday</label>
                        <input type="date" name="birthday" required>
                    </div>
                    <div>
                        <label>Age</label>
                        <input type="number" name="age" required>
                    </div>
                </div>

                <label>Province</label>
                <input type="text" name="province" required>
                <label>Municipality</label>
                <input type="text" name="municipality" required>
                <label>Barangay</label>
                <input type="text" name="barangay" required>
                <label>House No. / Street No.</label>
                <input type="text" name="houseno" required>

                <div class="form-buttons">
                    <button class="back-button" type="button" onclick="previousStep(2)">Back</button>
                    <a href="#heroText"><button type="button" onclick="nextStep(2)">Next</button></a>
                </div>
            </div>

            <!-- Step 3: Store Details -->
            <div class="form-step" id="step3">
                <h2>Store Details</h2>


                <label>Stall Number</label>
                <input type="text" name="stall_name" required>

                <label>Store Name</label>
                <input type="text" name="store_name" required>

                <label>Business Permit</label>
                <div id="image-container" class="img-container">
                    <label style=" font-weight: 600;" for="file-upload" class="img-file-upload">Upload Business
                        Permit</label>
                    <input type="file" id="file-upload" name="permit" accept="image/*" onchange="renderFiles(event)"
                        style="opacity: 0%; width:0.01%;" />
                </div>

                <label id="linkshowFormButton"
                    style="color: #0033A0; font-weight: 600; cursor: pointer; margin-bottom: 10px;">Link Your Accounts
                    +</label>

                <div class="link-container">

                </div>

                <div class="form-buttons">
                    <button class="back-button" type="button" onclick="previousStep(3)">Back</button>
                    <button type="button" onclick="nextStep(3)">Next</button>
                </div>
            </div>

            <!-- Step 4: Agreement -->
            <div class="form-step" id="step4">
                <h2>Agreement Section</h2>
                <p class="text-desc">I agree to the <a href="terms.php" target="_blank">Terms and Conditions</a> and
                    acknowledge that my personal data will be collected, stored, and processed in accordance with the <a
                        href="privacy.php" target="_blank">Privacy Policy</a>. By checking the box below, I confirm that
                    I have read, understood, and accept these terms.</p>

                <div class="checkbox-container">
                    <input class="checkbox" type="checkbox" name="agreement" required>
                    <p>By checking this box, I acknowledge and agree to the above statementm</p>
                </div>

                <div class="form-buttons">
                    <button class="back-button" type="button" onclick="previousStep(4)">Back</button>
                    <a href="#progressContainer"><button type="submit">Submit</button></a>
                </div>

                <div id="form-errors" class="form-errors"></div>
            </div>

            <!-- Step 5: Success (Optional, displayed after submission) -->
            <div class="form-step" id="step5">
                <h4 style="margin-bottom: 20px;">Thank you for your interest in joining the E-Tiangge Portal!</h4>
                <p style="text-align: justify; font-size: 17px; font-weight: 450;">Your registration has been received
                    successfully. Please wait while we review and verify your information. You will receive a
                    confirmation email once your account is approved and ready to post products. We appreciate your
                    patience and look forward to having you as part of our community!</p>
            </div>
        </form>
    </div>


    <div class="home-btn-container">

        <a href="home.php"><img src="../assets/arrowrightblack.png" alt="">Return to Homepage</a>

    </div>

    <div class="divider">
        <div></div>
    </div>

    <div class="login-shorcut-container">

        <p>Already have an Account?</p><a href="login.php">Login Now</a>

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

    <script src="../script/seller_register.js"></script>
    <script src="../script/form-show.js"></script>

    <script>
    function renderFiles(event) {
        const container = document.getElementById("image-container");
        const files = event.target.files; // Get the uploaded files

        // Clear the container
        const label = container.querySelector("label");
        if (label) {
            container.removeChild(label);
        }

        if (files.length) {
            Array.from(files).forEach((file) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement("img");
                    img.src = e.target.result;
                    img.style.maxWidth = "100px";
                    img.style.maxHeight = "100px";
                    img.style.margin = "5px";
                    img.style.position = "absolute";
                    container.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        } else {
            container.innerHTML = 'No files selected';
        }
    }
    </script>

</body>

</html>