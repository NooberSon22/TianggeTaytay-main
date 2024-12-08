<?php
include_once "connect.php";

// Function for counting new registered users (from 'created_at' column)
function getNewUserCount($tableNames, $daysAgo = 30)
{
    global $conn;  
    $totalCount = 0;
    $dateLimit = date('Y-m-d', strtotime("-$daysAgo days"));  // Get the date from $daysAgo days ago

    // Loop through all table names and sum up the row counts for new users
    foreach ($tableNames as $tablename) {
        // Validate and sanitize the table name to prevent SQL injection
        $table = validate($tablename);

        // Prepare the query to count rows for users created within the last $daysAgo days
        $query = "SELECT COUNT(*) AS totalCount FROM `$table` WHERE `created_at` >= '$dateLimit'";  // Filter by created_at

        // Execute the query using PDO
        try {
            $stmt = $conn->prepare($query);
            $stmt->execute();
            
            // Fetch the result as an associative array
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            // Add the row count of new users from the current table to the total count
            $totalCount += $row['totalCount'];
        } catch (PDOException $e) {
            // Handle PDO exception
            echo "Error executing query for table $table: " . $e->getMessage();
        }
    }
    
    return $totalCount;
}

// Function for counting active users (filter by status)
function getActiveUserCount($tableNames)
{
    global $conn;  
    $totalCount = 0;

    // Loop through all table names and sum up the row counts for active users
    foreach ($tableNames as $tablename) {
        // Validate and sanitize the table name to prevent SQL injection
        $table = validate($tablename);

        // Prepare the query to count rows where status is 'active'
        $query = "SELECT COUNT(*) AS totalCount FROM `$table` WHERE `status` = 'active'";  // Filtering by active status

        // Execute the query using PDO
        try {
            $stmt = $conn->prepare($query);
            $stmt->execute();
            
            // Fetch the result as an associative array
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            // Add the row count of active users from the current table to the total count
            $totalCount += $row['totalCount'];
        } catch (PDOException $e) {
            // Handle PDO exception
            echo "Error executing query for table $table: " . $e->getMessage();
        }
    }
    
    return $totalCount;
}

// Function for counting all rows in a table (no filters)
function getTotalUserCount($tableNames)
{
    global $conn;  
    $totalCount = 0;

    // Loop through all table names and sum up the row counts
    foreach ($tableNames as $tablename) {
        // Validate and sanitize the table name to prevent SQL injection
        $table = validate($tablename);

        // Prepare the query to count rows for each table
        $query = "SELECT COUNT(*) AS totalCount FROM `$table`";  // Using backticks around table name to avoid SQL errors with reserved words

        // Execute the query using PDO
        try {
            $stmt = $conn->prepare($query);
            $stmt->execute();
            
            // Fetch the result as an associative array
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            // Add the row count of the current table to the total count
            $totalCount += $row['totalCount'];
        } catch (PDOException $e) {
            // Handle PDO exception
            echo "Error executing query for table $table: " . $e->getMessage();
        }
    }
    
    return $totalCount;
}

// Sample validate function (sanitize input) - checks if the table name is valid
function validate($input)
{
    // Allow only alphanumeric characters, underscores, and dashes for table names.
    if (preg_match('/^[a-zA-Z0-9_-]+$/', $input)) {
        return $input;
    } else {
        // If the table name doesn't match the pattern, return a safe default table name or error
        return 'safe_default_table_name';
    }
}

// Assuming the database connection is already established
$tableNames = ['admintb', 'sellertb'];  // Array of table names

// Get counts for different categories
$newUserCount = getNewUserCount($tableNames, 30);  // Get the count of users registered in the last 30 days
$activeUserCount = getActiveUserCount($tableNames);  // Get the count of active users
$totalUserCount = getTotalUserCount($tableNames);  // Get the count of all users
?>

