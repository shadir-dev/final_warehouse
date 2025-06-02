<?php
// delete_stock.php
$servername = "localhost";
$username = "root";
$password = "";
$database = "warehouse_db";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Fetch item before deleting
    $stmt = $conn->prepare("SELECT * FROM stock WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $item = $stmt->get_result()->fetch_assoc();

    if ($item) {
        // Insert into goods_out
        $insert = $conn->prepare("INSERT INTO goods_out (item_type, item_name, quantity, location, moved_at) VALUES (?, ?, ?, ?, NOW())");
        $insert->bind_param("ssis", $item['item_type'], $item['item_name'], $item['quantity'], $item['location']);
        $insert->execute();

        // Delete from stock
        $delete = $conn->prepare("DELETE FROM stock WHERE id = ?");
        $delete->bind_param("i", $id);
        $delete->execute();

        echo "Item moved to goods out and deleted from stock.";
    } else {
        echo "Item not found.";
    }
} else {
    echo "Invalid request.";
}
?>
