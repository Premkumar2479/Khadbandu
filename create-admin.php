<?php

require_once "config/database.php";


$name = "KhadBhandu Admin";
$email = "admin@khadbhandu.com";
$password = "Admin@12345";


$hashedPassword = password_hash(
    $password,
    PASSWORD_DEFAULT
);


$stmt = $conn->prepare("
    INSERT INTO users
    (name, email, password, role)
    VALUES (?, ?, ?, 'admin')
");


$stmt->bind_param(
    "sss",
    $name,
    $email,
    $hashedPassword
);


if ($stmt->execute()) {

    echo "Admin account created successfully.";

} else {

    echo "Error: " . $conn->error;

}

?>
