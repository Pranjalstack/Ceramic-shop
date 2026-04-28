<?php
include 'db.php';
session_start();

// Unauthorized access check
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Collect and Sanitize inputs
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $stock = mysqli_real_escape_string($conn, $_POST['stock']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // 2. Build the base update query
    // We use a flexible query that adds the image only if a new one is provided
    $update_query = "UPDATE products SET 
                     name = '$name', 
                     price = '$price', 
                     stock = '$stock', 
                     description = '$description'";

    // 3. Handle Image Upload (Optional)
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $file_name = basename($_FILES["product_image"]["name"]);
        $target_file = $file_name; // Saving to root directory as per your file structure
        
        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            // Append the image update to the query if upload succeeded
            $update_query .= ", image = '$file_name'";
        }
    }

    // 4. Finalize the query with the WHERE clause
    $update_query .= " WHERE id = '$id'";

    // 5. Execute and Verify
    if (mysqli_query($conn, $update_query)) {
        // Redirect back to the inventory page with a success message
        header("Location: admin.php?page=inventory&status=success");
        exit();
    } else {
        // If it fails, this will show you exactly why
        die("Database Error: " . mysqli_error($conn));
    }
} else {
    // Redirect if someone tries to access this file directly
    header("Location: admin.php?page=inventory");
    exit();
}
?>