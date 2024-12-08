<?php
function insertData(PDO $connect, string $table, array $data): array
{
    $columns = implode(", ", array_keys($data));
    // Example: if $data = ['name' => 'John Doe', 'age' => 30]
    // $columns will be: "name, age"

    $placeholders = ":" . implode(", :", array_keys($data));
    // Example: if $data = ['name' => 'John Doe', 'age' => 30]
    // $placeholders will be: ":name, :age"

    $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
    // Example: if $table = "users"
    // $sql will be: "INSERT INTO users (name, age) VALUES (:name, :age)"

    $stmt = $connect->prepare($sql);

    try {
        if ($stmt->execute($data)) {
            return ['success' => true, 'message' => "Data inserted successfully", "id" => $connect->lastInsertId()];
        } else {
            return ['success' => false, 'message' => "Failed to insert data"];
        }
    } catch (PDOException $e) {
        return ['success' => false, 'message' => "Database error: " . $e->getMessage()];
    }
}

function readData(PDO $connect, string $table, array $conditions = [], int $limit = null, int $offset = null): array
{
    // Build the WHERE clause from conditions
    $condition_clause = implode(" AND ", array_map(fn($k) => "$k = :$k", array_keys($conditions)));

    // Start the SQL query
    $sql = "SELECT * FROM $table";

    // Add conditions if they exist
    if (!empty($condition_clause)) {
        $sql .= " WHERE $condition_clause";
    }

    // Add LIMIT and OFFSET if provided
    if ($limit !== null && $offset !== null) {
        $sql .= " LIMIT :limit OFFSET :offset";
    }

    $stmt = $connect->prepare($sql);

    try {
        // Bind parameters for conditions
        foreach ($conditions as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        // Bind LIMIT and OFFSET if they are used
        if ($limit !== null && $offset !== null) {
            $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
            $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
        }

        // Execute the query
        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => "Data read successfully",
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
        } else {
            return ['success' => false, 'message' => "Failed to read data"];
        }
    } catch (PDOException $e) {
        return ['success' => false, 'message' => "Database error: " . $e->getMessage()];
    }
}

// //read data with inner join and conditions
// function readDataWithJoins(PDO $connect, string $baseTable, array $joins, array $conditions = []): array
// {
//     $join_clause = '';
//     foreach ($joins as $join) {
//         // Each $join should be an associative array with keys: 'table', 'column1', 'column2'
//         $join_clause .= " INNER JOIN {$join['table']} ON {$baseTable}.{$join['column1']} = {$join['table']}.{$join['column2']}";
//     }
//     // Example: if $joins = [['table' => 'orders', 'column1' => 'id', 'column2' => 'user_id'], ['table' => 'products', 'column1' => 'product_id', 'column2' => 'id']]
//     // $join_clause will be: " INNER JOIN orders ON users.id = orders.user_id INNER JOIN products ON orders.product_id = products.id"

//     $condition_clause = '';
//     if (!empty($conditions)) {
//         $condition_clause = 'WHERE ' . implode(" AND ", array_map(fn($k) => "$k = :$k", array_keys($conditions)));
//     }
//     // Example: if $conditions = ['users.id' => 1]
//     // $condition_clause will be: "WHERE users.id = :id"

//     $sql = "SELECT * FROM $baseTable $join_clause $condition_clause";
//     // Example: if $baseTable = "users" and $join_clause and $condition_clause as above:
//     // $sql will be: "SELECT * FROM users INNER JOIN orders ON users.id = orders.user_id INNER JOIN products ON orders.product_id = products.id WHERE users.id = :id"

//     $stmt = $connect->prepare($sql);

//     try {
//         if ($stmt->execute($conditions)) {
//             return [
//                 'success' => true,
//                 'message' => "Data read successfully",
//                 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
//             ];
//         } else {
//             return ['success' => false, 'message' => "Failed to read data"];
//         }
//     } catch (PDOException $e) {
//         return ['success' => false, 'message' => "Database error: " . $e->getMessage()];
//     }
// }

function readDataWithJoins(PDO $connect, string $baseTable, array $joins = [], array $conditions = [], int $limit = null, int $offset = null): array
{
    try {
        // Validate joins
        $join_clause = '';
        foreach ($joins as $join) {
            if (!isset($join['table'], $join['leftTable'], $join['leftColumn'], $join['rightColumn'])) {
                throw new InvalidArgumentException("Invalid join structure");
            }
            $join_clause .= " INNER JOIN {$join['table']} ON {$join['leftTable']}.{$join['leftColumn']} = {$join['table']}.{$join['rightColumn']}";
        }

        // Build WHERE clause
        $condition_clause = '';
        if (!empty($conditions)) {
            $condition_clause = 'WHERE ' . implode(' AND ', array_map(fn($k) => "$k = :$k", array_keys($conditions)));
        }

        // Build LIMIT and OFFSET
        $limit_offset_clause = '';
        if ($limit !== null) {
            $limit_offset_clause = "LIMIT :limit";
            if ($offset !== null) {
                $limit_offset_clause .= " OFFSET :offset";
            }
        }

        // Construct SQL query
        $sql = "SELECT * FROM $baseTable $join_clause $condition_clause $limit_offset_clause";


        // Prepare statement
        $stmt = $connect->prepare($sql);

        // Bind parameters
        foreach ($conditions as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        

        if ($limit !== null) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        }
        if ($offset !== null) {
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }

        // Execute query
        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => "Data read successfully",
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            ];
        }

        return ['success' => false, 'message' => "Failed to execute query"];
    } catch (PDOException $e) {
        error_log("SQL Error: " . $e->getMessage());
        return ['success' => false, 'message' => "Database error: " . $e->getMessage()];
    } catch (Exception $e) {
        return ['success' => false, 'message' => "Error: " . $e->getMessage()];
    }
}


