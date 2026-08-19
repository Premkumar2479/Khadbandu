<?php

require_once "../config/database.php";

$name = "KhadBhandu Admin";
$email = "admin@khadbhandu.com";
$password = "Admin@123";

$hashedPassword = password_hash(
    $password,
    PASSWORD_DEFAULT
);

$stmt = $conn->prepare("
    INSERT INTO admin_users
    (name, email, password)
    VALUES (?, ?, ?)
");

$stmt->bind_param(
    "sss",
    $name,
    $email,
    $hashedPassword
);

if ($stmt->execute()) {

    echo "<h2>Admin account created successfully!</h2>";

    echo "<p>Email: <strong>$email</strong></p>";

    echo "<p>Password: <strong>$password</strong></p>";

    echo "<p>
        <a href='login.php'>
            Go to Admin Login
        </a>
    </p>";

} else {

    echo "Error: " . $stmt->error;

}