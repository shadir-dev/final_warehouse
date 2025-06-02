<?php
session_start();
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Enable error logging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "warehouse_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    echo json_encode(['success' => false, 'message' => 'Database connection failed: '.$conn->connect_error]);
    exit;
}

// Log raw input
$raw_input = file_get_contents('php://input');
error_log("Raw input received: " . $raw_input);

// Get JSON input
$input = json_decode($raw_input, true);

if (!$input) {
    error_log("JSON decode failed");
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit;
}

// Required fields
$required_fields = ['category', 'employee_contact', 'item_name', 'quantity', 'location', 'arrival_date', 'daily_rate'];
$errors = [];

foreach ($required_fields as $field) {
    if (!isset($input[$field])) {
        $errors[] = "$field is missing";
    } elseif (empty($input[$field])) {
        $errors[] = "$field is empty";
    }
}

if (!empty($errors)) {
    error_log("Validation errors: " . implode(', ', $errors));
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

// Sanitize inputs
$category = $conn->real_escape_string($input['category']);
$employee_contact = $conn->real_escape_string($input['employee_contact']);
$item_name = $conn->real_escape_string($input['item_name']);
$quantity = intval($input['quantity']);
$location = $conn->real_escape_string($input['location']);
$arrival_date = $conn->real_escape_string($input['arrival_date']);
$daily_rate = floatval($input['daily_rate']);

// Validate values
if ($quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Quantity must be greater than 0']);
    exit;
}

if ($daily_rate < 0) {
    echo json_encode(['success' => false, 'message' => 'Daily rate cannot be negative']);
    exit;
}

// Calculate storage
$today = new DateTime();
$arrival = new DateTime($arrival_date);
$interval = $today->diff($arrival);
$days_stored = $interval->days ?: 1;
$total_storage_cost = $daily_rate * $days_stored * $quantity;

// Check if item exists
$check_sql = "SELECT id, quantity, total_storage_cost FROM stock 
              WHERE item_name = '$item_name' 
              AND location = '$location' 
              AND category = '$category'";
              
error_log("Checking existing item with SQL: " . $check_sql);
$check_result = $conn->query($check_sql);

if ($check_result === false) {
    error_log("Check query failed: " . $conn->error);
    echo json_encode(['success' => false, 'message' => 'Database error checking existing item']);
    exit;
}

if ($check_result->num_rows > 0) {
    // Update existing
    $row = $check_result->fetch_assoc();
    $new_quantity = $row['quantity'] + $quantity;
    $new_total_cost = $row['total_storage_cost'] + $total_storage_cost;
    
    $update_sql = "UPDATE stock SET 
                  quantity = $new_quantity,
                  total_storage_cost = $new_total_cost,
                  last_updated = NOW()
                  WHERE id = {$row['id']}";
                  
    error_log("Update SQL: " . $update_sql);
    
    if ($conn->query($update_sql) === TRUE) {
        echo json_encode([
            'success' => true,
            'message' => 'Stock updated successfully',
            'total_storage_cost' => $new_total_cost
        ]);
    } else {
        error_log("Update failed: " . $conn->error);
        echo json_encode([
            'success' => false,
            'message' => 'Update failed: '.$conn->error
        ]);
    }
} else {
    // Insert new
    $insert_sql = "INSERT INTO stock (
                  category, employee_contact, item_name, quantity, 
                  location, arrival_date, daily_rate, total_storage_cost,
                  created_at, last_updated
              ) VALUES (
                  '$category', '$employee_contact', '$item_name', $quantity,
                  '$location', '$arrival_date', $daily_rate, $total_storage_cost,
                  NOW(), NOW()
              )";
              
    error_log("Insert SQL: " . $insert_sql);
    
    if ($conn->query($insert_sql) === TRUE) {
        echo json_encode([
            'success' => true,
            'message' => 'Stock added successfully',
            'total_storage_cost' => $total_storage_cost
        ]);
    } else {
        error_log("Insert failed: " . $conn->error);
        echo json_encode([
            'success' => false,
            'message' => 'Insert failed: '.$conn->error
        ]);
    }
}

$conn->close();
?>