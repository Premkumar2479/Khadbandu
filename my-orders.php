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
    SELECT *
    FROM khad_orders
    WHERE user_id = ?
    ORDER BY id DESC
");

$stmt->bind_param("i", $userId);
$stmt->execute();

$orders =
    $stmt->get_result();

include "includes/header.php";
include "includes/navbar.php";
?>

<section class="py-5">

<div class="container">

<h1>
    My Orders 📦
</h1>

<div class="table-responsive mt-4">

<table class="table">

<thead>

<tr>
<th>Order</th>
<th>Date</th>
<th>Total</th>
<th>Payment</th>
<th>Status</th>
</tr>

</thead>

<tbody>

<?php while ($order = $orders->fetch_assoc()): ?>

<tr>

<td>
#<?= $order['id'] ?>
</td>

<td>
<?= date(
    "d M Y",
    strtotime($order['created_at'])
) ?>
</td>

<td>
₹<?= number_format(
    $order['total_amount'],
    2
) ?>
</td>

<td>
<?= htmlspecialchars(
    $order['payment_method']
) ?>
</td>

<td>

<span class="badge bg-success">

<?= htmlspecialchars(
    $order['status']
) ?>

</span>

</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>

</div>

</section>

<?php include "includes/footer.php"; ?>