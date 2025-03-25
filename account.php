<?php
include 'includes/db.php';
include 'includes/header.php';

if (!isset($_SESSION["User_Id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["User_Id"];
$message = "";

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"])) {
    $action = $_POST["action"];

    // Change Email
    if ($action === "change_email") {
        $new_email = trim($_POST["new_email"]);

        if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
            $message = "Invalid email format.";
        } else {
            $stmt = $conn->prepare("SELECT User_Id FROM user WHERE Email = ?");
            $stmt->bind_param("s", $new_email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $message = "That email is already in use.";
            } else {
                $stmt = $conn->prepare("UPDATE user SET Email = ? WHERE User_Id = ?");
                $stmt->bind_param("si", $new_email, $user_id);
                if ($stmt->execute()) {
                    $_SESSION["Email"] = $new_email;
                    $message = "Email updated successfully.";
                } else {
                    $message = "Error updating email.";
                }
            }
        }
    }

    elseif ($action === "change_phone") {
    $new_phone = trim($_POST["new_phone"]);

    // Validate international format (e.g., 1234567890)
    if (!preg_match('/^\d{8,15}$/', $new_phone)) {
        $message = "Invalid phone number. Use format like 1234567890.";
    } else {
        $stmt = $conn->prepare("UPDATE user SET Phone_Number = ? WHERE User_Id = ?");
        $stmt->bind_param("si", $new_phone, $user_id);
        if ($stmt->execute()) {
            $message = "Phone number updated successfully.";
        } else {
            $message = "Error updating phone number.";
        }
    }
}



    elseif ($action === "delete_address") {
    $address_id = $_POST["address_id"];
    $stmt = $conn->prepare("DELETE FROM address WHERE Address_Id = ? AND User_Id = ?");
    $stmt->bind_param("ii", $address_id, $user_id);
    if ($stmt->execute()) {
        $message = "Address deleted successfully.";
    } else {
        $message = "Error deleting address.";
        }
    }

    elseif ($action === "update_address") {
    $id = $_POST["address_id"];
    $street = trim($_POST["street"]);
    $city = trim($_POST["city"]);
    $state = trim($_POST["state"]);
    $zip = trim($_POST["zip_code"]);
    $country = trim($_POST["country"]);
    $type = $_POST["address_type"];

    $stmt = $conn->prepare("UPDATE address SET Street = ?, City = ?, State = ?, Zip_Code = ?, Country = ?, Address_Type = ?
                            WHERE Address_Id = ? AND User_Id = ?");
    $stmt->bind_param("ssssssii", $street, $city, $state, $zip, $country, $type, $id, $user_id);

    if ($stmt->execute()) {
        $message = "Address updated successfully.";
    } else {
        $message = "Error updating address.";
        }
    }


    // Change Password
    if ($action === "change_password") {
        $current = $_POST["current_password"];
        $new = $_POST["new_password"];
        $confirm = $_POST["confirm_password"];

        $stmt = $conn->prepare("SELECT Password FROM user WHERE User_Id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($hashed);
        $stmt->fetch();
        $stmt->close();

        if (!password_verify($current, $hashed)) {
            $message = "Current password is incorrect.";
        } elseif ($new !== $confirm) {
            $message = "New passwords do not match.";
        } else {
            $new_hash = password_hash($new, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE user SET Password = ? WHERE User_Id = ?");
            $stmt->bind_param("si", $new_hash, $user_id);
            if ($stmt->execute()) {
                $message = "Password updated successfully.";
            } else {
                $message = "Error updating password.";
            }
        }
    }

    // Add Address
    if ($action === "add_address") {
        $street = trim($_POST["street"]);
        $city = trim($_POST["city"]);
        $state = trim($_POST["state"]);
        $zip = trim($_POST["zip_code"]);
        $country = trim($_POST["country"]);
        $type = $_POST["address_type"];

        $stmt = $conn->prepare("INSERT INTO address (User_Id, Street, City, State, Zip_Code, Country, Address_Type)
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $user_id, $street, $city, $state, $zip, $country, $type);
        if ($stmt->execute()) {
            $message = "Address added successfully.";
        } else {
            $message = "Error adding address.";
        }
    }

    elseif ($action === "change_name") {
    $new_first = trim($_POST["new_first"]);
    $new_last = trim($_POST["new_last"]);

    if (empty($new_first) || empty($new_last)) {
        $message = "First and last names cannot be empty.";
    } else {
        $stmt = $conn->prepare("UPDATE user SET First_Name = ?, Last_Name = ? WHERE User_Id = ?");
        $stmt->bind_param("ssi", $new_first, $new_last, $user_id);
        if ($stmt->execute()) {
            $message = "Name updated successfully.";
        } else {
            $message = "Error updating name.";
        }
    }
}

}

// Get user info
$stmt = $conn->prepare("SELECT First_Name, Last_Name, Email, Phone_Number FROM user WHERE User_Id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($first_name, $last_name, $email, $phone);
$stmt->fetch();
$stmt->close();

// Get addresses
$stmt = $conn->prepare("SELECT * FROM address WHERE User_Id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$addresses = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Account Settings</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <h2>Account Settings</h2>

    <?php if (!empty($message)) echo "<p style='color: green;'>$message</p>"; ?>

    <!-- Change Email -->
    <h3>Change Email</h3>
    <form method="POST">
        <input type="hidden" name="action" value="change_email">
        <p>Current Email: <strong><?php echo htmlspecialchars($email); ?></strong></p>
        <input type="email" name="new_email" placeholder="New Email" required>
        <button type="submit">Update Email</button>
    </form>

    <!-- Change Phone Number -->
<h3>Change Phone Number</h3>
<p>Current Phone Number: <strong><?php echo htmlspecialchars($phone); ?></strong></p>
<form method="POST">
    <input type="hidden" name="action" value="change_phone">
    <input
        type="tel"
        name="new_phone"
        placeholder="1234567890"
        pattern="\d{8,15}$"
        required
    >
    <button type="submit">Update Phone</button>
</form>

<!--Change Names -->
<h3>Change Name</h3>
<form method="POST">
    <input type="hidden" name="action" value="change_name">
    <p>Current Name: <strong><?php echo htmlspecialchars($first_name . " " . $last_name); ?></strong></p>
    <input type="text" name="new_first" placeholder="New First Name" required><br>
    <input type="text" name="new_last" placeholder="New Last Name" required><br>
    <button type="submit">Update Name</button>
</form>



    <!-- Change Password -->
    <h3>Change Password</h3>
    <form method="POST">
        <input type="hidden" name="action" value="change_password">
        <input type="password" name="current_password" placeholder="Current Password" required><br>
        <input type="password" name="new_password" placeholder="New Password" required><br>
        <input type="password" name="confirm_password" placeholder="Confirm New Password" required><br>
        <button type="submit">Update Password</button>
    </form>

    <!-- Addresses -->
    <h3>Your Addresses</h3>
<?php while ($addr = $addresses->fetch_assoc()): ?>
    <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;">
        <strong><?php echo htmlspecialchars($addr["Address_Type"]); ?>:</strong><br>
        <?php echo htmlspecialchars($addr["Street"]) . ", " .
                   htmlspecialchars($addr["City"]) . ", " .
                   htmlspecialchars($addr["State"]) . " " .
                   htmlspecialchars($addr["Zip_Code"]) . ", " .
                   htmlspecialchars($addr["Country"]); ?><br><br>

        <!-- Edit Address Form -->
        <form method="POST" style="display: inline;">
            <input type="hidden" name="action" value="edit_address">
            <input type="hidden" name="address_id" value="<?php echo $addr['Address_Id']; ?>">
            <input type="submit" value="Edit">
        </form>

        <!-- Delete Address Form -->
        <form method="POST" style="display: inline;">
            <input type="hidden" name="action" value="delete_address">
            <input type="hidden" name="address_id" value="<?php echo $addr['Address_Id']; ?>">
            <input type="submit" value="Delete" onclick="return confirm('Are you sure you want to delete this address?');">
        </form>
    </div>
<?php endwhile; ?>
<?php
// If the Edit form was submitted, fetch the address to populate the edit form
if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["action"] === "edit_address") {
    $edit_id = $_POST["address_id"];
    $stmt = $conn->prepare("SELECT * FROM address WHERE Address_Id = ? AND User_Id = ?");
    $stmt->bind_param("ii", $edit_id, $user_id);
    $stmt->execute();
    $edit_result = $stmt->get_result();
    if ($edit_result->num_rows === 1) {
        $edit_address = $edit_result->fetch_assoc();
?>
    <h3>Edit Address</h3>
    <form method="POST">
        <input type="hidden" name="action" value="update_address">
        <input type="hidden" name="address_id" value="<?php echo $edit_address["Address_Id"]; ?>">
        <input type="text" name="street" value="<?php echo htmlspecialchars($edit_address["Street"]); ?>" required><br>
        <input type="text" name="city" value="<?php echo htmlspecialchars($edit_address["City"]); ?>" required><br>
        <input type="text" name="state" value="<?php echo htmlspecialchars($edit_address["State"]); ?>" required><br>
        <input type="text" name="zip_code" value="<?php echo htmlspecialchars($edit_address["Zip_Code"]); ?>" required><br>
        <input type="text" name="country" value="<?php echo htmlspecialchars($edit_address["Country"]); ?>" required><br>
        <select name="address_type" required>
            <?php
            $types = ["Shipping", "Billing", "Home", "Work"];
            foreach ($types as $type) {
                $selected = ($type === $edit_address["Address_Type"]) ? "selected" : "";
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
        <input type="hidden" name="action" value="add_address">
        <input type="text" name="street" placeholder="Street" required><br>
        <input type="text" name="city" placeholder="City" required><br>
        <input type="text" name="state" placeholder="State" required><br>
        <input type="text" name="zip_code" placeholder="ZIP Code" required><br>
        <input type="text" name="country" placeholder="Country" required><br>
        <select name="address_type" required>
            <option value="Shipping">Shipping</option>
            <option value="Billing">Billing</option>
            <option value="Home">Home</option>
            <option value="Work">Work</option>
        </select><br>
        <button type="submit">Add Address</button>
    </form>

<?php include 'includes/footer.php' ?>