<?php
session_start(); // Ensure session starts

// Store last visited page only if it's not login.php and user isn't logged in
if (!isset($_SESSION["User_Id"]) && !isset($_SESSION["last_page"]) && basename($_SERVER["PHP_SELF"]) != "login.php") {
    $_SESSION["last_page"] = $_SERVER["REQUEST_URI"];
}

// Debugging output
echo "<!-- Last Page: " . ($_SESSION["last_page"] ?? 'Not Set') . " -->";
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
  </head>
  <body>
    <header>
      <a href="index.php"><img src="assets/images/Mythic_Prints_Logo.jpg" class="logo"/></a>
      <h3>Mythical Prints</h3>
      <div class="header-container">
        <!-- Navigation Links (New Arrivals & Genres next to Search Bar) -->
        <div class="nav-links">
            <a href="bestSellers.php">Best Sellers</a>
            <a href="newArrivals.php">New Arrivals</a>
            <a href="allPosters.php">All Posters</a>
            <a href="genres.php">Genres</a>
        </div>
        <div class="header-icons">
          <form method="GET" action="search.php" class="search-bar">
            <input type="text" name="query" placeholder="Search by name or tag..." required>
            <button type="submit" id="search-button"><i class="bi bi-search"></i></button>
          </form>
    <?php 
    if (isset($_SESSION["User_Id"])): ?>
        <span>Welcome, <a href="account.php"><?php echo htmlspecialchars($_SESSION["First_Name"]); ?></a>!</span>
        <a href="logout.php">Logout</a>
    <?php else: 
        // Store the last page visited before login
        $_SESSION["last_page"] = $_SERVER["REQUEST_URI"]; 
    ?>
        <a href="login.php"><i class="bi bi-person"></i></a>
    <?php endif; ?>
    <?php
session_start(); // Ensure session starts at the top of header.php
$_SESSION["last_page"] = $_SERVER["REQUEST_URI"]; 
echo "<!-- Last visited page: " . $_SESSION["last_page"] . " -->";
?>
          <a href="cart.php"><i class="bi bi-cart"></i></a>

          <script>
            document.addEventListener("DOMContentLoaded", function() {
              const searchBar = document.querySelector(".search-bar");
              const searchButton = document.querySelector("#search-button");
              const inputField = searchBar.querySelector("input");

              searchButton.addEventListener("click", function(event) {
                // If the search bar is hidden, toggle it open and prevent form submission
                if (!searchBar.classList.contains("active")) {
                  event.preventDefault();
                  searchBar.classList.add("active");
                  inputField.focus();
                }
                // Otherwise, allow the search to be submitted
              });

              // Allow pressing Enter to submit the search when active
              inputField.addEventListener("keypress", function(event) {
                if (event.key === "Enter" && searchBar.classList.contains("active")) {
                  searchBar.closest("form").submit();
                }
              });
            });
          </script>

        </div>
      </div>
    </header>
