<?php
//map location to mga tols
$latitude = 14.557675;
$longitude = 121.132690;

//eto yugn initial zoom
$zoomLevel = 18;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>e-Tiangge Taytay</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="../style/about.css">
    <link rel="stylesheet" href="../style/navandfoot.css">

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

</head>

<body>

    <div class="register">
        <p>Become a Seller? <a href="register.php">Register Now</a></p>
    </div>

    <!-- Navbar Section -->
    <nav class="navbar">
        <div class="left-side">
            <a href="#"><img src="../assets/shoppingbag.png" alt=""></a>
            <div class="input-with-icon">
                <img class="search-icon" src="../assets/Vector.png" alt="">
                <input type="text" placeholder="Search for Products...">
            </div>
        </div>
        <div class="right-side">
            <ul>
                <li><a href="home.php">Home</a></li>
                <li class="selected"><a href="about.php">About</a></li>
                <li><a href="products.php">Products</a></li>
                <li><a href="store.php">Store</a></li>
                <li><a href="contact.php">Contact us</a></li>
            </ul>
        </div>
    </nav>


    <section class="terms-conditions">
        <div class="about">
            <h1>ABOUT</h1>
            <p>Welcome to E-Tiangge Portal, an online marketplace that showcases the best of Taytay's Garments and
                Fashion. Originally created to bring the vibrant and affordable Ready-to-Wear (RTW) clothing and
                accessories from Taytay’s famous Tiangge (market) to the digital world, we have worked hard to adapt and
                evolve in response to new challenges. Our mission is to provide a platform where local Taytay sellers
                can easily showcase their high-quality garments to a wider audience, while helping the Taytayeños thrive
                during difficult times, especially through the COVID-19 pandemic. As Taytay is known as the Garments
                Capital of the Philippines, we aim to make it easier for people everywhere to access and buy affordable
                yet fashionable clothing, textiles, and accessories. At E-Tiangge Portal, we believe in supporting local
                businesses, promoting the unique craftsmanship of Taytay's garment industry, and providing a space for
                people to shop with ease.</p>
        </div>
        <div class="looking-back">
            <h1>LOOKING BACK</h1>
            <p>For the past several years, people from Taytay Rizal are already into textile and RTW (ready-to-wear)
                businesses in which garments are being sold in Divisoria, Baclaran, Tutuban, Greenhills and every part
                of the Philippines. Thanks to an initiative by the local Taytay government in 2014, Taytay-based tailors
                and dressmakers who used to rent space outside Taytay Rizal now have a more conducive business location
                right in their hometown. Few stalls started in a small lot of Club Manila East Compound. It gradually
                grew and continuously growing. As of 2017, there are around 10 garments center operating in Club Manila
                East Compound. Each garment center has hundreds to thousands of stalls selling different clothes. Of
                these ten, the biggest are Taytay Municipal Tiangge, Bagpi Garment Center, Igpai Garment Center,
                MASUERTE 4JC Tiangge and Freedom bazaar. Each center has varying schedule of opening and closing but
                almost all are open on main market days of Monday and Thursday evenings.
                Since then, the Taytay Tiangge has become a shopper’s paradise and a godsend for wholesalers. However,
                due to the ever growing businesses of Taytay Tiangge, there are some drawbacks and one worth mentioning
                is the traffic. For peak hours, the traffic stretches from Taytay Tiangge (Club Manila East) up to
                Ortigas Ave in Pasig City.</p>
        </div>
        <div class="map-tiangge">
            <h1>HOW TO GO TO TAYTAY TIANGGE</h1>
            <div class="outer-container">
                <div class="map-container">
                    <div id="map"></div>
                </div>
            </div>
        </div>
        <div class="images">
            <!-- Clickable Images -->
            <img src="../assets/uv.png" onclick="showInfo('uv')" style="cursor: pointer;">
            <img src="../assets/jeep.png" onclick="showInfo('jeep')" style="cursor: pointer;">
            <img src="../assets/mrt.png" onclick="showInfo('mrt')" style="cursor: pointer;">
            <img src="../assets/uvbus.png" onclick="showInfo('bus')" style="cursor: pointer;">
            <img src="../assets/grab.png" onclick="showInfo('grab')" style="cursor: pointer;">
        </div>

        <!-- Info Sections -->
        <div id="uv-info" class="info-box" style="display: block;">
            <h2>Via UV Express</h2>
            <p>
                <strong>Where to Ride:</strong><br>
                -> Go to a UV Express terminal (e.g., at Robinsons Galleria or SM Megamall).<br><br>
                <strong>Route:</strong>
                Take a UV Express bound for Taytay or Angono.<br><br>
                <strong>Drop-Off Point:</strong>
                Ask the driver to drop you at Taytay New Market or Bagpi Garment Center.<br><br>
                <strong>Estimated Fare:</strong>
                ₱30–₱50. <br><br>
                <strong>Travel Time:</strong>
                30–45 minutes, depending on traffic.
            </p>
        </div>

        <div id="jeep-info" class="info-box" style="display: none;">
            <h2>Via Jeepney</h2>
            <p>
                <strong>Where to Ride:</strong><br>
                Look for jeepneys with the signboard Taytay, Angono, or Binangonan.<br><br>
                <strong>Drop-Off Point:</strong><br>
                Same as above—ask to be dropped off near the Taytay Tiangge area.
            </p>
        </div>

        <div id="mrt-info" class="info-box" style="display: none;">
            <h2>From Quezon City or Northern Areas Via MRT + UV Express</h2>
            <p>
                <strong>Route:</strong><br>
                -> Take the MRT and alight at Ortigas Station.<br>
                -> Walk to SM Megamall or Robinsons Galleria for a UV Express bound for Taytay.
            </p>
        </div>

        <div id="bus-info" class="info-box" style="display: none;">
            <h2>From Southern Metro Manila</h2>
            <h3>Via Bus + UV Express</h3>
            <p>
                <strong>Route:</strong><br>
                -> Ride a bus bound for Ortigas or Cubao.<br>
                -> Transfer to a UV Express or jeepney bound for Taytay.
            </p>
        </div>

        <div id="grab-info" class="info-box" style="display: none;">
            <h2>Via Ride-Hailing Apps</h2>
            <p>
                <strong>Route:</strong><br>
                -> Use Grab or similar apps and input Bagpi Garment Center, Taytay or Taytay New Market as your
                destination.<br><br>
                <strong>Estimated Fare:</strong><br>
                ₱300–₱500, depending on distance and traffic.
            </p>
        </div>
    </section>

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
    //Pang start nung map with coordinates
    var map = L.map('map').setView([<?php echo $latitude; ?>, <?php echo $longitude; ?>], <?php echo $zoomLevel; ?>);

    //tile layer
    //pede baguhin tile layer para sa ibang design
    //ewan ko kung pano ko nagamit gmaps ang alam ko may bayad to e hahaha
    L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        attribution: '&copy; <a href="https://www.google.com/intl/en-US_US/help/terms_maps.html">Google Maps</a>'
    }).addTo(map);

    //marker sa map hehe
    var marker = L.marker([<?php echo $latitude; ?>, <?php echo $longitude; ?>]).addTo(map);

    // yung pop up pag nai click yung marker
    marker.bindPopup("<b>TAYTAY CAPITAL TIANGGE</b><br />HIGHWAY 2000, CORNER Market Rd, Taytay, 1920 Rizal");


    // JavaScript function to show the text when the UV image is clicked
    function showInfo(type) {
        // Hide all info boxes
        const allInfoBoxes = document.querySelectorAll('.info-box');
        allInfoBoxes.forEach((box) => {
            box.style.display = 'none';
        });

        // Show the specific info box
        const infoDiv = document.getElementById(type + '-info');
        infoDiv.style.display = 'block';
    }
    </script>
</body>

</html>