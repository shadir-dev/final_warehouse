<?php
// Database connection
$servername = "localhost";
$username = "root";  // Change this if your username is different
$password = "";  // Change this if your password is different
$dbname = "warehouse_db";  // Replace with your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// SQL query to fetch announcements
$sql = "SELECT title, message, date FROM announcements ORDER BY date DESC";
$result = $conn->query($sql);

$announcements = [];

if ($result->num_rows > 0) {
  // Fetch all announcements
  while($row = $result->fetch_assoc()) {
    $announcements[] = [
      'title' => $row['title'],
      'message' => $row['message'],
      'date' => $row['date']
    ];
  }
}

// Return the announcements as JSON
header('Content-Type: application/json');
echo json_encode($announcements);

// Close connection
$conn->close();
?>
