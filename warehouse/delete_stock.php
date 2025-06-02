<?php
header('Content-Type: application/json');

// Database connection parameters
$host = 'localhost';
$dbname = 'warehouse_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Get the JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (
    !isset($input['item_type']) ||
    !isset($input['item_name']) ||
    !isset($input['quantity_out']) ||
    !isset($input['destination'])
) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$category = $input['item_type']; // corresponds to 'category' in stock table
$itemName = $input['item_name'];
$quantityOut = (int)$input['quantity_out'];
$destination = trim($input['destination']);

if ($quantityOut <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid quantity']);
    exit;
}

try {
    // Start transaction
    $pdo->beginTransaction();

    // Select current quantity and storage_cost with row lock
    $stmt = $pdo->prepare("SELECT quantity, total_storage_cost FROM stock WHERE category = :category AND item_name = :itemName FOR UPDATE");
    $stmt->execute(['category' => $category, 'itemName' => $itemName]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Item not found']);
        exit;
    }

    $currentQuantity = (int)$row['quantity'];
    $storageCostPerItem = (float)$row['total_storage_cost'];

    if ($quantityOut > $currentQuantity) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Quantity to remove exceeds current stock']);
        exit;
    }

    // Calculate total storage cost for the goods moved out
    $totalStorageCost = $storageCostPerItem * $quantityOut;

    $newQuantity = $currentQuantity - $quantityOut;

    if ($newQuantity == 0) {
        // Delete row if quantity reaches zero
        $stmt = $pdo->prepare("DELETE FROM stock WHERE category = :category AND item_name = :itemName");
        $stmt->execute(['category' => $category, 'itemName' => $itemName]);
    } else {
        // Update quantity
        $stmt = $pdo->prepare("UPDATE stock SET quantity = :newQuantity WHERE category = :category AND item_name = :itemName");
        $stmt->execute(['newQuantity' => $newQuantity, 'category' => $category, 'itemName' => $itemName]);
    }

    // Insert goods out log with total storage cost
    $stmt = $pdo->prepare("
        INSERT INTO goods_out 
        (item_type, item_name, quantity_out, destination_location, moved_at, total_storage_cost) 
        VALUES 
        (:itemtype, :itemName, :quantityOut, :destination_location, NOW(), :total_Storage_Cost)
    ");
    $stmt->execute([
        'itemtype' => $category,
        'itemName' => $itemName,
        'quantityOut' => $quantityOut,
        'destination_location' => $destination,
        'total_Storage_Cost' => $totalStorageCost
    ]);

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Goods out recorded successfully']);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
