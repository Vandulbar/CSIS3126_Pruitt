<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
include '../includes/db.php';

// Initialize cart
if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}

// List of test products to add
$testProducts = [
    1 => 2,
    20 => 2
];

foreach ($testProducts as $productId => $quantityToAdd) {
    // Check if product exists
    $check = $conn->prepare("SELECT productId, name FROM product WHERE productId = ?");
    $check->bind_param("i", $productId);
    $check->execute();
    $result = $check->get_result();

    if ($product = $result->fetch_assoc()) {
        if (isset($_SESSION["cart"][$productId])) {
            $_SESSION["cart"][$productId] += $quantityToAdd;
            echo "🔁 Updated quantity of <strong>{$product['name']}</strong> (ID: $productId) in cart.<br>";
        } else {
            $_SESSION["cart"][$productId] = $quantityToAdd;
            echo "🛒 Added <strong>{$product['name']}</strong> (ID: $productId) to cart.<br>";
        }
    } else {
        echo "❌ Product with ID $productId does not exist in the database. Skipped.<br>";
    }
}

// Show cart contents
echo "<h3>🧾 Current Cart:</h3><ul>";
foreach ($_SESSION["cart"] as $id => $qty) {
    echo "<li>Product ID: $id — Quantity: $qty</li>";
}
echo "</ul>";

unset($_SESSION["cart"]);
echo "<p>🧹 Cart cleared.</p>";
?>