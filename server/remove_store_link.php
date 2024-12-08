 <?php
require("connect.php");
require("mysql-php-executions.php");

$json = file_get_contents('php://input');
$data = json_decode($json, true);


$storeid = $data['storeid'];
$platform = $data['platform'];

$deleteQuery = deleteData($conn, 'storetb', ['storeid' => $id]);
