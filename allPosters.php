<?php
// allPosters.php
include 'includes/header.php';
include 'includes/db.php';
include 'includes/productPreview.php';

// Fetch all available tags for the genre filter
$tagQuery = "SELECT * FROM tag ORDER BY tagName ASC";
$tagResult = $conn->query($tagQuery);
?>

<main class="text-center justify-content-center">
  <h2>Filter Products by Genre</h2>

  <!-- Genre Filter Form -->
  <form method="GET" action="allPosters.php">
    <div class="genre-tags">
      <?php while ($tag = $tagResult->fetch_assoc()): ?>
        <label>
          <input
            type="checkbox"
            name="tags[]"
            value="<?php echo htmlspecialchars($tag['tagName']); ?>"
            <?php if (!empty($_GET['tags']) && in_array($tag['tagName'], $_GET['tags'])) echo "checked"; ?>
          >
          <?php echo htmlspecialchars($tag['tagName']); ?>
        </label><br>
      <?php endwhile; ?>
    </div>
    <button type="submit">Apply Filter</button>
  </form>

  <hr>

  <h3>All Posters</h3>
  <div class="product-grid">
    <?php
    if (!empty($_GET['tags'])) {
        $selectedTags = $_GET['tags'];
        $tagCount = count($selectedTags);
        $placeholders = implode(',', array_fill(0, $tagCount, '?'));

        $query = "
            SELECT p.*
            FROM product p
            JOIN producttag pt ON p.productId = pt.productId
            JOIN tag t ON pt.tagId = t.tagId
            WHERE t.tagName IN ($placeholders)
            GROUP BY p.productId
            HAVING COUNT(DISTINCT t.tagName) = ?
        ";

        $stmt = $conn->prepare($query);

        if ($stmt) {
            $types = str_repeat("s", $tagCount) . "i";
            $params = array_merge($selectedTags, [$tagCount]);
            $stmt->bind_param($types, ...$params);

            $stmt->execute();
            $result = $stmt->get_result();
            displayProductPreviews($result);
        } else {
            echo "<p>Error in query.</p>";
        }
    } else {
        $sql = "SELECT * FROM product ORDER BY name ASC";
        $result = $conn->query($sql);
        displayProductPreviews($result);
    }
    ?>
  </div>
</main>

<?php include 'includes/footer.php'; ?>
