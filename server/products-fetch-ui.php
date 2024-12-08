<?php
require("connect.php");
require("mysql-php-executions.php");

$component = $_GET["component"];
$store_id = isset($_GET["store_id"]) ? $_GET["store_id"] : null;

$response = ["success" => false, "message" => "Failed to read data", "data" => []];

if ($component == "category") {
    $categories_data = readData($conn, "categorytb");
    $categories = [];
    $response = [];

    foreach ($categories_data["data"] as $category) {
        $data = [
            "value" => "general-{$category['categoryid']}",
            "label" => $category["category_name"]
        ];
        array_push($categories, $data);
    }

    if ($store_id != null) {
        $categories_data = readData($conn, "categorystoretb", ["storeid" => $store_id]);
        foreach ($categories_data["data"] as $category) {
            $data = [
                "value" => "store-{$category['idcategorystoretb']}",
                "label" => $category["category_name"]
            ];
            array_push($categories, $data);
        }
    }

    $response = ["success" => true, "message" => "Data read successfully", "categories" => $categories];
} else if ($component == "type") {
    $product_type = readData($conn, "producttypetb");
    $product_types = [];
    $response = [];

    foreach ($product_type["data"] as $type) {
        $data = [
            "value" => "general-{$type['typeid']}",
            "label" => $type["typename"]
        ];
        array_push($product_types, $data);
    }

    if ($store_id != null) {
        $product_type = readData($conn, "producttypestoretb", ["storeid" => $store_id]);
        foreach ($product_type["data"] as $type) {
            $data = [
                "value" => "store-{$type['idproductypestoretb']}",
                "label" => $type["typename"]
            ];
            array_push($product_types, $data);
        }
    }

    $response = ["success" => true, "message" => "Data read successfully", "product_types" => $product_types];
} else if ($component == "price") {
    $stmt = "SELECT MIN(CAST(price as FLOAT )) as min_price, MAX(CAST(price as FLOAT )) as max_price FROM producttb";
    $result = $conn->query($stmt);

    if ($result->rowCount() > 0) {
        $row = $result->fetch(PDO::FETCH_ASSOC);
        $min_price = $row["min_price"];
        $max_price = $row["max_price"];
        $response = ["success" => true, "message" => "Data read successfully", "min_price" => $min_price, "max_price" => $max_price];
    }
}


echo json_encode($response);
