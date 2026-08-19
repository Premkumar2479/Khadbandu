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

$rating =
    (int)($_POST['rating'] ?? 0);

$review =
    trim($_POST['review'] ?? '');

if (
    $productId <= 0 ||
    $rating < 1 ||
    $rating > 5 ||
    $review === ''
) {

    header(
        "Location: product-details.php?id=" .
        $productId
    );

    exit;
}

$stmt = $conn->prepare("
    INSERT INTO product_reviews
        (user_id, product_id, rating, review)
    VALUES
        (?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE
        rating = VALUES(rating),
        review = VALUES(review)
");

$stmt->bind_param(
    "iiis",
    $userId,
    $productId,
    $rating,
    $review
);

$stmt->execute();

header(
    "Location: product-details.php?id=" .
    $productId
);

exit;