<?php
session_start();
include_once 'connect.php';

// var_dump($_POST["links"]);

// foreach ($_POST['links'] as $id => $link) {
//     echo $id . " " . $link;
// }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure the seller is logged in
    if (!isset($_SESSION['seller_id'])) {
        header("Location: ../seller/login.php");
        exit();
    }

    // Get seller_id from session
    $seller_id = $_SESSION['seller_id'];

    // Fetch store_name for the current seller
    $stmt = $conn->prepare("SELECT storename FROM storetb WHERE sellerid = :sellerid");
    $stmt->execute(['sellerid' => $seller_id]);
    $store_name = $stmt->fetchColumn(); // Fetch the storename directly

    if (!$store_name) {
        die("Error: No store found for the logged-in seller.");
    }

    // Collect form data
    $product_name = $_POST['product_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    // Insert product into the database
    $stmt = $conn->prepare("
        INSERT INTO producttb (product_name, description, price, storename)
        VALUES (:product_name, :description, :price, :storename)
    ");

    $stmt->execute([
        'product_name' => $product_name,
        'description' => $description,
        'price' => $price,
        'storename' => $store_name
    ]);

    // Get the inserted product's ID
    $product_id = $conn->lastInsertId();

    // Handle multiple image uploads
    if (!empty($_FILES['product_imgs']['name'][0])) {
        $stmt = $conn->prepare("
            INSERT INTO product_img_tb (product_id, img)
            VALUES (:product_id, :img)
        ");

        foreach ($_FILES['product_imgs']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['product_imgs']['error'][$key] === UPLOAD_ERR_OK) {
                $image_blob = file_get_contents($tmp_name);

                // Insert the image into the product_images table
                $stmt->execute([
                    'product_id' => $product_id,
                    'img' => $image_blob
                ]);
            }
        }
    }

    if (isset($_POST['category']) && is_array($_POST['category'])) {
        $stmt = $conn->prepare("
            INSERT INTO product_categoriestb (category_type, category_id, productid)
            VALUES (:category_type, :category_id, :product_id)
        ");

        foreach ($_POST['category'] as $category) {
            if (strpos($category, "-") !== false) {
                $data = explode("-", $category);
                $stmt->execute([
                    'category_type' => $data[0],
                    'category_id' => $data[1],
                    'product_id' => $product_id
                ]);
            } else {
                error_log("Invalid category format: $category");
            }
        }
    }

    if (isset($_POST['type']) && is_array($_POST['type'])) {
        $stmt = $conn->prepare("
            INSERT INTO product_typestb (producttype_type, typeid, productid)
            VALUES (:producttype_type, :type_id, :product_id)
        ");

        foreach ($_POST['type'] as $type) {
            if (strpos($type, "-") !== false) {
                $data = explode("-", $type);
                $stmt->execute([
                    'producttype_type' => $data[0],
                    'type_id' => $data[1],
                    'product_id' => $product_id
                ]);
            } else {
                error_log("Invalid type format: $type");
            }
        }
    }

    if (isset($_POST['links']) && is_array($_POST['links'])) {
        $stmt = $conn->prepare("
            INSERT INTO products_linkstb (link, productid, idstore_linkstb)
            VALUES (:link, :productid, :idstore_linkstb)
        ");

        foreach ($_POST['links'] as $id => $link) {
            $stmt->execute([
                'link' => $link,
                'productid' => $product_id,
                'idstore_linkstb' => $id
            ]);
        }
    }

    // Redirect back to the store-info page
    header("Location: ../pages/store-info.php");
    exit();
}
