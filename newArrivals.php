<?php
// newArrivals.php
include 'includes/header.php';
include 'includes/db.php';
include 'includes/productPreview.php';
?>

<main class="text-center">
  <div class="product-grid">
    <?php
      $sql = "SELECT * FROM product WHERE dateAdded >= NOW() - INTERVAL 30 DAY ORDER BY dateAdded DESC LIMIT 9";
      $result = $conn->query($sql);

      displayProductPreviews($result);
    ?>
  </div>
</main>

<?php include 'includes/footer.php'; ?>
