<?php

$currentPage =
    basename($_SERVER['PHP_SELF']);

?>

<aside class="admin-sidebar">


    <!-- LOGO -->

    <div class="admin-logo">

        <div class="admin-logo-icon">

            <i class="bi bi-flower1"></i>

        </div>

        <div>

            <h4>KhadBhandu</h4>

            <span>ADMIN PANEL</span>

        </div>

    </div>


    <!-- NAVIGATION -->

    <nav class="admin-nav">


        <div class="nav-section-title">

            MAIN

        </div>


        <a
            href="index.php"
            class="admin-nav-link
            <?= $currentPage === 'index.php' ? 'active' : '' ?>"
        >

            <i class="bi bi-grid-1x2-fill"></i>

            <span>Dashboard</span>

        </a>


        <div class="nav-section-title">

            MANAGEMENT

        </div>


        <a
            href="products.php"
            class="admin-nav-link
            <?= $currentPage === 'products.php' ? 'active' : '' ?>"
        >

            <i class="bi bi-box-seam"></i>

            <span>Products</span>

        </a>


        <a
            href="categories.php"
            class="admin-nav-link
            <?= $currentPage === 'categories.php' ? 'active' : '' ?>"
        >

            <i class="bi bi-tags"></i>

            <span>Categories</span>

        </a>


        <a
            href="orders.php"
            class="admin-nav-link
            <?= $currentPage === 'orders.php' ? 'active' : '' ?>"
        >

            <i class="bi bi-cart-check"></i>

            <span>Orders</span>

        </a>


        <a
            href="users.php"
            class="admin-nav-link
            <?= $currentPage === 'users.php' ? 'active' : '' ?>"
        >

            <i class="bi bi-people"></i>

            <span>Customers</span>

        </a>


        <a
            href="reviews.php"
            class="admin-nav-link
            <?= $currentPage === 'reviews.php' ? 'active' : '' ?>"
        >

            <i class="bi bi-star"></i>

            <span>Reviews</span>

        </a>


        <div class="nav-section-title">

            REPORTS

        </div>


        <a
            href="analytics.php"
            class="admin-nav-link
            <?= $currentPage === 'analytics.php' ? 'active' : '' ?>"
        >

            <i class="bi bi-bar-chart-line"></i>

            <span>Analytics</span>

        </a>


        <div class="nav-section-title">

            SYSTEM

        </div>


        <a
            href="settings.php"
            class="admin-nav-link
            <?= $currentPage === 'settings.php' ? 'active' : '' ?>"
        >

            <i class="bi bi-gear"></i>

            <span>Settings</span>

        </a>


    </nav>


    <!-- LOGOUT -->

    <div class="admin-sidebar-bottom">

        <a
            href="logout.php"
            class="admin-logout"
        >

            <i class="bi bi-box-arrow-left"></i>

            <span>Logout</span>

        </a>

    </div>


</aside>