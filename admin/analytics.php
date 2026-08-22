<?php

session_start();

require_once "../config/database.php";


/*
|--------------------------------------------------------------------------
| ADMIN AUTHENTICATION
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['admin_id'])) {

    header("Location: login.php");

    exit;

}


$pageTitle = "Analytics - KisanSaathi";


/*
|--------------------------------------------------------------------------
| DEFAULT VALUES
|--------------------------------------------------------------------------
*/

$productCount = 0;
$customerCount = 0;
$orderCount = 0;
$categoryCount = 0;
$totalSales = 0;
$lowStockCount = 0;

$recentOrders = [];



/*
|--------------------------------------------------------------------------
| TOTAL PRODUCTS
|--------------------------------------------------------------------------
*/

$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM products
");

if ($result) {

    $row = $result->fetch_assoc();

    $productCount =
        (int)$row['total'];

}



/*
|--------------------------------------------------------------------------
| TOTAL CUSTOMERS
|--------------------------------------------------------------------------
|
| Your customer accounts use role = 'user'
|
*/

$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM users
    WHERE role = 'user'
");

if ($result) {

    $row = $result->fetch_assoc();

    $customerCount =
        (int)$row['total'];

}



/*
|--------------------------------------------------------------------------
| TOTAL ORDERS
|--------------------------------------------------------------------------
*/

$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM orders
");

if ($result) {

    $row = $result->fetch_assoc();

    $orderCount =
        (int)$row['total'];

}



/*
|--------------------------------------------------------------------------
| TOTAL CATEGORIES
|--------------------------------------------------------------------------
*/

$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM categories
");

if ($result) {

    $row = $result->fetch_assoc();

    $categoryCount =
        (int)$row['total'];

}



/*
|--------------------------------------------------------------------------
| TOTAL SALES
|--------------------------------------------------------------------------
*/

