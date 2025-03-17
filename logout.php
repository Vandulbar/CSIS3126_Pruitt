<?php
session_start();
session_destroy(); // Destroy the session

// Check if a redirect URL was passed
        if (!empty($_SESSION["last_page"]) && basename($_SESSION["last_page"]) != "login.php") {
            $redirect_page = $_SESSION["last_page"];
            unset($_SESSION["last_page"]); // Clear the session variable
        } else {
            $redirect_page = "index.php"; // Default to home page
        }

        header("Location: " . $redirect_page);
        exit();
