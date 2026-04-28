<?php
include 'db.php';
session_start();

// Security: Ensure only logged-in admins can delete feedback
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

if (isset($_GET['id'])) {
    $feedback_id = mysqli_real_escape_string($conn, $_GET['id']);

    // Execute the deletion
    $query = "DELETE FROM product_reviews WHERE id = '$feedback_id'";
    
    if (mysqli_query($conn, $query)) {
        // Redirect back to the feedbacks page with a success status
        header("Location: admin.php?page=feedbacks&status=deleted");
    } else {
        // Handle database errors
        echo "Error purging record: " . mysqli_error($conn);
    }
} else {
    // Redirect if no ID is provided
    header("Location: admin.php?page=feedbacks");
}
exit();
?>