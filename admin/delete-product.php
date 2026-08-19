<?php

session_start();

require_once "../config/database.php";

if (
    !isset($_SESSION['user_id']) ||
    ($_SESSION['user_role'] ?? '') !== 'admin'
) {
    header("Location: login.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: products.php");
    exit;
}

$stmt = $conn->prepare("
    SELECT image
    FROM products
    WHERE id = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();

$product = $stmt->get_result()->fetch_assoc();

if ($product) {

    if (!empty($product['image'])) {

        $image =
            "../assets/images/products/" .
            basename($product['image']);

        if (file_exists($image)) {
            unlink($image);
        }
    }

    $delete = $conn->prepare("
        DELETE FROM products
        WHERE id = ?
    ");

    $delete->bind_param("i", $id);
    $delete->execute();
}

header("Location: products.php?deleted=1");
exit;