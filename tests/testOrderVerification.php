<?php
//testOrderVerification
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
include '../includes/db.php';

$userId = 4;

// Get the latest order for the user
$orderStmt = $conn->prepare("
    SELECT * FROM `order`
    WHERE userId = ?
    ORDER BY orderId DESC
    LIMIT 1
");
$orderStmt->bind_param("i", $userId);
$orderStmt->execute();
$orderResult = $orderStmt->get_result();
$order = $orderResult->fetch_assoc();

if (!$order) {
    die("❌ No orders found for user ID $userId.");
}

$orderId = $order['orderId'];

echo "<h2>✅ Order Found</h2>";
echo "<p><strong>Order ID:</strong> {$order['orderId']}</p>";
echo "<p><strong>Shipping:</strong> {$order['shippingdetails']}</p>";
echo "<p><strong>Total:</strong> \${$order['totalprice']}</p>";
echo "<p><strong>Payment:</strong> {$order['paymentmethod']}</p>";

// Get orderDetails
$detailStmt = $conn->prepare("
    SELECT od.quantity, od.unitPrice, p.name
    FROM orderDetails od
    JOIN product p ON od.productId = p.productId
    WHERE od.orderId = ?
");
$detailStmt->bind_param("i", $orderId);
$detailStmt->execute();
$detailResult = $detailStmt->get_result();

echo "<h3>🧾 Items in Order:</h3><ul>";
while ($item = $detailResult->fetch_assoc()) {
    $subtotal = $item['unitPrice'] * $item['quantity'];
    echo "<li>{$item['name']} — {$item['quantity']} × \${$item['unitPrice']} = \${$subtotal}</li>";
}
echo "</ul>";
?>
