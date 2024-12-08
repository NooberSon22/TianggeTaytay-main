<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php 
include_once "connect.php";

try {
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query to count users per month based on created_at for the current year
    $stmt = $conn->prepare("
        SELECT MONTHNAME(created_at) AS monthname, COUNT(*) AS user_count
        FROM sellertb
        WHERE YEAR(created_at) = YEAR(CURDATE())
        GROUP BY MONTH(created_at)
        ORDER BY MONTH(created_at)
    ");
    $stmt->execute();

    // Initialize arrays for month names and user counts
    $months = [
        'January', 'February', 'March', 'April', 'May', 'June', 
        'July', 'August', 'September', 'October', 'November', 'December'
    ];
    $userCounts = array_fill(0, 12, 0);  // Start with an array of 12 zeros, one for each month

    // Fetch the results and populate the user counts
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $monthIndex = array_search($row['monthname'], $months); // Find the month index
        if ($monthIndex !== false) {
            $userCounts[$monthIndex] = $row['user_count'];  // Update the user count for that month
        }
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
