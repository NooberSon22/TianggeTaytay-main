<?php
include("./server/connect.php");

function fetchData($conn, $table, $filters = []) {
    $sql = "SELECT * FROM $table WHERE 1=1";
    $params = [];

    foreach ($filters as $key => $value) {
        $sql .= " AND $key = :$key";
        $params[":$key"] = $value;
    }

    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error fetching data: " . htmlspecialchars($e->getMessage()));
    }
}

$reportType = $_GET['reportType'] ?? '';

if ($reportType === 'user-seller') {
    $filters = [];
    if (!empty($_GET['status'])) {
        $filters['status'] = $_GET['status'];
    }

    $usersSellerData = fetchData($conn, 'sellertb', $filters);

    if ($usersSellerData) {
        foreach ($usersSellerData as &$row) {
            $row['fullname'] = trim(
                ($row['first_name'] ?? '') . ' ' . 
                ($row['middle_name'] ?? '') . ' ' . 
                ($row['last_name'] ?? '')
            );
        }
        echo getUserSellerHTML($usersSellerData);
    } else {
        echo 'No user data available.';
    }

} elseif ($reportType === 'user-admin') {
    $filters = [];
    if (!empty($_GET['status'])) {
        $filters['status'] = $_GET['status'];
    }

    $usersAdminData = fetchData($conn, 'admintb', $filters);

    if ($usersAdminData) {
        foreach ($usersAdminData as &$row) {
            $row['fullname'] = trim(
                ($row['first_name'] ?? '') . ' ' . 
                ($row['last_name'] ?? '')
            );
        }
        echo getUserAdminHTML($usersAdminData);
    } else {
        echo 'No user admin data available.';
    }

} elseif ($reportType === 'audit-trail') {
    $filters = [];
    if (!empty($_GET['user_id'])) {
        $filters['user_id'] = $_GET['user_id'];
    }

    $auditTrailData = fetchData($conn, 'actlogtb', $filters);

    if ($auditTrailData) {
        echo getAuditTrailHTML($auditTrailData);
    } else {
        echo 'No audit trail data available.';
    }

} elseif ($reportType === 'products') {
    $filters = [];
    if (!empty($_GET['productid'])) {
        $filters['productid'] = $_GET['productid'];
    }

    $products = fetchData($conn, 'producttb', $filters);

    if ($products) {
        echo getProductsHTML($products);
    } else {
        echo 'No Products data available.';
    }

} elseif ($reportType === 'sign-up-trends') {
    $filters = [];
    if (!empty($_GET['user_id'])) {
        $filters['user_id'] = $_GET['user_id'];
    }

    $auditTrailData = fetchData($conn, 'actlogtb', $filters);

    if ($auditTrailData) {
        echo getSignUpTrendsHTML($auditTrailData);
    } else {
        echo 'No Sign Up Trends data available.';
    }

} else {
    echo 'Invalid report type';
}
// Define your HTML generation functions
function getUserSellerHTML($usersSellerData) {
    ob_start();
    ?>
    <div class="header-with-filters">
        <h3>Users</h3>
        <div class="filter-controls">
            <select class="placeholder">
                <option value="" disabled selected>User Type</option>
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
            </select>
            <select class="placeholder">
                <option value="" disabled selected>Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
            <button type="submit">Filter</button>
        </div>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Seller ID</th>
                    <th>Email Address</th>
                    <th>Fullname</th>
                    <th>Stall No.</th>
                    <th>Store Name</th>
                    <th>Status</th>
                    <th>Address</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($usersSellerData as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['seller_id']) ?></td>
                    <td><?= htmlspecialchars($row['seller_email']) ?></td>
                    <td><?= htmlspecialchars($row['fullname']) ?></td>
                    <td><?= isset($row['stallnumber']) ? htmlspecialchars($row['stallnumber']) : 'N/A' ?></td>
                    <td><?= isset($row['storename']) ? htmlspecialchars($row['storename']) : 'N/A' ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td><?= htmlspecialchars($row['baranggay']) ?>, <?= htmlspecialchars($row['municipality']) ?>, <?= htmlspecialchars($row['province']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="actions">
        <button class="cancel-button">CANCEL</button>
        <button class="print-button">
            <i class="fas fa-print"></i> PRINT
        </button>
    </div>
    <?php
    return ob_get_clean();
}

// Define your User Admin HTML generation functions
function getUserAdminHTML($usersAdminData) {
    ob_start();
    ?>
    <div class="header-with-filters">
        <h3>Users</h3>
        <div class="filter-controls">
            <select class="placeholder">
                <option value="" disabled selected>User Type</option>
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
            </select>
            <select class="placeholder">
                <option value="" disabled selected>Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
            <button type="submit">Filter</button>
        </div>
    </div>
    <div class="table-container">
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
                          <?php foreach ($usersAdminData as $row): ?>
                              <tr>
                                  <td><?= htmlspecialchars($row['userid']) ?></td>
                                  <td><?= htmlspecialchars($row['email']) ?></td>
                                  <td><?= htmlspecialchars($row['username']) ?></td>
                                  <td><?= htmlspecialchars($row['status']) ?></td>
                              </tr>
                          <?php endforeach; ?>
                      </tbody>
                  </table>
    </div>
    <div class="actions">
        <button class="cancel-button">CANCEL</button>
        <button class="print-button">
            <i class="fas fa-print"></i> PRINT
        </button>
    </div>
    <?php
    return ob_get_clean();
}

// Define your Audit Trail function similarly
function getAuditTrailHTML($auditTrailData) {
    ob_start();
    ?>
    <div class="header-with-filters">
        <h3>Audit Trails</h3>
        <div class="filter-controls">
            <select class="placeholder">
                <option value="" disabled selected>User Type</option>
                <option value="admin">Admin</option>
                <option value="editor">Editor</option>
                <option value="viewer">Viewer</option>
            </select>
            <input type="date" class="audit-trail-input" id="date-picker" placeholder="Select a date">
            <button type="submit">Filter</button>
        </div>
    </div>
    <div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Act Log ID</th>
                <th>User Type</th>
                <th>Email</th>
                <th>Action</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($auditTrailData as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['actlog_id']) ?></td>
                <td><?= htmlspecialchars($row['usertype']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['action']) ?></td>
                <td><?= htmlspecialchars($row['created_at']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </div>
    <div class="actions">
        <button class="cancel-button">CANCEL</button>
        <button class="print-button">
            <i class="fas fa-print"></i> PRINT
        </button>
    </div>
    <?php
    return ob_get_clean();
}

// Function to generate the Products HTML
function getProductsHTML($products) {
    ob_start();
    ?>
      <div class="header-with-filters">
        <h3>PRODUCTS</h3>
        <div class="filter-controls">
          <label for="product_name">Product Name:</label>
                    <select name="product_name" id="product_name">
                        <option value="">All</option>
                        <?php foreach ($products as $product): ?>
                            <option value="<?= htmlspecialchars($product['product_name']) ?>" <?= ($_GET['product_name'] ?? '') === $product['product_name'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($product['product_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label for="typename">Type Name:</label>
                    <select name="typename" id="typename">
                        <option value="">All</option>
                        <?php foreach ($products as $type): ?>
                            <option value="<?= htmlspecialchars($type['typename']) ?>" <?= ($_GET['typename'] ?? '') === $type['typename'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($type['typename']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label for="max_views">Maximum Views:</label>
                    <input type="number" name="max_views" id="max_views" value="<?= htmlspecialchars($_GET['max_views'] ?? '') ?>">

                    <button type="submit">Filter</button>

        </div>
      </div>
      <div class="table-container">
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
                        <?php foreach ($products as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['productid']) ?></td>
                                <td><?= htmlspecialchars($row['product_name']) ?></td>
                                <td><?= htmlspecialchars($row['typename']) ?></td>
                                <td><?= htmlspecialchars($row['views']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
      </div>
      <div class="actions">
        <button class="cancel-button">CANCEL</button>
        <button class="print-button">
          <i class="fas fa-print"></i> PRINT
        </button>
      </div>
    <?php
    return ob_get_clean();
}


function getSignUpTrendsHTML($auditTrailData) {
    ob_start();
    ?>
    <div class="header-with-filters">
        <h3>Audit Trails</h3>
        <div class="filter-controls">
            <select class="placeholder">
                <option value="" disabled selected>User Type</option>
                <option value="admin">Admin</option>
                <option value="editor">Editor</option>
                <option value="viewer">Viewer</option>
            </select>
            <input type="date" class="audit-trail-input" id="date-picker" placeholder="Select a date">
            <button type="submit">Filter</button>
        </div>
    </div>
    <div class="table-container">
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
    <div class="actions">
        <button class="cancel-button">CANCEL</button>
        <button class="print-button">
            <i class="fas fa-print"></i> PRINT
        </button>
    </div>
    <?php
    return ob_get_clean();
}

?>
