<?php
// includes/accountFunctions.php

function updateEmail($conn, $userId) {
    $newEmail = trim($_POST["newEmail"]);
    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        return "Invalid email format.";
    }

    $stmt = $conn->prepare("SELECT userId FROM user WHERE email = ?");
    $stmt->bind_param("s", $newEmail);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        return "That email is already in use.";
    }

    $stmt = $conn->prepare("UPDATE user SET email = ? WHERE userId = ?");
    $stmt->bind_param("si", $newEmail, $userId);
    if ($stmt->execute()) {
        $_SESSION["email"] = $newEmail;
        return "Email updated successfully.";
    }
    return "Error updating email.";
}

function updatePhone($conn, $userId) {
    $newPhone = trim($_POST["newPhone"]);
    if (!preg_match('/^\d{8,15}$/', $newPhone)) {
        return "Invalid phone number. Use format like 1234567890.";
    }

    $stmt = $conn->prepare("UPDATE user SET phoneNumber = ? WHERE userId = ?");
    $stmt->bind_param("si", $newPhone, $userId);
    return $stmt->execute() ? "Phone number updated successfully." : "Error updating phone number.";
}

function changePassword($conn, $userId) {
    $current = $_POST["currentPassword"];
    $new = $_POST["newPassword"];
    $confirm = $_POST["confirmPassword"];

    $stmt = $conn->prepare("SELECT password FROM user WHERE userId = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($hashed);
    $stmt->fetch();
    $stmt->close();

    if (!password_verify($current, $hashed)) return "Current password is incorrect.";
    if ($new !== $confirm) return "New passwords do not match.";

    $newHash = password_hash($new, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE user SET password = ? WHERE userId = ?");
    $stmt->bind_param("si", $newHash, $userId);
    return $stmt->execute() ? "Password updated successfully." : "Error updating password.";
}

function updateName($conn, $userId) {
    $newFirst = trim($_POST["newFirst"]);
    $newLast = trim($_POST["newLast"]);

    if (empty($newFirst) || empty($newLast)) {
        return "First and last names cannot be empty.";
    }

    $stmt = $conn->prepare("UPDATE user SET firstName = ?, lastName = ? WHERE userId = ?");
    $stmt->bind_param("ssi", $newFirst, $newLast, $userId);

    if ($stmt->execute()) {
        $_SESSION["firstName"] = $newFirst;
        $_SESSION["lastName"] = $newLast;
        return "Name updated successfully.";
    } else {
        return "Error updating name.";
    }
}

function addAddress($conn, $userId) {
    $street = trim($_POST["street"]);
    $city = trim($_POST["city"]);
    $state = trim($_POST["state"]);
    $zip = trim($_POST["zipCode"]);
    $country = trim($_POST["country"]);
    $type = $_POST["addressType"];

    $stmt = $conn->prepare("INSERT INTO address (userId, street, city, state, zipCode, country, addressType)
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $userId, $street, $city, $state, $zip, $country, $type);
    return $stmt->execute() ? "Address added successfully." : "Error adding address.";
}

function updateAddress($conn, $userId) {
    $id = $_POST["addressId"];
    $street = trim($_POST["street"]);
    $city = trim($_POST["city"]);
    $state = trim($_POST["state"]);
    $zip = trim($_POST["zipCode"]);
    $country = trim($_POST["country"]);
    $type = $_POST["addressType"];

    $stmt = $conn->prepare("UPDATE address SET street = ?, city = ?, state = ?, zipCode = ?, country = ?, addressType = ?
                            WHERE addressId = ? AND userId = ?");
    $stmt->bind_param("ssssssii", $street, $city, $state, $zip, $country, $type, $id, $userId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        return "Address updated successfully.";
    } else {
        return "No changes made or address doesn't belong to this user.";
    }
}


function deleteAddress($conn, $userId) {
    $addressId = $_POST["addressId"];
    $stmt = $conn->prepare("DELETE FROM address WHERE addressId = ? AND userId = ?");
    $stmt->bind_param("ii", $addressId, $userId);
    return $stmt->execute() ? "Address deleted successfully." : "Error deleting address.";
}
