<?php
// search.php
include 'includes/header.php';
include 'includes/db.php';
include 'includes/product_preview.php';
?>

<main class="text-center">
    <h1>Search Results</h1>

    <div class="product-grid">
        <?php
        if (isset($_GET['query'])) {
            $search = trim($_GET['query']);

            // Query to search by product name OR by tag
            $sql = "SELECT DISTINCT p.* FROM product p
                    LEFT JOIN producttag pt ON p.productId = pt.productId
                    LEFT JOIN tag t ON pt.tagId = t.tagId
                    WHERE p.name LIKE ? OR t.tagName LIKE ?";

            $stmt = $conn->prepare($sql);
            $searchTerm = "%" . $search . "%";
            $stmt->bind_param("ss", $searchTerm, $searchTerm);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='product-preview'>";
                    echo "<a href='product.php?id=" . $row['productId'] . "'>";
                    echo "<img src='assets/images/" . htmlspecialchars($row['image']) . "' alt='" . htmlspecialchars($row['name']) . "'>";
                    echo "<p>" . htmlspecialchars($row['name']) . "</p>";
                    echo "<p>$" . number_format($row['price'], 2) . "</p>";
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
