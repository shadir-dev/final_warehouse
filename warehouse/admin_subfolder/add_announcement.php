<?php
// Database connection
$servername = "localhost"; // your DB server
$username = "root"; // your DB username
$password = ""; // your DB password
$dbname = "warehouse_db"; // your DB name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Get the posted data
$title = $_POST['title'];
$message = $_POST['message'];
$date = $_POST['date'];

// Insert the announcement into the database
$sql = "INSERT INTO announcements (title, message, date) VALUES ('$title', '$message', '$date')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
}

$conn->close();
?>
