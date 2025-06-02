<?php
session_start();// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'warehouse_db';

$conn = new mysqli($host, $username, $password, $database);

// Check if connection was successful
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
// Assuming you have a database connection file

header('Content-Type: application/json');

try {
    // Fetch all stock data grouped by category
    $categories = [
        'Electronics_and_Furniture' => [],
        'Household_and_Cleaning' => [],
        'Food_Products' => []
    ];

    foreach ($categories as $category => &$items) {
        $stmt = $conn->prepare("SELECT * FROM stock WHERE category = ?");
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            // Calculate storage cost based on days in storage
            $arrivalDate = new DateTime($row['arrival_date']);
            $currentDate = new DateTime();
            $daysInStorage = $currentDate->diff($arrivalDate)->days;
            $storageCost = $daysInStorage * $row['daily_rate'];
            
            $row['storage_cost'] = $storageCost;
            $items[] = $row;
        }
        $stmt->close();
    }

    echo json_encode([
        'success' => true,
        'Electronics_and_Furniture' => $categories['Electronics_and_Furniture'],
        'Household_and_Cleaning' => $categories['Household_and_Cleaning'],
        'Food_Products' => $categories['Food_Products']
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching stock data: ' . $e->getMessage()
    ]);
}

$conn->close();
?>