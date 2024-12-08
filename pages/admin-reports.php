<?php
session_start(); // Start session
include_once '../server/connect.php';


if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

$sql = "SELECT systemlogo, TC, PP FROM systeminfo WHERE id = 1";
$stmt = $conn->prepare($sql);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);

// Use the seller_id from the session
$userid = $_SESSION['userid'];

// Fetch store details from the database
$stmt = $conn->prepare("SELECT username, password, first_name, middle_name, surname, email, role, img FROM admintb WHERE userid = :userid");
$stmt->execute(['userid' => $userid]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if ($admin) {
    // Data fetched successfully
    $username = $admin['username'];
    $fname = $admin['first_name'];
    $mname = $admin['middle_name'];
    $lname = $admin['surname'];
    $email = $admin['email'];
    $fullname = $fname . " " . $lname;
    $role = $admin['role'];
    $password = html_entity_decode($admin['password']);
    if (!empty($admin['img'])) {
        $user_img = 'data:image/png;base64,' . base64_encode($admin['img']);
    } else {
        $user_img = '../assets/storepic.png';
    }
    
} else {
    // Handle case where no admin record was found
    echo "No admin record found for the given user ID.";
    exit();
}
// Fetch data function
function fetchData($conn, $query, $params = [])
{
    try {
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error fetching data: " . $e->getMessage());
    }
}

// Queries for dropdown data
$productNames = fetchData($conn, "SELECT DISTINCT product_name FROM producttb");
$typeNames = fetchData($conn, "SELECT DISTINCT typename FROM producttb");
$sellerStatuses = fetchData($conn, "SELECT DISTINCT status FROM sellertb");

// Admin Users Query
$adminStatuses = fetchData($conn, "SELECT userid, email, username, status FROM admintb");


// Product Views Query
$productSQL = "
    SELECT 
        productid, 
        product_name, 
        typename,
        views
    FROM 
        producttb 
    WHERE 1=1";
$productParams = [];

// Filters for Product Views
if (!empty($_GET['product_name'])) {
    $productSQL .= " AND product_name = :product_name";
    $productParams[':product_name'] = $_GET['product_name'];
}
if (!empty($_GET['typename'])) {
    $productSQL .= " AND typename = :typename";
    $productParams[':typename'] = $_GET['typename'];
}
if (!empty($_GET['max_views'])) {
    $productSQL .= " AND views <= :max_views";
    $productParams[':max_views'] = $_GET['max_views'];
}
$productData = fetchData($conn, $productSQL, $productParams);

// Audit Trail Query
$auditTrailSQL = "
    SELECT  
        usertype, 
        email, 
        action, 
        DATE_FORMAT(created_at, '%Y-%m-%d') AS formatted_date
    FROM 
        actlogtb 
    WHERE 1=1"; // Base WHERE clause to allow dynamic appending
$auditTrailParams = [];

// Append conditions only if values exist
if (!empty($_GET['usertype'])) {
    $auditTrailSQL .= " AND usertype = :usertype";
    $auditTrailParams[':usertype'] = $_GET['usertype'];
}
$auditTrailData = fetchData($conn, $auditTrailSQL, $auditTrailParams);

$signUpSQL = "
SELECT usertype, COUNT(*) AS count, DATE_FORMAT(created_at, '%Y-%m-%d') AS signup_date
FROM actlogtb 
GROUP BY usertype, signup_date 
ORDER BY signup_date DESC";
$signUpParams = [];

if (!empty($_GET['usertype'])) {
    $signUpSQL .= " AND usertype = :usertype";
    $signUpParams[':usertype'] = $_GET['usertype'];
}
if (!empty($_GET['start_date'])) {
    $signUpSQL .= " AND created_at >= :start_date";
    $signUpParams[':start_date'] = $_GET['start_date'];
}
if (!empty($_GET['end_date'])) {
    $signUpSQL .= " AND created_at <= :end_date";
    $signUpParams[':end_date'] = $_GET['end_date'];
}
$signUpData = fetchData($conn, $signUpSQL, $signUpParams);

// Users Seller Query
$sellerSQL = "
    SELECT 
        sl.seller_id, 
        sl.seller_email, 
        sl.first_name, 
        sl.middle_name, 
        sl.last_name, 
        so.storename, 
        st.stallnumber, 
        sl.status, 
        sl.baranggay, 
        sl.municipality, 
        sl.province
    FROM 
        sellertb sl
    LEFT JOIN 
        storetb so ON sl.seller_id = so.sellerid
    LEFT JOIN 
        stalltb st ON so.storename = st.storename
    WHERE 1=1";

$sellerParams = [];

// Filters for Users Seller
if (!empty($_GET['status'])) {
    $sellerSQL .= " AND sl.status = :status";
    $sellerParams[':status'] = $_GET['status'];
}

if (!empty($_GET['baranggay'])) {
    $sellerSQL .= " AND sl.baranggay = :baranggay";
    $sellerParams[':baranggay'] = $_GET['baranggay'];
}

if (!empty($_GET['municipality'])) {
    $sellerSQL .= " AND sl.municipality = :municipality";
    $sellerParams[':municipality'] = $_GET['municipality'];
}

$usersSellerData = fetchData($conn, $sellerSQL, $sellerParams);

// Concatenate full name for seller data
foreach ($usersSellerData as &$row) {
    $row['fullname'] = trim("{$row['first_name']} {$row['middle_name']} {$row['last_name']}");
}
unset($row);

// Fetch unique municipalities
$municipalities = [];
foreach ($usersSellerData as $row) {
    if (!in_array($row['municipality'], $municipalities)) {
        $municipalities[] = $row['municipality'];
    }
}

// Filter barangays based on selected municipality
$barangaysByMunicipality = [];
foreach ($usersSellerData as $row) {
    if (!isset($barangaysByMunicipality[$row['municipality']])) {
        $barangaysByMunicipality[$row['municipality']] = [];
    }
    if (!in_array($row['baranggay'], $barangaysByMunicipality[$row['municipality']])) {
        $barangaysByMunicipality[$row['municipality']][] = $row['baranggay'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- <link rel="stylesheet" href="../style/product-views-styles.css"> -->
    <link rel="stylesheet" href="../style/main-sidebar.css">
</head>

<style>
/* Sidebar */
.top-bar {
    margin: 0;
    overflow: hidden;
    background-color: #ffffff;
    position: fixed;
    top: 0;
    display: flex;
    right: 0;
    padding: 0 40px;
    width: 100%;
    height: 90px;
    z-index: 1;
    justify-content: flex-end;
}

/* General Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Body */
body {
    font-family: 'Poppins', sans-serif;
    display: flex;
    background-color: #f4f4f4;
}

/* Sidebar */
.sidebar {
    background-color: white;
    color: #0033A0;
    font-size: 15px;
    display: flex;
    flex-direction: column;
    height: 100vh;
    /* Full height */
    position: fixed;
    top: 0;
    /* Aligns to the top of the viewport */
    left: 0;
    /* Aligns to the left of the viewport */
    z-index: 1000;
    /* Ensures the sidebar is above other content */
}

.sidebar .logo {
    text-align: center;
}

.sidebar .menu {
    list-style-type: none;
    flex-grow: 1;
    /* Allow menu to fill space */
    display: flex;
    flex-direction: column;
    /* Ensure the menu items stack vertically */
}

.sidebar .menu li {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 50px;
}

.sidebar .menu li a {
    text-decoration: none;
    color: #0033A0;
    font-weight: 600;
    width: 100%;
    height: 50px;
    display: flex;
    justify-content: start;
    align-items: center;
    padding-left: 50px;
    box-sizing: border-box;
    gap: 20px;
}

.sidebar .menu li a.active {
    background-color: #0033A0;
    color: white;
}

.sidebar .menu li a.active i {
    color: white;
}

/* Style for the logout button */
.sidebar .logout {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    padding: 10px 20px;
    background-color: #7AAAFF;
    color: black;
    font-family: 'Poppins', sans-serif;
    font-size: 16px;
    border: none;
    cursor: pointer;
    margin-top: auto;
    /* Pushes the button to the bottom of the sidebar */
    border-radius: 0;
    /* Ensures square corners */
}

/* Main Content */
.main-content {
    margin: 90px 0px 0px 300px;

    flex: 1;
}

/* Main Header */
.main-header {
    background-color: #0033A0;
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.portal-title {
    padding-top: 20px;
    padding-left: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0px;
    font-size: 18px;
    font-weight: 600;
}

/* Reports Section */
.reports-section {
    padding: 20px;
}

.reports-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}


.reports-header select {
    padding: 5px;
    width: 250px;
    font-size: 16px;
    font-family: 'Poppins', sans-serif;
    border: 1px solid black;
    border-radius: 4px;
    color: #333;
}

.product-views-box {
    border: 1px solid black;
    padding: 20px;
    border-radius: 8px;
    margin-top: 20px;
    background-color: transparent;
    font-size: 12px;
}

.header-with-filters {
    display: flex;
    align-items: center;
    justify-content: space-between;
    /* Add space between text and filters */
    margin-bottom: 20px;
}

.header-with-filters h3 {
    margin: 0;
    /* Remove margin to align better */
}

.filter-controls {
    display: flex;
    gap: 10px;
    /* Add spacing between filter controls */
}

.filter-controls select {
    padding: 5px;
    width: 150px;
    font-size: 12px;
    font-family: 'Poppins', sans-serif;
    border: 1px solid black;
    border-radius: 4px;
}

.date-input-container {
    position: relative;
    /* Position relative to the input */
    width: 150px;
    /* Match input width */
}

.audit-trail-input[type="date"] {
    appearance: none;
    /* Keep or remove this based on your needs */
    -webkit-appearance: none;
    -moz-appearance: none;
    font-family: 'Poppins', sans-serif;
    font-size: 12px;
    padding: 5px 5px;
    border: 1px solid black;
    border-radius: 6px;
    background-color: #f9f9f9;
    color: black;
    cursor: pointer;
    width: 150px;
    /* Use full width of the container */
    padding-right: 5px;
    /* Add padding for the icon */
    transition: border-color 0.3s ease;
}

.audit-trail-input[type="date"]:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
}

.date-input-container::after {
    content: "📅";
    /* Unicode for calendar icon */
    position: absolute;
    right: 10px;
    /* Position the icon to the right */
    top: 50%;
    /* Center vertically */
    transform: translateY(-50%);
    /* Adjust for exact vertical centering */
    pointer-events: none;
    /* Make sure clicks go to the input */
}

.table-container {
    width: 100%;
    /* Outer border for the table container */
    padding: 20px;
    /* Inner padding for the table */
    margin-bottom: 20px;
    background-color: white;
    /* Background for the table container */
    border-radius: 10px;
}

table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    border-radius: 10px;
    overflow: hidden;
}

table {
    border: 1px solid #ccc;
}

th {
    border-bottom: 1px solid #ccc;
}

td,
th {
    font-size: 14px;
    padding: 15px;
    font-weight: 600;
    text-align: center;
    color: black;
}

tr:nth-child(even) {
    background-color: #fdfdfd;
}

table thead {
    background-color: #fdfdfd;
}

.actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.actions button {
    padding: 10px 20px;
    border: none;
    cursor: pointer;
    font-family: 'Poppins', sans-serif;
}

.actions .cancel {
    background-color: #ccc;
    color: black;
}

.actions .print {
    background-color: #00287e;
    color: white;
}

form {
    display: flex;
    margin-bottom: 20px;
}

button {
    padding: 10px 20px;
    margin-left: 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    display: flex;
    /* Enables flexbox for aligning items */
    align-items: center;
    /* Centers items vertically */
}

.print-button {
    background-color: #0033A0;
    color: white;
}

.print-button i {
    margin-right: 5px;
    /* Space between icon and text */
}

select,
.table-container input {
    padding: 5px;
    margin-right: 15px;
    width: 200px;
    font-size: 16px;
    font-family: 'Poppins', sans-serif;
    border: 1px solid black;
    border-radius: 4px;
    color: #333;
}
</style>

<body>

    <div class="top-bar">
        <div class="dropdown-container" id="dropdown">
            <div style="margin-right: 10px;">
                <p style="margin-bottom: 5px; color: #404040; font-weight: 600;"><?php echo $fullname; ?></p>
                <p style="color: #565656;"><?php echo $role; ?></p>
            </div>
            <img id="arrow" style="width: 15px; height: 15px; transform: rotate(90deg); margin-left: 20px;"
                src="../assets/arrowrightblack.png" alt="">
        </div>

        <!-- Dropdown Menu -->

    </div>
    <div class="dropdown-menu" id="dropdown-menu">
        <a href="settings.php?section=account">Account</a>
        <a style="color: red;" href="logout.php">Logout</a>
    </div>
    <div class="sidebar">
        <div class="logo">
        <img src="data:image/png;base64,<?= base64_encode($data['systemlogo']) ?>" alt="System Logo">
        </div>
        <ul>
            <li>
                <a href="admin-dashboard.php">
                    <img class="sidebar-icon" src="img/dashboard-grey.png" alt="Dashboard"
                        data-active-src="img/dashboard-grey.png"> Dashboard
                </a>
            </li>
            <li>
                <a href="admin-users.php">
                    <img class="sidebar-icon" src="img/users-grey.png" alt="Users" data-active-src="img/users-grey.png">
                    Users
                </a>
            </li>
            <li class="admin-active">
                <a href="reports.php">
                    <img class="sidebar-icon" src="img/reports-blue.png" alt="Reports"
                        data-active-src="img/reports-grey.png"> Reports
                </a>
            </li>
            <li>
                <a href="admin-settings.php">
                    <img class="sidebar-icon" src="img/settings-grey.png" alt="Settings"
                        data-active-src="img/settings-grey.png"> Settings
                </a>
            </li>
        </ul>
    </div>


    <main class="main-content">
        <div class="reports-section">
            <div class="reports-header">
                <h2 for="report-type">REPORTS</h2>
                <select id="report-type" onchange="changeContent(this.value);">
                    <option value="product-views" selected>Product Views</option>
                    <option value="audit-trail">Audit Trail</option>
                    <option value="users-seller">Users Seller</option>
                    <option value="user-admin">User Admin</option>
                    <option value="sign-up">Sign-up Trends</option>
                </select>
            </div>



            <!-- Product Views Section -->
            <section id="product-views" class="table-container" style="display: block;">
                <div style="display: flex;">
                    <form method="GET" action="">
                        <select name="product_name" id="product_name">
                            <option value="">All</option>
                            <?php foreach ($productNames as $product): ?>
                            <option value="<?= htmlspecialchars($product['product_name']) ?>"
                                <?= ($_GET['product_name'] ?? '') === $product['product_name'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($product['product_name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>

                        <select name="typename" id="typename">
                            <option value="">All</option>
                            <?php foreach ($typeNames as $type): ?>
                            <option value="<?= htmlspecialchars($type['typename']) ?>"
                                <?= ($_GET['typename'] ?? '') === $type['typename'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($type['typename']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>

                        <input type="number" name="max_views" id="max_views"
                            value="<?= htmlspecialchars($_GET['max_views'] ?? '') ?>">

                        <button type="submit">Filter</button>

                    </form>
                    <form action="../server/exportpdf.php" method="GET" target="_blank">
                        <input type="hidden" name="export_product_views" value="1">
                        <button type="submit">Export to PDF</button>
                    </form>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Product ID</th>
                            <th>Product Name</th>
                            <th>Type Name</th>
                            <th>Views</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productData as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['productid']) ?></td>
                            <td><?= htmlspecialchars($row['product_name']) ?></td>
                            <td><?= htmlspecialchars($row['typename']) ?></td>
                            <td><?= htmlspecialchars($row['views']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>

            <!-- Audit Trail Section -->
            <section id="audit-trail" class="table-container" style="display: none;">
                <div style="display: flex;">
                    <form method="GET" action="">
                        <select name="usertype" id="usertype">
                            <option value="">All</option>
                            <option value="admin" <?= ($_GET['usertype'] ?? '') === 'admin' ? 'selected' : '' ?>>admin
                            </option>
                            <option value="seller" <?= ($_GET['usertype'] ?? '') === 'seller' ? 'selected' : '' ?>>
                            seller
                            </option>
                        </select>
                        <button type="submit">Filter</button>
                    </form>
                    <form action="../server/exportpdf.php" method="GET" target="_blank">
                        <input type="hidden" name="export_audit" value="1">
                        <button type="submit">Export to PDF</button>
                    </form>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>User Type</th>
                            <th>Email</th>
                            <th>Action</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($auditTrailData as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['usertype']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['action']) ?></td>
                            <td><?= htmlspecialchars($row['formatted_date']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>

            <!-- Users Seller Section -->
            <section id="users-seller" class="table-container" style="display: none;">

                <div style="display:flex">
                    <form method="GET" action="">
                        <!-- Status Filter -->
                        <select name="status" id="status">
                            <option value="">All</option>
                            <?php foreach ($sellerStatuses as $status): ?>
                            <option value="<?= htmlspecialchars($status['status']) ?>"
                                <?= ($_GET['status'] ?? '') === $status['status'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($status['status']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>

                        <!-- Municipality Filter -->
                        <select name="municipality" id="municipality">
                            <option value="">All</option>
                            <?php foreach ($municipalities as $municipality): ?>
                            <option value="<?= htmlspecialchars($municipality) ?>"
                                <?= ($_GET['municipality'] ?? '') === $municipality ? 'selected' : '' ?>>
                                <?= htmlspecialchars($municipality) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>

                        <!-- Barangay Filter -->
                        <select name="baranggay" id="baranggay">
                            <option value="">All</option>
                            <?php
                      // If a municipality is selected, filter barangays
                      $selectedMunicipality = $_GET['municipality'] ?? '';
                      $barangaysToDisplay = $selectedMunicipality ? $barangaysByMunicipality[$selectedMunicipality] : [];
                      foreach ($barangaysToDisplay as $baranggay): ?>
                            <option value="<?= htmlspecialchars($baranggay) ?>"
                                <?= ($_GET['baranggay'] ?? '') === $baranggay ? 'selected' : '' ?>>
                                <?= htmlspecialchars($baranggay) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>

                        <button type="submit">Apply Filters</button>

                    </form>
                    <form action="../server/exportpdf.php" method="GET" target="_blank">
                        <input type="hidden" name="export_user_seller" value="1">
                        <button type="submit">Export to PDF</button>
                    </form>

                </div>

                <!-- Users Seller Table -->
                <table>
                    <thead>
                        <tr>
                            <th>Seller ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Store Name</th>
                            <th>Stall Number</th>
                            <th>Status</th>
                            <th>Barangay</th>
                            <th>Municipality</th>
                            <th>Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usersSellerData as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['seller_id']) ?></td>
                            <td><?= htmlspecialchars($row['fullname']) ?></td>
                            <td><?= htmlspecialchars($row['seller_email']) ?></td>
                            <td><?= htmlspecialchars($row['storename']) ?></td>
                            <td><?= htmlspecialchars($row['stallnumber']) ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                            <td><?= htmlspecialchars($row['baranggay']) ?></td>
                            <td><?= htmlspecialchars($row['municipality']) ?></td>
                            <td><?= htmlspecialchars("{$row['baranggay']}, {$row['municipality']}, {$row['province']}") ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
            <!-- User Admin Section -->
            <section style="display: none;" class="table-container" id="user-admin">
                <div style="display:flex">
                    <form method="GET" action="">
                        <select name="status" id="status">
                            <option value="">All</option>
                            <?php foreach ($adminStatuses as $status): ?>
                            <option value="<?= htmlspecialchars($status['status']) ?>"
                                <?= ($_GET['status'] ?? '') === $status['status'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($status['status']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit">Apply</button>

                    </form>
                    <form action="../server/exportpdf.php" method="GET" target="_blank">
                        <input type="hidden" name="export_user_admin" value="1">
                        <button type="submit" class="btn">Export to PDF</button>
                    </form>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Admin ID</th>
                            <th>Email Address</th>
                            <th>Username</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($adminStatuses as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['userid']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>


            <!-- Sign-up Trends -->
            <section style="display: none;" class="table-container" id="sign-up">
                <!-- Filter Form for User Type and Date -->

                <div style="display:flex;">
                    <form method="GET" action="">
                        <select name="usertype" id="usertype">
                            <option value="">All</option>
                            <option value="admin" <?= ($_GET['usertype'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin
                            </option>
                            <option value="seller" <?= ($_GET['usertype'] ?? '') === 'seller' ? 'selected' : '' ?>>
                                Seller
                            </option>
                        </select>

                        <input type="date" name="start_date" id="start_date"
                            value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>">

                        <input type="date" name="end_date" id="end_date"
                            value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>">

                        <button type="submit">Filter</button>

                    </form>

                    <form action="../server/exportpdf.php" method="GET" target="_blank">
                        <input type="hidden" name="export_signup_trends" value="1">
                        <button type="submit" class="btn">Export to PDF</button>
                    </form>

                </div>



                <table>
                    <thead>
                        <tr>
                            <th>User Type</th>
                            <th>Counts</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($signUpData as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['usertype']) ?></td>
                            <td><?= htmlspecialchars($row['count']) ?></td>
                            <td><?= htmlspecialchars($row['signup_date']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
        </div>
    </main>

    <script src="../script/drop-down.js"></script>
    <script>
    function changeContent(selectedValue) {
        document.querySelectorAll('.table-container').forEach(section => {
            section.style.display = 'none';
        });
        const selectedSection = document.getElementById(selectedValue);
        if (selectedSection) {
            selectedSection.style.display = 'block';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const selectedReportType = document.getElementById('report-type').value;
        changeContent(selectedReportType);
    });
    </script>
</body>

</html>