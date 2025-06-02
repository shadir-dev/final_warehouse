<?php
session_start();

$host = 'localhost';
$db = 'warehouse_db';
$user = 'root';
$pass = '';
$correct_secret_key = '123'; // 🔐 Change this to your actual secret key

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $secret_key = $_POST['secret_key']; // Get the submitted secret key

    // 🔐 Secret key check before proceeding
    if ($secret_key !== $correct_secret_key) {
        echo "❌ Invalid secret key.";
        exit;
    }

    $stmt = $conn->prepare("SELECT id, password, full_name FROM admins WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    if ($stmt->rowCount() === 1) {
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($password, $admin['password'])) {
            $_SESSION['admins_id'] = $admin['id'];
            $_SESSION['admins_name'] = $admin['full_name'];
            echo "✅ Welcome back, " . htmlspecialchars($admin['full_name']) . "! Redirecting to dashboard...";
            header("refresh:2; url=admin.php");
            exit;
        } else {
            echo "❌ Incorrect password.";
        }
    } else {
        echo "❌ Username not found.";
    }
}
