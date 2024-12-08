<?php
// Include database connection
include_once "connect.php";

try {
    // Query to fetch the first 6 categories only
    $query_6 = "SELECT * FROM categorytb LIMIT 6";  
    $stmt_6 = $conn->prepare($query_6);
    $stmt_6->execute();

    // Query to fetch all categories
    $query_all = "SELECT * FROM categorytb";  
    $stmt_all = $conn->prepare($query_all);
    $stmt_all->execute();

    // Store category HTML for the first 6 categories in a variable
    $categoryHTML = "";

    // Initialize an array to store all categories
    $categories = [];

    // Process first 6 categories (categoryHTML)
    if ($stmt_6->rowCount() > 0) {
        while ($row = $stmt_6->fetch(PDO::FETCH_ASSOC)) {
            // Append data to the HTML for the first 6 categories
            $categoryHTML .= '<div class="category">';
            $categoryHTML .= '<img src="data:image/png;base64,' . base64_encode($row['img']) . '" alt="' . htmlspecialchars($row['category_name']) . '">';
            $categoryHTML .= '<p>' . htmlspecialchars($row['category_name']) . '</p>';
            $categoryHTML .= '</div>';
        }
    } else {
        $categoryHTML = "<p>No categories found.</p>";
    }

    // Process all categories (to store in categories array)
    if ($stmt_all->rowCount() > 0) {
        while ($row = $stmt_all->fetch(PDO::FETCH_ASSOC)) {
            // Collect all category data into the $categories array
            $categories[] = [
                'categoryid' => $row['categoryid'],
                'category_name' => $row['category_name']
            ];
        }
    } else {
        $categories = [];
    }
} catch (PDOException $e) {
    $categoryHTML = "<p>Error fetching categories: " . $e->getMessage() . "</p>";
    $categories = [];
}

// Now $categories contains all category data
// Example: [{"categoryid":1, "category_name":"Clothing"}, ...]

// $categoryHTML contains the HTML for the first 6 categories
return [$categoryHTML, $categories];
?>
