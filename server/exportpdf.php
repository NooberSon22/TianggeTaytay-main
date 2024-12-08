<?php
require_once("../TCPDF-main/tcpdf.php");
include("connect.php");

session_start();

$userid = $_SESSION['userid'];

// Fetch store details from the database
$stmt = $conn->prepare("SELECT userid, username, password, first_name, middle_name, surname, email, role, img FROM admintb WHERE userid = :userid");
$stmt->execute(['userid' => $userid]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if ($admin) {
    $admin_id = $admin['userid'];
    $adminUsername = $admin['username']; 
    $adminRole = $admin['role'];
    $adminEmail = $admin['email'];
} 

// Function to fetch data based on filters
function fetchData($query, $params = []) {
    global $conn;
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Generate PDF function
function generatePDF($title, $headers, $data, $filename, $orientation = 'P') {
    // Include the TCPDF library
    require_once("../TCPDF-main/tcpdf.php");

    // Create a new TCPDF object with dynamic orientation ('P' for Portrait, 'L' for Landscape)
    $pdf = new TCPDF($orientation, 'mm', 'A4', true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('E-Tiangge Portal');
    $pdf->SetTitle($title);
    $pdf->SetSubject($title);

    // Add a page to the PDF
    $pdf->AddPage();

    // Title
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, $title, 0, 1, 'C');
    $pdf->Ln(10); // Line break after the title

    // Get margins using getMargins()
    $margins = $pdf->getMargins();
    $leftMargin = $margins['left'];
    $rightMargin = $margins['right'];

    // Calculate the available width of the page after considering the margins
    $pageWidth = $pdf->getPageWidth();
    $availableWidth = $pageWidth - $leftMargin - $rightMargin;

    // Calculate the total width of the columns (sum of all column widths)
    $totalWidth = 0;
    foreach ($headers as $header) {
        $totalWidth += $header['width'];
    }

    // Scale each column width to fit within the available width of the page
    $scalingFactor = $availableWidth / $totalWidth;

    // Table Header
    $pdf->SetFont('helvetica', '', 12);
    $pdf->SetFillColor(240, 240, 240); // Light gray fill for header cells
    foreach ($headers as $header) {
        $scaledWidth = $header['width'] * $scalingFactor;
        $pdf->Cell($scaledWidth, 10, $header['title'], 1, 0, 'C', 1); // 'C' for center alignment
    }
    $pdf->Ln(); // Line break after header row

    // Table Data
    $pdf->SetFont('helvetica', '', 10);
    foreach ($data as $row) {
        foreach ($headers as $header) {
            $key = $header['key'];
            $scaledWidth = $header['width'] * $scalingFactor;
            $pdf->Cell($scaledWidth, 10, htmlspecialchars($row[$key]), 1, 0, 'C'); // 'C' for center alignment
        }
        $pdf->Ln(); // Line break after each data row
    }

    // Output the PDF
    $pdf->Output($filename, 'I'); // 'I' for inline display (the browser will open the PDF)
    exit();
}

// Export Audit Trail
if (isset($_GET['export_audit'])) {
    // Query to fetch data for the Audit Trail
    $query = "
        SELECT usertype, email, action, DATE_FORMAT(created_at, '%Y-%m-%d') AS formatted_date 
        FROM actlogtb 
        WHERE 1=1";
    
    // Add filtering conditions based on query parameters
    $params = [];
    if (!empty($_GET['usertype'])) $query .= " AND usertype = :usertype";
    if (!empty($_GET['start_date'])) $query .= " AND created_at >= :start_date";
    if (!empty($_GET['end_date'])) $query .= " AND created_at <= :end_date";
    
    // Fetch data from the database
    $data = fetchData($query, $params);

    // Headers for the PDF table
    $headers = [
        ['title' => 'User Type', 'key' => 'usertype', 'width' => 40],
        ['title' => 'Email', 'key' => 'email', 'width' => 60],
        ['title' => 'Action', 'key' => 'action', 'width' => 40],
        ['title' => 'Date', 'key' => 'formatted_date', 'width' => 40],
    ];

    $action = $adminUsername . " export a report for audit trail.";
            $logSql = "INSERT INTO actlogtb (usertype, email, action) 
                       VALUES (:usertype, :email, :action)";

            $logStmt = $conn->prepare($logSql);
            $logStmt->bindParam(':usertype', $adminRole);
            $logStmt->bindParam(':email', $adminEmail);
            $logStmt->bindParam(':action', $action);

            // Execute the log query
            $logStmt->execute();

    // Generate the PDF report
    generatePDF('Audit Trail Report', $headers, $data, 'audit_trail_report.pdf');
}


// Export User-Seller
if (isset($_GET['export_user_seller'])) {
    // Query to fetch data for User-Seller
    $query = "
        SELECT 
            sl.seller_id, 
            CONCAT(sl.first_name, ' ', sl.middle_name, ' ', sl.last_name) AS fullname,  
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
    
    // Fetch data from the database
    $data = fetchData($query);

    // Headers for the PDF table
    $headers = [
        ['title' => 'Seller ID', 'key' => 'seller_id', 'width' => 20],
        ['title' => 'Full Name', 'key' => 'fullname', 'width' => 70],
        ['title' => 'Store Name', 'key' => 'storename', 'width' => 50],
        ['title' => 'Stall Number', 'key' => 'stallnumber', 'width' => 30],
        ['title' => 'Status', 'key' => 'status', 'width' => 30],
        ['title' => 'Barangay', 'key' => 'baranggay', 'width' => 40],
        ['title' => 'Municipality', 'key' => 'municipality', 'width' => 40],
        ['title' => 'Province', 'key' => 'province', 'width' => 40],
    ];

    $action = $adminUsername . " export a report for user seller.";
            $logSql = "INSERT INTO actlogtb (usertype, email, action) 
                       VALUES (:usertype, :email, :action)";

            $logStmt = $conn->prepare($logSql);
            $logStmt->bindParam(':usertype', $adminRole);
            $logStmt->bindParam(':email', $adminEmail);
            $logStmt->bindParam(':action', $action);

            // Execute the log query
            $logStmt->execute();

    // Generate the PDF
    generatePDF('User-Seller Report', $headers, $data, 'user_seller_report.pdf', "L");
}

// Export User-Admin
if (isset($_GET['export_user_admin'])) {
    // Query to fetch data for User-Admin
    $query = "
        SELECT 
            userid, 
            email, 
            username, 
            status 
        FROM admintb";
    $data = fetchData($query);

    // Headers for the PDF table
    $headers = [
        ['title' => 'Admin ID', 'key' => 'userid', 'width' => 40],
        ['title' => 'Email Address', 'key' => 'email', 'width' => 60],
        ['title' => 'Username', 'key' => 'username', 'width' => 60],
        ['title' => 'Status', 'key' => 'status', 'width' => 30],
    ];

    $action = $adminUsername . " export a report for user admin.";
            $logSql = "INSERT INTO actlogtb (usertype, email, action) 
                       VALUES (:usertype, :email, :action)";

            $logStmt = $conn->prepare($logSql);
            $logStmt->bindParam(':usertype', $adminRole);
            $logStmt->bindParam(':email', $adminEmail);
            $logStmt->bindParam(':action', $action);

            // Execute the log query
            $logStmt->execute();
    // Generate the PDF
    generatePDF('User-Admin Report', $headers, $data, 'user_admin_report.pdf');
}

// Export Product View
if (isset($_GET['export_product_views'])) {
    // Query to fetch data for Product Views
    $query = "
        SELECT productid, product_name, typename, views 
        FROM producttb";
    $data = fetchData($query);

    // Headers for the PDF table
    $headers = [
        ['title' => 'Product ID', 'key' => 'productid', 'width' => 40],
        ['title' => 'Product Name', 'key' => 'product_name', 'width' => 60],
        ['title' => 'Type Name', 'key' => 'typename', 'width' => 60],
        ['title' => 'Views', 'key' => 'views', 'width' => 30],
    ];

    // Generate the PDF
    $action = $adminUsername . " export a report for product views.";
    $logSql = "INSERT INTO actlogtb (usertype, email, action) 
               VALUES (:usertype, :email, :action)";

    $logStmt = $conn->prepare($logSql);
    $logStmt->bindParam(':usertype', $adminRole);
    $logStmt->bindParam(':email', $adminEmail);
    $logStmt->bindParam(':action', $action);

    // Execute the log query
    $logStmt->execute();

    generatePDF('Product Views Report', $headers, $data, 'product_views_report.pdf');
}

// Export Sign-Up Trends
if (isset($_GET['export_signup_trends'])) {
    $query = "
        SELECT usertype, COUNT(*) AS count, DATE_FORMAT(created_at, '%Y-%m-%d') AS signup_date
        FROM actlogtb 
        GROUP BY usertype, signup_date 
        ORDER BY signup_date DESC";
    $data = fetchData($query);
    $headers = [
        ['title' => 'User Type', 'key' => 'usertype', 'width' => 60],
        ['title' => 'Sign-Up Count', 'key' => 'count', 'width' => 40],
        ['title' => 'Date', 'key' => 'signup_date', 'width' => 60],
    ];

    $action = $adminUsername . " export a report for sign up trends.";
            $logSql = "INSERT INTO actlogtb (usertype, email, action) 
                       VALUES (:usertype, :email, :action)";

            $logStmt = $conn->prepare($logSql);
            $logStmt->bindParam(':usertype', $adminRole);
            $logStmt->bindParam(':email', $adminEmail);
            $logStmt->bindParam(':action', $action);

            // Execute the log query
            $logStmt->execute();
    generatePDF('Sign-Up Trends Report', $headers, $data, 'signup_trends_report.pdf');
}
?>
