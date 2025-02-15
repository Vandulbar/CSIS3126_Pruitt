<?php include 'includes/header.php'; ?>
<?php include 'includes/db.php'; ?>

<main>
    <h1>Search Products</h1>
    
    <!-- Search Form -->
    <form method="GET" action="search.php">
        <input type="text" name="query" placeholder="Search for products..." required>
        <button type="submit">Search</button>
    </form>

    <div class="search-results">
        <?php
        if (isset($_GET['query'])) {
            $search_term = "%" . $_GET['query'] . "%"; // Add wildcards for LIKE query
            $stmt = $conn->prepare("SELECT * FROM product WHERE Name LIKE ?");
            $stmt->bind_param("s", $search_term);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "<div class='product-grid'>";
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='product-preview'>";
                    echo "<a href='product.php?id=" . $row['Product_Id'] . "'>";
                    echo "<img src='assets/images/" . htmlspecialchars($row['Image']) . "' alt='" . htmlspecialchars($row['Name']) . "'>";
                    echo "<p>" . htmlspecialchars($row['Name']) . "</p>";
                    echo "<p>$" . number_format($row['Price'], 2) . "</p>";
                    echo "</a>";
                    echo "</div>";
                }
                echo "</div>";
            } else {
                echo "<p>No products found.</p>";
            }
        }
        ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
