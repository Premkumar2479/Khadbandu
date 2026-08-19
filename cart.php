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
        c.id,
        c.quantity,
        p.id AS product_id,
        p.name,
        p.price,
        p.image,
        p.stock
    FROM cart c
    JOIN products p
        ON p.id = c.product_id
    WHERE c.user_id = ?
    ORDER BY c.id DESC
");

$stmt->bind_param("i", $userId);
$stmt->execute();

$result = $stmt->get_result();

$total = 0;

include "includes/header.php";
include "includes/navbar.php";
?>

<section class="py-5">

<div class="container">

<h1 class="mb-4">
    Shopping Cart
</h1>

<?php if ($result->num_rows === 0): ?>

    <div class="text-center py-5">

        <i
            class="bi bi-cart-x"
            style="font-size:60px;"
        ></i>

        <h3 class="mt-3">
            Your cart is empty
        </h3>

        <a
            href="products.php"
            class="btn btn-success mt-3"
        >
            Shop Products
        </a>

    </div>

<?php else: ?>

<div class="table-responsive">

<table class="table align-middle">

<thead>

<tr>
    <th>Product</th>
    <th>Price</th>
    <th>Quantity</th>
    <th>Total</th>
    <th></th>
</tr>

</thead>

<tbody>

<?php while ($item = $result->fetch_assoc()): ?>

<?php

$itemTotal =
    $item['price'] * $item['quantity'];

$total += $itemTotal;

?>

<tr>

<td>

<div class="d-flex align-items-center gap-3">

<?php if (!empty($item['image'])): ?>

<img
    src="assets/images/products/<?= htmlspecialchars($item['image']) ?>"
    style="
        width:70px;
        height:70px;
        object-fit:cover;
        border-radius:8px;
    "
>

<?php endif; ?>

<strong>
    <?= htmlspecialchars($item['name']) ?>
</strong>

</div>

</td>

<td>
₹<?= number_format($item['price'], 2) ?>
</td>

<td>

<form
    method="POST"
    action="update-cart.php"
    class="d-flex"
>

<input
    type="hidden"
    name="cart_id"
    value="<?= $item['id'] ?>"
>

<input
    type="number"
    name="quantity"
    value="<?= $item['quantity'] ?>"
    min="1"
    max="<?= $item['stock'] ?>"
    class="form-control"
    style="width:80px;"
>

<button
    class="btn btn-outline-success ms-2"
>
    Update
</button>

</form>

</td>

<td>

<strong>
₹<?= number_format($itemTotal, 2) ?>
</strong>

</td>

<td>

<a
    href="remove-cart.php?id=<?= $item['id'] ?>"
    class="btn btn-sm btn-outline-danger"
>
    <i class="bi bi-trash"></i>
</a>

</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>


<div class="text-end mt-4">

<h3>

Total:
₹<?= number_format($total, 2) ?>

</h3>

<a
    href="checkout.php"
    class="btn btn-success btn-lg"
>
    Proceed to Checkout
</a>

</div>

<?php endif; ?>

</div>

</section>

<?php include "includes/footer.php"; ?>