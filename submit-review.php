<?php

session_start();

require_once "config/database.php";


if (!isset($_SESSION['user_id'])) {

    header("Location: login.php");
    exit;

}


$userId = $_SESSION['user_id'];

$productId = (int)($_POST['product_id'] ?? 0);

$rating = (int)($_POST['rating'] ?? 0);

$comment = trim($_POST['comment'] ?? '');


if (
    $productId <= 0 ||
    $rating < 1 ||
    $rating > 5 ||
    $comment === ''
) {

    header(
        "Location: product-details.php?id=" . $productId
    );

    exit;
}


$stmt = $conn->prepare(
    "INSERT INTO reviews
    (user_id, product_id, rating, comment)
    VALUES (?, ?, ?, ?)"
);

$stmt->bind_param(
    "iiis",
    $userId,
    $productId,
    $rating,
    $comment
);

$stmt->execute();


header(
    "Location: product-details.php?id=" . $productId
);

exit;