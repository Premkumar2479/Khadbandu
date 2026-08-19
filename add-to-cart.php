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

$quantity =
    max(1, (int)($_POST['quantity'] ?? 1));

if ($productId <= 0) {
    header("Location: products.php");
    exit;
}

$stmt = $conn->prepare("
    SELECT stock
    FROM products
    WHERE id = ?
");

$stmt->bind_param("i", $productId);
$stmt->execute();

$product =
    $stmt->get_result()->fetch_assoc();

if (!$product || $product['stock'] <= 0) {
    header("Location: product-details.php?id=$productId");
    exit;
}

if ($quantity > $product['stock']) {
    $quantity = $product['stock'];
}

$stmt = $conn->prepare("
    INSERT INTO cart
        (user_id, product_id, quantity)
    VALUES
        (?, ?, ?)
    ON DUPLICATE KEY UPDATE
        quantity = quantity + VALUES(quantity)
");

$stmt->bind_param(
    "iii",
    $userId,
    $productId,
    $quantity
);

$stmt->execute();

header("Location: cart.php");
exit;



