<?php
include 'includes/header.php';
include 'includes/db.php';

// Handle cart updates (quantity changes and removals)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_cart'])) {
    // Handle quantity updates
    if (isset($_POST['quantities']) && is_array($_POST['quantities'])) {
        foreach ($_POST['quantities'] as $product_id => $qty) {
            $product_id = intval($product_id);
            $qty = intval($qty);

            if ($qty > 0) {
                $_SESSION['cart'][$product_id] = $qty;
            } else {
                unset($_SESSION['cart'][$product_id]); // remove if quantity is 0
            }
        }
    }

    // Handle removals
    if (isset($_POST['remove']) && is_array($_POST['remove'])) {
        foreach ($_POST['remove'] as $remove_id) {
            $remove_id = intval($remove_id);
            unset($_SESSION['cart'][$remove_id]);
        }
    }

    // Optional: Redirect to avoid resubmitting form
    header("Location: cart.php");
    exit();
}

// Show cart if it exists and is not empty
?>

<main>

            <h2>Your Shopping Cart</h2>


    <?php if (empty($_SESSION['cart'])): ?>
        <p>Your cart is currently empty.</p>
    <?php else: ?>
        <form method="POST" action="cart.php">
            <table>

                <tr>
                    <th>Product</th>
                    <th>Image</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Remove</th>
                </tr>

                <?php
                $total = 0;
                foreach ($_SESSION['cart'] as $product_id => $quantity):
                    $stmt = $conn->prepare("SELECT * FROM product WHERE Product_Id = ?");
                    $stmt->bind_param("i", $product_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $product = $result->fetch_assoc();

                    if ($product):
                        $subtotal = $product['Price'] * $quantity;
                        $total += $subtotal;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['Name']); ?></td>
                    <td><img src="assets/images/<?php echo htmlspecialchars($product['Image']); ?>" width="80"></td>
                    <td>$<?php echo number_format($product['Price'], 2); ?></td>
                    <td>
                        <input type="number" name="quantities[<?php echo $product_id; ?>]" value="<?php echo $quantity; ?>" min="1" style="width: 60px;">
                    </td>
                    <td>$<?php echo number_format($subtotal, 2); ?></td>
                    <td>
                        <input type="checkbox" name="remove[]" value="<?php echo $product_id; ?>">
                    </td>
                </tr>
                <?php endif; endforeach; ?>
            </table>

            <p><strong>Total: $<?php echo number_format($total, 2); ?></strong></p>
            <button type="submit" name="update_cart">Update Cart</button>
        </form>

        <br>
        <a href="checkout.php"><button>Proceed to Checkout</button></a>
    <?php endif; ?>
</main>

<?php include 'includes/footer.php'; ?>
