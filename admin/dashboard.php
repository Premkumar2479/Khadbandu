<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION['admin_id'])) {

    header("Location: login.php");

    exit;
}
?>


<?php

session_start();

require_once "../config/database.php";


/*
|--------------------------------------------------------------------------
| ADMIN PROTECTION
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['admin_id'])) {

    header("Location: login.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| STATISTICS
|--------------------------------------------------------------------------
*/


// Products

$productResult =
    $conn->query("
        SELECT COUNT(*) AS total
        FROM products
    ");

$totalProducts =
    $productResult
    ->fetch_assoc()['total'];


// Categories

$categoryResult =
    $conn->query("
        SELECT COUNT(*) AS total
        FROM categories
    ");

$totalCategories =
    $categoryResult
    ->fetch_assoc()['total'];


// Users

$userResult =
    $conn->query("
        SELECT COUNT(*) AS total
        FROM users
    ");

$totalUsers =
    $userResult
    ->fetch_assoc()['total'];


?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Admin Dashboard - KhadBhandu
    </title>


    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet"
    >

</head>


<body
    class="bg-light"
>


<!-- NAVBAR -->

<nav
    class="navbar navbar-dark bg-success"
>

    <div
        class="container-fluid px-4"
    >

        <a
            href="dashboard.php"
            class="navbar-brand fw-bold"
        >

            🌱 KhadBhandu Admin

        </a>


        <div
            class="d-flex align-items-center gap-3"
        >

            <span
                class="text-white"
            >

                Hello,
                <?= htmlspecialchars(
                    $_SESSION['admin_name']
                ) ?>

            </span>


            <a
                href="logout.php"
                class="btn btn-light btn-sm"
            >

                Logout

            </a>

        </div>

    </div>

</nav>


<!-- CONTENT -->

<div
    class="container py-5"
>


    <div
        class="d-flex justify-content-between align-items-center mb-4"
    >

        <div>

            <h1>
                Dashboard
            </h1>

            <p
                class="text-muted"
            >
                Manage your KhadBhandu store.
            </p>

        </div>


        <a
            href="add-product.php"
            class="btn btn-success"
        >

            <i class="bi bi-plus-lg"></i>

            Add Product

        </a>

    </div>


    <!-- STATISTICS -->

    <div
        class="row g-4 mb-5"
    >


        <div
            class="col-md-4"
        >

            <div
                class="card border-0 shadow-sm"
            >

                <div
                    class="card-body"
                >

                    <div
                        class="d-flex justify-content-between"
                    >

                        <div>

                            <p
                                class="text-muted mb-1"
                            >
                                Products
                            </p>

                            <h2>
                                <?= $totalProducts ?>
                            </h2>

                        </div>


                        <i
                            class="bi bi-box-seam text-success fs-1"
                        ></i>

                    </div>

                </div>

            </div>

        </div>


        <div
            class="col-md-4"
        >

            <div
                class="card border-0 shadow-sm"
            >

                <div
                    class="card-body"
                >

                    <div
                        class="d-flex justify-content-between"
                    >

                        <div>

                            <p
                                class="text-muted mb-1"
                            >
                                Categories
                            </p>

                            <h2>
                                <?= $totalCategories ?>
                            </h2>

                        </div>


                        <i
                            class="bi bi-grid text-success fs-1"
                        ></i>

                    </div>

                </div>

            </div>

        </div>


        <div
            class="col-md-4"
        >

            <div
                class="card border-0 shadow-sm"
            >

                <div
                    class="card-body"
                >

                    <div
                        class="d-flex justify-content-between"
                    >

                        <div>

                            <p
                                class="text-muted mb-1"
                            >
                                Users
                            </p>

                            <h2>
                                <?= $totalUsers ?>
                            </h2>

                        </div>


                        <i
                            class="bi bi-people text-success fs-1"
                        ></i>

                    </div>

                </div>

            </div>

        </div>


    </div>


    <!-- ADMIN MENU -->

    <div
        class="row g-4"
    >


        <div class="col-md-4">

            <a
                href="add-product.php"
                class="text-decoration-none"
            >

                <div
                    class="card border-0 shadow-sm h-100"
                >

                    <div
                        class="card-body p-4"
                    >

                        <i
                            class="bi bi-plus-circle text-success fs-1"
                        ></i>

                        <h4
                            class="mt-3"
                        >
                            Add Product
                        </h4>

                        <p
                            class="text-muted"
                        >
                            Add new agricultural products.
                        </p>

                    </div>

                </div>

            </a>

        </div>


        <div class="col-md-4">

            <a
                href="products.php"
                class="text-decoration-none"
            >

                <div
                    class="card border-0 shadow-sm h-100"
                >

                    <div
                        class="card-body p-4"
                    >

                        <i
                            class="bi bi-box text-success fs-1"
                        ></i>

                        <h4
                            class="mt-3"
                        >
                            Manage Products
                        </h4>

                        <p
                            class="text-muted"
                        >
                            Edit or delete products.
                        </p>

                    </div>

                </div>

            </a>

        </div>


        <div class="col-md-4">

            <a
                href="../categories.php"
                class="text-decoration-none"
            >

                <div
                    class="card border-0 shadow-sm h-100"
                >

                    <div
                        class="card-body p-4"
                    >

                        <i
                            class="bi bi-grid-3x3-gap text-success fs-1"
                        ></i>

                        <h4
                            class="mt-3"
                        >
                            Categories
                        </h4>

                        <p
                            class="text-muted"
                        >
                            View product categories.
                        </p>

                    </div>

                </div>

            </a>

        </div>


    </div>


</div>


</body>

</html>