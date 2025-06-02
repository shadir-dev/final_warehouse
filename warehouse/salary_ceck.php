<?php 
$conn = new mysqli("localhost", "root", "", "warehouse_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$salary_data = [];
$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reg_num = trim($_POST['reg_num']);

    if (empty($reg_num)) {
        $error = "Please enter your registration number.";
    } else {
        $stmt = $conn->prepare("SELECT e.name, s.* 
                                FROM employees_salaries s 
                                JOIN employees e ON s.reg_num = e.reg_num 
                                WHERE s.reg_num = ? 
                                ORDER BY s.payment_date DESC");
        $stmt->bind_param("s", $reg_num);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $salary_data = $result->fetch_all(MYSQLI_ASSOC);
        } else {
            $error = "No salary records found for Reg No: $reg_num";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Check Salary Records</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background-color: #f5f5f5;
        }
        .container {
            background: #fff;
            max-width: 800px;
            margin: auto;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        }
        h2 {
            text-align: center;
        }
        form {
            text-align: center;
            margin-bottom: 20px;
        }
        input[type="text"] {
            padding: 8px;
            width: 250px;
            font-size: 16px;
        }
        input[type="submit"] {
            padding: 8px 16px;
            font-size: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <li><a href="stock_management.php">Stock Overview</a></li>
<div class="container">
    <h2>Check Your Salary Payment History</h2>
    <form method="POST">
        <input type="text" name="reg_num" placeholder="Enter your Reg Number" required>
        <input type="submit" value="Check Salary">
    </form>

    <?php if (!empty($error)) : ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <?php if (!empty($salary_data)) : ?>
        <h3>Salary Records for <?= htmlspecialchars($salary_data[0]['name']) ?> (<?= htmlspecialchars($salary_data[0]['reg_num']) ?>)</h3>
        <table>
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Net Salary</th>
                    <th>Payment Method</th>
                    <th>Payment Date</th>
                    <th>Receipt No</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($salary_data as $row) : ?>
                    <tr>
                        <td><?= htmlspecialchars($row['salary_month']) ?></td>
                        <td>KSh <?= number_format($row['net_salary'], 2) ?></td>
                        <td><?= htmlspecialchars($row['payment_method']) ?></td>
                        <td><?= htmlspecialchars($row['payment_date']) ?></td>
                        <td><?= htmlspecialchars($row['receipt_no']) ?></td>
                        <td>
                            <a href="print_receipt.php?receipt_no=<?= urlencode($row['receipt_no']) ?>" target="_blank">🧾 Print</a> 
                            <td><a href="print_receipt.php?receipt_no=<?= urlencode($row['receipt_no']) ?>">⬇️ Download</a></td>

                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
