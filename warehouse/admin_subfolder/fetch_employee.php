<?php
// fetch_employees.php

// Connect to database
$conn = new mysqli('localhost', 'root', '', 'warehouse_db');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch employees
$sql = "SELECT * FROM employees";
$result = $conn->query($sql);

// Start the table
echo '<table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>Name</th>
            <th>Registration Number</th>
            <th>Password</th>
            <th>Check-in Time</th>
            <th>Check-out Time</th>
            <th>Shift Time (hours)</th>
            <th>Shift Type</th>
            <th>Action</th>
        </tr>';

// Display employees
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<tr>
                <td>' . htmlspecialchars($row['name']) . '</td>
                <td>' . htmlspecialchars($row['reg_num']) . '</td>
                <td>' . htmlspecialchars($row['password']) . '</td>
                <td>' . (!empty($row['check_in_time']) ? htmlspecialchars($row['check_in_time']) : '-') . '</td>
                <td>' . (!empty($row['check_out_time']) ? htmlspecialchars($row['check_out_time']) : '-') . '</td>
                <td>' . htmlspecialchars($row['shift_time']) . '</td>
                <td>' . htmlspecialchars($row['shift_type']) . '</td>
                <td>
                    <button onclick="editEmployee(\'' . $row['reg_num'] . '\')">Edit</button>
                    <button onclick="deleteEmployee(\'' . $row['reg_num'] . '\')">Delete</button>
                </td>
            </tr>';
    }
} else {
    echo '<tr><td colspan="8" style="text-align:center;">No employees found</td></tr>';
}

echo '</table>';

$conn->close();
?>
