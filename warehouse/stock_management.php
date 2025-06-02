<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Stock Management</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #f2f2f2;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    .top-nav {
      background-color: #34495e;
      width: 100%;
    }

    .top-nav ul {
      list-style: none;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
    }

    .top-nav ul li {
      margin: 0;
    }

    .top-nav ul li a {
      display: block;
      padding: 14px 20px;
      color: white;
      text-decoration: none;
      transition: background-color 0.3s;
    }

    .top-nav ul li a:hover {
      background-color: #3d566e;
    }

    .main-wrapper {
      display: flex;
      flex: 1;
    }

    .sidebar {
      width: 250px;
      background: #2c3e50;
      color: white;
      padding-top: 20px;
    }

    .sidebar h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    .sidebar a {
      display: block;
      padding: 12px 20px;
      text-decoration: none;
      color: white;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .sidebar a:hover {
      background: #34495e;
    }

    .main-content {
      flex: 1;
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }

    header {
      background-color: #2c3e50;
      color: white;
      text-align: center;
      padding: 20px;
    }

    .container {
      padding: 20px;
      overflow-y: auto;
      flex: 1;
    }

    .form-container {
      background: white;
      padding: 20px;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      margin-bottom: 20px;
    }

    form {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      align-items: flex-end;
    }

    .form-group {
      display: flex;
      flex-direction: column;
      min-width: 200px;
    }

    form label {
      margin-bottom: 5px;
      font-weight: bold;
    }

    form input, form select, form button {
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 4px;
    }

    form button {
      background-color: #27ae60;
      color: white;
      border: none;
      cursor: pointer;
      transition: background-color 0.3s;
      height: 40px;
    }

    form button:hover {
      background-color: #219150;
    }

    .table-container {
      display: none;
      margin-top: 20px;
      background: white;
      padding: 20px;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }

    th {
      background-color: #2c3e50;
      color: white;
    }

    tr:hover {
      background-color: #f5f5f5;
    }

    .action-buttons {
      display: flex;
      gap: 5px;
    }

    .remove-btn {
      background-color: #e74c3c;
      color: white;
      padding: 6px 10px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .remove-btn:hover {
      background-color: #c0392b;
    }

    .goods-out-btn {
      background-color: #3498db;
    }

    .goods-out-btn:hover {
      background-color: #2980b9;
    }

    .success-message {
      background-color: #2ecc71;
      color: white;
      padding: 10px;
      border-radius: 4px;
      margin-bottom: 15px;
      display: none;
    }

    .error-message {
      background-color: #e74c3c;
      color: white;
      padding: 10px;
      border-radius: 4px;
      margin-bottom: 15px;
      display: none;
    }

    /* Goods Out Form Styles */
    .goods-out-form {
      background: #f8f9fa;
      padding: 15px;
      border-radius: 5px;
      margin-top: 10px;
      border: 1px solid #dee2e6;
    }

    .goods-out-form h4 {
      margin-top: 0;
      color: #2c3e50;
    }

    .goods-out-form div {
      margin-bottom: 10px;
    }

    .goods-out-form label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
    }

    .goods-out-form input {
      padding: 8px;
      width: 100%;
      border: 1px solid #ced4da;
      border-radius: 4px;
    }

    .goods-out-form .form-actions {
      display: flex;
      gap: 10px;
      margin-top: 15px;
    }

    .goods-out-form button {
      padding: 8px 15px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    .goods-out-form button[type="submit"] {
      background-color: #27ae60;
      color: white;
    }

    .goods-out-form button[type="button"] {
      background-color: #95a5a6;
      color: white;
    }
  </style>
</head>
<body>
  <nav class="top-nav">
    <ul>
      <li><a href="employee_dashboard.php">Home</a></li>
      <li><a href="stock_management.php">Stock Management</a></li>
      <li><a href="annoncements.html">Announcements</a></li>
      
      <li><a href="payment_processing.php">payhere</a></li>
      
        <li><a href="salary_ceck.php">check your salary</a></li>
    </ul>
  </nav>

  <div class="main-wrapper">
    <div class="sidebar">
      <h2>Categories</h2>
      <a onclick="showTable('electronics')">Electronics & Furniture</a>
      <a onclick="showTable('household')">Household & Cleaning</a>
      <a onclick="showTable('food')">Food Products</a>
    </div>

    <div class="main-content">
      <header>
        <h1>Stock Management</h1>
      </header>

      <div class="container">
        <div id="successMessage" class="success-message"></div>
        <div id="errorMessage" class="error-message"></div>

        <div class="form-container">
          <h2>Add / Update Stock</h2>
          <form id="stockForm" onsubmit="handleStock(event)">
            <div class="form-group">
              <label for="itemType">Item Type</label>
              <select id="itemType" required>
                <option value="">Select Item Type</option>
                <option value="Electronics_and_Furniture">Electronics and Furniture</option>
                <option value="Household_and_Cleaning">Household and Cleaning</option>
                <option value="Food_Products">Food Products</option>
              </select>
            </div>

            <div class="form-group">
              <label for="empcontacts">Employee</label>
              <input type="text" id="empcontacts" name="emp_contacts" 
                value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Unknown'); ?>" readonly />
            </div>

            <div class="form-group">
              <label for="itemName">Item Name</label>
              <input type="text" id="itemName" placeholder="Item Name" required />
            </div>

            <div class="form-group">
              <label for="quantity">Quantity</label>
              <input type="number" id="quantity" placeholder="Quantity" min="1" required />
            </div>

            <div class="form-group">
              <label for="location">Location</label>
              <input type="text" id="location" placeholder="Location" required />
            </div>

            <div class="form-group">
              <label for="arrival_date">Arrival Date</label>
              <input type="date" name="arrival_date" id="arrival_date" 
                value="<?php echo date('Y-m-d'); ?>" required />
            </div>

            <div class="form-group">
              <label for="daily_rate">Daily Rate (KES)</label>
              <input type="number" step="0.01" name="daily_rate" id="daily_rate" 
                placeholder="0.00" min="0" required />
            </div>

            <button type="submit">Save Stock</button>
          </form>
        </div>

        <!-- Tables for each category -->
        <div id="electronics" class="table-container">
          <h2>Electronics & Furniture</h2>
          <table>
            <thead>
              <tr>
                <th>Employee</th>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Location</th>
                <th>Arrival Date</th>
                <th>Daily Rate</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>

        <div id="household" class="table-container">
          <h2>Household & Cleaning</h2>
          <table>
            <thead>
              <tr>
                <th>Employee</th>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Location</th>
                <th>Arrival Date</th>
                <th>Daily Rate</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>

        <div id="food" class="table-container">
          <h2>Food Products</h2>
          <table>
            <thead>
              <tr>
                <th>Employee</th>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Location</th>
                <th>Arrival Date</th>
                <th>Daily Rate</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

    <script>
      // Stock data structure
      const stockData = {
        Electronics_and_Furniture: [],
        Household_and_Cleaning: [],
        Food_Products: []
      };

      // DOM elements
      const successMessage = document.getElementById('successMessage');
      const errorMessage = document.getElementById('errorMessage');

      // Show message functions
      function showSuccess(message) {
        successMessage.textContent = message;
        successMessage.style.display = 'block';
        setTimeout(() => {
          successMessage.style.display = 'none';
        }, 5000);
      }

      function showError(message) {
        errorMessage.textContent = message;
        errorMessage.style.display = 'block';
        setTimeout(() => {
          errorMessage.style.display = 'none';
        }, 5000);
      }

      // Category mapping
      const categoryMap = {
        'electronics': 'Electronics_and_Furniture',
        'household': 'Household_and_Cleaning',
        'food': 'Food_Products'
      };

      // Show the selected category table
      function showTable(uiCategory) {
        document.querySelectorAll('.table-container').forEach(table => {
          table.style.display = 'none';
        });
        
        const tableToShow = document.getElementById(uiCategory);
        if (tableToShow) {
          tableToShow.style.display = 'block';
          const dataCategory = categoryMap[uiCategory];
          renderTable(dataCategory);
        }
      }

      // Handle form submission
      async function handleStock(event) {
        event.preventDefault();
        
        const itemType = document.getElementById('itemType').value;
        const empContacts = document.getElementById('empcontacts').value;
        const itemName = document.getElementById('itemName').value.trim();
        const quantity = document.getElementById('quantity').value.trim();
        const location = document.getElementById('location').value.trim();
        const arrivalDate = document.getElementById('arrival_date').value;
        const dailyRate = document.getElementById('daily_rate').value;

        if (!itemType || !itemName || !quantity || !location || !arrivalDate || !dailyRate) {
          showError('Please fill in all required fields.');
          return;
        }

        const newItem = {
          employee_contact: empContacts,
          item_name: itemName,
          quantity: quantity,
          location: location,
          arrival_date: arrivalDate,
          daily_rate: dailyRate
        };

        try {
          const saved = await saveStockToServer(itemType, newItem);
          
          if (saved) {
            stockData[itemType].push(newItem);
            document.getElementById('stockForm').reset();
            showSuccess('Stock item added successfully!');
            renderTable(itemType);
          } else {
            showError('Failed to save stock item.');
          }
        } catch (error) {
          console.error('Error saving stock:', error);
          showError('An error occurred while saving stock.');
        }
      }

      // Save stock to server
      async function saveStockToServer(itemType, newItem) {
        try {
          const response = await fetch('stock_handler.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              category: itemType,
              ...newItem
            })
          });

          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }

          const result = await response.json();
          return result.success;
        } catch (error) {
          console.error('Save stock error:', error);
          throw error;
        }
      }

      // Render table with data
      function renderTable(dataCategory) {
        const uiCategory = Object.keys(categoryMap).find(
          key => categoryMap[key] === dataCategory
        );
        
        if (!uiCategory) {
          console.error('No UI category found for data category:', dataCategory);
          return;
        }

        const tbody = document.querySelector(`#${uiCategory} tbody`);
        
        if (!tbody) {
          console.error('Table body not found for UI category:', uiCategory);
          return;
        }
        
        tbody.innerHTML = '';
        
        if (!stockData[dataCategory] || stockData[dataCategory].length === 0) {
          const tr = document.createElement('tr');
          tr.innerHTML = `<td colspan="7" style="text-align: center;">No items found</td>`;
          tbody.appendChild(tr);
          return;
        }

        stockData[dataCategory].forEach((item, index) => {
          const tr = document.createElement('tr');
          
          tr.innerHTML = `
            <td>${item.employee_contact}</td>
            <td>${item.item_name}</td>
            <td>${item.quantity}</td>
            <td>${item.location}</td>
            <td>${item.arrival_date}</td>
            <td>${item.daily_rate}</td>
            <td class="action-buttons">
              <button class="goods-out-btn" onclick="showGoodsOutForm('${dataCategory}', ${index})">Goods Out</button>
              <button class="remove-btn" onclick="removeStockItem('${dataCategory}', ${index})">Delete</button>
            </td>
          `;
          
          tbody.appendChild(tr);
        });
      }

      // Show goods out form
      function showGoodsOutForm(category, index) {
        const item = stockData[category][index];
        
        document.querySelectorAll('.goods-out-form').forEach(form => form.remove());
        
        const uiCategory = Object.keys(categoryMap).find(key => categoryMap[key] === category);
        const row = document.querySelector(`#${uiCategory} tbody tr:nth-child(${index + 1})`);
        
        if (row) {
          const formHtml = `
            <tr class="goods-out-form-row">
              <td colspan="7">
                <form  ,class="goods-out-form" onsubmit="processGoodsOut(event, '${category}', ${index})">
                  <h4>Process Goods Out</h4>
                  <div>
                    <label>Item: ${item.item_name}</label>
                  </div>
                  <div>
                    <label>Current Quantity: ${item.quantity}</label>
                  </div>
                  <div>
                    <label for="goodsOutQuantity">Quantity to Remove</label>
                    <input type="number" id="goodsOutQuantity" 
                           min="1" max="${item.quantity}" 
                           required>
                  </div>
                  <div>
                    <label for="goodsOutDestination">Destination</label>
                    <input type="text" id="goodsOutDestination" 
                           placeholder="Where are the goods going?" required>
                  </div>
                  <div class="form-actions">
                    <button type="submit">Confirm</button>
                    <button type="button" onclick="cancelGoodsOut()">Cancel</button>
                  </div>
                </form>
              </td>
            </tr>
          `;
          
          row.insertAdjacentHTML('afterend', formHtml);
          row.nextElementSibling.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
      }

      // Process goods out with destination - CORRECTED VERSION
      async function recordGoodsOut(item_type, itemName, quantityOut, destination) {
        try {
          const response = await fetch('delete_stock.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
              item_type: item_type, 
              item_name: itemName, 
              quantity_out: quantityOut,
              destination: destination
            })
          });

          // First check if response is OK
          if (!response.ok) {
            throw new Error(`Server returned ${response.status} ${response.statusText}`);
          }

          // Get response as text first to properly handle errors
          const responseText = await response.text();
          let result;
          
          try {
            result = JSON.parse(responseText);
          } catch (e) {
            console.error('Failed to parse JSON:', responseText);
            throw new Error('Server returned invalid JSON response');
          }

          if (result.success || result.message === 'Goods out recorded successfully') {
            return result;
          } else {
            throw new Error(result.message || 'Unknown error from server');
          }
        } catch (error) {
          console.error('Goods out error:', error);
          throw error;
        }
      }

      // Process goods out - CORRECTED VERSION
      async function processGoodsOut(event, category, index) {
        event.preventDefault();
        
        const item = stockData[category][index];
        const quantityOut = document.getElementById('goodsOutQuantity').value;
        const destination = document.getElementById('goodsOutDestination').value;
        
        if (!quantityOut || isNaN(quantityOut) || !destination) {
          showError('Please enter valid quantity and destination.');
          return;
        }
        
        const quantityNum = parseInt(quantityOut);
        if (quantityNum > parseInt(item.quantity)) {
          showError('Cannot remove more than available quantity.');
          return;
        }
        
        try {
          const result = await recordGoodsOut(category, item.item_name, quantityOut, destination);
          
          if (result) {
            // Update local quantity
            item.quantity -= quantityNum;
            
            // Remove item if quantity reaches 0
            if (item.quantity <= 0) {
              stockData[category].splice(index, 1);
            }
            
            // Remove the goods out form
            const formRow = document.querySelector('.goods-out-form-row');
            if (formRow) {
              formRow.remove();
            }
            
            showSuccess(`${quantityOut} ${item.item_name} sent to ${destination}`);
            renderTable(category);
          }
        } catch (error) {
          console.error('Error in processGoodsOut:', error);
          showError(error.message || 'Failed to process goods out');
        }
      }

      // Cancel goods out operation
      function cancelGoodsOut() {
        document.querySelectorAll('.goods-out-form-row').forEach(row => row.remove());
      }

      // Remove stock item
      async function removeStockItem(item_type, index) {
        if (!confirm('Are you sure you want to delete this item?')) return;
        
        const item = stockData[item_type][index];
        
        try {
          const success = await deleteStockFromServer(item_type, item.item_name);
          
          if (success) {
            stockData[item_type].splice(index, 1);
            showSuccess('Item deleted successfully!');
            renderTable(item_type);
          } else {
            showError('Failed to delete item.');
          }
        } catch (error) {
          console.error('Error deleting item:', error);
          showError('An error occurred while deleting item.');
        }
      }

      // Delete stock from server
      async function deleteStockFromServer(item_type, itemName) {
        try {
          const response = await fetch('fetch_stock.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ item_type, item_name: itemName })
          });

          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }

          const result = await response.json();
          return result.success;
        } catch (error) {
          console.error('Delete stock error:', error);
          throw error;
        }
      }

      // Initialize the page
      document.addEventListener('DOMContentLoaded', function() {
        loadInitialDataFromServer();
        showTable('electronics');
      });

      // Load initial data from server
      async function loadInitialDataFromServer() {
        try {
          const response = await fetch('fetch_stock.php');
          
          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }

          const data = await response.json();
          
          if (data.success) {
            if (data.Electronics_and_Furniture) {
              stockData.Electronics_and_Furniture = data.Electronics_and_Furniture;
            }
            if (data.Household_and_Cleaning) {
              stockData.Household_and_Cleaning = data.Household_and_Cleaning;
            }
            if (data.Food_Products) {
              stockData.Food_Products = data.Food_Products;
            }
            
            renderTable('Electronics_and_Furniture');
          } else {
            showError(data.message || 'Failed to load stock data from server');
          }
        } catch (error) {
          console.error('Error loading initial data:', error);
          showError('An error occurred while loading stock data');
        }
      }
    </script>
  </div>
</body>
</html>
</body>
</html>