<?php
require('connect.php');
require('mysql-php-executions.php');

$id = $_GET['id'];

$response = [];
$productData = readData($conn, 'producttb', ['productid' => $id]);
$store = readData($conn, 'storetb', ['storename' => $productData["data"][0]["storename"]]);
$storeid = $store["data"][0]["storeid"];
$imagesData = readData($conn, 'product_img_tb', ['product_id' => $id]);
$categoriesData = readData($conn, 'product_categoriestb', ['productid' => $id]);
$categories = [];

foreach ($categoriesData["data"] as $category) {
    $category_type = $category["category_type"];
    $category_id = $category["category_id"];

    if ($category_type == "general") {
        $category_name = readData($conn, 'categorytb', ['categoryid' => $category_id])["data"][0]["category_name"];
    } else if ($category_type == "store") {
        $category_name = readData($conn, 'categorystoretb', ['idcategorystoretb' => $category_id])["data"][0]["category_name"];
    }

    $categories[] = $category_name;
}

$typesData = readData($conn, 'product_typestb', ['productid' => $id]);
$types = [];

foreach ($typesData["data"] as $type) {
    $product_type = $type["producttype_type"];
    $typeid = $type["typeid"];

    if ($product_type == "general") {
        $result = readData($conn, 'producttypetb', ['typeid' => $typeid]);
        $typename = ($result != null && isset($result["data"][0]["typename"])) ? $result["data"][0]["typename"] : "";
    } else if ($product_type == "store") {
        $typename = readData($conn, 'producttypestoretb', ['idproductypestoretb' => $typeid])["data"][0]["typename"];
    }

    $types[] = $typename;
}

$images = [];

foreach ($imagesData['data'] as $image) {
    $images[] = base64_encode($image['img']);
}

$linksData = readDataWithJoins(
    $conn,
    "products_linkstb",
    [
        [
            "table" => "store_linkstb",
            "leftTable" => "products_linkstb",
            "leftColumn" => "idstore_linkstb",
            "rightColumn" => "idstore_linkstb"
        ],
        [
            "table" => "platformtb",
            "leftTable" => "store_linkstb",
            "leftColumn" => "platformid",
            "rightColumn" => "platformid"
        ]
    ],
    [
        "productid" => $id
    ]
);

$links = [];

foreach ($linksData["data"] as $link) {
    $links[] = [
        "link" => $link["main_link"],
        "linkimg" => base64_encode($link["img"]),
    ];
}


$response = [
    'success' => true,
    'message' => "Data fetched successfully",
    'product' => $productData["data"][0],
    'images' => $images,
    'categories' => $categories,
    'types' => $types,
    'storeid' => $storeid,
    'links' => $links
];

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
