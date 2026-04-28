<?php
// 1. Connect to your existing database
include 'db.php'; 
session_start();

// 2. Only proceed if the user clicked the Submit button
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get the product ID and basic info securely
    $p_id = mysqli_real_escape_string($conn, $_POST['product_id']);
    
    // Check if the user is logged in
    if (isset($_SESSION['user_id'])) {
        $u_id = $_SESSION['user_id'];

        // --- RQD BLOCK: Logic for Rating Only ---
        if (isset($_POST['submit_rating'])) {
            $rating = mysqli_real_escape_string($conn, $_POST['rating']);
            // Insert ONLY the rating, leave review_text empty
            $query = "INSERT INTO product_reviews (product_id, user_id, rating, review_text) 
                      VALUES ('$p_id', '$u_id', '$rating', '')";
        } 
        // --- RQD BLOCK: Logic for Written Review Only ---
        else if (isset($_POST['submit_review'])) {
            $review_text = mysqli_real_escape_string($conn, $_POST['review_text']);
            // Insert ONLY the text, leave rating as 0 or NULL
            $query = "INSERT INTO product_reviews (product_id, user_id, rating, review_text) 
                      VALUES ('$p_id', '$u_id', '0', '$review_text')";
        }
        
        // Execute the relevant query defined above
        if (mysqli_query($conn, $query)) {
            // Success! Send the user back to the product page
            header("Location: product_review.php?id=$p_id&status=success");
            exit();
        } else {
            echo "Error saving entry: " . mysqli_error($conn);
        }
    } else {
        // If not logged in, send them to login.php
        header("Location: login.php?error=must_login_to_rate");
        exit();
    }
}
?>