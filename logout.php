<?php
// logout.php
session_start();

$redirectPage = isset($_SESSION["lastPage"]) ? $_SESSION["lastPage"] : "index.php";
session_unset();

session_destroy();

header("Location: $redirectPage");
exit();