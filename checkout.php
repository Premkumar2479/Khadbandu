<?php

session_start();

require_once "config/database.php";


if (!isset($_SESSION['user_id'])) {

    header("Location: login.php");
    exit;

}


if (empty($_SESSION['cart'])) {

    header("Location: cart.php");
    exit;

}


$userId = $_SESSION['user_id'];

$error = "";


/*
|--------------------------------------------------------------------------
| GET CART
|--------------------------------------------------------------------------
*/

$ids = array_keys($_SESSION['cart']);

$ids = array_map('intval', $ids);

$idList = implode(',', $ids);


$result = $conn->query(
    "SELECT *
     FROM products
     WHERE id IN ($idList)"
);


$products = [];

$total = 0;


while ($product = $result->fetch_assoc()) {

    $quantity =
        $_SESSION['cart'][$product['id']];

    if ($quantity > $product['stock']) {
        $quantity = $product['stock'];
    }

    $product['quantity'] = $quantity;

    $product['subtotal'] =
        $product['price'] * $quantity;

    $total += $product['subtotal'];

    $products[] = $product;

}


/*
|--------------------------------------------------------------------------
| PLACE ORDER
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $address = trim($_POST['address'] ?? '');


    if ($address === '') {

        $error = "Please enter your delivery address.";

    } else {

        $conn->begin_transaction();


        try {

            /*
            Check stock again
            */

            foreach ($products as $product) {

                if ($product['quantity'] <= 0) {

                    throw new Exception(
                        "Invalid product quantity."
                    );

                }

                if ($product['quantity'] > $product['stock']) {

                    throw new Exception(
                        "Insufficient stock for "
                        . $product['name']
                    );

                }

            }


            /*
            Create order
            */

            $stmt = $conn->prepare(
                "INSERT INTO orders
                (user_id, total_amount, shipping_address)
                VALUES (?, ?, ?)"
            );

            $stmt->bind_param(
                "ids",
                $userId,
                $total,
                $address
            );

            $stmt->execute();

            $orderId = $conn->insert_id;


            /*
            Order items
            */

            $itemStmt = $conn->prepare(
                "INSERT INTO order_items
                (order_id, product_id, quantity, price)
                VALUES (?, ?, ?, ?)"
            );


            $stockStmt = $conn->prepare(
                "UPDATE products
                 SET stock = stock - ?
                 WHERE id = ?"
            );


            foreach ($products as $product) {

                $itemStmt->bind_param(
                    "iiid",
                    $orderId,
                    $product['id'],
                    $product['quantity'],
                    $product['price']
                );

                $itemStmt->execute();


                $stockStmt->bind_param(
                    "ii",
                    $product['quantity'],
                    $product['id']
                );

                $stockStmt->execute();

            }


            $conn->commit();


            $_SESSION['cart'] = [];


            header(
                "Location: order-details.php?id="
                . $orderId
            );

            exit;


        } catch (Exception $e) {

            $conn->rollback();

            $error = $e->getMessage();

        }

    }

}

?>

<?php

$pageTitle = "Checkout - KhadBhandu";

include "includes/header.php";
include "includes/navbar.php";

?>


<section class="products-page py-5">

    <div class="container">

        <div class="section-heading text-center mb-5">

            <span>CHECKOUT</span>

            <h1>Complete Your Order</h1>

        </div>


        <?php if ($error): ?>

            <div class="alert alert-danger">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>


        <div class="row g-4">


            <div class="col-lg-7">

                <div class="checkout-card">

                    <h4>
                        Delivery Information
                    </h4>

                    <hr>


                    <form method="POST">

                        <label class="form-label">
                            Delivery Address *
                        </label>

                        <textarea
                            name="address"
                            class="form-control"
                            rows="6"
                            placeholder="Enter your complete delivery address..."
                            required
                        ></textarea>


                        <div class="payment-method mt-4">

                            <h6>
                                Payment Method
                            </h6>

                            <div class="payment-option">

                                <i class="bi bi-cash-coin"></i>

                                Cash on Delivery

                            </div>

                        </div>


                        <button
                            class="btn btn-success btn-lg w-100 mt-4">

                            <i class="bi bi-check-circle"></i>

                            Place Order

                        </button>

                    </form>

                </div>

            </div>


            <div class="col-lg-5">

                <div class="cart-summary">

                    <h4>
                        Order Summary
                    </h4>

                    <hr>


                    <?php foreach ($products as $product): ?>

                        <div class="summary-product">

                            <span>

                                <?= htmlspecialchars(
                                    $product['name']
                                ) ?>

                                × <?= $product['quantity'] ?>

                            </span>

                            <strong>

                                ₹<?= number_format(
                                    $product['subtotal'],
                                    2
                                ) ?>

                            </strong>

                        </div>

                    <?php endforeach; ?>


                    <hr>


                    <div class="summary-total">

                        <span>
                            Total
                        </span>

                        <strong>
                            ₹<?= number_format($total, 2) ?>
                        </strong>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


<?php include "includes/footer.php"; ?>
