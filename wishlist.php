<?php

session_start();

require_once "config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId =
    (int)$_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT
        w.id,
        p.id AS product_id,
        p.name,
        p.price,
        p.image,
        p.stock
    FROM wishlist w
    JOIN products p
        ON p.id = w.product_id
    WHERE w.user_id = ?
    ORDER BY w.id DESC
");

$stmt->bind_param("i", $userId);
$stmt->execute();

$result = $stmt->get_result();

include "includes/header.php";
include "includes/navbar.php";
?>

<section class="py-5">

<div class="container">

<h1>
    My Wishlist ❤️
</h1>

<div class="row g-4 mt-3">

<?php while ($product = $result->fetch_assoc()): ?>

<div class="col-md-4 col-lg-3">

<div class="card h-100">

<?php if ($product['image']): ?>

<img
    src="assets/images/products/<?= htmlspecialchars($product['image']) ?>"
    class="card-img-top"
    style="height:220px;object-fit:cover;"
>

<?php endif; ?>

<div class="card-body">

<h5>
<?= htmlspecialchars($product['name']) ?>
</h5>

<h5 class="text-success">
₹<?= number_format($product['price'], 2) ?>
</h5>

<a
    href="product-details.php?id=<?= $product['product_id'] ?>"
    class="btn btn-outline-success"
>
    View Product
</a>

<a
    href="remove-wishlist.php?id=<?= $product['id'] ?>"
    class="btn btn-outline-danger"
>
    ❤️
</a>

</div>

</div>

</div>

<?php endwhile; ?>

</div>

</div>

</section>

<?php include "includes/footer.php"; ?>