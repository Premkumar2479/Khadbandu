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
        c.quantity,
        p.id,
        p.name,
        p.price,
        p.stock
    FROM cart c
    JOIN products p
        ON p.id = c.product_id
    WHERE c.user_id = ?
");

$stmt->bind_param("i", $userId);
$stmt->execute();

$result = $stmt->get_result();

$items = [];
$total = 0;

while ($item = $result->fetch_assoc()) {

    if ($item['quantity'] > $item['stock']) {
        $item['quantity'] = $item['stock'];
    }

    $itemTotal =
        $item['price'] * $item['quantity'];

    $total += $itemTotal;

    $items[] = $item;
}

if (!$items) {
    header("Location: cart.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name =
        trim($_POST['name'] ?? '');

    $phone =
        trim($_POST['phone'] ?? '');

    $address =
        trim($_POST['address'] ?? '');

    $payment =
        $_POST['payment_method'] ?? 'COD';

    if (
        $name === '' ||
        $phone === '' ||
        $address === ''
    ) {

        $error =
            "Please fill all shipping details.";

    } else {

        $conn->begin_transaction();

        try {

            $order = $conn->prepare("
                INSERT INTO khad_orders
                (
                    user_id,
                    total_amount,
                    payment_method,
                    shipping_name,
                    shipping_phone,
                    shipping_address
                )
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $order->bind_param(
                "idssss",
                $userId,
                $total,
                $payment,
                $name,
                $phone,
                $address
            );

            $order->execute();

            $orderId =
                $conn->insert_id;


            foreach ($items as $item) {

                $orderItem =
                    $conn->prepare("
                        INSERT INTO khad_order_items
                        (
                            order_id,
                            product_id,
                            product_name,
                            price,
                            quantity
                        )
                        VALUES (?, ?, ?, ?, ?)
                    ");

                $orderItem->bind_param(
                    "iissi",
                    $orderId,
                    $item['id'],
                    $item['name'],
                    $item['price'],
                    $item['quantity']
                );

                $orderItem->execute();


                $stock = $conn->prepare("
                    UPDATE products
                    SET stock = stock - ?
                    WHERE id = ?
                      AND stock >= ?
                ");

                $stock->bind_param(
                    "iii",
                    $item['quantity'],
                    $item['id'],
                    $item['quantity']
                );

                $stock->execute();
            }


            $clear = $conn->prepare("
                DELETE FROM cart
                WHERE user_id = ?
            ");

            $clear->bind_param(
                "i",
                $userId
            );

            $clear->execute();


            $conn->commit();

            header(
                "Location: order-success.php?id=" .
                $orderId
            );

            exit;

        } catch (Exception $e) {

            $conn->rollback();

            $error =
                "Unable to place order.";
        }
    }
}

include "includes/header.php";
include "includes/navbar.php";
?>

<section class="py-5">

<div class="container">

<h1>
    Checkout
</h1>

<?php if ($error): ?>

<div class="alert alert-danger">
    <?= htmlspecialchars($error) ?>
</div>

<?php endif; ?>

<form method="POST">

<div class="row">

<div class="col-lg-7">

<div class="card p-4">

<h4>
    Shipping Information
</h4>

<div class="mb-3">

<label>
    Full Name
</label>

<input
    type="text"
    name="name"
    class="form-control"
    required
>

</div>

<div class="mb-3">

<label>
    Phone
</label>

<input
    type="text"
    name="phone"
    class="form-control"
    required
>

</div>

<div class="mb-3">

<label>
    Address
</label>

<textarea
    name="address"
    class="form-control"
    rows="5"
    required
></textarea>

</div>

<h5>
    Payment
</h5>

<div class="form-check">

<input
    type="radio"
    name="payment_method"
    value="COD"
    checked
    class="form-check-input"
>

<label class="form-check-label">
    Cash on Delivery
</label>

</div>

</div>

</div>


<div class="col-lg-5">

<div class="card p-4">

<h4>
    Order Summary
</h4>

<?php foreach ($items as $item): ?>

<div class="d-flex justify-content-between mb-2">

<span>
<?= htmlspecialchars($item['name']) ?>
× <?= $item['quantity'] ?>
</span>

<strong>
₹<?= number_format(
    $item['price'] * $item['quantity'],
    2
) ?>
</strong>

</div>

<?php endforeach; ?>

<hr>

<h4 class="d-flex justify-content-between">

<span>
Total
</span>

<span class="text-success">
₹<?= number_format($total, 2) ?>
</span>

</h4>

<button
    type="submit"
    class="btn btn-success w-100 btn-lg mt-3"
>
    Place Order
</button>

</div>

</div>

</div>

</form>

</div>

</section>

<?php include "includes/footer.php"; ?>