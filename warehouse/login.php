<?php
session_start(); // Start the session

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'warehouse_db';

$conn = new mysqli($host, $username, $password, $database);

// Check if connection was successful
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reg_num = $_POST['reg_num'];
    $password = $_POST['password'];

    // Sanitize inputs
    $reg_num = mysqli_real_escape_string($conn, trim($reg_num));
    $password = mysqli_real_escape_string($conn, trim($password));

    // Query to check if user exists (including shift_type now)
    $sql = "SELECT id, password, name, shift_type, role FROM employees WHERE reg_num = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $reg_num); // 's' for string
    $stmt->execute();
    $result = $stmt->get_result();

    // If user exists, check the password
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Login success - Store user info in session
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['user_shift_type'] = $row['shift_type'];
            $_SESSION['user_role'] = $row['role']; // Store user role info

            // Redirect to dashboard
            header("Location: employee_dashboard.php");
            exit;
        } else {
            // Incorrect password
            $_SESSION['error_message'] = "Incorrect password.";
        }
    } else {
        // No user found
        $_SESSION['error_message'] = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Login</title>
    <style>
        /* Simple Styling for the Login Form */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background-color: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: 300px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #2980b9;
        }
        .error {
            color: red;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Employee Login</h2>
    <!-- Login Form -->
    <form action="login.php" method="POST">
        <input type="text" name="reg_num" placeholder="Registration Number" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>

    <!-- Display error message if any -->
    <?php
    if (isset($_SESSION['error_message'])) {
        echo '<p class="error">' . $_SESSION['error_message'] . '</p>';
        unset($_SESSION['error_message']);
    }
    ?>
</div>

</body>
</html>
