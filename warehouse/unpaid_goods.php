<?php
session_start();
require_once 'db.php'; // Make sure this file exists and works

try {
    // Modified query with proper error handling
    $sql = "
        SELECT 
            g.id,
            s.item_name,
            s.unit_price,
            g.quantity,
            (s.unit_price * g.quantity) AS total_amount,
            g.destination_location,
            g.moved_at,
            e.name AS employee_name,
            c.name AS customer_name,
            c.phone AS customer_contact
        FROM goods_out g
        JOIN stock s ON g.stock_id = s.id
        LEFT JOIN employees e ON g.employee_id = e.id
        LEFT JOIN customers c ON g.customer_id = c.id
        WHERE g.payment_status != 'paid' OR g.payment_status IS NULL
        ORDER BY g.moved_at DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $unpaidGoods = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Unpaid Goods | Warehouse System</title>
    <style>
        .unpaid-goods-container {
            margin: 20px;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .unpaid-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .unpaid-table th, .unpaid-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .unpaid-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .unpaid-table tr:hover {
            background-color: #f5f5f5;
        }
        .action-btn {
            padding: 6px 12px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .action-btn:hover {
            background: #2980b9;
        }
        .status-unpaid {
            color: #e74c3c;
            font-weight: bold;
        }
        .total-amount {
            font-weight: bold;
            color: #2c3e50;
        }
        .search-filter {
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        .search-filter input, .search-filter select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    
    <div class="content">
        <div class="unpaid-goods-container">
            <h2>Unpaid Goods</h2>
            
            <div class="search-filter">
                <input type="text" id="search-input" placeholder="Search items...">
                <select id="customer-filter">
                    <option value="">All Customers</option>
                    <?php
                    $customers = $conn->query("SELECT DISTINCT customer_id, name FROM customers ORDER BY name")->fetchAll();
                    foreach ($customers as $customer) {
                        echo "<option value='{$customer['customer_id']}'>{$customer['name']}</option>";
                    }
                    ?>
                </select>
                <input type="date" id="date-from">
                <input type="date" id="date-to">
                <button id="filter-btn" class="action-btn">Apply Filters</button>
            </div>
            
            <div class="table-responsive">
                <table class="unpaid-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Item Name</th>
                            <th>Unit Price</th>
                            <th>Quantity</th>
                            <th>Total Amount</th>
                            <th>Customer</th>
                            <th>Contact</th>
                            <th>Moved By</th>
                            <th>Date Moved</th>
                            <th>Destination</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($unpaidGoods)): ?>
                            <tr>
                                <td colspan="12" style="text-align: center;">No unpaid goods found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($unpaidGoods as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['id']) ?></td>
                                    <td><?= htmlspecialchars($item['item_name']) ?></td>
                                    <td><?= number_format($item['unit_price'], 2) ?></td>
                                    <td><?= $item['quantity'] ?></td>
                                    <td class="total-amount"><?= number_format($item['total_amount'], 2) ?></td>
                                    <td><?= htmlspecialchars($item['customer_name'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($item['customer_contact'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($item['employee_name'] ?? 'N/A') ?></td>
                                    <td><?= date('M d, Y', strtotime($item['moved_at'])) ?></td>
                                    <td><?= htmlspecialchars($item['destination_location']) ?></td>
                                    <td class="status-unpaid">UNPAID</td>
                                    <td>
                                        <a href="payment_processing.php?goods_out_id=<?= $item['id'] ?>" class="action-btn">Process Payment</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="pagination">
                <button id="prev-page" disabled>Previous</button>
                <span id="page-info">Page 1 of 1</span>
                <button id="next-page" disabled>Next</button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        // Implement client-side filtering
        $('#filter-btn').click(function() {
            const searchTerm = $('#search-input').val().toLowerCase();
            const customerId = $('#customer-filter').val();
            const dateFrom = $('#date-from').val();
            const dateTo = $('#date-to').val();
            
            $('.unpaid-table tbody tr').each(function() {
                const row = $(this);
                const itemName = row.find('td:eq(1)').text().toLowerCase();
                const customer = row.find('td:eq(5)').text();
                const dateMoved = row.find('td:eq(8)').text();
                const rowCustomerId = row.find('td:eq(5)').data('customer-id');
                const rowDate = new Date(dateMoved);
                
                let matchesSearch = itemName.includes(searchTerm) || searchTerm === '';
                let matchesCustomer = customerId === '' || rowCustomerId == customerId;
                let matchesDate = true;
                
                if (dateFrom) {
                    const fromDate = new Date(dateFrom);
                    matchesDate = matchesDate && rowDate >= fromDate;
                }
                
                if (dateTo) {
                    const toDate = new Date(dateTo);
                    toDate.setDate(toDate.getDate() + 1); // Include entire end day
                    matchesDate = matchesDate && rowDate <= toDate;
                }
                
                if (matchesSearch && matchesCustomer && matchesDate) {
                    row.show();
                } else {
                    row.hide();
                }
            });
        });
        
        // Implement pagination
        const rowsPerPage = 10;
        let currentPage = 1;
        const totalRows = $('.unpaid-table tbody tr:visible').length;
        const totalPages = Math.ceil(totalRows / rowsPerPage);
        
        function updatePagination() {
            $('#page-info').text(`Page ${currentPage} of ${totalPages}`);
            $('#prev-page').prop('disabled', currentPage === 1);
            $('#next-page').prop('disabled', currentPage === totalPages);
            
            $('.unpaid-table tbody tr:visible').each(function(index) {
                const showRow = index >= (currentPage - 1) * rowsPerPage && 
                                index < currentPage * rowsPerPage;
                $(this).toggle(showRow);
            });
        }
        
        $('#prev-page').click(function() {
            if (currentPage > 1) {
                currentPage--;
                updatePagination();
            }
        });
        
        $('#next-page').click(function() {
            if (currentPage < totalPages) {
                currentPage++;
                updatePagination();
            }
        });
        
        // Initialize
        updatePagination();
    });
    </script>
</body>
</html>