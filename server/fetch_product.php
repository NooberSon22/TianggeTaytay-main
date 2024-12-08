<?php
require("connect.php");
require("mysql-php-executions.php");

// Retrieve query parameters
$page = isset($_GET["page"]) ? max((int)$_GET["page"], 1) : 1;
$typename = $_GET["type"] ?? null;
$category = $_GET["category"] ?? null;
$storename = $_GET["store"] ?? null;
$max = isset($_GET["max"]) ? max((int)$_GET["max"], 1) : 12;
$store_id = isset($_GET["store_id"]) ? (int)$_GET["store_id"] : null;
$debug = false;

// Calculate offset for pagination
$offset = ($page - 1) * $max;

// Initialize conditions and joins
$conditions = [];
$joins = [];

// Handle typename filter
if ($typename) {
    $typename_temp = explode("-", $typename);
    $typename_type = $typename_temp[0] ?? null;
    $typename_id = $typename_temp[1] ?? null;

    if ($typename_type && $typename_id) {
        $conditions['producttype_type'] = $typename_type;
        $conditions['typeid'] = $typename_id;
    }

    $joins[] = [
        'table' => 'product_typestb',
        'leftTable' => 'producttb',
        'leftColumn' => 'productid',
        'rightColumn' => 'productid'
    ];
}

// Handle category filter
if ($category) {
    $category_temp = explode("-", $category);
    $category_type = $category_temp[0] ?? null;
    $category_id = $category_temp[1] ?? null;

    if ($category_type && $category_id) {
        $conditions["category_type"] = $category_type;
        $conditions["category_id"] = (int) $category_id;
    }

    $joins[] = [
        'table' => 'product_categoriestb',
        'leftTable' => 'producttb',
        'leftColumn' => 'productid',
        'rightColumn' => 'productid'
    ];
}

if ($storename) {
    $conditions["storename"] = $storename;
}

if ($debug) {
    print_r($joins);
    print_r($conditions);
}

// Fetch total records
$total_response = readDataWithJoins($conn, "producttb", $joins, $conditions);

$total_records = count($total_response["data"]);

// Fetch paginated data
$product_data = readDataWithJoins($conn, "producttb", $joins, $conditions, $max, $offset);

// Prepare product list
$products = [];
foreach ($product_data["data"] as $data) {
    $img_data = readData($conn, "product_img_tb", ["product_id" => $data["productid"]], 1)["data"];
    $product = [
        "product_id" => $data["productid"],
        "product_name" => $data["product_name"],
        "price" => $data["price"],
        "img" => $img_data ? base64_encode($img_data[0]["img"]) : null,
    ];
    $products[] = $product;
}

// Calculate start and end values
$start = $total_records > 0 ? $offset + 1 : 0;
$end = min($offset + $max, $total_records);

// Construct response
$response = [
    "success" => true,
    "data" => $products,
    "total_records" => $total_records,
    "pages" => ceil($total_records / $max),
    "page" => $page,
    "start" => $start,
    "end" => $end,
];

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
