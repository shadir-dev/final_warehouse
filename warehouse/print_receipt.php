<?php
// print_receipt.php

$conn = new mysqli("localhost", "root", "", "warehouse_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$receipt_no = $_GET['receipt_no'] ?? '';

if (empty($receipt_no)) {
    die("Missing receipt number.");
}

$query = $conn->prepare("SELECT e.name, s.* 
                         FROM employees_salaries s 
                         JOIN employees e ON s.reg_num = e.reg_num 
                         WHERE s.receipt_no = ?");
$query->bind_param("s", $receipt_no);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    die("Receipt not found.");
}

$row = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Salary Receipt - <?php echo $receipt_no; ?></title>
  <style>
    body {
      font-family: Arial, sans-serif;
      width: 600px;
      margin: auto;
      padding: 30px;
      border: 1px solid #ccc;
    }
    h2 {
      text-align: center;
    }
    table {
      width: 100%;
      margin-top: 20px;
    }
    td {
      padding: 8px;
    }
    .print-btn {
      margin-top: 20px;
      text-align: center;
    }
  </style>
</head>
<body>

<h2>Salary Payment Receipt</h2>
<table>
  <tr><td><strong>Receipt No:</strong></td><td><?php echo $row['receipt_no']; ?></td></tr>
  <tr><td><strong>Employee Name:</strong></td><td><?php echo $row['name']; ?></td></tr>
  <tr><td><strong>Reg Number:</strong></td><td><?php echo $row['reg_num']; ?></td></tr>
  <tr><td><strong>Month:</strong></td><td><?php echo $row['salary_month']; ?></td></tr>
  <tr><td><strong>Net Salary:</strong></td><td>KSh <?php echo number_format($row['net_salary'], 2); ?></td></tr>
  <tr><td><strong>Payment Method:</strong></td><td><?php echo $row['payment_method']; ?></td></tr>
  <tr><td><strong>Payment Date:</strong></td><td><?php echo $row['payment_date']; ?></td></tr>
</table>

<div class="print-btn">
  <button onclick="window.print()">🖨️ Print This Receipt</button>
</div>

</body>
</html>
