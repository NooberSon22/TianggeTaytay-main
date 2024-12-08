<?php
session_start(); // Start session
include_once 'connect.php';

// Fetch logs in descending order based on the primary key or timestamp
$sql = "SELECT * FROM actlogtb ORDER BY log_id DESC"; // Replace `log_id` with your primary key or timestamp column if different
$stmt = $conn->prepare($sql);
$stmt->execute();

// Fetch all data
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Debugging
if (empty($logs)) {
    echo "<div>No records found in actlogtb.</div>";
} else {
    // Display logs
    foreach ($logs as $log) {
        echo "<div>";
        echo "User Type: " . htmlspecialchars($log['usertype']) . "<br>";
        echo "Email: " . htmlspecialchars($log['email']) . "<br>";
        echo "Action: " . htmlspecialchars($log['action']) . "<br>";
        echo "Timestamp: " . htmlspecialchars($log['timestamp']) . "<br>";
        echo "</div><hr>";
    }
}
?>
