<?php
// employees.php

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'warehouse_db';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]));
}

function sanitize_input($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['name']) || empty($data['reg_num']) || empty($data['password']) || empty($data['shift_time']) || empty($data['shift_type'])) {
        echo json_encode(["success" => false, "message" => "All fields are required."]);
        exit;
    }

    $raw_name = sanitize_input($data['name']);

    if (preg_match("/\d/", $raw_name)) {
        echo json_encode(["success" => false, "message" => "Name must not contain numbers."]);
        exit;
    }

    // Clean and uppercase the name after validation
    $name = strtoupper(preg_replace("/[^a-zA-Z\s]/", "", $raw_name));
    $reg_num = sanitize_input($data['reg_num']);
    $password = sanitize_input($data['password']);
    $shift_time = sanitize_input($data['shift_time']);
    $shift_type = sanitize_input($data['shift_type']);

    if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/", $password)) {
        echo json_encode(["success" => false, "message" => "Password must be at least 8 characters long and include both letters and numbers."]);
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO employees (name, reg_num, password, shift_time, shift_type) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $reg_num, $hashed_password, $shift_time, $shift_type);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Employee added successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to add employee. Error: " . $stmt->error]);
    }

    $stmt->close();
    exit;
}

?>
