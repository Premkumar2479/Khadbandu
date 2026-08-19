<?php

session_start();

require_once "config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId =
    (int)$_SESSION['user_id'];

$productId =
    (int)($_POST['product_id'] ?? 0);

$stmt = $conn->prepare("
    INSERT IGNORE INTO wishlist
        (user_id, product_id)
    VALUES
        (?, ?)
");

$stmt->bind_param(
    "ii",
    $userId,
    $productId
);

$stmt->execute();

header(
    "Location: product-details.php?id=" .
    $productId
);

exit;