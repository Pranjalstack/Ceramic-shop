<?php
// Configuration
include 'db.php'; // To get your credentials
$host = "localhost";
$user = "root";
$pass = ""; // Leave empty if you haven't set a XAMPP password
$dbname = "ceramic_db";

// 1. Create backup folder if it doesn't exist
$backup_dir = "backups/";
if (!is_dir($backup_dir)) {
    mkdir($backup_dir, 0777, true);
}

// 2. CLEANUP LOGIC: Remove files older than 10 days
$days_to_keep = 10;
$seconds_to_keep = $days_to_keep * 24 * 60 * 60;
$files = glob($backup_dir . "*.sql");

foreach ($files as $file) {
    if (is_file($file)) {
        if (time() - filemtime($file) > $seconds_to_keep) {
            unlink($file); // Delete the file
        }
    }
}

// 3. GENERATE NEW BACKUP
$file_name = $backup_dir . $dbname . "_" . date("Y-m-d_H-i") . ".sql";

// Path to XAMPP mysqldump - using double backslashes for Windows
$mysqldump_path = "C:\\xampp\\mysql\\bin\\mysqldump.exe";

// Construct the command
$command = "\"$mysqldump_path\" --user=$user --password=$pass --host=$host $dbname > \"$file_name\" 2>&1";

// Execute the command
exec($command, $output, $return_var);

// 4. HANDLE REDIRECTION OR STATUS
if ($return_var === 0) {
    if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'admin.php') !== false) {
        header("Location: admin.php?page=dashboard&backup=success");
        exit();
    } else {
        echo "Backup successful! Saved to: " . $file_name . "<br>";
        echo "Cleanup complete: Any files older than $days_to_keep days were removed.";
    }
} else {
    echo "<h2>Backup Failed</h2>";
    echo "Error code: " . $return_var . "<br>";
    echo "Technical Detail: <pre>" . implode("\n", $output) . "</pre>";
    echo "<br><a href='admin.php?page=dashboard'>Return to Dashboard</a>";
}
?>