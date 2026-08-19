<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user_id']);
$userName = $_SESSION['user_name'] ?? '';

?>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">

    <div class="container">

        <a class="navbar-brand fw-bold" href="index.php">

            <span class="brand-icon">
                <i class="bi bi-flower1"></i>
            </span>

            Khad<span>Bhandu</span>

        </a>


        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#mainNavbar">

            <span class="navbar-toggler-icon"></span>

        </button>


        <div class="collapse navbar-collapse" id="mainNavbar">

            <ul class="navbar-nav mx-auto">

                <li class="nav-item">
                    <a class="nav-link" href="index.php">
                        Home
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="products.php">
                        Products
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="categories.php">
                        Categories
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="blog.php">
                        Farming Blog
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="contact.php">
                        Contact
                    </a>
                </li>

            </ul>


            <div class="d-flex align-items-center gap-3">

                <?php if ($isLoggedIn): ?>

                    <a href="wishlist.php" class="header-icon">
                        <i class="bi bi-heart"></i>
                    </a>

                <?php endif; ?>


                <a href="cart.php" class="header-icon">
                    <i class="bi bi-cart3"></i>

                    <?php

                    $cartCount = 0;

                    if (!empty($_SESSION['cart'])) {

                        foreach ($_SESSION['cart'] as $qty) {
                            $cartCount += $qty;
                        }

                    }

                    ?>

                    <span class="cart-badge">
                        <?= $cartCount ?>
                    </span>

                </a>


                <?php if ($isLoggedIn): ?>

                    <div class="dropdown">

                        <button
                            class="btn btn-outline-success dropdown-toggle"
                            data-bs-toggle="dropdown">

                            <i class="bi bi-person-circle"></i>

                            <?= htmlspecialchars($userName) ?>

                        </button>

                        <ul class="dropdown-menu dropdown-menu-end">

                            <li>
                                <a class="dropdown-item" href="orders.php">
                                    <i class="bi bi-box-seam"></i>
                                    My Orders
                                </a>
                            </li>

                        
                            <li>
                                <a class="dropdown-item" href="wishlist.php">
                                    <i class="bi bi-heart"></i>
                                    Wishlist
                                </a>
                            </li>

                            <?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?>

                                <li>
                                    <hr class="dropdown-divider">
                                </li>

                                <li>
                                    <a class="dropdown-item" href="admin/index.php">
                                        <i class="bi bi-speedometer2"></i>
                                        Admin Dashboard
                                    </a>
                                </li>

                            <?php endif; ?>

                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <li>
                                <a class="dropdown-item text-danger" href="logout.php">
                                    <i class="bi bi-box-arrow-right"></i>
                                    Logout
                                </a>
                            </li>

                        </ul>

                    </div>

                <?php else: ?>

                    <a href="login.php" class="btn btn-outline-success">

                        <i class="bi bi-person"></i>
                        Login

                    </a>

                <?php endif; ?>

            </div>

        </div>

    </div>

</nav>