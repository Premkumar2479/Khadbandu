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


$pageTitle = "Admin Dashboard - KhadBhandu";


/*
|--------------------------------------------------------------------------
| TOTAL PRODUCTS
|--------------------------------------------------------------------------
*/

$productCount = 0;

$result = $conn->query(
    "SELECT COUNT(*) AS total FROM products"
);

if ($result) {

    $row = $result->fetch_assoc();

    $productCount =
        (int) $row['total'];
}


/*
|--------------------------------------------------------------------------
| TOTAL USERS
|--------------------------------------------------------------------------
*/

$userCount = 0;

$result = $conn->query(
    "SELECT COUNT(*) AS total FROM users"
);

if ($result) {

    $row = $result->fetch_assoc();

    $userCount =
        (int) $row['total'];
}


/*
|--------------------------------------------------------------------------
| TOTAL ORDERS
|--------------------------------------------------------------------------
*/

$orderCount = 0;

$result = $conn->query(
    "SELECT COUNT(*) AS total FROM orders"
);

if ($result) {

    $row = $result->fetch_assoc();

    $orderCount =
        (int) $row['total'];
}


/*
|--------------------------------------------------------------------------
| TOTAL CATEGORIES
|--------------------------------------------------------------------------
*/

$categoryCount = 0;

$result = $conn->query(
    "SELECT COUNT(*) AS total FROM categories"
);

if ($result) {

    $row = $result->fetch_assoc();

    $categoryCount =
        (int) $row['total'];
}


/*
|--------------------------------------------------------------------------
| RECENT PRODUCTS
|--------------------------------------------------------------------------
*/

$recentProducts = [];

$result = $conn->query("
    SELECT
        id,
        name,
        price,
        stock,
        image
    FROM products
    ORDER BY id DESC
    LIMIT 5
");

if ($result) {

    while ($row = $result->fetch_assoc()) {

        $recentProducts[] = $row;

    }

}


include "includes/header.php";

include "includes/sidebar.php";

?>


<div class="admin-main">


    <!-- TOPBAR -->

    <header class="admin-topbar">


        <h2 class="admin-page-title">

            Dashboard

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

                    <strong>Administrator</strong>

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


    <!-- CONTENT -->

    <section class="admin-content">


        <!-- WELCOME -->

        <div class="admin-welcome">

            <h1>

                Welcome back, Admin 👋

            </h1>


            <p>

                Here's what's happening with your KhadBhandu store today.

            </p>

        </div>


        <!-- STATISTICS -->

        <div class="stats-grid">


            <!-- PRODUCTS -->

            <div class="stat-card">

                <div class="stat-info">

                    <p>Total Products</p>

                    <h2>

                        <?= $productCount ?>

                    </h2>

                </div>


                <div class="stat-icon">

                    <i class="bi bi-box-seam"></i>

                </div>

            </div>


            <!-- ORDERS -->

            <div class="stat-card">

                <div class="stat-info">

                    <p>Total Orders</p>

                    <h2>

                        <?= $orderCount ?>

                    </h2>

                </div>


                <div class="stat-icon">

                    <i class="bi bi-cart-check"></i>

                </div>

            </div>


            <!-- CUSTOMERS -->

            <div class="stat-card">

                <div class="stat-info">

                    <p>Customers</p>

                    <h2>

                        <?= $userCount ?>

                    </h2>

                </div>


                <div class="stat-icon">

                    <i class="bi bi-people"></i>

                </div>

            </div>


            <!-- CATEGORIES -->

            <div class="stat-card">

                <div class="stat-info">

                    <p>Categories</p>

                    <h2>

                        <?= $categoryCount ?>

                    </h2>

                </div>


                <div class="stat-icon">

                    <i class="bi bi-tags"></i>

                </div>

            </div>


        </div>


        <!-- DASHBOARD GRID -->

        <div class="dashboard-grid">


            <!-- RECENT PRODUCTS -->

            <div class="admin-card">


                <div class="admin-card-header">

                    <h3>

                        Recent Products

                    </h3>


                    <a
                        href="products.php"
                        class="view-all"
                    >

                        View All

                    </a>

                </div>


                <?php if (count($recentProducts) > 0): ?>


                    <div class="table-responsive">

                        <table class="admin-table">

                            <thead>

                                <tr>

                                    <th>
                                        Product
                                    </th>

                                    <th>
                                        Price
                                    </th>

                                    <th>
                                        Stock
                                    </th>

                                    <th>
                                        Action
                                    </th>

                                </tr>

                            </thead>


                            <tbody>


                                <?php foreach (
                                    $recentProducts
                                    as $product
                                ): ?>


                                    <tr>


                                        <td>

                                            <span class="product-name">

                                                <?= htmlspecialchars(
                                                    $product['name']
                                                ) ?>

                                            </span>

                                        </td>


                                        <td>

                                            ₹<?= number_format(
                                                $product['price'],
                                                2
                                            ) ?>

                                        </td>


                                        <td>

                                            <span class="stock-badge">

                                                <?= (int) $product['stock'] ?>

                                            </span>

                                        </td>


                                        <td>

                                            <a
                                                href="edit-product.php?id=<?= (int) $product['id'] ?>"
                                                class="action-btn"
                                                title="Edit"
                                            >

                                                <i class="bi bi-pencil"></i>

                                            </a>


                                        </td>


                                    </tr>


                                <?php endforeach; ?>


                            </tbody>

                        </table>

                    </div>


                <?php else: ?>


                    <div class="text-center py-5">

                        <i
                            class="bi bi-box-seam"
                            style="
                                font-size:40px;
                                color:#98a2b3;
                            "
                        ></i>


                        <p class="mt-3 text-muted">

                            No products found.

                        </p>


                        <a
                            href="add-product.php"
                            class="btn btn-success"
                        >

                            <i class="bi bi-plus-lg"></i>

                            Add Product

                        </a>

                    </div>


                <?php endif; ?>


            </div>


            <!-- QUICK ACTIONS -->

            <div class="admin-card">


                <div class="admin-card-header">

                    <h3>

                        Quick Actions

                    </h3>

                </div>


                <div class="quick-actions">


                    <a
                        href="add-product.php"
                        class="quick-action"
                    >

                        <i class="bi bi-plus-circle"></i>

                        <span>
                            Add Product
                        </span>

                    </a>


                    <a
                        href="products.php"
                        class="quick-action"
                    >

                        <i class="bi bi-box"></i>

                        <span>
                            Manage Products
                        </span>

                    </a>


                    <a
                        href="categories.php"
                        class="quick-action"
                    >

                        <i class="bi bi-tags"></i>

                        <span>
                            Categories
                        </span>

                    </a>


                    <a
                        href="orders.php"
                        class="quick-action"
                    >

                        <i class="bi bi-cart-check"></i>

                        <span>
                            View Orders
                        </span>

                    </a>


                    <a
                        href="users.php"
                        class="quick-action"
                    >

                        <i class="bi bi-people"></i>

                        <span>
                            Customers
                        </span>

                    </a>


                    <a
                        href="analytics.php"
                        class="quick-action"
                    >

                        <i class="bi bi-bar-chart"></i>

                        <span>
                            Analytics
                        </span>

                    </a>


                </div>


            </div>


        </div>


    </section>


</div>


<?php

include "includes/footer.php";

?>