<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "warehouse_db";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle registration form
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize input
    $full_name = filter_var(trim($_POST["full_name"]), FILTER_SANITIZE_STRING);
    $username = filter_var(trim($_POST["username"]), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Input validation
    if (!$email) {
        echo "Invalid email format.";
        exit;
    }

    // Check password strength
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number    = preg_match('@[0-9]@', $password);
    $specialChars = preg_match('@[^\w]@', $password);

    if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
        echo "Password must be at least 8 characters and include at least one uppercase letter, one lowercase letter, one number, and one special character.";
        exit;
    }

    if ($password !== $confirm_password) {
        echo "Passwords do not match.";
        exit;
    }

    // Check uniqueness
    $check = $conn->prepare("SELECT id FROM admins WHERE username = :username OR email = :email");
    $check->bindParam(':username', $username);
    $check->bindParam(':email', $email);
    $check->execute();

    if ($check->rowCount() > 0) {
        echo "Username or Email already taken.";
        exit;
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Insert admin
    $stmt = $conn->prepare("INSERT INTO admins (full_name, username, email, password, created_at)
                            VALUES (:full_name, :username, :email, :password, NOW())");
    $stmt->bindParam(':full_name', $full_name);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashed_password);

    if ($stmt->execute()) {
        echo "✅ Registration successful. <a href='admin_login.html'>Login now</a>";
    } else {
        echo "❌ Registration failed. Please try again.";
    }
}
?>
