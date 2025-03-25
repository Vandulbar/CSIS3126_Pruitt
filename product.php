<?php 
include 'includes/header.php';
include 'includes/db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity'] ?? 1);

    // Create cart if not exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // If already in cart, increase quantity
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }

    // Optional: Redirect to cart page or show message
    header("Location: cart.php");
    exit();
}

?>

<main>
    <?php
    if (isset($_GET['id'])) {
        $product_id = intval($_GET['id']);
        $stmt = $conn->prepare("SELECT * FROM product WHERE Product_Id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        if ($product) {
            echo "<div class='product-container'>";

            // Product Image
            echo "<div class='product-image'>";
            echo "<img src='assets/images/" . htmlspecialchars($product['Image']) . "' alt='" . htmlspecialchars($product['Name']) . "'>";
            echo "</div>";

            // Product Info
            echo "<div class='product-info'>";
            echo "<h1>" . htmlspecialchars($product['Name']) . "</h1>";
            echo "<p><strong>Price:</strong> $" . number_format($product['Price'], 2) . "</p>";
            echo "<p class='product-description'>Each poster is 24x36 inches and is shipped in a protective cardboard case to ensure it arrives safely.</p>";
            echo "<form method='POST' action=''>";
            echo "<input type='hidden' name='product_id' value='" . $product['Product_Id'] . "'>";
            echo "<input type='number' name='quantity' value='1' min='1' style='width: 60px;'> ";
            echo "<button type='submit' name='add_to_cart'>Add to Cart</button>";
            echo "</form>";

            echo "</div>";

            echo "</div>"; // Close .product-container
        } else {
            echo "<p>Product not found.</p>";
        }
    } else {
        echo "<p>No product selected.</p>";
    }
    ?>
    <section class="related-products">
        <h2>Check out our other products!</h2>
        <div class="product-grid">
            <?php
            // Fetch 2 random products excluding the current one
            $random_stmt = $conn->prepare("SELECT * FROM product WHERE Product_Id != ? ORDER BY RAND() LIMIT 3");
            $random_stmt->bind_param("i", $product_id);
            $random_stmt->execute();
            $random_result = $random_stmt->get_result();

            while ($random_product = $random_result->fetch_assoc()) {
                echo "<div class='product-preview'>";
                echo "<a href='product.php?id=" . $random_product['Product_Id'] . "'>";
                echo "<img src='assets/images/" . htmlspecialchars($random_product['Image']) . "' alt='" . htmlspecialchars($random_product['Name']) . "'>";
                echo "<p>" . htmlspecialchars($random_product['Name']) . "</p>";
                echo "<p>$" . number_format($random_product['Price'], 2) . "</p>";
                echo "</a>";
                echo "</div>";
            }
            ?>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
