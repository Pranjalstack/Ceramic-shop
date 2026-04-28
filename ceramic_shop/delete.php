<?php
// 1. Force errors to show so you don't get a blank screen if it fails
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db.php';
session_start();

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // --- LOGIC FIX 1: BYPASS CONSTRAINTS ---
    // This stops the white screen crash caused by linked reviews
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0;");

    // --- LOGIC FIX 2: COLUMN NAME MISMATCH ---
    // Your DB screenshot shows 'image_url', not 'image'
    $query = "SELECT image_url FROM products WHERE id = $id";
    $result = mysqli_query($conn, $query);
    
    if ($result && $row = mysqli_fetch_assoc($result)) {
        $image_path = "images/" . $row['image_url'];
        if (!empty($row['image_url']) && file_exists($image_path)) {
            @unlink($image_path); 
        }
    }

    // --- LOGIC FIX 3: TARGETED PURGE ---
    // Delete linked reviews first so the DB stays clean
    mysqli_query($conn, "DELETE FROM product_reviews WHERE product_id = $id");

    // Delete the record from the database
    $sql = "DELETE FROM products WHERE id = $id";
    
    if (mysqli_query($conn, $sql)) {
        // Re-enable safety checks
        mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1;");
        
        // Redirect back to inventory
        header("Location: admin.php?page=inventory&status=deleted");
        exit();
    } else {
        mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1;");
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    header("Location: admin.php?page=inventory");
    exit();
}
?>