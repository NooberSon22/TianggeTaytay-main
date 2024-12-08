<?php
require("connect.php");
require("mysql-php-executions.php");

$storename = $_GET["storename"] ?? null;

// Validate required input
if (!$storename) {
    echo json_encode(["error" => "storename is required"], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

// Fetch store data
$response = readData($conn, "storetb", ["storename" => $storename]);

// Check if store exists
if (empty($response["data"])) {
    echo json_encode(["error" => "Store not found"], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

// Fetch stall data
$stallNumber = readData($conn, "stalltb", ["storename" => $storename]);

// Count products
$product_count = countData($conn, "producttb", ["storename" => $storename]);

// Fetch linked accounts
$accountsData = readDataWithJoins(
    $conn,
    "store_linkstb",
    [
        [
            'table' => 'platformtb',
            'leftTable' => 'store_linkstb',
            'leftColumn' => 'platformid',
            'rightColumn' => 'platformid'
        ]
    ],
    ["storeid" => $response["data"][0]["storeid"]]
);

$accounts = [];
if (!empty($accountsData["data"])) {
    foreach ($accountsData["data"] as $account) {
        $accounts[] = [
            "link" => $account["link"],
            "img" => $account["img"] ? base64_encode($account["img"]) : null,
        ];
    }
}

// Add additional data to response
$response["data"][0]["img"] = base64_encode($response["data"][0]["img"] ?? "");
$response["data"][0]["product_count"] = $product_count;
$response["data"][0]["stall_numbers"] = implode(", ", array_map(function ($stall) {
    return $stall["stallnumber"];
}, $stallNumber["data"] ?? []));
$response["data"][0]["accounts"] = $accounts;

// Output the response as JSON
echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);