<?php
header('Content-Type: application/json');
include 'db.php'; // Ensure this points to your database connection file

// 1. DYNAMIC DATA FETCHING
// This tells the AI what is actually in your shop right now
$collection_summary = "";
$query = "SELECT name, price, stock FROM products WHERE stock > 0 LIMIT 10";
$res = mysqli_query($conn, $query);

if ($res) {
    while($row = mysqli_fetch_assoc($res)) {
        $collection_summary .= "Item: " . $row['name'] . " (Price: INR " . $row['price'] . "), ";
    }
}

// 2. CONFIGURATION
// !! REPLACE THE KEY BELOW WITH A FRESHLY GENERATED ONE !!
$api_key = 'ENTER YOUR API KEY'; 
$api_url = 'ENTER API URL';

// 3. USER INPUT
$input = json_decode(file_get_contents('php://input'), true);
$user_message = $input['message'] ?? '';

if (empty($user_message)) {
    echo json_encode(['reply' => 'The archive is silent. How may I assist you?']);
    exit;
}

// 4. PERSONA & PAYLOAD
$system_prompt = "You are the TSC Gallery Curator. You are sophisticated, minimalist, and expert in ceramics. "
               . "The current private collection includes: " . $collection_summary 
               . " Assist clients with inquiries about these specific pieces or general ceramic care. "
               . "Refer to purchases as 'acquisitions' and customers as 'collectors'.";

$data = [
    'model' => 'llama-3.3-70b-versatile',
    'messages' => [
        ['role' => 'system', 'content' => $system_prompt],
        ['role' => 'user', 'content' => $user_message]
    ],
    'temperature' => 0.6
];

// 5. THE REQUEST
$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $api_key
]);

$response = curl_exec($ch);
$result = json_decode($response, true);
curl_close($ch);

// 6. OUTPUT
if (isset($result['choices'][0]['message']['content'])) {
    echo json_encode(['reply' => $result['choices'][0]['message']['content']]);
} else {
    // This helps you see exactly why it fails (Rate limit, Invalid key, etc.)
    $error_info = $result['error']['message'] ?? "The curator is currently indisposed.";
    echo json_encode(['reply' => "Curator Note: " . $error_info]);
}