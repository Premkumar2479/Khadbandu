<?php

session_start();

require_once "config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$orderId =
    (int)($_GET['id'] ?? 0);

include "includes/header.php";
include "includes/navbar.php";
?>

<section class="py-5">

<div class="container text-center">

<div style="font-size:70px;">
    ✅
</div>

<h1>
    Order Placed Successfully!
</h1>

<p class="text-muted">
    Thank you for shopping with KhadBhandu.
</p>

<p>
    Order ID:
    <strong>
        #<?= $orderId ?>
    </strong>
</p>

<a
    href="my-orders.php"
    class="btn btn-success"
>
    View My Orders
</a>

<a
    href="products.php"
    class="btn btn-outline-success"
>
    Continue Shopping
</a>

</div>

</section>

<?php include "includes/footer.php"; ?>