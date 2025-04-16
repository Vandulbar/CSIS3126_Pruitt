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

include 'includes/header.php';
?>

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