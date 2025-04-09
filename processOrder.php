<?php
// processOrder.php
session_start();
include 'includes/db.php';

if (!isset($_SESSION["userId"]) || empty($_SESSION["cart"])) {
    header("Location: index.php");
    exit();
}

$userId = $_SESSION["userId"];
$cart = $_SESSION["cart"];

// Pull submitted form values
$shippingAddress = implode(", ", [
    $_POST["shippingStreet"],
    $_POST["shippingCity"],
    $_POST["shippingState"],
    $_POST["shippingZipCode"],
    $_POST["shippingCountry"]
]);

$billingAddress = implode(", ", [
    $_POST["billingStreet"],
    $_POST["billingCity"],
    $_POST["billingState"],
    $_POST["billingZipCode"],
    $_POST["billingCountry"]
]);

$paymentMethod = $_POST["paymentMethod"];

// Get product prices and calculate total
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

// Insert into `order` table
$orderStmt = $conn->prepare("
    INSERT INTO `order` (userId, shippingdetails, paymentmethod, totalprice)
    VALUES (?, ?, ?, ?)
");
$orderStmt->bind_param("issd", $userId, $shippingAddress, $paymentMethod, $totalPrice);
$orderStmt->execute();
$orderId = $orderStmt->insert_id;

// Insert into `orderDetails` table
$detailStmt = $conn->prepare("
    INSERT INTO orderDetails (orderId, productId, quantity, unitPrice)
    VALUES (?, ?, ?, ?)
");

foreach ($cart as $productId => $qty) {
    $unitPrice = $prices[$productId];
    $detailStmt->bind_param("iiid", $orderId, $productId, $qty, $unitPrice);
    $detailStmt->execute();
}

// Clear the cart and redirect
$_SESSION["cart"] = [];
header("Location: confirmation.php?orderId=$orderId");
exit();
?>
