<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

// Retrieve user details from session
$name = $_SESSION['user_name'];
$role = $_SESSION['user_role'];
$user_shift = $_SESSION['user_shift_type'];
$id = $_SESSION['user_id'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Dashboard</title>
    <style>
        /* General Styles */
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
            color: #333;
        }

        header {
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
        }

        h1 {
            margin: 0;
            font-size: 2.5em;
        }

        nav {
            background-color: #34495e;
            padding: 10px 0;
        }

        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
        }

        nav ul li {
            margin: 0 15px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            font-size: 1.1em;
            display: block;
        }

        nav ul li a:hover {
            background-color: #3d566e;
            border-radius: 4px;
        }

        .container {
            padding: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .card {
            background-color: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .card h3 {
            margin-top: 0;
            font-size: 1.5em;
            color: #34495e;
        }

        .task-list {
            list-style: none;
            padding: 0;
        }

        .task-list li {
            font-size: 1.1em;
            margin-bottom: 10px;
        }

        .task-list li.✔ {
            color: green;
        }

        .task-list li.❌ {
            color: red;
        }

        .profile-info {
            font-size: 1.1em;
            margin-top: 10px;
        }

        .profile-info span {
            display: block;
            margin-bottom: 8px;
        }

        .logout-btn {
            background-color: #e74c3c;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 4px;
            font-size: 1.2em;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }

        .logout-btn:hover {
            background-color: #c0392b;
        }

    </style>
</head>
<body>

<header>
    <h1>Employee Dashboard</h1>
</header>

<nav>
    <ul>
        <li><a href="employee_dashboard.php">Home</a></li>
        <li><a href="stock_management.php">Stock Overview</a></li>
        <li><a href="annoncements.html">Announcements</a></li>
        
    </ul>
</nav>

<div class="container">
    <div class="card">
        <h3>Welcome, <?php echo htmlspecialchars($name); ?>!</h3>
        <p>You are logged in as <strong><?php echo htmlspecialchars($role); ?></strong>. Here's your dashboard overview.</p>
    </div>


    <div class="card">
        <h3>Profile Summary</h3>
        <div class="profile-info">
            <span><strong>Name:</strong> <?php echo htmlspecialchars($name); ?></span>
            <span><strong>Employee ID:</strong> <?php echo htmlspecialchars($id); ?></span>
            <span><strong>Role:</strong> <?php echo htmlspecialchars($role); ?></span>
            <span><strong>Shift:</strong> <?php echo htmlspecialchars($user_shift); ?></span>
        </div>
    </div>

    <a href="login.php" class="logout-btn">Logout</a>
</div>

</body>
</html>
