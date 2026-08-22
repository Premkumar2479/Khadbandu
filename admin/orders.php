<?php


// <?php

require_once "includes/admin-auth.php";

require_once "../config/database.php";

$pageTitle = "Orders - KhadBhandu";



session_start();

require_once "../config/database.php";


if (
    !isset($_SESSION['user_id']) ||
    $_SESSION['user_role'] !== 'admin'
) {

    header("Location: ../login.php");
    exit;

}


/*
|--------------------------------------------------------------------------
| UPDATE STATUS
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $orderId = (int)($_POST['order_id'] ?? 0);

    $status = $_POST['status'] ?? 'Pending';


    $allowedStatuses = [
        'Pending',
        'Confirmed',
        'Shipped',
        'Delivered',
        'Cancelled'
    ];


    if (
        $orderId > 0 &&
        in_array($status, $allowedStatuses, true)
    ) {

        $stmt = $conn->prepare(
            "UPDATE orders
             SET status = ?
             WHERE id = ?"
        );

        $stmt->bind_param(
            "si",
            $status,
            $orderId
        );

        $stmt->execute();

    }

}


$result = $conn->query("
    SELECT
        orders.*,
        users.name,
        users.email

    FROM orders

    INNER JOIN users
    ON orders.user_id = users.id

    ORDER BY orders.created_at DESC
");

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>Orders - KhadBhandu Admin</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        href="../assets/css/style.css"
        rel="stylesheet">

</head>


<body class="admin-body">


<div class="admin-navbar">

    <div class="container-fluid">

        <a href="index.php"
           class="admin-brand">

            🌾 KhadBhandu Admin

        </a>

        <a
            href="logout.php"
            class="btn btn-outline-danger btn-sm">

            Logout

        </a>

    </div>

</div>


<div class="container py-5">

    <h2 class="mb-4">
        Orders
    </h2>


    <div class="table-responsive">

        <table class="table table-bordered bg-white">

            <thead>

                <tr>

                    <th>Order</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Update</th>

                </tr>

            </thead>


            <tbody>

                <?php while ($order = $result->fetch_assoc()): ?>

                    <tr>

                        <td>
                            #<?= $order['id'] ?>
                        </td>

                        <td>

                            <?= htmlspecialchars(
                                $order['name']
                            ) ?>

                            <br>

                            <small>

                                <?= htmlspecialchars(
                                    $order['email']
                                ) ?>

                            </small>

                        </td>

                        <td>
                            ₹<?= number_format(
                                $order['total_amount'],
                                2
                            ) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $order['status']
                            ) ?>
                        </td>

                        <td>

                            <form
                                method="POST"
                                class="d-flex gap-2">

                                <input
                                    type="hidden"
                                    name="order_id"
                                    value="<?= $order['id'] ?>"
                                >

                                <select
                                    name="status"
                                    class="form-select form-select-sm">

                                    <?php

                                    $statuses = [
                                        'Pending',
                                        'Confirmed',
                                        'Shipped',
                                        'Delivered',
                                        'Cancelled'
                                    ];

                                    foreach ($statuses as $status):

                                    ?>

                                        <option
                                            value="<?= $status ?>"
                                            <?= $order['status'] === $status
                                                ? 'selected'
                                                : '' ?>>

                                            <?= $status ?>

                                        </option>

                                    <?php endforeach; ?>

                                </select>


                                <button
                                    class="btn btn-success btn-sm">

                                    Save

                                </button>

                            </form>

                        </td>

                    </tr>

                <?php endwhile; ?>

            </tbody>

        </table>

    </div>

</div>


</body>

</html>