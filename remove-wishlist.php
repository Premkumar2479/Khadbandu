<?php

session_start();

require_once "config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId =
    (int)$_SESSION['user_id'];

$id =
    (int)($_GET['id'] ?? 0);

$stmt = $conn->prepare("
    DELETE FROM wishlist
    WHERE id = ?
      AND user_id = ?
");

$stmt->bind_param(
    "ii",
    $id,
    $userId
);

$stmt->execute();

header("Location: wishlist.php");
exit;