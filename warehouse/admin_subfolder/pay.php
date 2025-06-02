<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "warehouse_db"); // Change this to your DB

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

// Validate
if (
    !isset($data['reg_num']) || !isset($data['salary_month']) ||
    !isset($data['payment_method']) || !isset($data['net_salary'])
) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$reg_num = $conn->real_escape_string($data['reg_num']);
$salary_month = $conn->real_escape_string($data['salary_month']);
$payment_method = $conn->real_escape_string($data['payment_method']);
$net_salary = floatval($data['net_salary']);
$created_at = date('Y-m-d H:i:s');
$receipt_no = strtoupper(uniqid('RCPT-'));

// Check if employee exists
$employee_query = $conn->prepare("SELECT name FROM employees WHERE reg_num = ?");
if (!$employee_query) {
    echo json_encode(['success' => false, 'message' => 'Query error: ' . $conn->error]);
    exit;
}
$employee_query->bind_param("s", $reg_num);
$employee_query->execute();
$result = $employee_query->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Employee not found']);
    exit;
}

$row = $result->fetch_assoc();
$name = $row['name'];

// Insert into employee_salaries
$insert_stmt = $conn->prepare("INSERT INTO employees_salaries (reg_num, salary_month, net_salary, payment_method, receipt_no, payment_date)
                               VALUES (?, ?, ?, ?, ?, ?)");
if (!$insert_stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
    exit;
}

$insert_stmt->bind_param("ssdsss", $reg_num, $salary_month, $net_salary, $payment_method, $receipt_no, $payment_date);

if (!$insert_stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Insert failed: ' . $insert_stmt->error]);
    exit;
}

// Update last_paid_date
$update_stmt = $conn->prepare("UPDATE employees SET last_paid_date = ? WHERE reg_num = ?");
if ($update_stmt) {
    $update_stmt->bind_param("ss", $created_at, $reg_num);
    $update_stmt->execute();
}

echo json_encode([
    'success' => true,
    'message' => 'Salary processed successfully',
    'salary_info' => [
        'employee' => $name,
        'month' => $salary_month,
        'net_salary' => number_format($net_salary, 2),
        'receipt_no' => $receipt_no,
        'print_url' => "print_receipt.php?receipt_no=" . urlencode($receipt_no)
    ]
]);

$conn->close();
?>
