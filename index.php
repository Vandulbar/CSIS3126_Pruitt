<?php include 'includes/header.php'; ?>
<?php include 'includes/db.php'; ?>

<main>
    <h1>Best Sellers</h1>
    <p>Browse through these favorites!</p>

    <!-- Product Grid -->
    <div class="product-grid">
        <?php
        $sql = "SELECT * FROM Product ORDER BY amount_sold DESC LIMIT 3";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='product-preview'>";
                echo "<a href='product.php?id=" . $row['Product_Id'] . "'>";
                echo "<img src='assets/images/" . htmlspecialchars($row['Image']) . "' alt='" . htmlspecialchars($row['Name']) . "'>";
                echo "<p>" . htmlspecialchars($row['Name']) . "</p>";
                echo "<p>$" . number_format($row['Price'], 2) . "</p>";
                echo "</a>";
                echo "</div>";
            }
        } else {
            echo "<p>No products found.</p>";
        }
        ?>
    </div>
    <br>
    <br>
    <br>
    <br>
    <h1>Newest Arrivals</h1>
    <p>Check out some of our newest additions!</p>
    <div class="product-grid">
        <?php
        // Fetch products added in the last 30 days
        $sql = "SELECT * FROM product WHERE Date_Added >= NOW() - INTERVAL 30 DAY ORDER BY date_added DESC LIMIT 3";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='product-preview'>";
                echo "<a href='product.php?id=" . $row['Product_Id'] . "'>";
                echo "<img src='assets/images/" . htmlspecialchars($row['Image']) . "' alt='" . htmlspecialchars($row['Name']) . "'>";
                echo "<p>" . htmlspecialchars($row['Name']) . "</p>";
                echo "<p>$" . number_format($row['Price'], 2) . "</p>";
                echo "</a>";
                echo "</div>";
            }
        } else {
            echo "<p>No new arrivals in the last month.</p>";
        }
        ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
