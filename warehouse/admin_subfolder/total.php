<?php
header('Content-Type: application/json');

// DB connection
$host = 'localhost';
$db = 'warehouse_db';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

//<?php

// Check connection
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit();
}

try {
    // Initialize structured response
    $response = [
        'success' => true,
        'Electronics_and_Furniture' => [],
        'Household_and_Cleaning' => [],
        'Food_Products' => [],
        'Outgoing_Goods' => [],
        'total_counts' => [
            'electronics' => 0,
            'household' => 0,
            'food' => 0,
            'outgoing' => 0
        ],
        'total_values' => [
            'electronics' => 0,
            'household' => 0,
            'food' => 0
        ]
    ];

    // 1. Fetch current stock items with calculations
    $stock_query = "SELECT 
                    s.id,
                    s.item_name,
                    s.item_type,
                    s.quantity,
                    s.location,
                    s.arrival_date,
                    s.daily_rate,
                    s.expiry_date,
                    s.employee_contact,
                    s.last_updated,
                    DATEDIFF(NOW(), s.arrival_date) AS days_in_storage,
                    (DATEDIFF(NOW(), s.arrival_date) * s.daily_rate) AS storage_cost
                FROM stock s
                WHERE s.quantity > 0
                ORDER BY s.item_type, s.item_name";

    $stock_result = $conn->query($stock_query);

    if ($stock_result) {
        while ($row = $stock_result->fetch_assoc()) {
            $category = '';
            
            // Categorize items
            if (stripos($row['item_type'], 'electronic') !== false || 
                stripos($row['item_type'], 'furniture') !== false) {
                $category = 'Electronics_and_Furniture';
                $response['total_counts']['electronics'] += $row['quantity'];
                $response['total_values']['electronics'] += $row['storage_cost'];
            } 
            elseif (stripos($row['item_type'], 'household') !== false || 
                   stripos($row['item_type'], 'cleaning') !== false) {
                $category = 'Household_and_Cleaning';
                $response['total_counts']['household'] += $row['quantity'];
                $response['total_values']['household'] += $row['storage_cost'];
            } 
            elseif (stripos($row['item_type'], 'food') !== false) {
                $category = 'Food_Products';
                $response['total_counts']['food'] += $row['quantity'];
                $response['total_values']['food'] += $row['storage_cost'];
            }

            if ($category) {
                $response[$category][] = $row;
            }
        }
    }

    // 2. Fetch outgoing goods
    $outgoing_query = "SELECT 
                        o.id,
                        o.item_id,
                        s.item_name,
                        o.quantity,
                        o.destination,
                        o.contact_person,
                        o.departure_date,
                        o.shipping_method,
                        o.tracking_number,
                        o.status,
                        o.notes
                    FROM goods_out
                    JOIN stock s ON o.item_id = s.id
                    ORDER BY o.departure_date DESC";

    $outgoing_result = $conn->query($outgoing_query);

    if ($outgoing_result) {
        while ($row = $outgoing_result->fetch_assoc()) {
            $response['goods_out'][] = $row;
            $response['total_counts']['outgoing'] += $row['quantity'];
        }
    }

    // 3. Calculate inventory status indicators
    $response['inventory_status'] = [
        'electronics' => getInventoryStatus($response['total_counts']['electronics']),
        'household' => getInventoryStatus($response['total_counts']['household']),
        'food' => getInventoryStatus($response['total_counts']['food'])
    ];

    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
} finally {
    $conn->close();
}

function getInventoryStatus($count) {
    if ($count < 10) return 'Low';
    if ($count < 25) return 'Medium';
    return 'Good';
}
?>a