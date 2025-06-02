
<?php
session_start();
$host = 'localhost';
$db = 'warehouse_db';
$user = 'root';
$pass = '';
// 🔐 Change this to your actual secret key

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
// Assuming this file contains your DB connection

$_SESSION['last_activity'] = time();
$adminsName = htmlspecialchars($_SESSION['admins_name']);

// Function to get goods out data
function getGoodsOutData($category) {
    global $conn;
    
    $categoryMap = [
        'electronics and furniture' => ['electronic', 'furniture'],
        'cleaning and household' => ['cleaning', 'household'],
        'food products' => ['food']
    ];
    
    if (!isset($categoryMap[$category])) {
        return [];
    }
    
    $searchTerms = $categoryMap[$category];
    $placeholders = implode(',', array_fill(0, count($searchTerms), '?'));
    $types = str_repeat('s', count($searchTerms));
    
    $query = "SELECT 
                g.id,
                g.item_id,
                s.item_name,
                s.item_type as category,
                g.quantity,
                s.location as from_location,
                g.destination,
                g.moved_at,
                g.reason,
                e.name as employee_name,
                e.contact as emp_contacts
              FROM goods_out g
              JOIN stock s ON g.item_id = s.id
              LEFT JOIN employees e ON g.employee_id = e.id
              WHERE s.item_type IN ($placeholders)
              ORDER BY g.moved_at DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$searchTerms);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Get total counts (this should match your total.php implementation)
function getTotalCounts() {
    global $conn;
    
    $counts = [
        'electronics and furniture' => 0,
        'cleaning and household' => 0,
        'food products' => 0
    ];
    
    $query = "SELECT 
                CASE 
                    WHEN item_type LIKE '%electronic%' OR item_type LIKE '%furniture%' THEN 'electronics and furniture'
                    WHEN item_type LIKE '%cleaning%' OR item_type LIKE '%household%' THEN 'cleaning and household'
                    WHEN item_type LIKE '%food%' THEN 'food products'
                END as category,
                SUM(quantity) as total
              FROM stock
              GROUP BY category";
    
    $result = $conn->query($query);
    
    while ($row = $result->fetch_assoc()) {
        if (isset($counts[$row['category']])) {
            $counts[$row['category']] = $row['total'];
        }
    }
    
    return $counts;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Welcome <?php echo $adminName; ?> | Admin Panel</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      display: flex;
    }

    .sidebar {
      width: 220px;
      height: 100vh;
      background-color: #2c3e50;
      display: flex;
      flex-direction: column;
      padding-top: 30px;
      position: fixed;
    }

    .sidebar h2 {
      color: white;
      text-align: center;
      margin-bottom: 30px;
      font-size: 24px;
    }

    .sidebar a {
      padding: 15px 25px;
      text-decoration: none;
      color:rgb(219, 233, 243);
      font-size: 18px;
      display: block;
      transition: background-color 0.3s, color 0.3s;
    }

    .sidebar a:hover {
      background-color: #34495e;
      color: #fff;
    }

    .sidebar a.active {
      background-color: #1abc9c;
      color: white;
    }

    .content {
      margin-left: 220px;
      padding: 20px;
      width: calc(100% - 220px);
      background: #ecf0f1;
      min-height: 100vh;
    }

    .dashboard-cards {
      display: flex;
      justify-content: space-between;
      margin-bottom: 30px;
      flex-wrap: wrap;
      gap: 15px;
    }

    .card {
      background: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      flex: 1;
      min-width: 200px;
      text-align: center;
    }

    .card h3 {
      margin-bottom: 10px;
      color: #2c3e50;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 40px;
      background: white;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    th, td {
      padding: 12px 15px;
      border: 1px solid #ddd;
      text-align: left;
    }

    th {
      background: #3498db;
      color: black;
      position: sticky;
      top: 0;
    }

    tr:nth-child(even) {
      background-color:rgb(209, 194, 194);
    }

    tr:hover {
      background-color:rgb(70, 75, 77);
    }

    h1, h2 {
      color: #2c3e50;
      margin: 20px 0;
    }

    h1 {
      margin-top: 0;
    }

    .no-data {
      text-align: center;
      padding: 20px;
      color:rgb(17, 22, 22);
    }

    @media (max-width: 768px) {
      .sidebar {
        width: 100%;
        height: auto;
        position: relative;
      }
      .content {
        margin-left: 0;
        width: 100%;
      }
      .dashboard-cards {
        flex-direction: column;
      }
    }
  </style>
</head>
<body>

<div class="sidebar">
  <h2>Admin Panel</h2>
  <a href="admin.php" class="active">Dashboard</a>
  <a href="stock.html">Stock Overview</a>
  <a href="employees_registration.html">Employees</a>
  <a href="admin_announcements.html">Announce</a>
  <a href="pay_employee.php" class="active">pay employee</a>
  
  <a href="salary_report.php" class="active">salary_report</a>
  <a href="logout.php">Logout</a>
</div>

<div class="content"> 
  <h1 id="welcomeMsg">Welcome, <?php echo $adminsName; ?> 👋</h1>

  <!-- Cards -->
  <?php

$host = 'localhost';
$db = 'warehouse_db';
$user = 'root';
$pass = '';
// 🔐 Change this to your actual secret key

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
// Assuming this file contains your DB connection

function getPaymentReport() {
    global $conn;

    $query = "SELECT 
                id,
                category,
                date_paid,
                destination,
                item_name,
                payment_method,
                price,
                quantity,
                receipt_no,
                total_paid
              FROM payments
              ORDER BY date_paid DESC";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch and display
$payments = getPaymentReport();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Goods Out Payment Report</title>
  <style>
    table {
      border-collapse: collapse;
      width: 100%;
      margin: 20px 0;
    }

    th, td {
      padding: 10px;
      border: 1px solid #ddd;
      text-align: left;
    }

    th {
      background-color: #f4f4f4;
    }

    h2 {
      text-align: center;
    }
  </style>
</head>
<body>

<h2>Goods Out Payment Report</h2>

<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Category</th>
      <th>Date Paid</th>
      <th>Destination</th>
      <th>Item Name</th>
      <th>Payment Method</th>
      <th>Price (KES)</th>
      <th>Quantity</th>
      <th>Total Paid (KES)</th>
      <th>Receipt No</th>
    </tr>
  </thead>
  <tbody>
    <?php if (!empty($payments)): ?>
      <?php foreach ($payments as $index => $row): ?>
        <tr>
          <td><?= $index + 1 ?></td>
          <td><?= htmlspecialchars($row['category']) ?></td>
          <td><?= $row['date_paid'] ? date("Y-m-d H:i:s", strtotime($row['date_paid'])) : 'N/A' ?></td>
          <td><?= htmlspecialchars($row['destination'] ?? 'N/A') ?></td>
          <td><?= htmlspecialchars($row['item_name']) ?></td>
          <td><?= htmlspecialchars($row['payment_method'] ?? 'N/A') ?></td>
          <td><?= number_format($row['price'], 2) ?></td>
          <td><?= $row['quantity'] ?></td>
          <td><?= number_format($row['total_paid'], 2) ?></td>
          <td><?= htmlspecialchars($row['receipt_no']) ?></td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr>
        <td colspan="10" style="text-align:center;">No payment records found.</td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>

</body>
</html>
