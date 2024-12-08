
<?php
if ($_FILES['backupFile']['error'] == UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['backupFile']['tmp_name'];
    $command = "mysql -u root -pYOUR_PASSWORD tianggedb < $fileTmpPath";
    
    $output = shell_exec($command);
    if ($output === null) {
        echo json_encode(['status' => 'success', 'message' => 'Restore completed successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Restore failed.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No file uploaded or error occurred.']);
}
?>
