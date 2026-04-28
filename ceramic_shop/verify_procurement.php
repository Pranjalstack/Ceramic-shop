<?php
include 'db.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 1. Access Control: Ensure only an logged-in admin can run this script
if (!isset($_SESSION['admin_logged_in'])) {
    die("CRITICAL ERROR: Unauthorized Database Access Attempted.");
}

// 2. Capture Post-Payment Data
$payment_id = $_POST['razorpay_payment_id'] ?? '';
$product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
$added_stock = (int)$_POST['added_stock'];

if (!empty($payment_id)) {
    
    // 3. Update the Inventory
    // Incrementing the existing stock count
    $sql = "UPDATE products SET stock = stock + $added_stock WHERE id = '$product_id'";
    
    if (mysqli_query($conn, $sql)) {
        // SUCCESS: Redirect to admin.php with success flags
        // We include 'status' for the message and 'pid' for the transaction reference
        header("Location: admin.php?status=success&pid=" . $payment_id);
        exit();
    } else {
        // ERROR: Database failed to update
        header("Location: admin.php?status=error");
        exit();
    }

} else {
    // 4. Security Fallback: If someone tries to run this script without a payment ID
    header("Location: admin.php?status=failed");
    exit();
}
?>