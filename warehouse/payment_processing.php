<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Goods Out Payment</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f4;
      margin: 0;
      padding: 0;
    }

    .top-nav {
      background-color: #34495e;
      padding: 10px 0;
    }

    .top-nav ul {
      list-style: none;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
    }

    .top-nav ul li {
      margin: 0 10px;
    }

    .top-nav ul li a {
      color: white;
      text-decoration: none;
      padding: 10px 15px;
      display: block;
      border-radius: 4px;
    }

    .top-nav ul li a:hover {
      background-color: #2c3e50;
    }

    .form-container {
      max-width: 600px;
      margin: 40px auto;
      background: white;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    label {
      font-weight: bold;
    }

    input, select, button {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    button {
      background-color: #28a745;
      color: white;
      font-weight: bold;
      border: none;
      cursor: pointer;
    }

    button:hover {
      background-color: #218838;
    }

    .message {
      display: none;
      padding: 10px;
      border-radius: 5px;
      margin-top: 15px;
    }

    .success {
      background: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    .error {
      background: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }

    #receipt {
      display: none;
      margin-top: 20px;
      background: #fff;
      border: 1px dashed #999;
      padding: 20px;
      border-radius: 8px;
      font-family: monospace;
      line-height: 1.6;
    }

    #receiptButtons {
      display: none;
      text-align: center;
      margin-top: 15px;
    }

    #receiptButtons button {
      width: auto;
      margin: 5px;
    }
  </style>
</head>
<body>

  <nav class="top-nav">
    <ul>
      <li><a href="dashboard.php">Dashboard</a></li>
      <li><a href="stock_management.php">Stock Management</a></li>
      <li><a href="goods_out_payment.php">Goods Out Payment</a></li>
    </ul>
  </nav>

  <div class="form-container">
    <form id="paymentForm">
      <h2>Goods Out Payment</h2>

      <label>Item Type</label>
      <select name="item_type" required>
        <option value="">-- Select --</option>
        <option value="electronics_and_furniture">Electronics and Furniture</option>
        <option value="household_and_cleaning">Household and Cleaning</option>
        <option value="food_products">Food Products</option>
      </select>

      <label>Item Name</label>
      <input type="text" name="item_name" required>

      <label>Quantity Out</label>
      <input type="number" name="quantity_out" required min="1">

      <label>Destination</label>
      <input type="text" name="destination" required>

      <label>Payment Method</label>
      <select name="payment_method" required>
        <option value="">-- Select --</option>
        <option value="Cash">Cash</option>
        <option value="M-Pesa">M-Pesa</option>
        <option value="Bank">Bank</option>
      </select>

      <label>Amount Paid (KSh)</label>
      <input type="number" name="amount_paid" step="0.01" required>

      <button type="submit">Submit Payment</button>
      <div id="msgBox" class="message"></div>
    </form>

    <div id="receipt">
      <h3>Payment Receipt</h3>
      <pre id="receiptContent"></pre>
    </div>

    <div id="receiptButtons">
      <button onclick="printReceipt()">Print Receipt</button>
      <button onclick="downloadReceipt()">Download Receipt</button>
    </div>
  </div>

<script>
  document.getElementById("paymentForm").addEventListener("submit", function(e) {
    e.preventDefault();

    const form = e.target;
    const msgBox = document.getElementById("msgBox");
    const receiptBox = document.getElementById("receipt");
    const receiptContent = document.getElementById("receiptContent");

    const formData = {
      item_type: form.item_type.value.trim(),
      item_name: form.item_name.value.trim(),
      quantity_out: form.quantity_out.value.trim(),
      destination: form.destination.value.trim(),
      payment_method: form.payment_method.value.trim(),
      amount_paid: form.amount_paid.value.trim()
    };

    for (const key in formData) {
      if (formData[key] === "" || (key === "quantity_out" && formData[key] <= 0)) {
        msgBox.style.display = "block";
        msgBox.className = "message error";
        msgBox.textContent = `Please enter a valid value for "${key.replace("_", " ")}"`;
        return;
      }
    }

    fetch('goods_out_payment.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(formData)
    })
    .then(res => res.json())
    .then(data => {
      msgBox.style.display = "block";
      msgBox.textContent = data.message || "An error occurred.";
      msgBox.className = 'message ' + (data.success ? 'success' : 'error');

      if (data.success && data.receipt) {
        const r = data.receipt;
        const receiptText = `
RECEIPT
-------------------------
Item Type     : ${r.item_type}
Item Name     : ${r.item_name}
Quantity Out  : ${r.quantity_out}
Destination   : ${r.destination}
Payment Method: ${r.payment_method}
Amount Paid   : KSh ${r.amount_paid}
Date & Time   : ${r.timestamp}
Receipt No    : ${r.receipt_no}
        `;
        receiptContent.textContent = receiptText;
        receiptBox.style.display = "block";
        document.getElementById("receiptButtons").style.display = "block";
        form.reset();
      } else {
        receiptBox.style.display = "none";
        document.getElementById("receiptButtons").style.display = "none";
      }
    })
    .catch(err => {
      console.error(err);
      msgBox.style.display = "block";
      msgBox.textContent = "Failed to connect to the server.";
      msgBox.className = "message error";
      receiptBox.style.display = "none";
      document.getElementById("receiptButtons").style.display = "none";
    });
  });

  function printReceipt() {
    const receiptText = document.getElementById("receiptContent").textContent;
    const printWindow = window.open('', '', 'height=400,width=600');
    printWindow.document.write('<pre>' + receiptText + '</pre>');
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
  }

  function downloadReceipt() {
    const receiptText = document.getElementById("receiptContent").textContent;
    const blob = new Blob([receiptText], { type: "text/plain;charset=utf-8" });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "receipt.txt";
    link.click();
  }
</script>

</body>
</html>
