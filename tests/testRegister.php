<?php
//testRegister.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
include '../includes/db.php';

// Test user info
$firstName = "Testy";
$lastName = "McTestface";
$email = "testuser" . rand(1000, 9999) . "@example.com";
$password = "TestPassword123";
$phoneNumber = "555-1234";

// Address info
$street = "123 Test Lane";
$city = "Testville";
$state = "TS";
$zipCode = "12345";
$country = "Testland";
$addressType = "Shipping";

// 1. Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// 2. Insert user
$userStmt = $conn->prepare("INSERT INTO user (firstName, lastName, email, password, phoneNumber) VALUES (?, ?, ?, ?, ?)");
$userStmt->bind_param("sssss", $firstName, $lastName, $email, $hashedPassword, $phoneNumber);

if ($userStmt->execute()) {
    $userId = $userStmt->insert_id;
    echo "✅ User registered successfully with ID $userId and email $email<br>";

    // 3. Insert address
    $addrStmt = $conn->prepare("INSERT INTO address (userId, street, city, state, zipCode, country, addressType) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $addrStmt->bind_param("issssss", $userId, $street, $city, $state, $zipCode, $country, $addressType);

    if ($addrStmt->execute()) {
        echo "✅ Address registered successfully.<br>";
    } else {
        echo "❌ Failed to insert address: " . $addrStmt->error;
    }

} else {
    echo "❌ Failed to register user: " . $userStmt->error;
}
?>
