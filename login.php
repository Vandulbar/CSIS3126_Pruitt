<?php
// login.php
session_start();
include 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Check for email
    $stmt = $conn->prepare("SELECT userId, firstName, password FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user["password"])) {
        // Store user info in session
        $_SESSION["userId"] = $user["userId"];
        $_SESSION["firstName"] = $user["firstName"];

        // Determine the correct redirect location
        if (!empty($_SESSION["lastPage"]) && basename($_SESSION["lastPage"]) != "login.php") {
            $redirectPage = $_SESSION["lastPage"];
            unset($_SESSION["lastPage"]); // Clear the session variable
        } else {
            $redirectPage = "index.php"; // Default to home page
        }

        header("Location: " . $redirectPage);
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mythical Prints</title>
  <link rel="stylesheet" href="assets/css/styles.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" />
</head>
<body>
  <header>
    <a href="index.php"><img src="assets/images/Mythic_Prints_Logo.jpg" class="logo" /></a>
    <h3>Mythical Prints</h3>
    <div class="header-container">
      <div class="nav-links">
        <a href="bestSellers.php">Best Sellers</a>
        <a href="newArrivals.php">New Arrivals</a>
        <a href="#">Genres</a>
      </div>
      <div class="header-icons">
        <form method="GET" action="search.php" class="search-bar">
          <input type="text" name="query" placeholder="Search by name or tag..." required>
          <button type="submit" id="search-button"><i class="bi bi-search"></i></button>
        </form>
        <a href="login.php"><i class="bi bi-person"></i></a>
        <i class="bi bi-cart"></i>

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

  <main class="text-center">
    <h2>Login</h2>
    <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>

    <form action="login.php" method="POST">
      <input type="email" name="email" placeholder="Email" required><br>
      <input type="password" name="password" placeholder="Password" required><br>
      <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register here</a></p>
  </main>

  <?php include 'includes/footer.php'; ?>