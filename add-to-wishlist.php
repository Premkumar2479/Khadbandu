<?php

session_start();

require_once "config/database.php";


/*
|--------------------------------------------------------------------------
| LOGIN CHECK
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['user_id'])) {

    header("Location: login.php");

    exit;
}


$userId = (int)$_SESSION['user_id'];

$productId = (int)($_POST['product_id'] ?? 0);


if ($productId <= 0) {

    header("Location: products.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| CHECK PRODUCT
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT id
    FROM products
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param(
    "i",
    $productId
);

$stmt->execute();

$product = $stmt->get_result()->fetch_assoc();


if (!$product) {

    header("Location: products.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| CHECK WISHLIST
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT id
    FROM wishlist
    WHERE user_id = ?
    AND product_id = ?
    LIMIT 1
");

$stmt->bind_param(
    "ii",
    $userId,
    $productId
);

$stmt->execute();

$existing = $stmt->get_result()->fetch_assoc();


/*
|--------------------------------------------------------------------------
| ADD TO WISHLIST
|--------------------------------------------------------------------------
*/

if (!$existing) {

    $insert = $conn->prepare("
        INSERT INTO wishlist
        (user_id, product_id)
        VALUES (?, ?)
    ");

    $insert->bind_param(
        "ii",
        $userId,
        $productId
    );

    $insert->execute();

}


/*
|--------------------------------------------------------------------------
| REDIRECT
|--------------------------------------------------------------------------
*/

header(
    "Location: product-details.php?id=" . $productId
);

exit;

?>