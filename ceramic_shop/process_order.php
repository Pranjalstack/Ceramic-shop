<?php
// 1. FORCE PHP TO SHOW ALL ERRORS
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include('db.php'); 

// 2. Fetch data from session
$customer_email = $_SESSION['email'] ?? ''; 
$product_name = $_SESSION['last_purchased_item'] ?? '';
$product_price = $_SESSION['last_price'] ?? '0.00';

$unique_order_id = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);
$order_number = $unique_order_id;

// DEBUG CHECK 1: Are the session variables actually there?
echo "<h3>DEBUG INFO:</h3>";
echo "Attempting to send to: '" . htmlspecialchars($customer_email) . "'<br>";
echo "Product: '" . htmlspecialchars($product_name) . "'<br><hr>";

if (!empty($customer_email) && !empty($product_name)) {
    $user_id = $_SESSION['user_id'] ?? 0;
    
    $sql_track = "INSERT INTO track (order_number, user_id, customer_email, product_name, price, status) 
                  VALUES ('$unique_order_id', '$user_id', '$customer_email', '$product_name', '$product_price', 'Received')";
    
    if (!mysqli_query($conn, $sql_track)) {
        die("SQL Error: " . mysqli_error($conn)); 
    }
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// DEBUG CHECK 2: Is the vendor folder correct?
if (!file_exists('vendor/autoload.php')) {
    die("FATAL ERROR: Cannot find vendor/autoload.php. Did you install PHPMailer?");
}
require 'vendor/autoload.php'; 

if (!empty($customer_email) && !empty($product_name)) {

    $mail = new PHPMailer(true);

    try {
        // --- TURN ON VERBOSE DEBUGGING ---
        $mail->SMTPDebug = 2; // This will print the exact conversation with Gmail
        
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; 
        $mail->SMTPAuth   = true;
        $mail->Username   = 'pranjalaggarwal706@gmail.com'; 
        $mail->Password   = 'ovmesnbcpikoyqzf'; // Ensure this App Password is still valid!
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('pranjalaggarwal706@gmail.com', 'TSC Artisan Gallery');
        $mail->addAddress($customer_email); 

        $mail->isHTML(true);
        $mail->Subject = 'Order Confirmed: ' . $order_number;

        $mail->Body = "
        <div style='background-color: #0a0a0a; padding: 50px; font-family: \"Cinzel\", serif, sans-serif; color: #ffffff; text-align: center; border: 1px solid #d4af37;'>
            <h1 style='color: #d4af37; letter-spacing: 4px;'>TSC.</h1>
            <p>Order {$unique_order_id} Confirmed.</p>
        </div>";

        $mail->send();
        echo "<br><strong style='color: green;'>SUCCESS: Email sent perfectly.</strong>";
        
    } catch (Exception $e) {
        echo "<br><strong style='color: red;'>MAILER ERROR: </strong>" . $mail->ErrorInfo;
    }
} else {
    echo "<strong style='color: red;'>ERROR: Process stopped. Customer data is missing from the session.</strong>";
}
?>