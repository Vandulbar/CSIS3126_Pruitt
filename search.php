<?php include 'includes/header.php'; ?>
<?php include 'includes/db.php'; ?>

<main>
    <h1>Search Results</h1>

    <div class="product-grid">
        <?php
        if (isset($_GET['query'])) {
            $search = trim($_GET['query']);

            // Query to search by product name OR by tag
            $sql = "SELECT DISTINCT p.* FROM Product p
                    LEFT JOIN ProductTag pt ON p.Product_Id = pt.Product_Id
                    LEFT JOIN Tag t ON pt.Tag_ID = t.Tag_ID
                    WHERE p.Name LIKE ? OR t.TagName LIKE ?";

            $stmt = $conn->prepare($sql);
            $searchTerm = "%" . $search . "%";
            $stmt->bind_param("ss", $searchTerm, $searchTerm);
            $stmt->execute();
            $result = $stmt->get_result();

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
                echo "<p>No products found for '$search'.</p>";
            }
        } else {
            echo "<p>Please enter a search term.</p>";
        }
        ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>