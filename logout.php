<?php
// logout.php
session_start();

if (!empty($_SESSION["lastPage"]) && basename($_SESSION["lastPage"]) != "login.php") {
    $redirectPage = $_SESSION["lastPage"];
    unset($_SESSION["lastPage"]);
} else {
    $redirectPage = "index.php";
}

session_destroy();

header("Location: $redirectPage");
exit();
