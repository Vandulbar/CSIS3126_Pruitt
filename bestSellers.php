<?php include 'includes/header.php'; ?>
<?php include 'includes/db.php'; ?>

<main>
  <div class="product-grid">
    <?php
      $sql = "SELECT p.* FROM Product p
      JOIN ProductTag pt ON p.Product_Id = pt.Product_Id
      JOIN Tag t ON pt.Tag_ID = t.Tag_ID
      WHERE t.TagName = 'Best Seller'";
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
</main>

<?php include 'includes/footer.php'; ?>