<?php
session_start();

if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    if ($action == 'add' || $action == 'buy') {
        // Increment quantity if exists, else set to 1
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]++;
        } else {
            $_SESSION['cart'][$id] = 1;
        }
        
        if ($action == 'buy') {
            header("Location: cart.php");
            exit();
        }
    } 
    
    if ($action == 'remove') {
        if (isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
        }
    }

    if (isset($_SERVER['HTTP_REFERER'])) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
    } else {
        header("Location: index.php");
    }
    exit();
}
?>