<?php
// product.php
include 'includes/header.php';
include 'includes/db.php';
include 'includes/product_preview.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
    $productId = intval($_POST['productId']);
    $quantity = intval($_POST['quantity'] ?? 1);

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += $quantity;
    } else {
        $_SESSION['cart'][$productId] = $quantity;
    }

    header("Location: cart.php");
    exit();
}
?>

<main class="text-center">
<?php
if (isset($_GET['id'])) {
    $productId = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM product WHERE productId = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if ($product) {
        echo "<div class='product-container'>";

        echo "<div class='product-image'>";
        echo "<img src='assets/images/" . htmlspecialchars($product['image']) . "' alt='" . htmlspecialchars($product['name']) . "'>";
        echo "</div>";

        echo "<div class='product-info'>";
        echo "<h1>" . htmlspecialchars($product['name']) . "</h1>";
        echo "<p><strong>Price:</strong> $" . number_format($product['price'], 2) . "</p>";
        echo "<p class='product-description'>Each poster is 24x36 inches and is shipped in a protective cardboard case to ensure it arrives safely.</p>";
        echo "<form method='POST' action=''>";
        echo "<input type='hidden' name='productId' value='" . $product['productId'] . "'>";
        echo "<input type='number' name='quantity' value='1' min='1' style='width: 60px;'> ";
        echo "<button type='submit' name='add_to_cart'>Add to Cart</button>";
        echo "</form>";
        echo "</div>";

        echo "</div>";
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
        $randomStmt = $conn->prepare("SELECT * FROM product WHERE productId != ? ORDER BY RAND() LIMIT 3");
        $randomStmt->bind_param("i", $productId);
        $randomStmt->execute();
        $randomResult = $randomStmt->get_result();

        while ($randomProduct = $randomResult->fetch_assoc()) {
            echo "<div class='product-preview'>";
            echo "<a href='product.php?id=" . $randomProduct['productId'] . "'>";
            echo "<img src='assets/images/" . htmlspecialchars($randomProduct['image']) . "' alt='" . htmlspecialchars($randomProduct['name']) . "'>";
            echo "<p>" . htmlspecialchars($randomProduct['name']) . "</p>";
            echo "<p>$" . number_format($randomProduct['price'], 2) . "</p>";
            echo "</a>";
            echo "</div>";
        }
        ?>
    </div>
</section>
</main>

<?php include 'includes/footer.php'; ?>
