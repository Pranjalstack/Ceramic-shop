<?php
// Force error reporting so you see exactly what's happening
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = $_POST['price'];
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    
    // --- ADDED: Capture stock value from the form ---
    // If stock isn't provided, we default it to 0
    $stock = isset($_POST['stock']) ? (int)$_POST['stock'] : 0;
    
    // Image Handling
    $target_dir = "images/";
    
    // Create directory if it doesn't exist
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_name = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        // FIXED LOGIC: Included the 'stock' column in the INSERT statement
        // Matches your table structure after the ALTER TABLE command
        $sql = "INSERT INTO products (name, price, description, image_url, stock) 
                VALUES ('$name', '$price', '$desc', '$file_name', '$stock')";
        
        if (mysqli_query($conn, $sql)) {
            // Sends you back to the admin page automatically
            header("Location: admin.php?page=inventory&status=success");
            exit(); 
        } else {
            // This will show you the exact SQL error if it still fails
            die("Database Error: " . mysqli_error($conn));
        }
    } else {
        die("Error: Failed to upload image to the 'images/' folder. Check folder permissions.");
    }
}
?>