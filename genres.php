<?php
include 'includes/header.php';
include 'includes/db.php';

// Fetch all available tags from the Tag table
$tagQuery = "SELECT * FROM tag ORDER BY TagName ASC";
$tagResult = $conn->query($tagQuery);
?>

<main>
<h2>Filter Products by Genre</h2>

<!-- Genre Filter Form -->
<form method="GET" action="genres.php">
    <div class="checkbox-container">
        <?php while ($tag = $tagResult->fetch_assoc()): ?>
            <label>
                <input type="checkbox" name="tags[]" value="<?php echo htmlspecialchars($tag['TagName']); ?>"
                    <?php if (!empty($_GET['tags']) && in_array($tag['TagName'], $_GET['tags'])) echo "checked"; ?>>
                <?php echo htmlspecialchars($tag['TagName']); ?>
            </label><br>
        <?php endwhile; ?>
    </div>
    <button type="submit">Apply Filter</button>
</form>

<hr>

<h3>Filtered Products</h3>
<div class="product-grid">
    <?php
    if (!empty($_GET['tags'])) {
        $selectedTags = $_GET['tags'];
        $placeholders = implode(',', array_fill(0, count($selectedTags), '?'));

        // Build SQL query to find products matching selected tags by TagName
        $query = "
            SELECT DISTINCT p.* FROM product p
            JOIN producttag pt ON p.Product_Id = pt.Product_Id
            JOIN tag t ON pt.Tag_ID = t.Tag_ID
            WHERE t.TagName IN ($placeholders)
        ";

        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param(str_repeat("s", count($selectedTags)), ...$selectedTags);
            $stmt->execute();
            $result = $stmt->get_result();

            // Display filtered products
            while ($product = $result->fetch_assoc()):
    ?>
            <div class="product-preview">
                <img src="assets/images/<?php echo htmlspecialchars($product['Image']); ?>" alt="Product Image">
                <p><?php echo htmlspecialchars($product['Name']); ?></p>
                <p>Price: $<?php echo htmlspecialchars($product['Price']); ?></p>
            </div>
    <?php 
            endwhile;
        } else {
            echo "Error in query.";
        }
    } else {
        echo "<p>Select a genre to see products.</p>";
    }
    ?>
</div>
</main>

<?php include 'includes/footer.php';?>