<?php
// confirmation.php
session_start();
include 'includes/db.php';
include 'includes/header.php';

if (!isset($_GET["orderId"])) {
    echo "<main><p>Order ID missing.</p></main>";
    include 'includes/footer.php';
    exit();
}

$orderId = intval($_GET["orderId"]);

// Get order info
$orderStmt = $conn->prepare("SELECT * FROM `order` WHERE orderId = ?");
$orderStmt->bind_param("i", $orderId);
$orderStmt->execute();
$orderResult = $orderStmt->get_result();
$order = $orderResult->fetch_assoc();

if (!$order) {
    echo "<main><p>Order not found.</p></main>";
    include 'includes/footer.php';
    exit();
}

// Get ordered items
$itemStmt = $conn->prepare("
    SELECT od.quantity, od.unitPrice, p.name
    FROM orderDetails od
    JOIN product p ON od.productId = p.productId
    WHERE od.orderId = ?
");
$itemStmt->bind_param("i", $orderId);
$itemStmt->execute();
$itemResult = $itemStmt->get_result();
?>

<main class="text-center">
  <h2>Thank You for Your Order!</h2>
  <p>Your order has been placed successfully.</p>
  <p><strong>Order ID:</strong> <?php echo $orderId; ?></p>
  <p><strong>Shipping To:</strong><br><?php echo nl2br(htmlspecialchars($order["shippingdetails"])); ?></p>
  <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order["paymentmethod"]); ?></p>

  <h3>Items Ordered:</h3>
  <ul class="list-unstyled">
    <?php
    while ($item = $itemResult->fetch_assoc()):
      $subtotal = $item['quantity'] * $item['unitPrice'];
    ?>
      <li>
        <?php echo htmlspecialchars($item['name']); ?> —
        <?php echo $item['quantity']; ?> × $<?php echo number_format($item['unitPrice'], 2); ?>
        = $<?php echo number_format($subtotal, 2); ?>
      </li>
    <?php endwhile; ?>
  </ul>

  <p><strong>Total Paid:</strong> $<?php echo number_format($order["totalprice"], 2); ?></p>
</main>

<?php include 'includes/footer.php'; ?>
