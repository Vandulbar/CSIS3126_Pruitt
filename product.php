<?php include 'includes/header.php'; ?>
<?php include 'includes/db.php'; ?>

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
            echo "<button class='add-to-cart'>Add to Cart</button>";
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
        <div class="random-products">
            <?php
            // Fetch 2 random products excluding the current one
            $random_stmt = $conn->prepare("SELECT * FROM product WHERE Product_Id != ? ORDER BY RAND() LIMIT 2");
            $random_stmt->bind_param("i", $product_id);
            $random_stmt->execute();
            $random_result = $random_stmt->get_result();

            while ($random_product = $random_result->fetch_assoc()) {
                echo "<div class='random-product'>";
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
