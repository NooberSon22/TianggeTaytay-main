<?php
require_once('connection.php');
$search_term = isset($_GET['search']) ? $_GET['search'] : '';
$selected_table = isset($_GET['table_select']) ? $_GET['table_select'] : 'seller';
// Display Seller Table


if ($selected_table == 'user_type') {
  $sql = "SELECT sellertb.seller_id, sellertb.seller_email, 
                 CONCAT(sellertb.first_name, ' ', sellertb.middle_name, ' ', sellertb.last_name) AS full_name, 
                 sellertb.status, sellertb.permit, stalltb.stallnumber, stalltb.storename
          FROM sellertb
          INNER JOIN storetb ON sellertb.seller_id = storetb.sellerid
          INNER JOIN stalltb ON storetb.storename = stalltb.storename
          WHERE storetb.storename LIKE ? ";



  $stmt = $conn->prepare($sql);
  $search_like = '%' . $search_term . '%';
  $stmt->bind_param("s", $search_like);
  $stmt->execute();
  $result = $stmt->get_result();



  // Check if we have data
  if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>Seller ID</th><th>Email Address</th><th>Full Name</th><th>Store Name</th><th>Status</th><th>Permit</th></tr>";

    // Output data of each row
    while ($row = $result->fetch_assoc()) {
      echo "<tr>";
      echo "<td>" . $row['seller_id'] . "</td>";
      echo "<td>" . $row['seller_email'] . "</td>";
      echo "<td>" . $row['full_name'] . "</td>";
      echo "<td>" . ($row['storename'] ?? 'N/A') . "</td>";

      // Display the status dropdown without a submit button
      echo "<td>
              <select name='status' class='status-dropdown' data-userid='" . $row['seller_id'] . "'>
                  <option value='Not Verified'" . ($row['status'] === 'Not Verified' ? ' selected' : '') . ">Not Verified</option>
                  <option value='Pending'" . ($row['status'] === 'Pending' ? ' selected' : '') . ">Pending</option>
                  <option value='Verified'" . ($row['status'] === 'Verified' ? ' selected' : '') . " >Verified</option>
              </select>
            </td>";
      echo "<td>" . ($row['permit'] ?? 'N/A') . "</td>";
      echo "</tr>";
    }


    echo "</table>";
  } else {
    echo "<p style='text-align: center;'>No seller data found.</p>";
  }
} elseif ($selected_table == 'seller') {
  // Modify SQL query with search term if provided
  $sql = "SELECT sellertb.seller_id, sellertb.seller_email, 
                 CONCAT(sellertb.first_name, ' ', sellertb.middle_name, ' ', sellertb.last_name) AS full_name, 
                 sellertb.status, sellertb.permit, stalltb.stallnumber, stalltb.storename
          FROM sellertb
          INNER JOIN storetb ON sellertb.seller_id = storetb.sellerid
          INNER JOIN stalltb ON storetb.storename = stalltb.storename
          WHERE storetb.storename LIKE ? ";



  $stmt = $conn->prepare($sql);
  $search_like = '%' . $search_term . '%';
  $stmt->bind_param("s", $search_like);
  $stmt->execute();
  $result = $stmt->get_result();



  // Check if we have data
  if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>Seller ID</th><th>Email Address</th><th>Full Name</th><th>Store Name</th><th>Status</th><th>Permit</th></tr>";

    // Output data of each row
    while ($row = $result->fetch_assoc()) {
      echo "<tr>";
      echo "<td>" . $row['seller_id'] . "</td>";
      echo "<td>" . $row['seller_email'] . "</td>";
      echo "<td>" . $row['full_name'] . "</td>";
      echo "<td>" . ($row['storename'] ?? 'N/A') . "</td>";

      // Display the status dropdown without a submit button
      echo "<td>
              <select name='status' class='status-dropdown' data-userid='" . $row['seller_id'] . "'>
                  <option value='Not Verified'" . ($row['status'] === 'Not Verified' ? ' selected' : '') . ">Not Verified</option>
                  <option value='Pending'" . ($row['status'] === 'Pending' ? ' selected' : '') . ">Pending</option>
                  <option value='Verified'" . ($row['status'] === 'Verified' ? ' selected' : '') . " >Verified</option>
              </select>
            </td>";
      echo "<td>" . ($row['permit'] ?? 'N/A') . "</td>";
      echo "</tr>";
    }


    echo "</table>";
  } else {
    echo "<p style='text-align: center;'>No seller data found.</p>";
  }
}
// Display Administrator Table
elseif ($selected_table == 'administrator') {
  // Modify SQL query with search term if provided
  $sql = "SELECT userid, email, username, status 
        FROM admintb 
        WHERE userid LIKE ? OR email LIKE ? OR username LIKE ? OR status LIKE ?";

  $stmt = $conn->prepare($sql);
  $search_like = '%' . $search_term . '%';
  $stmt->bind_param("ssss", $search_like, $search_like, $search_like, $search_like);
  $stmt->execute();
  $result = $stmt->get_result();

  // Check if we have data
  if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>Admin ID</th><th>Email Address</th><th>Username</th><th>Status</th></tr>";

    // Output data of each row
    while ($row = $result->fetch_assoc()) {
      echo "<tr>";
      echo "<td>" . $row['userid'] . "</td>";
      echo "<td>" . $row['email'] . "</td>";
      echo "<td>" . $row['username'] . "</td>";

      // Display the status dropdown
      echo "<td>
                <form method='POST'>
                    <select name='status' class='status-dropdown' data-userid='" . $row['userid'] . "'>
                        <option value='active' " . ($row['status'] == 'active' ? 'selected' : '') . ">Active</option>
                        <option value='inactive' " . ($row['status'] == 'inactive' ? 'selected' : '') . ">Inactive</option>
                    </select>
                    <input type='hidden' name='userid' value='" . $row['userid'] . "'>
                </form>
              </td>";
      echo "</tr>";
    }
    echo "</table>";
  } else {
    echo "<p style='text-align: center;'>No administrator data found.</p>";
  }

  // Handle the status update
  if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['userid'])) {
    $userid = $_POST['userid'];
    $new_status = $_POST['status']; // Either 'active' or 'inactive'

    // Update the status in the database
    $update_sql = "UPDATE admintb SET status = ? WHERE userid = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ss", $new_status, $userid);
    if ($update_stmt->execute()) {
      echo "<p style='color: green;'>Status updated successfully.</p>";
    } else {
      echo "<p style='color: red;'>Error updating status.</p>";
    }
  }
}




$conn->close();
