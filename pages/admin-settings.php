<?php
session_start(); // Start session
include_once '../server/connect.php';

// Fetch current data
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

if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

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
    <title>e-Tiangge Taytay</title>
    <link rel="stylesheet" href="../style/main-sidebar.css">
    <script src="https://cdn.tiny.cloud/1/yfzcekqme9bnde6m4kj5va4phv7cwoyw2ttqg0r14c3xdjcl/tinymce/7/tinymce.min.js"
        referrerpolicy="origin"></script>
</head>

<style>
/* General Styles */

@import url('https://fonts.googleapis.com/css2?family=Roboto&display=swap');

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Roboto', sans-serif;
    display: flex;
    height: 100vh;
    background-color: #f5f6fa;
}

input,
button,
select {
    outline: none;
}

input {
    border: 1px solid #cccccc;
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

/* Main Content */
.main-container {
    margin: 90px 0 0 300px;
    display: flex;
    flex: 1;
    padding: 20px;
    flex-direction: column;
    border-radius: 10px;
}


.header {
    margin-bottom: 20px;
}

.main-content {
    display: flex;
    flex: 1;
    background-color: white;
    border-radius: 10px;
}

.left-container {
    width: 280px;
    margin-right: 40px;
    border-right: 1px solid #f3f3f3;
}

.left-container p {
    font-weight: 600;
    font-size: 17px;
    color: #202020;
    text-align: center;
    padding: 15px;
}

.sidebar-item {
    padding: 10px;
    cursor: pointer;
}

.sidebar-item.active {
    background-color: #e4e9f4;
    color: #0033A0;
}

.right-container {
    flex: 1;
    padding: 60px 20px 20px 20px;
}

.section-container {
    display: none;
    /* Initially hidden */
}

.section-container.active {
    display: block;
    /* Visible when active */
}

.section-header {
    font-size: 20px;
    margin-bottom: 10px;
    font-weight: bold;
}

/* Form Styles */
.form-container {
    display: flex;
    flex-direction: column;
    width: 100%;
    justify-content: space-between;
}

.form-group {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    margin-bottom: 20px;
    align-items: flex-start;
}

.form-group label {
    font-size: 17px;
    font-weight: 600;
    margin-bottom: 10px;
}

.form-group input {
    font-size: 17px;
    font-weight: 600;
    height: 40px;
    width: 100%;
    padding: 5px;
    border-radius: 5px;
    border: 1px solid #ccc;
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

/* Buttons */
.btn {
    padding: 13px 20px;
    font-weight: 600;
    font-size: 15px;
    width: 140px;
    cursor: pointer;
    border-radius: 5px;
}

.add-btn {
    padding: 13px 20px;
    font-weight: 600;
    font-size: 15px;
    width: 140px;
    cursor: pointer;
    border-radius: 5px;
    background-color: #0033A0;
    color: white;
    border: none;
}

.close-btn {
    background-color: white;
    color: #0033A0;
    border: 1px solid #0033A0;
    padding: 10px 50px;
    padding: 13px 20px;
    font-weight: 600;
    font-size: 15px;
    width: 140px;
    cursor: pointer;
    border-radius: 5px;
}


.btn-submit {
    background-color: #0033A0;
    color: white;
    border: none;
}

.btn-cancel {
    background-color: white;
    color: #0033A0;
    border: 1px solid #0033A0;
}

.button-group {
    display: flex;
    justify-content: flex-end;
    align-items: center;
}

.success-message-container {
    padding: 14px;
    border-radius: 5px;
    border: 1px solid green;
    margin-right: 10px;
}


.error-message-container {
    padding: 14px;
    border-radius: 5px;
    border: 1px solid red;
    margin-right: 10px;
}

.filter-btn {
    padding: 13px 20px;
    font-weight: 600;
    font-size: 15px;
    width: 140px;
    cursor: pointer;
    border-radius: 5px;
    color: #89898a;
    border: 1px solid #89898a;
}

.delete-btn {
    width: 45px;
    height: 45px;
    border-radius: 25px;
    cursor: pointer;
    background-color: white;
    border: 1px solid #0033a0;
}

.filter-lbl {

    align-content: center;
    font-size: 16px;
    font-weight: 500;
    margin-right: 20px;
    color: #89898a;
}

textarea {
    border-radius: 10px;
    border: 1px solid #e2e2e3;
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
            <li>
                <a href="admin-reports.php">
                    <img class="sidebar-icon" src="img/reports-grey.png" alt="Reports"
                        data-active-src="img/reports-grey.png"> Reports
                </a>
            </li>
            <li class="active">
                <a href="admin-settings.php">
                    <img class="sidebar-icon" src="img/settings-blue.png" alt="Settings"
                        data-active-src="img/settings-grey.png"> Settings
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-container">
        <h2 class="header">SETTINGS</h2>
        <div class="main-content">
            <div class="left-container">
                <p style="border-top-left-radius: 10px;" class="sidebar-item active" data-section="categories">
                    Categories</p>
                <p class="sidebar-item" data-section="type">Product Type</p>
                <p class="sidebar-item" data-section="general">General Information</p>
                <p class="sidebar-item" data-section="archive">Archive</p>
                <p class="sidebar-item" data-section="account">Profile Account</p>
            </div>


            <div class="right-container">
                <div class="section-container categories-section">
                    <form action="../server/add_categories.php" method="POST" class="add-category-form"
                        id="add-category-form">
                        <h2 style="margin-bottom: 15px;">Product Category</h2>
                        <div class="form-group">
                            <label for="surname">Category Name</label>
                            <input type="text" id="category_name" name="category_name" required>
                        </div>

                        <div style="display: flex; justify-content: flex-end; width: 100%;">
                            <?php if ($errorMessage): ?>
                            <div id="cat-error-message" class="error-message-container">
                                <p style="color: red;"><?php echo htmlspecialchars($errorMessage); ?></p>
                            </div>
                            <?php endif; ?>
                            <?php if ($successMessage): ?>
                            <div id="cat-success-message" class="success-message-container">
                                <p style="color: green;"><?php echo htmlspecialchars($successMessage); ?></p>
                            </div>
                            <?php endif; ?>
                            <button type="button" class="close-btn" id="clearBtn"
                                style="margin-right: 10px;">Cancel</button>
                            <button class="add-btn" type="submit" style="cursor: pointer;">Add Category</button>
                        </div>
                    </form>

                    <!-- Table to display categories -->
                    <table style="margin-top: 30px;">
                        <thead>
                            <tr>
                                <th>Category ID</th>
                                <th>Category Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($category['categoryid']); ?></td>
                                <td><?php echo htmlspecialchars($category['category_name']); ?></td>
                                <td>
                                    <form action="../server/archive_categories.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="categoryid"
                                            value="<?php echo htmlspecialchars($category['categoryid']); ?>">
                                        <button type="submit" class="delete-btn"><img src="../assets/archive.png"
                                                alt=""></button>
                                    </form>
                                </td>

                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="3">No categories found.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Type Section -->
                <div class="section-container type-section">
                    <form action="../server/add_types.php" method="POST" class="add-type-form" id="add-type-form">

                        <h2 style="margin-bottom: 15px;">Product Type</h2>

                        <div class="form-group">
                            <label for="surname">Type Name</label>

                            <input type="text" id="type_name" name="typename" required>

                        </div>

                        <div style="display: flex; justify-content: flex-end; width: 100%;">
                            <?php if ($errorMessage): ?>
                            <div id="type-error-message" class="error-message-container">
                                <p style="color: red;"><?php echo htmlspecialchars($errorMessage); ?></p>
                            </div>
                            <?php endif; ?>
                            <?php if ($successMessage): ?>
                            <div id="type-success-message" class="success-message-container">
                                <p style="color: green;"><?php echo htmlspecialchars($successMessage); ?></p>
                            </div>
                            <?php endif; ?>
                            <button type="button" class="close-btn" id="clearBtn"
                                style="margin-right: 10px;">Clear</button>
                            <button class="add-btn" type="submit" style="cursor: pointer;">Add Type</button>
                        </div>
                    </form>
                    <!-- Type Table -->
                    <table style="margin-top: 30px;">
                        <thead>
                            <tr>
                                <th>Type ID</th>
                                <th>Type Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($types)): ?>
                            <?php foreach ($types as $type): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($type['typeid']); ?></td>
                                <td><?php echo htmlspecialchars($type['typename']); ?></td>
                                <td>
                                    <!-- Delete Button -->
                                    <!-- <form action="../server/delete_type.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="typeid"
                                            value="?php echo htmlspecialchars($type['typeid']); ?>">
                                        <button type="submit" class="delete-btn">Delete</button>
                                    </form> -->

                                    <!-- Archive Button -->
                                    <form action="../server/archive_types.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="typeid"
                                            value="<?php echo htmlspecialchars($type['typeid']); ?>">
                                        <button type="submit" class="delete-btn"><img src="../assets/archive.png"
                                                alt=""></button>
                                    </form>
                                </td>

                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="3">No types found.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="section-container archive-section">
                    <div style="width: 100%; display: flex; justify-content: flex-end;">

                    

                        <label class="filter-lbl" for="">Filter by</label>
                        <select class="filter-btn" name="" id="archive-select">
                            <option data-section="archive" style="background-color: white; color: black;"
                                value="Category">Category</option>
                            <option data-section="archive" style="background-color: white; color: black;" value="Type">
                                Type</option>
                        </select>

                    </div>

                    <!-- Archive Category Section -->
                    <div style="display: block;" class="archive-container archiveCategory-section" id="archive-category">
                        <h2 style="margin-bottom: 15px;">Archived Categories</h2>
                        <table style="margin-top: 10px;">
                            <thead>
                                <tr>
                                    <th>Category ID</th>
                                    <th>Category Name</th>
                                    <th>Archived At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($archived_categories)): ?>
                                <?php foreach ($archived_categories as $category): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($category['categoryid']); ?></td>
                                    <td><?php echo htmlspecialchars($category['category_name']); ?></td>
                                    <td><?php echo htmlspecialchars($category['archived_at']); ?></td>
                                    <td>
                                        <form action="../server/restore_categories.php" method="POST"
                                            style="display:inline;">
                                            <input type="hidden" name="categoryid"
                                                value="<?php echo htmlspecialchars($category['categoryid']); ?>">
                                            <button type="submit" class="delete-btn"><img src="../assets/restore.png"
                                                    alt=""></button>
                                        </form>
                                    </td>

                                </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="3">No archived categories found.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Archive Type Section -->
                    <div style="display: none;" class="archive-container archiveCategory-section" id="archive-type">
                        <h2 style="margin-bottom: 15px;">Archived Types</h2>

                        <table style="margin-top: 10px;">
                            <thead>
                                <tr>
                                    <th>Type ID</th>
                                    <th>Type Name</th>
                                    <th>Archived At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($archived_types)): ?>
                                <?php foreach ($archived_types as $type): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($type['typeid']); ?></td>
                                    <td><?php echo htmlspecialchars($type['typename']); ?></td>
                                    <td><?php echo htmlspecialchars($type['archived_at']); ?></td>
                                    <td>
                                        <form action="../server/restore_types.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="typeid"
                                                value="<?php echo htmlspecialchars($type['typeid']); ?>">
                                            <button type="submit" class="delete-btn"><img src="../assets/restore.png"
                                                    alt=""></button>
                                        </form>
                                    </td>

                                </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="3">No archived types found.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="section-container account-section">
                    <form id="adminForm" action="../server/update_admin.php" method="POST">
                        <div style="margin-bottom: 15px;" class="form-container">
                            <div class="left-form">
                                <h2 style="margin-bottom: 15px; font-size: 30px; color: #0033a0;">Personal Information
                                </h2>
                                <div class="form-group">
                                    <label for="firstName">First Name</label>
                                    <input type="text" id="firstName" value="<?php echo $fname; ?>" name="first_name"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="middleName">Middle Name</label>
                                    <input type="text" id="middleName" value="<?php echo $mname; ?>" name="middle_name">
                                </div>
                                <div class="form-group">
                                    <label for="surname">Surname</label>
                                    <input type="text" id="surname" value="<?php echo $lname; ?>" name="surname"
                                        required>
                                </div>
                            </div>
                            <div class="right-form">
                                <h2 style="margin-bottom: 15px;font-size: 30px;color: #0033a0;">Account Information</h2>
                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="email" id="email" value="<?php echo $email; ?>" name="email" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" id="username" value="<?php echo $username; ?>" name="username"
                                        readonly>
                                </div>
                                <div class="form-group">
                                    <label for="password">Current Password</label>
                                    <input type="password" id="password" name="password">
                                </div>
                                <div class="form-group">
                                    <label for="password">New Password</label>
                                    <input type="password" id="password" name="new_password">
                                </div>
                            </div>
                        </div>
                        <div class="button-group">
                            <?php if ($errorMessage): ?>
                            <div id="update-error-message" class="error-message-container">
                                <p style="color: red;"><?php echo htmlspecialchars($errorMessage); ?></p>
                            </div>
                            <?php endif; ?>
                            <?php if ($successMessage): ?>
                            <div id="update-success-message" class="success-message-container">
                                <p style="color: green;"><?php echo htmlspecialchars($successMessage); ?></p>
                            </div>
                            <?php endif; ?>
                            <div style="dislay: flex;">
                                <button type="submit" class="btn btn-submit">Save</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="section-container general-section">
                    <h2>Logo</h2>
                    <form id="logoForm" enctype="multipart/form-data" method="post"
                        action="../server/update_systeminfos.php">
                        <img style="margin-bottom: 15px; width: 100%;"
                            src="data:image/png;base64,<?= base64_encode($data['systemlogo']) ?>" alt="System Logo">
                        <input type="file" name="systemlogo" style="display: none;" id="logoInput"
                            onchange="handleFileChange(event, 'logoForm')">
                        <div style="display: flex; justify-content: flex-end; margin-bottom: 25px;">
                            <button style="border-radius: 10px; padding: 15px 20px; width: 90px;" type="button"
                                class="btn btn-submit" onclick="document.getElementById('logoInput').click()">
                                Change
                            </button>
                        </div>
                    </form>

                    <h2 style="margin-bottom: 10px;">Privacy</h2>

                    <div>
                        <h3 style="margin-bottom: 10px;">Terms & Conditions</h3>
                        <form id="termsForm" method="post" action="../server/update_systeminfos.php">
                            <textarea id="TCTextEditor"
                                style="margin-bottom: 15px; width: 100%; resize: none; outline: none; font-size: 17px; padding: 20px; height: 500px;"
                                name="terms"><?= htmlspecialchars($data['TC']) ?></textarea>
                            <div style="display: flex; justify-content: flex-end; margin-bottom: 25px;">
                                <button style="border-radius: 10px; padding: 15px 20px; width: 90px;" type="submit"
                                    class="btn btn-submit">
                                    Update
                                </button>
                            </div>
                        </form>
                    </div>

                    <div>
                        <h3 style="margin-bottom: 10px;">Privacy Policy</h3>
                        <form id="privacyForm" method="post" action="../server/update_systeminfos.php">
                            <textarea id="PPTextEditor"
                                style="margin-bottom: 15px; width: 100%; resize: none; outline: none; font-size: 17px; padding: 20px; height: 500px;"
                                name="privacy"><?= htmlspecialchars($data['PP']) ?></textarea>
                            <div style="display: flex; justify-content: flex-end; margin-bottom: 25px;">
                                <button style="border-radius: 10px; padding: 15px 20px; width: 90px;" type="submit"
                                    class="btn btn-submit">
                                    Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script src="../script/setting_scripts.js"></script>
        <script src="../script/drop-down.js"></script>
        <script>
        tinymce.init({
            selector: '#TCTextEditor',
            plugins: 'code link',
            toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | link | code',
            menubar: false
        });

        tinymce.init({
            selector: '#PPTextEditor',
            plugins: 'code link',
            toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | link | code',
            menubar: false
        });
        </script>
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
                const successMessage = document.getElementById("cat-success-message");
                const adminsuccessMessage = document.getElementById("admin-success-message");
                const typesuccessMessage = document.getElementById("type-success-message");
                const updatesuccessMessage = document.getElementById("update-success-message");
                const restoreType = document.getElementById("restore-type-success-message");
                if (successMessage) {
                    successMessage.style.display = "none";
                    adminsuccessMessage.style.display = "none";
                    typesuccessMessage.style.display = "none";
                    updatesuccessMessage.style.display = "none";
                    restoreType.style.display = "none";
                }
                const errorMessage = document.getElementById("cat-error-message");
                const adminerrorMessage = document.getElementById("admin-error-message");
                const typeerrorMessage = document.getElementById("type-error-message");
                const updateerrorMessage = document.getElementById("update-error-message");
                const errorRestoreType = document.getElementById("restore-type-error-message");
                if (errorMessage) {
                    errorMessage.style.display = "none";
                    adminerrorMessage.style.display = "none";
                    typeerrorMessage.style.display = "none";
                    updateerrorMessage.style.display = "none";
                    errorRestoreType.style.display = "none";
                }
            }, 4000);

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
        function handleFileChange(event, formId) {
            const form = document.getElementById(formId);
            form.submit(); // Automatically submit the form after file selection
        }
        </script>

</body>

</html>