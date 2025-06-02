<?php
$conn = new mysqli("localhost", "root", "", "warehouse_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user requested CSV download
if (isset($_GET['download']) && $_GET['download'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="salary_report.csv"');

    $output = fopen('php://output', 'w');
    // Headers
    fputcsv($output, ['Reg Number', 'Name', 'Salary Month', 'Net Salary (KSh)', 'Payment Method', 'Payment Date', 'Receipt No']);

    $sql = "SELECT s.reg_num, e.name, s.salary_month, s.net_salary, s.payment_method, s.payment_date, s.receipt_no 
            FROM employees_salaries s
            JOIN employees e ON s.reg_num = e.reg_num
            ORDER BY s.payment_date DESC";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, [
                $row['reg_num'],
                $row['name'],
                $row['salary_month'],
                number_format($row['net_salary'], 2),
                $row['payment_method'],
                $row['payment_date'],
                $row['receipt_no']
            ]);
        }
    }
    fclose($output);
    exit;
}

// Otherwise display normal report
$sql = "SELECT s.reg_num, e.name, s.salary_month, s.net_salary, s.payment_method, s.payment_date, s.receipt_no 
        FROM employees_salaries s
        JOIN employees e ON s.reg_num = e.reg_num
        ORDER BY s.payment_date DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Salary Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background-color: #f9f9f9; }
        h2 { text-align: center; margin-bottom: 20px; }
        table { border-collapse: collapse; width: 100%; background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        th, td { padding: 12px 15px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background-color: #007BFF; color: white; }
        tr:hover { background-color: #f1f1f1; }
        a.print-link { color: #007BFF; text-decoration: none; font-weight: bold; }
        a.print-link:hover { text-decoration: underline; }
        .buttons {
            margin-bottom: 20px;
            text-align: right;
        }
        .buttons button, .buttons a {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px 15px;
            margin-left: 10px;
            cursor: pointer;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }
        .buttons button:hover, .buttons a:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
        function printReport() {
            const printContents = document.getElementById('reportTable').outerHTML;
            const originalContents = document.body.innerHTML;

            document.body.innerHTML = `<h2>All Employee Salary Payments</h2>` + printContents;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload(); // reload to restore event handlers and styles
        }
    </script>
</head>
<body>

  <a href="admin.php" class="active">Dashboard</a>
<h2>All Employee Salary Payments</h2>

<div class="buttons">
    <a href="?download=csv" title="Download CSV">⬇️ Download CSV</a>
    <button onclick="printReport()">🖨️ Print Report</button>
</div>

<table id="reportTable">
    <thead>
        <tr>
            <th>Reg Number</th>
            <th>Name</th>
            <th>Salary Month</th>
            <th>Net Salary (KSh)</th>
            <th>Payment Method</th>
            <th>Payment Date</th>
            <th>Receipt No</th>
            <th>Print Receipt</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['reg_num']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['salary_month']) ?></td>
                    <td><?= number_format($row['net_salary'], 2) ?></td>
                    <td><?= htmlspecialchars($row['payment_method']) ?></td>
                    <td><?= htmlspecialchars($row['payment_date']) ?></td>
                    <td><?= htmlspecialchars($row['receipt_no']) ?></td>
                    <td><a href="print_receipt.php?receipt_no=<?= urlencode($row['receipt_no']) ?>" target="_blank" class="print-link">🧾 Print</a></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="8" style="text-align:center;">No salary records found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>

<?php $conn->close(); ?>
