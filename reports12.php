<?php
include("../server/connect.php");

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
    WHERE 1=1";
$auditTrailParams = [];

if (!empty($_GET['usertype'])) {
    $auditTrailSQL .= " AND usertype = :usertype";
    $auditTrailParams[':usertype'] = $_GET['usertype'];
}
if (!empty($_GET['start_date'])) {
    $auditTrailSQL .= " AND created_at >= :start_date";
    $auditTrailParams[':start_date'] = $_GET['start_date'];
}
if (!empty($_GET['end_date'])) {
    $auditTrailSQL .= " AND created_at <= :end_date";
    $auditTrailParams[':end_date'] = $_GET['end_date'];
}
$auditTrailData = fetchData($conn, $auditTrailSQL, $auditTrailParams);

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
        storetb so ON sl.seller_id = sl.seller_id
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
    <link rel="stylesheet" href="../style/product-views-styles.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="img/logo.png" alt="E-Tiangge Portal Logo">
        </div>
        <ul class="menu">
            <li><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="#"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="#" class="active"><i class="fas fa-chart-bar"></i> Reports</a></li>
            <li><a href="#"><i class="fas fa-cogs"></i> Settings</a></li>
        </ul>
        <button class="logout"><i class="fas fa-sign-out-alt"></i> Log Out</button>
    </div>

    <main class="main-content">
        <header class="main-header"></header>
        <h1 class="portal-title">E-TIANGGE PORTAL</h1>
        <div class="reports-section">
            <div class="reports-header">
                <label for="report-type">REPORTS</label>
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
                    <form method="GET" action="">
                        <label for="product_name">Product Name:</label>
                        <select name="product_name" id="product_name">
                            <option value="">All</option>
                            <?php foreach ($productNames as $product): ?>
                                <option value="<?= htmlspecialchars($product['product_name']) ?>" <?= ($_GET['product_name'] ?? '') === $product['product_name'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($product['product_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <label for="typename">Type Name:</label>
                        <select name="typename" id="typename">
                            <option value="">All</option>
                            <?php foreach ($typeNames as $type): ?>
                                <option value="<?= htmlspecialchars($type['typename']) ?>" <?= ($_GET['typename'] ?? '') === $type['typename'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($type['typename']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <label for="max_views">Maximum Views:</label>
                        <input type="number" name="max_views" id="max_views" value="<?= htmlspecialchars($_GET['max_views'] ?? '') ?>">

                        <button type="submit">Filter</button>
                    </form>
                    <form action="exportpdf.php" method="GET" target="_blank">
                        <input type="hidden" name="export_product_views" value="1">
                         <button type="submit">Export Product Views</button>
                    </form>

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
                    <form method="GET" action="">
                        <label for="usertype">User Type:</label>
                        <select name="usertype" id="usertype">
                            <option value="">All</option>
                            <option value="admin" <?= ($_GET['usertype'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="seller" <?= ($_GET['usertype'] ?? '') === 'seller' ? 'selected' : '' ?>>Seller</option>
                            <option value="buyer" <?= ($_GET['usertype'] ?? '') === 'buyer' ? 'selected' : '' ?>>Buyer</option>
                        </select>
                        <label for="start_date">Start Date:</label>
                        <input type="date" name="start_date" id="start_date" value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>">
                        <label for="end_date">End Date:</label>
                        <input type="date" name="end_date" id="end_date" value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>">
                        <button type="submit">Filter</button>
                    </form>
                    <form action="exportpdf.php" method="GET">
                        <input type="hidden" name="export_audit" value="1">
                        <button type="submit">Export Audit trail</button>
                    </form>
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
                 <form method="GET" action="">
                  <!-- Status Filter -->
                  <label for="status">Status:</label>
                  <select name="status" id="status">
                      <option value="">All</option>
                      <?php foreach ($sellerStatuses as $status): ?>
                          <option value="<?= htmlspecialchars($status['status']) ?>" <?= ($_GET['status'] ?? '') === $status['status'] ? 'selected' : '' ?>>
                              <?= htmlspecialchars($status['status']) ?>
                          </option>
                      <?php endforeach; ?>
                  </select>

                  <!-- Municipality Filter -->
                  <label for="municipality">Municipality:</label>
                  <select name="municipality" id="municipality">
                      <option value="">All</option>
                      <?php foreach ($municipalities as $municipality): ?>
                          <option value="<?= htmlspecialchars($municipality) ?>" <?= ($_GET['municipality'] ?? '') === $municipality ? 'selected' : '' ?>>
                              <?= htmlspecialchars($municipality) ?>
                          </option>
                      <?php endforeach; ?>
                  </select>

                  <!-- Barangay Filter -->
                  <label for="baranggay">Barangay:</label>
                  <select name="baranggay" id="baranggay">
                      <option value="">All</option>
                      <?php
                      // If a municipality is selected, filter barangays
                      $selectedMunicipality = $_GET['municipality'] ?? '';
                      $barangaysToDisplay = $selectedMunicipality ? $barangaysByMunicipality[$selectedMunicipality] : [];
                      foreach ($barangaysToDisplay as $baranggay): ?>
                          <option value="<?= htmlspecialchars($baranggay) ?>" <?= ($_GET['baranggay'] ?? '') === $baranggay ? 'selected' : '' ?>>
                              <?= htmlspecialchars($baranggay) ?>
                          </option>
                      <?php endforeach; ?>
                  </select>

                  

              </form>
              <form action="exportpdf.php" method="GET">
                    <input type="hidden" name="export_user_seller" value="1">
                    <button type="submit">Export User-Seller</button>
              </form>

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
                              <td><?= htmlspecialchars("{$row['baranggay']}, {$row['municipality']}, {$row['province']}") ?></td>
                          </tr>
                      <?php endforeach; ?>
                  </tbody>
              </table>
              </section>
            <!-- User Admin Section -->
              <section style="display: none;" class="table-container" id="user-admin">
                  <form method="GET" action="">
                      <label for="status">Status:</label>
                      <select name="status" id="status">
                          <option value="">All</option>
                          <?php foreach ($adminStatuses as $status): ?>
                              <option value="<?= htmlspecialchars($status['status']) ?>" <?= ($_GET['status'] ?? '') === $status['status'] ? 'selected' : '' ?>>
                                  <?= htmlspecialchars($status['status']) ?>
                              </option>
                          <?php endforeach; ?>
                      </select>
                      <button type="submit">Apply</button>
                  </form>
                  <form action="exportpdf.php" method="GET">
                    <input type="hidden" name="export_user_admin" value="1">
                    <button type="submit">Export User-Admin</button>
                  </form>

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
              <form method="GET" action="">
                  <label for="usertype">User Type:</label>
                  <select name="usertype" id="usertype">
                      <option value="">All</option>
                      <option value="admin" <?= ($_GET['usertype'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                      <option value="seller" <?= ($_GET['usertype'] ?? '') === 'seller' ? 'selected' : '' ?>>Seller</option>
                      <option value="buyer" <?= ($_GET['usertype'] ?? '') === 'buyer' ? 'selected' : '' ?>>Buyer</option>
                  </select>

                  <label for="start_date">Start Date:</label>
                  <input type="date" name="start_date" id="start_date" value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>">

                  <label for="end_date">End Date:</label>
                  <input type="date" name="end_date" id="end_date" value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>">

                  <button type="submit">Filter</button>
              </form>

              <form action="../exportpdf.php" method="GET" target="_blank">
                 <input type="hidden" name="export_signup_trends" value="1">
                 <button type="submit">Export Sign-Up Trends</button>
              </form>


              <table>
                  <thead>
                      <tr>
                          <th>User ID</th>
                          <th>User Type</th>
                          <th>Action</th>
                          <th>Date</th>
                      </tr>
                  </thead>
                  <tbody>
                      <?php foreach ($auditTrailData as $row): ?>
                          <tr>
                              <td><?= htmlspecialchars($row['userid']) ?></td>
                              <td><?= htmlspecialchars($row['usertype']) ?></td>
                              <td><?= htmlspecialchars($row['action']) ?></td>
                              <td><?= htmlspecialchars($row['formatted_date']) ?></td>
                          </tr>
                      <?php endforeach; ?>
                  </tbody>
              </table>
        </div>
    </main>

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

        document.addEventListener('DOMContentLoaded', function () {
            const selectedReportType = document.getElementById('report-type').value;
            changeContent(selectedReportType);
        });
    </script>
</body>
</html>