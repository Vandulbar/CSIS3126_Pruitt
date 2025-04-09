<?php
// register.php
session_start();
include 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = trim($_POST["firstName"]);
    $lastName = trim($_POST["lastName"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirmPassword"];
    $phoneNumber = trim($_POST["phoneNumber"]);
    $street = $_POST["street"];
    $city = $_POST["city"];
    $state = $_POST["state"];
    $zipCode = $_POST["zipCode"];
    $country = $_POST["country"];
    $addressType = $_POST["addressType"];

    if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($confirmPassword) || empty($phoneNumber)) {
        $error = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match!";
    } else {
        $stmt = $conn->prepare("SELECT email FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "This email is already registered. Please use a different email.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO user (firstName, lastName, email, password, phoneNumber) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $firstName, $lastName, $email, $hashedPassword, $phoneNumber);

            if ($stmt->execute()) {
                $userId = $stmt->insert_id;

                $stmt = $conn->prepare("INSERT INTO address (userId, street, city, state, zipCode, country, addressType) VALUES (?, ?, ?, ?, ?, ?, ?)");
                for ($i = 0; $i < count($street); $i++) {
                    $stmt->bind_param("issssss", $userId, $street[$i], $city[$i], $state[$i], $zipCode[$i], $country[$i], $addressType[$i]);
                    $stmt->execute();
                }

                $_SESSION["userId"] = $userId;
                $_SESSION["firstName"] = $firstName;
                $_SESSION["lastName"] = $lastName;
                $_SESSION["email"] = $email;

                header("Location: index.php");
                exit();
            } else {
                $error = "Error registering account!";
            }
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<main>
  <h2>Register</h2>
  <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>

  <form action="register.php" method="POST">
    <input type="text" name="firstName" placeholder="First Name" required><br>
    <input type="text" name="lastName" placeholder="Last Name" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <input type="password" name="confirmPassword" placeholder="Confirm Password" required><br>
    <input type="text" name="phoneNumber" placeholder="Phone Number" required><br>

    <h2>Address Information</h2>
    <div id="address-container">
      <div class="address-group">
        <input type="text" name="street[]" placeholder="Street Address" required><br>
        <input type="text" name="city[]" placeholder="City" required><br>
        <input type="text" name="state[]" placeholder="State" required><br>
        <input type="text" name="zipCode[]" placeholder="ZIP Code" required><br>
        <input type="text" name="country[]" placeholder="Country" required><br>
        <select name="addressType[]" required>
          <option value="Shipping">Shipping</option>
          <option value="Billing">Billing</option>
          <option value="Home">Home</option>
          <option value="Work">Work</option>
        </select><br>
      </div>
    </div>

    <button type="button" id="add-address">Add Another Address</button><br><br>
    <button type="submit">Register</button>
  </form>
</main>

<script>
  // Optional: Basic logic to add another address block
  document.getElementById("add-address").addEventListener("click", function () {
    const container = document.getElementById("address-container");
    const clone = container.querySelector(".address-group").cloneNode(true);
    Array.from(clone.querySelectorAll("input")).forEach(input => input.value = "");
    container.appendChild(clone);
  });
</script>

<?php include 'includes/footer.php'; ?>
