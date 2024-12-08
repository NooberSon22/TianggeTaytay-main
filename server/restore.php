<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if a file was uploaded
    if (isset($_FILES['backup_file']) && $_FILES['backup_file']['error'] === UPLOAD_ERR_OK) {
        $uploadedFile = $_FILES['backup_file']['tmp_name'];

        // Database connection details
        $host = 'localhost';
        $username = 'root';
        $password = '';
        $database = 'backup';

        try {
            // Connect to the database
            $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Read the uploaded SQL file
            $sql = file_get_contents($uploadedFile);

            // Disable foreign key checks
            $pdo->exec('SET foreign_key_checks = 0;');

            // Drop tables before restoring
            preg_match_all('/CREATE TABLE `([^`]+)`/i', $sql, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $tableName) {
                    // Drop the table if it exists
                    $pdo->exec("DROP TABLE IF EXISTS `$tableName`");
                }
            }

            // Execute the SQL commands from the backup file
            $pdo->exec($sql);

            // Enable foreign key checks after restoration
            $pdo->exec('SET foreign_key_checks = 1;');

            echo "Database restored successfully.";
        } catch (PDOException $e) {
            echo "Error restoring database: " . $e->getMessage();
        }
    } else {
        echo "Error uploading file.";
    }
} else {
    echo "Invalid request method.";
}
?>
