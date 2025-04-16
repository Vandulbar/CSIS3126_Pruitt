<?php
//testCheckout.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
include '../includes/db.php';

// Step 1: Simulate a logged-in user and cart
$_SESSION["userId"] = 4; // Use a real user ID in your DB
$_SESSION["cart"] = [
    1 => 1, // productId => quantity
    2 => 2
];

$userId = $_SESSION["userId"];
$cart = $_SESSION["cart"];

// Step 2: Dummy form values
$shippingAddress = "123 Test Lane, Testville, TS, 12345, Testland";
$billingAddress = "321 Billing Rd, Billtown, BL, 54321, Billonia";
$paymentMethod = "Credit Card";

// Step 3: Recalculate total from DB
$productIds = array_keys($cart);
$placeholders = implode(',', array_fill(0, count($productIds), '?'));
$types = str_repeat('i', count($productIds));
$stmt = $conn->prepare("SELECT productId, price FROM product WHERE productId IN ($placeholders)");
$stmt->bind_param($types, ...$productIds);
$stmt->execute();
$result = $stmt->get_result();

$prices = [];
while ($row = $result->fetch_assoc()) {
    $prices[$row['productId']] = $row['price'];
}

$totalPrice = 0;
foreach ($cart as $productId => $qty) {
    $subtotal = $prices[$productId] * $qty;
    $totalPrice += $subtotal;
}
$shippingCost = 3.95;
$totalPrice += $shippingCost;

// Step 4: Insert order
$orderStmt = $conn->prepare("
    INSERT INTO `order` (userId, shippingdetails, paymentmethod, totalprice)
    VALUES (?, ?, ?, ?)
");
$orderStmt->bind_param("issd", $userId, $shippingAddress, $paymentMethod, $totalPrice);
$orderStmt->execute();
$orderId = $orderStmt->insert_id;

if ($orderId) {
    echo "✅ Order inserted with ID $orderId<br>";
} else {
    die("❌ Failed to insert order: " . $orderStmt->error);
}

// Step 5: Insert orderDetails
$detailStmt = $conn->prepare("
    INSERT INTO orderDetails (orderId, productId, quantity, unitPrice)
    VALUES (?, ?, ?, ?)
");

foreach ($cart as $productId => $qty) {
    $unitPrice = $prices[$productId];
    $detailStmt->bind_param("iiid", $orderId, $productId, $qty, $unitPrice);
    $detailStmt->execute();
    echo "🛒 Added product $productId x$qty to orderDetails<br>";
}

// Step 6: Clear cart
$_SESSION["cart"] = [];
echo "<br>🧹 Cart cleared.";
?>