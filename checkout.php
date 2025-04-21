<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

if (!isset($_SESSION["userId"])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION["userId"];
$cart = $_SESSION["cart"] ?? [];

if (empty($cart)) {
    echo "<main><p>Your cart is empty.</p></main>";
    include 'includes/footer.php';
    exit();
}

// Get shipping/billing addresses
$addressQuery = $conn->prepare("SELECT * FROM address WHERE userId = ?");
$addressQuery->bind_param("i", $userId);
$addressQuery->execute();
$addressResult = $addressQuery->get_result();

$shippingAddress = null;
$billingAddress = null;

while ($address = $addressResult->fetch_assoc()) {
    if ($address['addressType'] === 'Shipping') {
        $shippingAddress = $address;
    } elseif ($address['addressType'] === 'Billing') {
        $billingAddress = $address;
    }
}

// Get products in cart
$productIds = array_keys($cart);
$placeholders = implode(',', array_fill(0, count($productIds), '?'));
$types = str_repeat('i', count($productIds));
$stmt = $conn->prepare("SELECT * FROM product WHERE productId IN ($placeholders)");
$stmt->bind_param($types, ...$productIds);
$stmt->execute();
$result = $stmt->get_result();
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[$row['productId']] = $row;
}
?>

<main class="text-center">
  <h2>Checkout</h2>

  <form method="POST" action="processOrder.php">
    <!-- Shipping Address -->
    <h3>Shipping Address</h3>
    <?php if ($shippingAddress): ?>
      <p><?php echo htmlspecialchars($shippingAddress['street'] . ', ' . $shippingAddress['city'] . ', ' . $shippingAddress['state'] . ', ' . $shippingAddress['zipCode'] . ', ' . $shippingAddress['country']); ?></p>
      <input type="hidden" name="shippingStreet" value="<?php echo htmlspecialchars($shippingAddress['street']); ?>">
      <input type="hidden" name="shippingCity" value="<?php echo htmlspecialchars($shippingAddress['city']); ?>">
      <input type="hidden" name="shippingState" value="<?php echo htmlspecialchars($shippingAddress['state']); ?>">
      <input type="hidden" name="shippingZipCode" value="<?php echo htmlspecialchars($shippingAddress['zipCode']); ?>">
      <input type="hidden" name="shippingCountry" value="<?php echo htmlspecialchars($shippingAddress['country']); ?>">
    <?php else: ?>
      <input type="text" name="shippingStreet" placeholder="Street" required><br>
      <input type="text" name="shippingCity" placeholder="City" required><br>
      <input type="text" name="shippingState" placeholder="State" required><br>
      <input type="text" name="shippingZipCode" placeholder="ZIP Code" required><br>
      <input type="text" name="shippingCountry" placeholder="Country" required><br>
    <?php endif; ?>

    <br>
    <label>
      <input type="checkbox" id="sameAsShipping"> Billing address is same as shipping
    </label><br><br>

    <!-- Billing Address -->
    <div id="billing-section">
      <h3>Billing Address</h3>
      <?php if ($billingAddress): ?>
        <p><?php echo htmlspecialchars($billingAddress['street'] . ', ' . $billingAddress['city'] . ', ' . $billingAddress['state'] . ', ' . $billingAddress['zipCode'] . ', ' . $billingAddress['country']); ?></p>
        <input type="hidden" name="billingStreet" value="<?php echo htmlspecialchars($billingAddress['street']); ?>">
        <input type="hidden" name="billingCity" value="<?php echo htmlspecialchars($billingAddress['city']); ?>">
        <input type="hidden" name="billingState" value="<?php echo htmlspecialchars($billingAddress['state']); ?>">
        <input type="hidden" name="billingZipCode" value="<?php echo htmlspecialchars($billingAddress['zipCode']); ?>">
        <input type="hidden" name="billingCountry" value="<?php echo htmlspecialchars($billingAddress['country']); ?>">
      <?php else: ?>
        <input type="text" name="billingStreet" id="billingStreet" placeholder="Street" required><br>
        <input type="text" name="billingCity" id="billingCity" placeholder="City" required><br>
        <input type="text" name="billingState" id="billingState" placeholder="State" required><br>
        <input type="text" name="billingZipCode" id="billingZipCode" placeholder="ZIP Code" required><br>
        <input type="text" name="billingCountry" id="billingCountry" placeholder="Country" required><br>
      <?php endif; ?>
    </div>

    <br>
    <!-- Payment -->
    <h3>Payment Method</h3>
    <select name="paymentMethod" required>
      <option value="Credit Card">Credit Card</option>
      <option value="PayPal">PayPal</option>
    </select><br><br>

    <!-- Order Summary -->
    <h3>Order Summary</h3>
    <ul>
      <?php
      $grandTotal = 0;
      foreach ($cart as $id => $qty):
          $name = $products[$id]['name'];
          $price = $products[$id]['price'];
          $subtotal = $price * $qty;
          $grandTotal += $subtotal;
      ?>
        <li>
          <?php echo htmlspecialchars($name); ?> — <?php echo $qty; ?> × $<?php echo number_format($price, 2); ?>
          = $<?php echo number_format($subtotal, 2); ?>
        </li>
      <?php endforeach; ?>
      <li><strong>Shipping: $3.95</strong></li>
      <?php $grandTotal += 3.95; ?>
    </ul>

    <strong>Total: $<?php echo number_format($grandTotal, 2); ?></strong><br><br>

    <button type="submit">Place Order</button>
  </form>
</main>

<script>
  document.getElementById("sameAsShipping")?.addEventListener("change", function () {
    const checked = this.checked;

    const getFieldValue = name =>
      document.querySelector(`input[name="${name}"]`)?.value || "";

    if (checked && document.getElementById("billingStreet")) {
      document.getElementById("billingStreet").value = getFieldValue("shippingStreet");
      document.getElementById("billingCity").value = getFieldValue("shippingCity");
      document.getElementById("billingState").value = getFieldValue("shippingState");
      document.getElementById("billingZipCode").value = getFieldValue("shippingZipCode");
      document.getElementById("billingCountry").value = getFieldValue("shippingCountry");
    }
  });
</script>

<?php include 'includes/footer.php'; ?>
