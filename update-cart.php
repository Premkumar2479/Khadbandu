<?php

session_start();

require_once "config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId =
    (int)$_SESSION['user_id'];

$cartId =
    (int)($_POST['cart_id'] ?? 0);

$quantity =
    max(1, (int)($_POST['quantity'] ?? 1));

$stmt = $conn->prepare("
    UPDATE cart c
    JOIN products p
        ON p.id = c.product_id
    SET c.quantity =
        LEAST(?, p.stock)
    WHERE c.id = ?
      AND c.user_id = ?
");

$stmt->bind_param(
    "iii",
    $quantity,
    $cartId,
    $userId
);

$stmt->execute();

header("Location: cart.php");
exit;