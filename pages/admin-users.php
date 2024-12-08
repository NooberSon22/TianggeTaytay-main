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

?>


<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../style/main-sidebar.css">
    <!-- <link rel="stylesheet" href="../style/users.css"> -->

</head>

<style>
.main-container {
    margin: 90px 0px 0px 300px;
    display: flex;
    flex: 1;
    padding: 20px 20px 0 20px;
    flex-direction: column;
    border-radius: 10px;
}

.delete-btn {
    
    width: 45px;
    height: 45px;
    border-radius: 25px;
    cursor: pointer;
    background-color: white;
    border: 1px solid #0033a0;
}

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

.container {
  display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 20px auto;
}

body {
  margin: 0;
  padding: 0;
  background-color: #F5F6FA;
}

.content {
  margin-top: 100px;
  margin-left: 400px;
  /* Increased margin-left to match the new sidebar width */
  padding: 20px;
  width: calc(100% - 300px);
  /* Adjusted content width */
}


table {
    border: 1px solid #ccc;
}
table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    border-radius: 10px;
    overflow: hidden;
}

table thead {
    background-color: #fdfdfd;
}

td, th {
    font-size: 14px;
    padding: 15px;
    font-weight: 600;
    text-align: center;
    color: black;
}

th {
    border-bottom: 1px solid #ccc;
}

tr{
  background-color: #FFFFFF;
}

tr:hover {
  background-color: #FFFFFF;
}

#search{ 
     width: 50%;

  border-radius: 25px;
}

input[type="text"] {
  padding: 10px;
  width: 200px;
  margin-right: 10px;
  /* Space between search input and dropdown */
  display: inline-block;
  border: 1px solid #ccc;
  border-radius: 5px;
}

.content {
  margin-top: 130px;
  margin-left: 300px;
  /* Increased margin-left to match the new sidebar width */
  padding: 20px;
  width: calc(100% - 300px);
  /* Adjusted content width */
}


td:nth-child(4), th:nth-child(4) {
  width: 150px; /* Make the "Status" column smaller */
  white-space: nowrap; /* Prevent text from wrapping */
}

.status-dropdown {
  width: 100px; /* Adjust the width as needed */
  font-size: 14px; /* Adjust the font size */
  border: none; /* Remove the border */
  outline: none; /* Remove the focus outline (optional) */
  background: none; /* Remove background (optional) */
}

.filterby{
  border-radius: 15px;
  padding: 10px;
  margin-left: 10px;
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
            <li class="admin-active">
                <a href="users.php">
                    <img class="sidebar-icon" src="img/users-blue.png" alt="Users" data-active-src="img/users-grey.png">
                    Users
                </a>
            </li>
            <li>
                <a href="admin-reports.php">
                    <img class="sidebar-icon" src="img/reports-grey.png" alt="Reports"
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


    <div class="main-container">
        <h2>USERS</h2>
        <div class="main-content">
            <!-- Container to hold search box and dropdown side by side -->
            <div class="container">
                <!-- Search Box -->
                <input type="text" id="search" placeholder="Search..." onkeyup="searchData()">

                <!-- Dropdown to select which table to display -->
                <form method="GET">
                    Filter by:<select name="table_select" onchange="this.form.submit()" class="filterby" id="user_type">
                        <option value="administrator" disabled selected>User Type</option>
                        <option value="seller"
                            <?php echo isset($_GET['table_select']) && $_GET['table_select'] == 'seller' ? 'selected' : ''; ?>>
                            Seller Table</option>
                        <option value="administrator"
                            <?php echo isset($_GET['table_select']) && $_GET['table_select'] == 'administrator' ? 'selected' : ''; ?>>
                            Administrator Table</option>
                    </select>
                </form>
            </div>

            <div id="table-container">
                <!-- The tables will be loaded dynamically here -->
            </div>

        </div>
    </div>
    <!-- Main content -->


    <script src="../script/drop-down.js"></script>
    <script>
    // Get all the sidebar list items (excluding logout)
    const sidebarItems = document.querySelectorAll('.sidebar ul li:not(.logout)');

    // Loop through all sidebar items and add a click event listener
    sidebarItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remove the 'active' class from all items and revert their icons to blue
            sidebarItems.forEach(i => {
                i.classList.remove('active'); // Remove 'active' class from all items
                const icon = i.querySelector('.sidebar-icon');
                const defaultIconSrc = icon.getAttribute('src').replace('-grey',
                    ''); // Get the default blue icon (remove any '-white' part)
                icon.src = defaultIconSrc; // Set the icon back to the default blue
            });

            // Add the 'active' class to the clicked item and change its icon to white
            this.classList.add('active');
            const icon = this.querySelector('.sidebar-icon');
            const activeIconSrc = icon.getAttribute('data-active-src'); // Get the white icon path
            icon.src = activeIconSrc; // Set the icon to the white version
        });
    });

    // Function to perform search
    function searchData() {
        let searchTerm = document.getElementById('search').value;
        let tableSelect = document.querySelector('[name="table_select"]').value;

        // Prepare the URL with search term and selected table
        let url = '../server/fetch_data_users.php?search=' + searchTerm + '&table_select=' + tableSelect;

        // Make the AJAX request to fetch data
        fetch(url)
            .then(response => response.text())
            .then(data => {
                // Update the table container with the returned HTML
                document.getElementById('table-container').innerHTML = data;
            });
    }

    document.addEventListener('change', function(e) {
        // Check if the target is the status dropdown
        const type = document.querySelector('#user_type');
        if (e.target.classList.contains('status-dropdown')) {
            const status = e.target.value;
            const userid = e.target.getAttribute('data-userid');

            // Send the status update to the server using fetch
            fetch('http://localhost/ETianggeTaytay/server/update.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        status: status,
                        userid: userid,
                        usertype: type.value
                    })
                })
                .then(response => response.json()) // Expecting a JSON response
                .then(data => {
                    if (data.success) {} else {}
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('There was an error updating the status.');
                });
        }
    });



    // Automatically call the search function when the page loads to display the initial table
    window.onload = searchData;
    </script>



</body>

</html>