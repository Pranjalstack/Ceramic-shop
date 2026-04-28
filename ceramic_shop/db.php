<?php
error_reporting(0); 

$servername = "localhost"; // MySQL default port
$username = "root";
$password = "";
$dbname = "ceramic_db";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("VAULT OFFLINE: Connection Failed.");
}
?>