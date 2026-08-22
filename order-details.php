<?php

session_start();

require_once "config/database.php";


if (!isset($_SESSION['user_id'])) {

    header("Location: login.php");
    exit;

}


$orderId = (int)($_GET['id'] ?? 0);

$userId = $_SESSION['user_id'];


$stmt = $conn->prepare(
    "SELECT *
     FROM orders
     WHERE id = ?
     AND user_id = ?
     LIMIT 1"
);

$stmt->bind_param(
    "ii",
    $orderId,
    $userId
);

$stmt->execute();

$order = $stmt->get_result()->fetch_assoc();


if (!$order) {

    header("Location: orders.php");
    exit;

}


$itemStmt = $conn->prepare("
    SELECT
        order_items.*,
        products.name,
        products.image

    FROM order_items

    INNER JOIN products
    ON order_items.product_id = products.id

    WHERE order_items.order_id = ?
");

$itemStmt->bind_param(
    "i",
    $orderId
);

$itemStmt->execute();

$items = $itemStmt->get_result();

?>

<?php

$pageTitle = "Order #" . $orderId . " - KisanSaathi";

include "includes/header.php";
include "includes/navbar.php";

?>


<section class="products-page py-5">

    <div class="container">

        <div class="section-heading mb-4">

            <span>ORDER</span>

            <h1>
                Order #<?= $orderId ?>
            </h1>

        </div>


        <div class="row g-4">


            <div class="col-lg-8">

                <?php while ($item = $items->fetch_assoc()): ?>

                    <div class="cart-item mb-3">

                        <div class="cart-item-image">

                            <img
                                src="assets/images/products/<?= htmlspecialchars($item['image']) ?>"
                                alt="<?= htmlspecialchars($item['name']) ?>"
                                onerror="this.style.display='none';"
                            >

                        </div>


                        <div class="cart-item-info">

                            <h5>

                                <?= htmlspecialchars(
                                    $item['name']
                                ) ?>

                            </h5>

                            <p>

                                ₹<?= number_format(
                                    $item['price'],
                                    2
                                ) ?>

                                × <?= $item['quantity'] ?>

                            </p>

                        </div>


                        <div class="cart-item-total">

                            <strong>

                                ₹<?= number_format(
                                    $item['price'] * $item['quantity'],
                                    2
                                ) ?>

                            </strong>

                        </div>

                    </div>

                <?php endwhile; ?>

            </div>


            <div class="col-lg-4">

                <div class="cart-summary">

                    <h4>
                        Order Information
                    </h4>

                    <hr>

                    <p>
                        <strong>Status:</strong>
                        <?= htmlspecialchars($order['status']) ?>
                    </p>

                    <p>
                        <strong>Date:</strong>
                        <?= date(
                            'd M Y, h:i A',
                            strtotime($order['created_at'])
                        ) ?>
                    </p>

                    <p>
                        <strong>Address:</strong><br>
                        <?= nl2br(
                            htmlspecialchars(
                                $order['shipping_address']
                            )
                        ) ?>
                    </p>

                    <hr>

                    <div class="summary-total">

                        <span>
                            Total
                        </span>

                        <strong>
                            ₹<?= number_format(
                                $order['total_amount'],
                                2
                            ) ?>
                        </strong>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


<?php include "includes/footer.php"; ?>