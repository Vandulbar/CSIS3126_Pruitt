<?php
//testFiltering.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include '../includes/db.php';

// Define test cases
$testCases = [
    "Single Tag" => ["Thief"],
    "Multiple Matching Tags" => ["Thief", "Elf"],
    "Multiple Non-Matching Tags" => ["Thief", "Mage"], // unlikely combo
    "Fake Tag" => ["NotATag"]
];

foreach ($testCases as $description => $selectedTags) {
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
    $types = str_repeat("s", $tagCount) . "i";
    $params = array_merge($selectedTags, [$tagCount]);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<h3>🧪 $description</h3>";
    echo "<p>Tags: " . implode(", ", $selectedTags) . "</p>";

    if ($result->num_rows === 0) {
        echo "<p>❌ No products matched the tag criteria.</p>";
    } else {
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>✅ {$row['name']} — \${$row['price']}</li>";
        }
        echo "</ul>";
    }

    echo "<hr>";}
?>
