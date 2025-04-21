<?php
// index.php
include 'includes/header.php';
include 'includes/db.php';
include 'includes/productPreview.php';
?>

<main class="text-center">
    <h1>Best Sellers</h1>
    <p>Browse through these favorites!</p>

    <div class="product-grid">
        <?php
        $sql = "SELECT * FROM product ORDER BY amountSold DESC LIMIT 3";
        $result = $conn->query($sql);

        displayProductPreviews($result);
        ?>
    </div>

    <br><br><br><br>

    <h1>Newest Arrivals</h1>
    <p>Check out some of our newest additions!</p>

    <div class="product-grid">
        <?php
        $sql = "SELECT * FROM product WHERE dateAdded >= NOW() - INTERVAL 30 DAY ORDER BY dateAdded DESC LIMIT 3";
        $result = $conn->query($sql);

        displayProductPreviews($result);
        ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>