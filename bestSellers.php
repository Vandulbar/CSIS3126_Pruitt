<?php
// bestSellers.php
include 'includes/header.php';
include 'includes/db.php';
include 'includes/product_preview.php';
?>

<main class="text-center">
  <div class="product-grid">
    <?php
      $sql = "SELECT * FROM product ORDER BY amountSold DESC LIMIT 9";
      $result = $conn->query($sql);

      displayProductPreviews($result);
    ?>
  </div>
</main>

<?php include 'includes/footer.php'; ?>