$result = $conn->query("
    SELECT
        COALESCE(
            SUM(total_amount),
            0
        ) AS total
    FROM orders
    WHERE status != 'Cancelled'
");

if ($result) {

    $row = $result->fetch_assoc();

    $totalSales =
        (float)$row['total'];

}



/*
|--------------------------------------------------------------------------
| LOW STOCK PRODUCTS
|--------------------------------------------------------------------------
*/

$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM products
    WHERE stock <= 5
");

if ($result) {

    $row = $result->fetch_assoc();

    $lowStockCount =
        (int)$row['total'];

}



/*
|--------------------------------------------------------------------------
| RECENT ORDERS
|--------------------------------------------------------------------------
*/

$result = $conn->query("
    SELECT
        o.id,
        o.total_amount,
        o.status,
        o.created_at,
        u.name
    FROM orders o
    INNER JOIN users u
        ON u.id = o.user_id
    ORDER BY o.id DESC
    LIMIT 10
");

if ($result) {

    while ($row = $result->fetch_assoc()) {

        $recentOrders[] = $row;

    }

}



/*
|--------------------------------------------------------------------------
| LOAD ADMIN UI
|--------------------------------------------------------------------------
*/

include "includes/header.php";

include "includes/sidebar.php";

?>


<div class="admin-main">


    <!-- =====================================================
         TOPBAR
    ====================================================== -->

    <header class="admin-topbar">

        <h2 class="admin-page-title">

            Analytics

        </h2>


        <div class="admin-topbar-right">

            <div class="admin-notification">

                <i class="bi bi-bell"></i>

            </div>


            <div class="admin-user">

                <div class="admin-avatar">

                    A

                </div>


                <div>

                    <strong>

                        <?= htmlspecialchars(
                            $_SESSION['admin_name']
                            ?? 'Administrator'
                        ) ?>

                    </strong>


                    <div
                        style="
                            font-size:11px;
                            color:#98a2b3;
                        "
                    >

                        Admin

                    </div>

                </div>

            </div>

        </div>

    </header>



    <!-- =====================================================
         CONTENT
    ====================================================== -->

    <section class="admin-content">


        <!-- PAGE HEADER -->

        <div class="admin-welcome">

            <h1>

                Store Analytics 📊

            </h1>


            <p>

                Monitor your KisanSaathi store performance.

            </p>

        </div>



        <!-- =================================================
             STATISTICS
        ================================================== -->

        <div class="stats-grid">


            <!-- PRODUCTS -->

            <div class="stat-card">

                <div class="stat-info">

                    <p>
                        Total Products
                    </p>

                    <h2>
                        <?= $productCount ?>
                    </h2>

                </div>


                <div class="stat-icon">

                    <i class="bi bi-box-seam"></i>

                </div>

            </div>



            <!-- CUSTOMERS -->

            <div class="stat-card">

                <div class="stat-info">

                    <p>
                        Customers
                    </p>

                    <h2>
                        <?= $customerCount ?>
                    </h2>

                </div>


                <div class="stat-icon">

                    <i class="bi bi-people"></i>

                </div>

            </div>



            <!-- ORDERS -->

            <div class="stat-card">

                <div class="stat-info">

                    <p>
                        Total Orders
                    </p>

                    <h2>
                        <?= $orderCount ?>
                    </h2>

                </div>


                <div class="stat-icon">

                    <i class="bi bi-cart-check"></i>

                </div>

            </div>



            <!-- CATEGORIES -->

            <div class="stat-card">

                <div class="stat-info">

                    <p>
                        Categories
                    </p>

                    <h2>
                        <?= $categoryCount ?>
                    </h2>

                </div>


                <div class="stat-icon">

                    <i class="bi bi-tags"></i>

                </div>

            </div>


        </div>



        <!-- =================================================
             SALES + LOW STOCK
        ================================================== -->

        <div class="dashboard-grid">


            <!-- TOTAL SALES -->

            <div class="admin-card">

                <div class="admin-card-header">

                    <h3>

                        Total Sales

                    </h3>

                </div>


                <div class="p-4">

                    <h1 class="text-success">

                        ₹<?= number_format(
                            $totalSales,
                            2
                        ) ?>

                    </h1>


                    <p class="text-muted mb-0">

                        Sales excluding cancelled orders.

                    </p>

                </div>

            </div>



            <!-- LOW STOCK -->

            <div class="admin-card">

                <div class="admin-card-header">

                    <h3>

                        Low Stock Products

                    </h3>

                </div>


                <div class="p-4">

                    <h1 class="text-warning">

                        <?= $lowStockCount ?>

                    </h1>


                    <p class="text-muted mb-0">

                        Products with 5 or fewer units.

                    </p>

                </div>

            </div>


        </div>



        <!-- =================================================
             RECENT ORDERS
        ================================================== -->

        <div class="admin-card mt-4">


            <div class="admin-card-header">

                <h3>

                    Recent Orders

                </h3>


                <a
                    href="orders.php"
                    class="view-all"
                >

                    View All

                </a>

            </div>



            <?php if (count($recentOrders) > 0): ?>


                <div class="table-responsive">

                    <table class="admin-table">

                        <thead>

                            <tr>

                                <th>
                                    Order ID
                                </th>

                                <th>
                                    Customer
                                </th>

                                <th>
                                    Amount
                                </th>

                                <th>
                                    Status
                                </th>

                                <th>
                                    Date
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                            <?php foreach (
                                $recentOrders
                                as $order
                            ): ?>


                                <tr>


                                    <td>

                                        #<?= (int)$order['id'] ?>

                                    </td>


                                    <td>

                                        <?= htmlspecialchars(
                                            $order['name']
                                        ) ?>

                                    </td>


                                    <td>

                                        ₹<?= number_format(
                                            (float)$order['total_amount'],
                                            2
                                        ) ?>

                                    </td>


                                    <td>

                                        <?php

                                        $status =
                                            $order['status']
                                            ?? 'Pending';

                                        ?>


                                        <span class="stock-badge">

                                            <?= htmlspecialchars(
                                                $status
                                            ) ?>

                                        </span>

                                    </td>


                                    <td>

                                        <?= htmlspecialchars(
                                            $order['created_at']
                                        ) ?>

                                    </td>


                                </tr>


                            <?php endforeach; ?>


                        </tbody>

                    </table>

                </div>


            <?php else: ?>


                <div class="text-center py-5">

                    <i
                        class="bi bi-cart-x"
                        style="
                            font-size:40px;
                            color:#98a2b3;
                        "
                    ></i>


                    <p class="mt-3 text-muted">

                        No orders found yet.

                    </p>

                </div>


            <?php endif; ?>


        </div>


    </section>


</div>


<?php

include "includes/footer.php";

?>