<?php
// account.php
include 'includes/db.php';
include 'includes/header.php';
require 'includes/accountFunctions.php';

if (!isset($_SESSION["userId"])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION["userId"];
$message = "";

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"])) {
    $action = $_POST["action"];

    switch ($action) {
        case "changeEmail":
            $message = updateEmail($conn, $userId);
            break;
        case "changePhone":
            $message = updatePhone($conn, $userId);
            break;
        case "changePassword":
            $message = changePassword($conn, $userId);
            break;
        case "changeName":
            $message = updateName($conn, $userId);
            break;
        case "addAddress":
            $message = addAddress($conn, $userId);
            break;
        case "updateAddress":
            $message = updateAddress($conn, $userId);
            break;
        case "deleteAddress":
            $message = deleteAddress($conn, $userId);
            break;
        default:
            $message = "Unknown action.";
    }
}

// Get user info
$stmt = $conn->prepare("SELECT firstName, lastName, email, phoneNumber FROM user WHERE userId = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($firstName, $lastName, $email, $phone);
$stmt->fetch();
$stmt->close();

// Get addresses
$stmt = $conn->prepare("SELECT * FROM address WHERE userId = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$addresses = $stmt->get_result();
?>

<main class="text-center">
  <h2>Account Settings</h2>
  <?php if (!empty($message)) echo "<p style='color: green;'>$message</p>"; ?>

  <h3>Change Email</h3>
  <form method="POST">
    <input type="hidden" name="action" value="changeEmail">
    <p>Current Email: <strong><?php echo htmlspecialchars($email); ?></strong></p>
    <input type="email" name="newEmail" placeholder="New Email" required>
    <button type="submit">Update Email</button>
  </form>

  <h3>Change Phone Number</h3>
  <p>Current Phone Number: <strong><?php echo htmlspecialchars($phone); ?></strong></p>
  <form method="POST">
    <input type="hidden" name="action" value="changePhone">
    <input type="tel" name="newPhone" placeholder="1234567890" pattern="\d{8,15}$" required>
    <button type="submit">Update Phone</button>
  </form>

  <h3>Change Name</h3>
  <form method="POST">
    <input type="hidden" name="action" value="changeName">
    <p>Current Name: <strong><?php echo htmlspecialchars($firstName . " " . $lastName); ?></strong></p>
    <input type="text" name="newFirst" placeholder="New First Name" required><br>
    <input type="text" name="newLast" placeholder="New Last Name" required><br>
    <button type="submit">Update Name</button>
  </form>

  <h3>Change Password</h3>
  <form method="POST">
    <input type="hidden" name="action" value="changePassword">
    <input type="password" name="currentPassword" placeholder="Current Password" required><br>
    <input type="password" name="newPassword" placeholder="New Password" required><br>
    <input type="password" name="confirmPassword" placeholder="Confirm New Password" required><br>
    <button type="submit">Update Password</button>
  </form>

  <h3>Your Addresses</h3>
  <?php while ($addr = $addresses->fetch_assoc()): ?>
    <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;">
      <strong><?php echo htmlspecialchars($addr["addressType"]); ?>:</strong><br>
      <?php
        echo htmlspecialchars($addr["street"]) . ", " .
             htmlspecialchars($addr["city"]) . ", " .
             htmlspecialchars($addr["state"]) . " " .
             htmlspecialchars($addr["zipCode"]) . ", " .
             htmlspecialchars($addr["country"]);
      ?><br><br>

      <form method="POST" style="display: inline;">
        <input type="hidden" name="action" value="editAddress">
        <input type="hidden" name="addressId" value="<?php echo $addr['addressId']; ?>">
        <input type="submit" value="Edit">
      </form>

      <form method="POST" style="display: inline;">
        <input type="hidden" name="action" value="deleteAddress">
        <input type="hidden" name="addressId" value="<?php echo $addr['addressId']; ?>">
        <input type="submit" value="Delete" onclick="return confirm('Are you sure you want to delete this address?');">
      </form>
    </div>
  <?php endwhile; ?>

  <?php
  if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["action"] === "editAddress") {
      $editId = $_POST["addressId"];
      $stmt = $conn->prepare("SELECT * FROM address WHERE addressId = ? AND userId = ?");
      $stmt->bind_param("ii", $editId, $userId);
      $stmt->execute();
      $editResult = $stmt->get_result();
      if ($editResult->num_rows === 1) {
          $editAddress = $editResult->fetch_assoc();
  ?>
    <h3>Edit Address</h3>
    <form method="POST">
      <input type="hidden" name="action" value="updateAddress">
      <input type="hidden" name="addressId" value="<?php echo $editAddress["addressId"]; ?>">
      <input type="text" name="street" value="<?php echo htmlspecialchars($editAddress["street"]); ?>" required><br>
      <input type="text" name="city" value="<?php echo htmlspecialchars($editAddress["city"]); ?>" required><br>
      <input type="text" name="state" value="<?php echo htmlspecialchars($editAddress["state"]); ?>" required><br>
      <input type="text" name="zipCode" value="<?php echo htmlspecialchars($editAddress["zipCode"]); ?>" required><br>
      <input type="text" name="country" value="<?php echo htmlspecialchars($editAddress["country"]); ?>" required><br>
      <select name="addressType" required>
        <?php
        $types = ["Shipping", "Billing", "Home", "Work"];
        foreach ($types as $type) {
            $selected = ($type === $editAddress["addressType"]) ? "selected" : "";
            echo "<option value=\"$type\" $selected>$type</option>";
        }
        ?>
      </select><br>
      <button type="submit">Update Address</button>
    </form>
  <?php
      }
  }
  ?>

  <h3>Add New Address</h3>
  <form method="POST">
    <input type="hidden" name="action" value="addAddress">
    <input type="text" name="street" placeholder="Street" required><br>
    <input type="text" name="city" placeholder="City" required><br>
    <input type="text" name="state" placeholder="State" required><br>
    <input type="text" name="zipCode" placeholder="ZIP Code" required><br>
    <input type="text" name="country" placeholder="Country" required><br>
    <select name="addressType" required>
      <option value="Shipping">Shipping</option>
      <option value="Billing">Billing</option>
      <option value="Home">Home</option>
      <option value="Work">Work</option>
    </select><br>
    <button type="submit">Add Address</button>
  </form>
</main>

<?php include 'includes/footer.php'; ?>
