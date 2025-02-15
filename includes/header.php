<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Best Sellers</title>
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
            <a href="#">Genres</a>
        </div>
        <div class="header-icons">
          <form method="GET" action="search.php" class="search-bar">
            <input type="text" name="query" placeholder="Search..." required>
            <button type="submit"><i class="bi bi-search"></i></button>
          </form>
         <i class="bi bi-person"></i>
          <i class="bi bi-cart"></i>
          <script>
            document.addEventListener("DOMContentLoaded", function() {
              const searchBar = document.querySelector(".search-bar");
              const searchButton = searchBar.querySelector("button");

              searchButton.addEventListener("click", function(event) {
                event.preventDefault();
                searchBar.classList.toggle("active");
            
                // Focus on the input field when expanded
                const inputField = searchBar.querySelector("input");
                if (searchBar.classList.contains("active")) {
                  inputField.focus();
                } else {
                  inputField.blur();
                }
              });
            });
          </script>
        </div>
      </div>
    </header>