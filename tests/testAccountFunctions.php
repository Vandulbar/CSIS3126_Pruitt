<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
include '../includes/db.php';
include '../includes/account_functions.php';

$userId = 5; // Change to a real test user ID

// Set up fake $_POST data
$_POST["newEmail"] = "verify" . rand(1000, 9999) . "@example.com";
$_POST["newPhone"] = "1234567890";
$_POST["currentPassword"] = "password123";  // Set this to the user's real current password
$_POST["newPassword"] = "NewSecurePass123!";
$_POST["confirmPassword"] = "NewSecurePass123!";
$_POST["newFirst"] = "UpdatedFirst";
$_POST["newLast"] = "UpdatedLast";

// Address update values
$_POST["addressId"] = 2; // Replace with real addressId tied to this user
$_POST["street"] = "456 Updated St";
$_POST["city"] = "Updatetown";
$_POST["state"] = "UP";
$_POST["zipCode"] = "54321";
$_POST["country"] = "Updatedland";
$_POST["addressType"] = "Shipping";

// 🔧 Run each update and output result
echo "<h3>🧪 Account Update Tests for User ID $userId</h3>";

echo "<p><strong>Email:</strong> " . updateEmail($conn, $userId) . "</p>";
echo "<p><strong>Phone:</strong> " . updatePhone($conn, $userId) . "</p>";
echo "<p><strong>Password:</strong> " . changePassword($conn, $userId) . "</p>";
echo "<p><strong>Name:</strong> " . updateName($conn, $userId) . "</p>";
echo "<p><strong>Address:</strong> " . updateAddress($conn, $userId) . "</p>";

// RESET test user's info back to original values
$_POST["newEmail"] = "testuser@example.com";
$_POST["newPhone"] = "5551234567";
$_POST["newFirst"] = "Testy";
$_POST["newLast"] = "McTestface";
$_POST["currentPassword"] = "NewSecurePass123!"; // whatever the test set it to
$_POST["newPassword"] = "password123";
$_POST["confirmPassword"] = "password123";

// Reset actions
echo "<hr><h3>♻️ Resetting Test User $userId</h3>";
echo "<p><strong>Email:</strong> " . updateEmail($conn, $userId) . "</p>";
echo "<p><strong>Phone:</strong> " . updatePhone($conn, $userId) . "</p>";
echo "<p><strong>Name:</strong> " . updateName($conn, $userId) . "</p>";
echo "<p><strong>Password:</strong> " . changePassword($conn, $userId) . "</p>";

// Set addressId to a real one for this user
$_POST["addressId"] = 2; // Replace with correct addressId for the test user
$_POST["street"] = "123 Test Lane";
$_POST["city"] = "Testville";
$_POST["state"] = "TS";
$_POST["zipCode"] = "12345";
$_POST["country"] = "Testland";
$_POST["addressType"] = "Shipping"; // Or "Home", depending on what it originally was

echo "<p><strong>Address:</strong> " . updateAddress($conn, $userId) . "</p>";

echo "<hr><p>♻️ Test user reset to original state.</p>";

?>
