<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Employee Salary Payment</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 30px;
    }

    label {
      display: block;
      margin-top: 10px;
    }

    input, select {
      padding: 8px;
      width: 300px;
    }

    button {
      margin-top: 20px;
      padding: 10px 20px;
      background: green;
      color: white;
      border: none;
      cursor: pointer;
    }

    .receipt-link {
      margin-top: 20px;
    }

    #message {
      margin-top: 20px;
    }
  </style>
</head>
<body>

<a href="admin.php" class="active">Dashboard</a>
<h2>Pay Employee Salary</h2>

<form id="salaryForm">
  <label for="reg_num">Employee Reg Number:</label>
  <input type="text" id="reg_num" name="reg_num" required>

  <label for="salary_month">Salary Month (e.g., June 2025):</label>
  <input type="text" id="salary_month" name="salary_month" required>

  <label for="salary_amount">Net Salary Amount (KSh):</label>
  <input type="number" id="salary_amount" name="salary_amount" required>

  <label for="payment_method">Payment Method:</label>
  <select id="payment_method" name="payment_method" required>
    <option value="">--Select Method--</option>
    <option value="Cash">Cash</option>
    <option value="Bank Transfer">Bank Transfer</option>
    <option value="Cheque">Cheque</option>
    <option value="M-PESA">M-PESA</option>
  </select>

  <button type="submit">Process Payment</button>
</form>

<div id="message"></div>

<script>
  document.getElementById('salaryForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const formData = {
      reg_num: document.getElementById('reg_num').value.trim(),
      salary_month: document.getElementById('salary_month').value.trim(),
      net_salary: document.getElementById('salary_amount').value.trim(),
      payment_method: document.getElementById('payment_method').value.trim()
    };

    const msgDiv = document.getElementById('message');
    msgDiv.innerHTML = 'Processing...';

    try {
      const response = await fetch('pay.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData)
      });

      const text = await response.text(); // Raw response
      let result;

      try {
        result = JSON.parse(text);
      } catch (jsonError) {
        msgDiv.innerHTML = `<p style="color: red;">Server returned invalid response:<br><pre>${text}</pre></p>`;
        return;
      }

      if (result.success) {
        msgDiv.innerHTML = `
          <p style="color: green;">${result.message}</p>
          <strong>Employee:</strong> ${result.salary_info.employee} <br>
          <strong>Month:</strong> ${result.salary_info.month} <br>
          <strong>Net Salary:</strong> KSh ${result.salary_info.net_salary} <br>
          <strong>Receipt No:</strong> ${result.salary_info.receipt_no} <br><br>
          <a class="receipt-link" href="print_receipt.php" target="_blank">🧾 Print Receipt</a>
        `;
      } else {
        msgDiv.innerHTML = `<p style="color: red;">${result.message}</p>`;
      }
    } catch (error) {
      msgDiv.innerHTML = `<p style="color: red;">An unexpected error occurred: ${error.message}</p>`;
    }
  });
</script>

</body>
</html>
