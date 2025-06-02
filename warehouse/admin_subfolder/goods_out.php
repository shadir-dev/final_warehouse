<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "warehouse_db");

if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

$item_type = isset($_GET['item_type']) ? trim($_GET['item_type']) : '';
$item_type = $conn->real_escape_string($item_type);

if ($item_type === '') {
    $stmt = $conn->prepare("SELECT id, emp_contacts, item_type, item_name, quantity, location, moved_at FROM goods_out ORDER BY moved_at DESC");
} else {
    $stmt = $conn->prepare("SELECT id, emp_contacts, item_type, item_name, quantity, location, moved_at FROM goods_out WHERE item_type = ? ORDER BY moved_at DESC");
    $stmt->bind_param("s", $item_type);
}

$stmt->execute();
$result = $stmt->get_result();

$items = [];

while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}

echo json_encode($items);

$stmt->close();
$conn->close();
?>
