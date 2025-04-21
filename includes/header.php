<?php
// header.php
session_start(); // Ensure session starts

// Store last visited page only if it's not login.php and user isn't logged in
$currentPage = basename($_SERVER['PHP_SELF']);
$excludePages = ['login.php', 'register.php', 'logout.php'];
 
if (!in_array($currentPage, $excludePages)) {
  $_SESSION["lastPage"] = $_SERVER["REQUEST_URI"];
}


// Debugging output
echo "<!-- Last Page: " . ($_SESSION["lastPage"] ?? 'Not Set') . " -->";
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Mythical Prints</title>
    <link rel="stylesheet" href="assets/css/styles.css" />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css"
    />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7"
      crossorigin="anonymous"
    />
  </head>
  <body>
    <header>
      <a href="index.php"><img src="assets/images/Mythic_Prints_Logo.jpg" class="logo"/></a>
      <h3>Mythical Prints</h3>
      <div class="header-container">
        <!-- Navigation Links -->
        <div class="nav-links">
          <a href="bestSellers.php">Best Sellers</a>
          <a href="newArrivals.php">New Arrivals</a>
          <a href="allPosters.php">All Posters</a>
        </div>
        <div class="header-icons">
          <form method="GET" action="search.php" class="search-bar">
            <input type="text" name="query" placeholder="Search by name or tag..." required>
            <button type="submit" id="search-button"><i class="bi bi-search"></i></button>
          </form>

          <?php if (isset($_SESSION["userId"])): ?>
            <span>
              Welcome, <a href="account.php"><?php echo htmlspecialchars($_SESSION["firstName"]); ?></a>!
            </span>
            <a href="logout.php">Logout</a>
          <?php else:
            $_SESSION["lastPage"] = $_SERVER["REQUEST_URI"];
          ?>
            <a href="login.php?from=<?= urlencode($_SERVER['REQUEST_URI']) ?>"><i class="bi bi-person"></i></a>
          <?php endif; ?>

          <a href="cart.php"><i class="bi bi-cart"></i></a>

          <script>
            document.addEventListener("DOMContentLoaded", function () {
              const searchBar = document.querySelector(".search-bar");
              const searchButton = document.querySelector("#search-button");
              const inputField = searchBar.querySelector("input");

              searchButton.addEventListener("click", function (event) {
                if (!searchBar.classList.contains("active")) {
                  event.preventDefault();
                  searchBar.classList.add("active");
                  inputField.focus();
                }
              });

              inputField.addEventListener("keypress", function (event) {
                if (event.key === "Enter" && searchBar.classList.contains("active")) {
                  searchBar.closest("form").submit();
                }
              });
            });
          </script>
        </div>
      </div>
    </header>