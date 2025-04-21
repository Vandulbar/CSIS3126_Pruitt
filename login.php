<?php
// login.php
session_start();
include 'includes/db.php';
include 'includes/User.php';

$redirectFrom = $_GET['from'] ?? 'index.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $user = User::verifyLogin($conn, $email, $password);
    if ($user) {
        $_SESSION["userId"] = $user->userId;
        $_SESSION["firstName"] = $user->firstName;

        // Redirect to last visited page if it exists
        $redirectPage = 'index.php';
        if (!empty($_POST["redirectFrom"])) {
            $basename = basename(parse_url($_POST["redirectFrom"], PHP_URL_PATH));
        if (!in_array($basename, ['login.php', 'logout.php', 'register.php'])) {
            $redirectPage = $_POST["redirectFrom"];
        }
    }

    header("Location: $redirectPage");
    exit();
    } else {
        $error = "Invalid email or password!";
    }
}

include 'includes/header.php';
?>

<main class="text-center">
<h2>Login</h2>
<?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>

<form action="login.php" method="POST">
    <input type="hidden" name="redirectFrom" value="<?= htmlspecialchars($redirectFrom) ?>">
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Login</button>
</form>

<p>Don't have an account? <a href="register.php">Register here</a></p>

<?php include 'includes/footer.php'; ?>