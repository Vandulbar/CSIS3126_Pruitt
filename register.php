<?php
include 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST["First_Name"]);
    $last_name = trim($_POST["Last_Name"]);
    $email = trim($_POST["Email"]);
    $password = $_POST["Password"];
    $confirm_password = $_POST["Confirm_Password"];
    $phone_number = trim($_POST["Phone_Number"]);

    $streets = $_POST["Street"];
    $cities = $_POST["City"];
    $states = $_POST["State"];
    $zip_codes = $_POST["Zip_Code"];
    $countries = $_POST["Country"];
    $address_types = $_POST["Address_Type"];

    if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($confirm_password) || empty($phone_number)) {
        $error = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT Email FROM User WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "This email is already registered. Please use a different email.";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $stmt = $conn->prepare("INSERT INTO User (First_Name, Last_Name, Email, Password, Phone_Number) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $first_name, $last_name, $email, $hashed_password, $phone_number);

            if ($stmt->execute()) {
                $user_id = $stmt->insert_id; // Get the ID of the newly created user

                // Insert multiple addresses
                $stmt = $conn->prepare("INSERT INTO Address (User_Id, Street, City, State, Zip_Code, Country, Address_Type) VALUES (?, ?, ?, ?, ?, ?, ?)");
                for ($i = 0; $i < count($streets); $i++) {
                    $stmt->bind_param("issssss", $user_id, $streets[$i], $cities[$i], $states[$i], $zip_codes[$i], $countries[$i], $address_types[$i]);
                    $stmt->execute();
                }

                header("Location: login.php?success=registered");
                exit();
            } else {
                $error = "Error registering account!";
            }
        }
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
            <input type="text" name="query" placeholder="Search by name or tag..." required>
            <button type="submit" id="search-button"><i class="bi bi-search"></i></button>
          </form>
    <?php 
    if (isset($_SESSION["User_Id"])): ?>
        <span>Welcome, <?php echo htmlspecialchars($_SESSION["Name"]); ?>!</span>
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
          <i class="bi bi-cart"></i>

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
<body>
    <h2>Register</h2>
    <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
    <form action="register.php" method="POST">
        <input type="text" name="First_Name" placeholder="First Name" required><br>
        <input type="text" name="Last_Name" placeholder="Last Name" required><br>
        <input type="email" name="Email" placeholder="Email" required><br>
        <input type="password" name="Password" placeholder="Password" required><br>
        <input type="password" name="Confirm_Password" placeholder="Confirm Password" required><br>
        <input type="text" name="Phone_Number" placeholder="Phone Number" required><br>
        <h2>Address Information</h2>
<div id="address-container">
    <div class="address-group">
        <input type="text" name="Street[]" placeholder="Street Address" required><br>
        <input type="text" name="City[]" placeholder="City" required><br>
        <input type="text" name="State[]" placeholder="State" required><br>
        <input type="text" name="Zip_Code[]" placeholder="ZIP Code" required><br>
        <input type="text" name="Country[]" placeholder="Country" required><br>
        <select name="Address_Type[]" required>
            <option value="Shipping">Shipping</option>
            <option value="Billing">Billing</option>
            <option value="Home">Home</option>
            <option value="Work">Work</option>
        </select><br>
    </div>
</div>
<button type="button" id="add-address">Add Another Address</button><br>

        <button type="submit">Register</button>
    </form>
    <script>
document.getElementById("add-address").addEventListener("click", function() {
    let container = document.getElementById("address-container");
    let newAddress = document.querySelector(".address-group").cloneNode(true);
    
    // Clear input fields in the cloned address section
    newAddress.querySelectorAll("input").forEach(input => input.value = "");
    
    container.appendChild(newAddress);
});
</script>

</body>
</html>

<?php include 'includes/footer.php'?>