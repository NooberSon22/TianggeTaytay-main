<?php
session_start(); // Start session
include_once '../server/connect.php';

$sql = "SELECT * FROM actlogtb ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Fetch all data
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Debugging
    if (empty($logs)) {
        echo "<div>No records found in actlogtb.</div>";
    }

    $sql = "SELECT systemlogo, TC, PP FROM systeminfo WHERE id = 1";
$stmt = $conn->prepare($sql);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);


// Check if there's an error or success message passed via URL query parameters
$errorMessage = isset($_GET['error']) ? $_GET['error'] : '';
$successMessage = isset($_GET['success']) ? $_GET['success'] : '';

list($categoryHTML, $categories) = include_once '../server/fetchcategory.php';
include_once '../server/fetchtype.php';

include_once '../server/list_categories.php';
include_once '../server/list_type.php';
include_once '../server/list_admin.php';
include_once "../server/fetchgraph.php";
include_once "../server/fetchusersummary.php";

// Use the seller_id from the session
$userid = $_SESSION['userid'];

// Fetch store details from the database
$stmt = $conn->prepare("SELECT userid, username, password, first_name, middle_name, surname, email, role, img FROM admintb WHERE userid = :userid");
$stmt->execute(['userid' => $userid]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if ($admin) {
    // Data fetched successfully
    
    $id = $admin['userid'];
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
    
} elseif ($superadmin) {
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
    <title>DASHBOARD</title>
    <link rel="stylesheet" href="../style/main-sidebar.css">
    <link rel="stylesheet" href="../style/dashboardd.css">
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

p {
    font-size: 16px;
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


@import url('https://fonts.googleapis.com/css2?family=Roboto&display=swap');

body {
    margin: 0;
    padding: 0;
    background-color: rgb(245, 246, 250);
    font-family: 'Roboto';
}

h2,
p {
    color: #2c2c2c;
}

.main-content {
    display: flex;
    background-color: white;
    align-items: center;
    margin-bottom: 10px;
    border-radius: 14px;
}

.main-wrapper {
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
    align-items: center;
    width: 100%;
    height: 30vh;
    gap: 40px;
    padding-bottom: 5px;
}

.content {
    margin-top: 80px;
    margin-left: 330px;
    padding: 10px;
}

.chart-container {
    width: 1500px;
    margin: 0 auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 14px;
}

.chart-title {
    font-weight: bold;
    text-align: left;
    width: 100%;
    margin: 0;
    font-size: 20px;
    color: black;
    margin-bottom: 10px;
}


.chart {
    width: 100%;
    height: 400px;
}

.inner-container {
    display: flex;
    flex-direction: row;
    gap: 20px;
}


.container {
    background-color: white;
    text-align: center;
    width: 400px;
    height: 10px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}



h1 {
    font-size: 23px;
    color: black;
    font-weight: regular;
}


p.usersum {
    font-size: 20px;
    color: black;
    font-weight: bold;
}



.container h1.total {
    font-size: 15px;
    color: rgb(0, 51, 160);
    font-weight: regular;
}

.container h1.act {
    font-size: 15px;
    color: rgb(160, 112, 100);
    font-weight: regular;
}

.container h1.new {
    font-size: 15px;
    color: rgb(199, 21, 55);
    font-weight: regular;
}

.row-count_total {
    font-size: 36px;
    font-weight: 700;
    color: rgb(0, 51, 160);
    padding-bottom: 0px;
    margin-bottom: 0px;
}

.row-count_act {
    font-size: 36px;
    font-weight: 700;
    color: rgb(160, 112, 100);
    padding-bottom: 0px;
    margin-bottom: 0px;
}

.row-count_new {
    font-size: 36px;
    font-weight: 700;
    color: rgb(199, 21, 55);
    padding-bottom: 0px;
    margin-bottom: 0px;
}


hr {
    height: 1px;
    background-color: rgba(0, 0, 0, 0.15);
    width: 100%;
    margin: 0px 0;
}

hr.vertical {
    width: 0.5px;
    height: 130px;
    background-color: rgba(0, 0, 0, 0.15);
    border-left: 0 solid
}




.activity_container {
    width: 1500px;
    height: 300px;
    padding: 20px;
    background-color: #fff;
    border-radius: 10px;
    overflow-y: auto;
}

/* Title styling */
h1.Activity_Log {
    font-size: 20px;
    text-align: left;
    color: #333;
    margin-top: 0px;
    margin-bottom: 10px;
}

/* Table styling */
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

td,
th {
    font-size: 14px;
    padding: 15px;
    font-weight: 600;
    text-align: center;
    color: black;
}

th {
    border-bottom: 1px solid #ccc;
}

tr {
    background-color: #FFFFFF;
}

tr:hover {
    background-color: #FFFFFF;
}


.no-records {
    color: black;
    font-size: 16px;
    text-align: center;
    padding: 20px;
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
            <li class="active">
                <a href="admin-dashboard.php">
                    <img class="sidebar-icon" src="img/dashboard-blue.png" alt="Dashboard"
                        data-active-src="img/dashboard-grey.png"> Dashboard
                </a>
            </li>
            <li>
                <a href="admin-users.php">
                    <img class="sidebar-icon" src="img/users-grey.png" alt="Users" data-active-src="img/users-grey.png">
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
        <h2 style="font-size: 35px;margin-bottom: 10px;" class="header">Dashboard</h2>
        <p style="font-weight: 600;">Welcome to the e-Tiangge Portal Admin Dashoard!</p>
        <hr style="border: 0; margin-bottom:20px; margin-top: 20px;">
        <p style="margin-bottom: 10px;" class="usersum">USER SUMMARY</p>

        <div class="main-content">
            <div class="container">
                <p class="row-count_total"><?php echo $totalUserCount; ?></p>
                <h1 class="total">Total Users</h1>
            </div>

            <hr class="vertical">

            <div class="container">
                <p class="row-count_act"><?php echo $activeUserCount; ?></p>
                <h1 class="act">Active Admins</h1>
            </div>

            <hr class="vertical">

            <div class="container">
                <p class="row-count_new"><?php echo $newUserCount; ?></p>
                <h1 class="new">New Users</h1>
            </div>
        </div>

        <div style="height: 500px;" class="main-content">
            <div class="main-wrapper">
                <div class="chart-container">
                    <p class="chart-title">USER GROWTH</p>
                    <div class="chart">
                        <canvas id="myChart"></canvas>
                    </div>
                </div>
            </div>
        </div>


        <div class="main-content">
            <div class="activity_container">
                <h1 class="Activity_Log">ACTIVITY LOG</h1>

                <?php if (count($logs) > 0): ?>
                <table>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                        <tr>
                            <td class="email"><?php echo htmlspecialchars($log['email']); ?></td>
                            <td class="action"><?php echo htmlspecialchars($log['action']); ?></td>
                            <td class="created_at"><?php echo htmlspecialchars($log['created_at']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="no-records">No records found.</div>
                <?php endif; ?>
            </div>
        </div>

    </div>






    <script src="../script/setting_scripts.js"></script>
    <script src="../script/drop-down.js"></script>
    <script>
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

    document.getElementById('archive-select').addEventListener('change', function() {
        var selectedValue = this.value;

        // Hide all sections
        document.querySelectorAll('.archive-container').forEach(function(container) {
            container.style.display = 'none';
        });

        // Show the selected section
        if (selectedValue === 'Admin') {
            document.getElementById('archive-admin').style.display = 'block';
        } else if (selectedValue === 'Category') {
            document.getElementById('archive-category').style.display = 'block';
        } else if (selectedValue === 'Type') {
            document.getElementById('archive-type').style.display = 'block';
        }
    });
    document.addEventListener("DOMContentLoaded", function() {
        // Handle success/error message visibility
        setTimeout(function() {
            const successMessage = document.getElementById("successmessage");
            if (successMessage) {
                successMessage.style.display = "none";
            }
            const errorMessage = document.getElementById("errormessage");
            if (errorMessage) {
                errorMessage.style.display = "none";
            }
        }, 3000);

        // Toggle Category Form
        document.getElementById("add-category-button").onclick = function() {
            const form = document.getElementById("add-category-form");
            form.style.display = form.style.display === "none" ? "flex" : "none";
        };
        document.getElementById("close-category-form").onclick = function() {
            const form = document.getElementById("add-category-form");
            form.style.display = "none";
        };

        // Toggle Type Form
        document.getElementById("add-type-button").onclick = function() {
            const form = document.getElementById("add-type-form");
            form.style.display = form.style.display === "none" ? "flex" : "none";
        };
        document.getElementById("close-type-form").onclick = function() {
            const form = document.getElementById("add-type-form");
            form.style.display = "none";
        };
    });
    </script>
    <script>
    const ctx = document.getElementById('myChart').getContext('2d');

    const labels = <?php echo json_encode($months) ?>;
    const data = {
        labels: labels,
        datasets: [{
            label: 'Registered Users',
            data: <?php echo json_encode($userCounts) ?>,
            backgroundColor: 'rgba(0, 123, 255, 0.2)',
            borderColor: 'rgba(0, 123, 255, 1)',
            borderWidth: 2,
            fill: true
        }]
    };

    const config = {
        type: 'line',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            elements: {
                line: {
                    tension: .3, // yung sharpness nung graph line
                    borderWidth: 3,
                }
            }
        },
    };

    var myChart = new Chart(
        document.getElementById('myChart'),
        config
    );
    </script>
</body>

</html>