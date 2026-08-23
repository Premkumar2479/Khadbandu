<?php

session_start();

require_once "../config/database.php";

/*
|--------------------------------------------------------------------------
| ADMIN LOGIN CHECK
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$pageTitle = "Manage Products - KisanSaathi";

/*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
*/

$search = trim($_GET['search'] ?? '');

/*
|--------------------------------------------------------------------------
| GET PRODUCTS
|--------------------------------------------------------------------------
*/

if ($search !== '') {

    $searchTerm = "%" . $search . "%";

    $stmt = $conn->prepare("
        SELECT
            products.*,
            categories.name AS category_name
        FROM products
        LEFT JOIN categories
            ON products.category_id = categories.id
        WHERE
            products.name LIKE ?
            OR products.brand LIKE ?
            OR products.description LIKE ?
            OR categories.name LIKE ?
        ORDER BY products.id DESC
    ");

    $stmt->bind_param(
        "ssss",
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm
    );

    $stmt->execute();

    $result = $stmt->get_result();

} else {

    $result = $conn->query("
        SELECT
            products.*,
            categories.name AS category_name
        FROM products
        LEFT JOIN categories
            ON products.category_id = categories.id
        ORDER BY products.id DESC
    ");
}

/*
|--------------------------------------------------------------------------
| PRODUCT COUNT
|--------------------------------------------------------------------------
*/

$countResult = $conn->query("
    SELECT COUNT(*) AS total
    FROM products
");

$productCount = 0;

if ($countResult) {
    $productCount = $countResult->fetch_assoc()['total'];
}

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
        <?= htmlspecialchars($pageTitle) ?>
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet"
    >

    <style>

        body {
            background: #f5f7f6;
        }

        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: #198754;
            position: fixed;
            left: 0;
            top: 0;
            padding: 25px 15px;
        }

        .sidebar-brand {
            color: white;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 5px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: rgba(255,255,255,0.18);
        }

        .main-content {
            margin-left: 250px;
            padding: 30px;
        }

        .top-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.07);
        }

        .product-image {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #ddd;
        }

        .image-placeholder {
            width: 70px;
            height: 70px;
            border-radius: 10px;
            background: #eee;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #777;
            font-size: 25px;
        }

        .table td {
            vertical-align: middle;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        @media (max-width: 768px) {

            .sidebar {
                width: 100%;
                min-height: auto;
                position: relative;
            }

            .main-content {
                margin-left: 0;
                padding: 15px;
            }

        }

    </style>

</head>

<body>


<!-- =========================================================
     SIDEBAR
========================================================= -->

<div class="sidebar">

    <div class="sidebar-brand">

        🌱 KisanSaathi

        <small
            class="d-block"
            style="font-size:12px;"
        >
            Admin Panel
        </small>

    </div>


    <a href="index.php">

        <i class="bi bi-speedometer2 me-2"></i>

        Dashboard

    </a>


    <a
        href="products.php"
        class="active"
    >

        <i class="bi bi-box-seam me-2"></i>

        Products

    </a>


    <a href="orders.php">

        <i class="bi bi-cart-check me-2"></i>

        Orders

    </a>


    <a href="customers.php">

        <i class="bi bi-people me-2"></i>

        Customers

    </a>


    <a href="reviews.php">

        <i class="bi bi-star me-2"></i>

        Reviews

    </a>


    <a href="analytics.php">

        <i class="bi bi-bar-chart-line me-2"></i>

        Analytics

    </a>


    <a href="settings.php">

        <i class="bi bi-gear me-2"></i>

        Settings

    </a>


    <hr class="border-light">


    <a
        href="../index.php"
        target="_blank"
    >

        <i class="bi bi-globe me-2"></i>

        Visit Website

    </a>


    <a href="logout.php">

        <i class="bi bi-box-arrow-right me-2"></i>

        Logout

    </a>

</div>



<!-- =========================================================
     MAIN CONTENT
========================================================= -->

<div class="main-content">


    <!-- HEADER -->

    <div
        class="d-flex justify-content-between align-items-center mb-4"
    >

        <div>

            <h2 class="fw-bold mb-1">

                Products

            </h2>

            <p class="text-muted mb-0">

                Manage your agricultural products

            </p>

        </div>


        <div>

            <span class="me-3">

                <i class="bi bi-person-circle"></i>

                <?= htmlspecialchars(
                    $_SESSION['admin_name'] ?? 'Admin'
                ) ?>

            </span>


            <a
                href="add-product.php"
                class="btn btn-success"
            >

                <i class="bi bi-plus-circle"></i>

                Add Product

            </a>

        </div>

    </div>



    <!-- =====================================================
         STAT
    ====================================================== -->

    <div class="row mb-4">

        <div class="col-md-4">

            <div class="card top-card">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <small class="text-muted">

                                Total Products

                            </small>

                            <h3 class="fw-bold mb-0">

                                <?= (int)$productCount ?>

                            </h3>

                        </div>


                        <div
                            class="text-success"
                            style="font-size:35px;"
                        >

                            <i class="bi bi-box-seam"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>



    <!-- =====================================================
         SEARCH
    ====================================================== -->

    <div class="card top-card mb-4">

        <div class="card-body">

            <form
                method="GET"
                action="products.php"
            >

                <div class="row g-2">

                    <div class="col-md-10">

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Search product, brand or category..."
                            value="<?= htmlspecialchars($search) ?>"
                        >

                    </div>


                    <div class="col-md-2">

                        <button
                            type="submit"
                            class="btn btn-success w-100"
                        >

                            <i class="bi bi-search"></i>

                            Search

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>



    <!-- =====================================================
         PRODUCTS TABLE
    ====================================================== -->

    <div class="card top-card">

        <div class="card-body">

            <div
                class="d-flex justify-content-between align-items-center mb-3"
            >

                <h5 class="fw-bold mb-0">

                    All Products

                </h5>


                <?php if ($search !== ''): ?>

                    <a
                        href="products.php"
                        class="btn btn-sm btn-outline-secondary"
                    >

                        Clear Search

                    </a>

                <?php endif; ?>

            </div>


            <div class="table-responsive">

                <table class="table table-hover">

                    <thead>

                        <tr>

                            <th>ID</th>

                            <th>Image</th>

                            <th>Product</th>

                            <th>Brand</th>

                            <th>Category</th>

                            <th>Price</th>

                            <th>Stock</th>

                            <th>Actions</th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php if (
                        $result &&
                        $result->num_rows > 0
                    ): ?>


                        <?php while (
                            $product =
                            $result->fetch_assoc()
                        ): ?>


                            <tr>


                                <!-- ID -->

                                <td>

                                    <?= (int)$product['id'] ?>

                                </td>



                                <!-- IMAGE -->

                                <td>

                                    <?php

                                    $image =
                                        trim(
                                            $product['image'] ?? ''
                                        );

                                    $imagePath =
                                        "../assets/images/products/" .
                                        $image;

                                    ?>


                                    <?php if (
                                        $image !== '' &&
                                        file_exists($imagePath)
                                    ): ?>

                                        <img
                                            src="<?= htmlspecialchars($imagePath) ?>"
                                            alt="<?= htmlspecialchars($product['name']) ?>"
                                            class="product-image"
                                        >

                                    <?php else: ?>

                                        <div class="image-placeholder">

                                            <i class="bi bi-image"></i>

                                        </div>

                                    <?php endif; ?>

                                </td>



                                <!-- NAME -->

                                <td>

                                    <strong>

                                        <?= htmlspecialchars(
                                            $product['name']
                                        ) ?>

                                    </strong>

                                    <br>

                                    <small class="text-muted">

                                        <?= htmlspecialchars(
                                            mb_substr(
                                                strip_tags(
                                                    $product['description'] ?? ''
                                                ),
                                                0,
                                                60
                                            )
                                        ) ?>

                                    </small>

                                </td>



                                <!-- BRAND -->

                                <td>

                                    <?= htmlspecialchars(
                                        $product['brand']
                                        ?? 'KisanSaathi'
                                    ) ?>

                                </td>



                                <!-- CATEGORY -->

                                <td>

                                    <span
                                        class="badge bg-success-subtle text-success"
                                    >

                                        <?= htmlspecialchars(
                                            $product['category_name']
                                            ?? 'Agriculture'
                                        ) ?>

                                    </span>

                                </td>



                                <!-- PRICE -->

                                <td>

                                    <strong>

                                        ₹<?= number_format(
                                            (float)$product['price'],
                                            2
                                        ) ?>

                                    </strong>

                                </td>



                                <!-- STOCK -->

                                <td>

                                    <?php if (
                                        (int)$product['stock'] > 0
                                    ): ?>

                                        <span
                                            class="badge bg-success"
                                        >

                                            <?= (int)$product['stock'] ?>

                                        </span>

                                    <?php else: ?>

                                        <span
                                            class="badge bg-danger"
                                        >

                                            Out of Stock

                                        </span>

                                    <?php endif; ?>

                                </td>



                                <!-- ACTIONS -->

                                <td>

                                    <div class="action-buttons">


                                        <!-- EDIT -->

                                        <a
                                            href="edit-product.php?id=<?= (int)$product['id'] ?>"
                                            class="btn btn-sm btn-outline-success"
                                        >

                                            <i class="bi bi-pencil"></i>

                                            Edit

                                        </a>



                                        <!-- VIEW -->

                                        <a
                                            href="../product-details.php?id=<?= (int)$product['id'] ?>"
                                            class="btn btn-sm btn-outline-primary"
                                            target="_blank"
                                        >

                                            <i class="bi bi-eye"></i>

                                            View

                                        </a>



                                        <!-- DELETE -->

                                        <a
                                            href="delete-product.php?id=<?= (int)$product['id'] ?>"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Are you sure you want to delete this product?');"
                                        >

                                            <i class="bi bi-trash"></i>

                                            Delete

                                        </a>

                                    </div>

                                </td>


                            </tr>


                        <?php endwhile; ?>


                    <?php else: ?>


                        <tr>

                            <td
                                colspan="8"
                                class="text-center py-5"
                            >

                                <i
                                    class="bi bi-box-seam text-muted"
                                    style="font-size:50px;"
                                ></i>

                                <h5 class="mt-3">

                                    No Products Found

                                </h5>


                                <?php if ($search !== ''): ?>

                                    <p class="text-muted">

                                        No product found for:

                                        <strong>

                                            <?= htmlspecialchars($search) ?>

                                        </strong>

                                    </p>

                                <?php else: ?>

                                    <p class="text-muted">

                                        No products have been added yet.

                                    </p>

                                <?php endif; ?>


                                <a
                                    href="add-product.php"
                                    class="btn btn-success"
                                >

                                    <i class="bi bi-plus-circle"></i>

                                    Add Product

                                </a>

                            </td>

                        </tr>


                    <?php endif; ?>


                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>



<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>