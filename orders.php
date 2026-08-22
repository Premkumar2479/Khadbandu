<?php

require_once "includes/admin-auth.php";

// require_once "../config/database.php";

$pageTitle = "Orders - KisanSaathi";
session_start();

require_once "config/database.php";


if (!isset($_SESSION['user_id'])) {

    header("Location: login.php");
    exit;

}


$userId = $_SESSION['user_id'];


$stmt = $conn->prepare(
    "SELECT *
     FROM orders
     WHERE user_id = ?
     ORDER BY created_at DESC"
);

$stmt->bind_param("i", $userId);

$stmt->execute();

$result = $stmt->get_result();

?>

<?php

$pageTitle = "My Orders - KisanSaathi";

include "includes/header.php";
include "includes/navbar.php";

?>


<section class="products-page py-5">

    <div class="container">

        <div class="section-heading text-center mb-5">

            <span>MY ACCOUNT</span>

            <h1>My Orders</h1>

        </div>


        <?php if ($result->num_rows > 0): ?>

            <div class="table-responsive">

                <table class="table order-table align-middle">

                    <thead>

                        <tr>

                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th></th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php while ($order = $result->fetch_assoc()): ?>

                            <tr>

                                <td>
                                    #<?= $order['id'] ?>
                                </td>

                                <td>
                                    <?= date(
                                        'd M Y',
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

                                    <span class="status-badge">

                                        <?= htmlspecialchars(
                                            $order['status']
                                        ) ?>

                                    </span>

                                </td>

                                <td>

                                    <a
                                        href="order-details.php?id=<?= $order['id'] ?>"
                                        class="btn btn-sm btn-outline-success">

                                        View

                                    </a>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                    </tbody>

                </table>

            </div>

        <?php else: ?>

            <div class="no-products">

                <i class="bi bi-box-seam"></i>

                <h4>
                    No orders yet
                </h4>

                <p>
                    Your orders will appear here.
                </p>

                <a
                    href="products.php"
                    class="btn btn-success">

                    Start Shopping

                </a>

            </div>

        <?php endif; ?>

    </div>

</section>


<?php include "includes/footer.php"; ?>