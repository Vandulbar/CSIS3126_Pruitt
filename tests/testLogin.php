<?php
//testLogin.php
include '../includes/db.php';

// Simulate login POST
$email = "testuser@example.com";
$password = "password123"; // Expected password for this test account

// Fetch the user
$stmt = $conn->prepare("SELECT userId, firstName, password FROM user WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user["password"])) {
    echo "✅ Login test passed for user: " . $user["firstName"] . "<br>";
} else {
    echo "❌ Login test failed. Check email/password or DB connection.<br>";
}
?>