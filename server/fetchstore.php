<?php
// Include database connection
include_once "connect.php";

try {
    $query = "SELECT storename, img FROM storetb";  // Fetch store_name and img from sellertb
    $stmt = $conn->prepare($query);
    $stmt->execute();

    // Store seller HTML in a variable
    $sellerHTML = "";

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sellerHTML .= '<div style="cursor: default;" class="seller" data-store-name="' . htmlspecialchars($row['storename']) . '" onclick="handleClick(this)">';
            $sellerHTML .= '<img src="data:image/png;base64,' . base64_encode($row['img']) . '" alt="' . htmlspecialchars($row['storename']) . '">   ';
            $sellerHTML .= '<p>' . htmlspecialchars($row['storename']) . '</p>';
            $sellerHTML .= '</div>';
        }
    } else {
        $sellerHTML = "<p>No sellers found.</p>";
    }
} catch (PDOException $e) {
    $sellerHTML = "<p>Error fetching sellers: " . $e->getMessage() . "</p>";
}

// Export the seller HTML for inclusion
return $sellerHTML;
?>