function countData(PDO $connect, string $table, array $conditions = [])
{
    if (!empty($conditions)) {
        $condition_clause = implode(" AND ", array_map(fn($k) => "$k = :$k", array_keys($conditions)));
        $sql = "SELECT COUNT(*) AS total FROM $table WHERE $condition_clause";
    } else {
        $sql = "SELECT COUNT(*) AS total FROM $table";
    }

    $stmt = $connect->prepare($sql);
    $stmt->execute($conditions);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'];
}

function updateData(PDO $connect, string $table, array $data, array $conditions)
{
    $set_clause = implode(", ", array_map(fn($k) => "$k = :$k", array_keys($data)));
    // Example: if $data = ['name' => 'Jane Doe', 'age' => 35]
    // $set_clause will be: "name = :name, age = :age"

    $condition_clause = implode(" AND ", array_map(fn($k) => "$k = :$k", array_keys($conditions)));
    // Example: if $conditions = ['id' => 1]
    // $condition_clause will be: "id = :id"

    $sql = "UPDATE $table SET $set_clause WHERE $condition_clause";
    // Example: if $table = "users"
    // $sql will be: "UPDATE users SET name = :name, age = :age WHERE id = :id"

    $stmt = $connect->prepare($sql);

    $params = array_merge($data, $conditions);
    // Example: if $data = ['name' => 'Jane Doe', 'age' => 35] and $conditions = ['id' => 1]
    // $params will be: ['name' => 'Jane Doe', 'age' => 35, 'id' => 1]

    try {
        if ($stmt->execute($params)) {
            return ['success' => true, 'message' => "Data updated successfully"];
        } else {
            return ['success' => false, 'message' => "Failed to update data"];
        }
    } catch (PDOException $e) {
        return ['success' => false, 'message' => "Database error: " . $e->getMessage()];
    }
}

function deleteData(PDO $connect, string $table, array $conditions)
{
    $condition_clause = implode(" AND ", array_map(fn($k) => "$k = :$k", array_keys($conditions)));
    // Example: if $conditions = ['id' => 1]
    // $condition_clause will be: "id = :id"

    $sql = "DELETE FROM $table WHERE $condition_clause";
    // Example: if $table = "users"
    // $sql will be: "DELETE FROM users WHERE id = :id"

    $stmt = $connect->prepare($sql);

    try {
        if ($stmt->execute($conditions)) {
            return ['success' => true, 'message' => "Data deleted successfully"];
        } else {
            return ['success' => false, 'message' => "Failed to delete data"];
        }
    } catch (PDOException $e) {
        return ['success' => false, 'message' => "Database error: " . $e->getMessage()];
    }
}

//delete data by updating is_active to 0
function deleteDataByUpdate(PDO $connect, string $table, array $conditions)
{
    $condition_clause = implode(" AND ", array_map(fn($k) => "$k = :$k", array_keys($conditions)));
    // Example: if $conditions = ['id' => 1]
    // $condition_clause will be: "id = :id"

    $sql = "UPDATE $table SET is_active = 0 WHERE $condition_clause";

    $stmt = $connect->prepare($sql);

    try {
        if ($stmt->execute($conditions)) {
            return ['success' => true, 'message' => "Data deleted successfully"];
        } else {
            return ['success' => false, 'message' => "Failed to delete data"];
        }
    } catch (PDOException $e) {
        return ['success' => false, 'message' => "Database error: " . $e->getMessage()];
    }
}

// Example usage
// $result = insertData($connect, 'users', ['name' => 'John Doe', 'age' => 25]);
// $result = readData($connect, 'users', ['id' => 1]);
// $result = updateData($connect, 'users', ['name' => 'Jane Doe', 'age' => 35], ['id' => 1]);
// $result = deleteData($connect, 'users', ['id' => 1]);
// $result = deleteDataByUpdate($connect, 'users', ['id' => 1]);

// limit: Specifies the maximum number of rows to fetch.
// offset: Specifies the starting point for fetching rows.
// $response = readData($connect, 'users', [], 10, 0);      first page
// $response = readData($connect, 'users', [], 10, 10);     second page


// $baseTable = 'users';
// $joins = [
//     ['table' => 'orders', 'column1' => 'id', 'column2' => 'user_id'],
//     ['table' => 'products', 'column1' => 'product_id', 'column2' => 'id'],
//     ['table' => 'payments', 'column1' => 'order_id', 'column2' => 'id']
// ];
// $conditions = ['users.id' => 1, 'products.status' => 'active'];

// $result = readDataWithJoins($connect, $baseTable, $joins, $conditions);
// SELECT * FROM users
// INNER JOIN orders ON users.id = orders.user_id
// INNER JOIN products ON orders.product_id = products.id
// INNER JOIN payments ON orders.id = payments.order_id
// WHERE users.id = :id AND products.status = :status

// $baseTable = "tb_user_account";
// $joins = [
//     [
//         'table' => 'tb_user_profile',
//         'leftTable' => 'tb_user_account',
//         'leftColumn' => 'idtb_user_profile',
//         'rightColumn' => 'idtb_user_profile'
//     ],
//     [
//         'table' => 'tb_user_store',
//         'leftTable' => 'tb_user_profile',
//         'leftColumn' => 'idtb_user_store',
//         'rightColumn' => 'idtb_user_store'
//     ]
// ];
// $conditions = [
//     "username" =>  $_POST["username"],
//     "password" =>  $_POST["password"],
// ];

// $user = readDataWithJoins($connect, "tb_user_account", $joins, $conditions);


// echo json_encode($user);
