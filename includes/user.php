<?php
// includes/user.php

class User {
    public $userId;
    public $firstName;
    public $lastName;
    public $email;

    public function __construct($data) {
        $this->userId = $data['userId'];
        $this->firstName = $data['firstName'];
        $this->lastName = $data['lastName'];
        $this->email = $data['email'];
    }

    public static function fetchByEmail($conn, $email) {
        $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function verifyLogin($conn, $email, $password) {
        $userData = self::fetchByEmail($conn, $email);
        if ($userData && password_verify($password, $userData["password"])) {
            return new self($userData);
        }
        return null;
    }
}
