<?php
session_start();
include 'includes/header.php';
include 'includes/db.php';

if (!isset($_SESSION["User_Id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["User_Id"];
$stmt = $conn->prepare("SELECT * FROM Address WHERE User_Id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <h2>Your Addresses</h2>
    <?php while ($row = $result->fetch_assoc()): ?>
        <p>
            <strong><?php echo htmlspecialchars($row["Address_Type"]); ?> Address:</strong><br>
            <?php echo htmlspecialchars($row["Street"]) . ", " . htmlspecialchars($row["City"]) . ", " . htmlspecialchars($row["State"]) . " " . htmlspecialchars($row["Zip_Code"]) . ", " . htmlspecialchars($row["Country"]); ?>
        </p>
    <?php endwhile; ?>
    <a href="add_address.php">Add Another Address</a>
</body>
</html>

<?php include 'includes/footer.php';?>