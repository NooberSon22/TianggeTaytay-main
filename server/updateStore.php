<?php
session_start();
include_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get form data
        $storename = trim($_POST['storename']);
        $description = trim($_POST['description']);
        $stallnumber = trim($_POST['stallnumber']);
        $contact = trim($_POST['contact']);
        $email = trim($_POST['email']);
        $links = $_POST['links'];
        $store_categories = $_POST['storecategory'];

        // Initialize img to null
        $img = null;
        if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
            $img = file_get_contents($_FILES['img']['tmp_name']);
        }

        if (empty($storename)) {
            throw new Exception("Store Name is required.");
        }

        // Start the transaction
        $conn->beginTransaction();

        // Update store details (excluding image for now)
        $storeUpdateQuery = "
            UPDATE storetb 
            SET description = :description, 
                store_contact = :store_contact, 
                store_email = :store_email 
            WHERE storename = :storename";
        $storeStmt = $conn->prepare($storeUpdateQuery);
        $storeStmt->bindParam(':description', $description, PDO::PARAM_STR);
        $storeStmt->bindParam(':store_contact', $contact, PDO::PARAM_STR);
        $storeStmt->bindParam(':store_email', $email, PDO::PARAM_STR);
        $storeStmt->bindParam(':storename', $storename, PDO::PARAM_STR);
        $storeStmt->execute();

        // Get the store ID
        $storeQuery = "SELECT storeid FROM storetb WHERE storename = :storename";
        $storeStmt = $conn->prepare($storeQuery);
        $storeStmt->bindParam(':storename', $storename, PDO::PARAM_STR);
        $storeStmt->execute();
        $storeid = $storeStmt->fetch(PDO::FETCH_COLUMN);

        // update store links
        foreach ($links as $id => $link) {
            if ($id < 0 && !empty($link)) {
                // New link to be added
                $platform_id = (int) $id * -1;
                $linkInsertQuery = "INSERT INTO store_linkstb (platformid, storeid, link) VALUES (:platformid, :storeid, :link)";
                $linkStmt = $conn->prepare($linkInsertQuery);
                $linkStmt->bindParam(':platformid', $platform_id, PDO::PARAM_INT);
                $linkStmt->bindParam(':storeid', $storeid, PDO::PARAM_INT);
                $linkStmt->bindParam(':link', $link, PDO::PARAM_STR);
                $linkStmt->execute();
            } else if (empty($link)) {
                echo ("id: $id   link: $link");
                // If link is empty, delete it
                $linkDeleteQuery = "DELETE FROM store_linkstb WHERE idstore_linkstb = :idstore_linkstb";
                $linkStmt = $conn->prepare($linkDeleteQuery);
                $linkStmt->bindParam(':idstore_linkstb', $id, PDO::PARAM_INT);
                $linkStmt->execute();
            } elseif ($id > 0) {
                // Existing link to be updated
                $linkUpdateQuery = "UPDATE store_linkstb SET link = :link WHERE idstore_linkstb = :idstore_linkstb";
                $linkStmt = $conn->prepare($linkUpdateQuery);
                $linkStmt->bindParam(':link', $link, PDO::PARAM_STR);
                $linkStmt->bindParam(':idstore_linkstb', $id, PDO::PARAM_INT);
                $linkStmt->execute();
            }
        }

        // Update store categories
        foreach ($store_categories as $categoryid => $category_name) {
            if ($categoryid < 0) {
                // Check if the category already exists
                $checkQuery = "SELECT COUNT(*) FROM categorystoretb WHERE storeid = :storeid AND category_name = :category_name";
                $checkStmt = $conn->prepare($checkQuery);
                $checkStmt->bindParam(':storeid', $storeid, PDO::PARAM_INT);
                $checkStmt->bindParam(':category_name', $category_name, PDO::PARAM_STR);
                $checkStmt->execute();

                $exists = $checkStmt->fetchColumn();

                if ($exists == 0) {
                    // If the category does not exist, insert it
                    $categoryInsertQuery = "INSERT INTO categorystoretb (storeid, category_name) VALUES (:storeid, :category_name)";
                    $categoryStmt = $conn->prepare($categoryInsertQuery);
                    $categoryStmt->bindParam(':storeid', $storeid, PDO::PARAM_INT);
                    $categoryStmt->bindParam(':category_name', $category_name, PDO::PARAM_STR);
                    $categoryStmt->execute();
                }
            } else if (empty($category_name)) {
                $categoryDeleteQuery = "DELETE FROM categorystoretb WHERE idcategorystoretb = :idcategorystoretb";
                $categoryStmt = $conn->prepare($categoryDeleteQuery);
                $categoryStmt->bindParam(':idcategorystoretb', $categoryid, PDO::PARAM_INT);
                $categoryStmt->execute();
            } elseif ($categoryid > 0) {
                $categoryUpdateQuery = "UPDATE categorystoretb SET category_name = :category_name WHERE idcategorystoretb = :idcategorystoretb";
                $categoryStmt = $conn->prepare($categoryUpdateQuery);
                $categoryStmt->bindParam(':category_name', $category_name, PDO::PARAM_STR);
                $categoryStmt->bindParam(':idcategorystoretb', $categoryid, PDO::PARAM_INT);
                $categoryStmt->execute();
            }
        }


        // Update store image if provided
        if ($img !== null) {
            $imageUpdateQuery = "UPDATE storetb SET img = :img WHERE storename = :storename";
            $imageStmt = $conn->prepare($imageUpdateQuery);
            $imageStmt->bindParam(':img', $img, PDO::PARAM_LOB);
            $imageStmt->bindParam(':storename', $storename, PDO::PARAM_STR);
            $imageStmt->execute();
        }

        // Update stall number
        // $stallUpdateQuery = "UPDATE stalltb SET stallnumber = :stallnumber WHERE storename = :storename";
        // $stallStmt = $conn->prepare($stallUpdateQuery);
        // $stallStmt->bindParam(':stallnumber', $stallnumber, PDO::PARAM_STR);
        // $stallStmt->bindParam(':storename', $storename, PDO::PARAM_STR);
        // $stallStmt->execute();

        // Commit the transaction
        $conn->commit();

        // Redirect to the store info page
        header("Location: ../pages/store-info.php");
        exit;
    } catch (Exception $e) {
        // Rollback if something went wrong
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        die("Error: " . $e->getMessage());
    }
}
