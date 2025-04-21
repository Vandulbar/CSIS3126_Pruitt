<?php
// includes/productPreview.php

function displayProductPreviews($result) {
    if ($result && $result->num_rows > 0) {
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
        echo "<p>No products found.</p>";
    }
}
?>
