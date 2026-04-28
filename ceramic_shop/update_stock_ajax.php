<?php
include 'db.php';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id']) && isset($_POST['action'])) {
    $id = (int)$_POST['id'];
    $action = $_POST['action'];
    
    if ($action === 'increase') {
        mysqli_query($conn, "UPDATE products SET stock = stock + 1 WHERE id = $id");
    } elseif ($action === 'decrease') {
        mysqli_query($conn, "UPDATE products SET stock = GREATEST(0, stock - 1) WHERE id = $id");
    }
    
    $res = mysqli_query($conn, "SELECT stock FROM products WHERE id = $id");
    $row = mysqli_fetch_assoc($res);
    echo $row['stock'];
    exit;
}
?>