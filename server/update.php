<?php
require_once('connect.php');

// Get the raw POST data
$data = json_decode(file_get_contents("php://input"), true);

// Check if the data is valid
if (isset($data['status']) && isset($data['userid']) && isset($data['usertype'])) {
    $status = $data['status'];
    $userid = $data['userid'];
    $usertype = $data['usertype'];

    try {
        // Determine the SQL query based on user type
        if ($usertype === 'seller') {
            $sql = "UPDATE sellertb SET status = :status WHERE seller_id = :userid";
        } else {
            $sql = "UPDATE admintb SET status = :status WHERE userid = :userid";
        }

        // Prepare the statement
        $stmt = $conn->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);

        // Execute the query
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    } catch (PDOException $e) {
        // Handle database errors
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
}

// Close the database connection (optional, as PDO automatically handles this at the script end)
$conn = null;
