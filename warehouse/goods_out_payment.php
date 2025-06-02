<?php

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'warehouse_db';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['success' => false, 'message' => "Connection failed: " . $e->getMessage()]));
}

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$requiredFields = ['item_type', 'item_name', 'quantity_out', 'destination', 'payment_method', 'amount_paid'];
foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        echo json_encode(['success' => false, 'message' => "Missing $field"]);
        exit;
    }
}

$category = trim($data['item_type']);
$itemName = trim($data['item_name']);
$quantityOut = (int)$data['quantity_out'];
$destination = trim($data['destination']);
$paymentMethod = trim($data['payment_method']);
$amountPaid = (float)$data['amount_paid'];
$receiptNo = strtoupper(uniqid("RCPT"));

try {
    $pdo->beginTransaction();

    // Fetch item quantity and price
    $stmt = $pdo->prepare("SELECT quantity, total_storage_cost FROM stock 
        WHERE LOWER(category) = LOWER(:category) AND LOWER(item_name) = LOWER(:item_name) FOR UPDATE");
    $stmt->execute([
        ':category' => $category,
        ':item_name' => $itemName
    ]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        throw new Exception("Item not found in stock.");
    }

    if ($item['quantity'] < $quantityOut) {
        throw new Exception("Not enough stock. Available: {$item['quantity']}");
    }

    // Calculate total cost
    $total_storage_cost = (float)$item['total_storage_cost'];
    $totalCost = $quantityOut * $total_storage_cost;

    if ($amountPaid < $totalCost) {
        throw new Exception("Insufficient payment. Total cost: KSh " . number_format($totalCost, 2));
    }

    // Update stock
    $newQuantity = $item['quantity'] - $quantityOut;
    $update = $pdo->prepare("UPDATE stock SET quantity = :quantity WHERE category = :category AND item_name = :item_name");
    $update->execute([
        ':quantity' => $newQuantity,
        ':category' => $category,
        ':item_name' => $itemName
    ]);

    // Insert into payments
    $insert = $pdo->prepare("INSERT INTO payments 
        (receipt_no, item_name, category, quantity, price, total_paid, payment_method, destination, date_paid)
        VALUES 
        (:receipt_no, :item_name, :category, :quantity, :price, :total_paid, :payment_method, :destination, NOW())");

    $insert->execute([
        ':receipt_no' => $receiptNo,
        ':item_name' => $itemName,
        ':category' => $category,
        ':quantity' => $quantityOut,
        ':price' => $total_storage_cost,
        ':total_paid' => $amountPaid,
        ':payment_method' => $paymentMethod,
        ':destination' => $destination
    ]);

    // Insert into goods_out
    $insertOut = $pdo->prepare("INSERT INTO goods_out 
        (item_name, item_type, location, moved_at, payment_date, payment_method, payment_status, quantity, quantity_out, total_storage_cost)
        VALUES 
        (:item_name, :item_type, :location, NOW(), NOW(), :payment_method, :payment_status, :quantity, :quantity_out, :total_storage_cost)");

    $insertOut->execute([
        ':item_name' => $itemName,
        ':item_type' => $category,
        ':location' => $destination,
        ':payment_method' => $paymentMethod,
        ':payment_status' => 'Paid',
        ':quantity' => $item['quantity'],
        ':quantity_out' => $quantityOut,
        ':total_storage_cost' => $total_storage_cost
    ]);

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Payment recorded and goods moved out successfully.',
        'receipt' => [
            'item_type' => $category,
            'item_name' => $itemName,
            'quantity_out' => $quantityOut,
            'destination' => $destination,
            'payment_method' => $paymentMethod,
            'amount_paid' => number_format($amountPaid, 2),
            'timestamp' => date('Y-m-d H:i:s')
        ]
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
