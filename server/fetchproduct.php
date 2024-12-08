<?php
include "connect.php";

function fetchProducts($type = null)
{
    global $conn;
    // Fetch all products from producttb without the store name filter
    if ($type == "NEW_ARRIVALS") {
        $stmt = $conn->prepare("
            SELECT * FROM producttb ORDER BY date_created DESC LIMIT 4
        ");
    } else if ($type == "MOST_VIEWED") {
        $stmt = $conn->prepare("
            SELECT * FROM producttb ORDER BY views DESC LIMIT 4
        ");
    } else {
        $stmt = $conn->prepare("
            SELECT * FROM producttb
        ");
    }

    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare an array to hold the product details along with the first image
    $product_details = [];

    foreach ($products as $product) {
        // Fetch the first image for the product (LIMIT 1)
        $stmt = $conn->prepare("
            SELECT img FROM product_img_tb WHERE product_id = :product_id LIMIT 1
        ");
        $stmt->execute(['product_id' => $product['productid']]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);

        // If an image exists, add it to the product data
        if ($image) {
            $product['first_image'] = $image['img']; // Store the first image's data
        } else {
            $product['first_image'] = null; // No image found
        }

        // Add the product data (with the first image) to the final result
        $product_details[] = $product;
    }

    return $product_details;
}
