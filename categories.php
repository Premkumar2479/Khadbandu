<?php

session_start();

require_once "config/database.php";

$pageTitle = "Categories - KisanSaathi";


/*
|--------------------------------------------------------------------------
| GET CATEGORY ID
|--------------------------------------------------------------------------
*/

$categoryId = $_GET['id'] ?? 0;

$categoryId = (int)$categoryId;


/*
|--------------------------------------------------------------------------
| IF CATEGORY SELECTED
|--------------------------------------------------------------------------
*/

$category = null;
$products = null;

if ($categoryId > 0) {


    /*
    |--------------------------------------------------------------------------
    | GET CATEGORY
    |--------------------------------------------------------------------------
    */

    $categoryStmt = $conn->prepare("
        SELECT id, name
        FROM categories
        WHERE id = ?
        LIMIT 1
    ");

    $categoryStmt->bind_param(
        "i",
        $categoryId
    );

    $categoryStmt->execute();

    $category =
        $categoryStmt
        ->get_result()
        ->fetch_assoc();


    /*
    |--------------------------------------------------------------------------
    | CATEGORY NOT FOUND
    |--------------------------------------------------------------------------
    */

    if (!$category) {

        header("Location: categories.php");

        exit;
    }


    $pageTitle =
        $category['name'] . " - KisanSaathi";


    /*
    |--------------------------------------------------------------------------
    | GET PRODUCTS
    |--------------------------------------------------------------------------
    */

    $productStmt = $conn->prepare("
        SELECT *
        FROM products
        WHERE category_id = ?
        ORDER BY id DESC
    ");

    $productStmt->bind_param(
        "i",
        $categoryId
    );

    $productStmt->execute();

    $products =
        $productStmt
        ->get_result();

}

?>

<?php include "includes/header.php"; ?>

<?php include "includes/navbar.php"; ?>


<!-- =========================================================
     CATEGORY PAGE
========================================================= -->

<section class="categories-page py-5">

    <div class="container">


        <?php if ($category): ?>


            <!-- =================================================
                 CATEGORY HEADER
            ================================================== -->

            <div class="category-page-header text-center mb-5">

                <span class="section-label">
                    KISANSAATHI
                </span>

                <h1>
                    <?= htmlspecialchars($category['name']) ?>
                </h1>

                <p>
                    Explore all products in this category.
                </p>

            </div>


            <!-- =================================================
                 PRODUCTS
            ================================================== -->

            <div class="row g-4">


                <?php if ($products && $products->num_rows > 0): ?>


                    <?php while ($product = $products->fetch_assoc()): ?>


                        <div class="col-lg-3 col-md-4 col-sm-6">


                            <div class="product-card h-100">


                                <!-- IMAGE -->

                                <a
                                    href="product-details.php?id=<?= $product['id'] ?>"
                                    class="product-image-link"
                                >

                                    <div class="product-image">

                                        <?php if (!empty($product['image'])): ?>

                                            <img
                                                src="assets/images/products/<?= htmlspecialchars($product['image']) ?>"
                                                alt="<?= htmlspecialchars($product['name']) ?>"
                                                onerror="this.style.display='none';"
                                            >

                                        <?php else: ?>

                                            <div class="product-placeholder">

                                                <i class="bi bi-flower1"></i>

                                            </div>

                                        <?php endif; ?>

                                    </div>

                                </a>


                                <!-- CONTENT -->

                                <div class="product-card-content">


                                    <h3>

                                        <?= htmlspecialchars(
                                            $product['name']
                                        ) ?>

                                    </h3>


                                    <?php if (!empty($product['brand'])): ?>

                                        <small>

                                            <?= htmlspecialchars(
                                                $product['brand']
                                            ) ?>

                                        </small>

                                    <?php endif; ?>


                                    <div class="product-price">

                                        ₹<?= number_format(
                                            $product['price'],
                                            2
                                        ) ?>

                                    </div>


                                    <?php if ($product['stock'] > 0): ?>

                                        <span class="stock-available">

                                            <i class="bi bi-check-circle"></i>

                                            In Stock

                                        </span>

                                    <?php else: ?>

                                        <span class="stock-unavailable">

                                            Out of Stock

                                        </span>

                                    <?php endif; ?>


                                    <a
                                        href="product-details.php?id=<?= $product['id'] ?>"
                                        class="btn btn-success w-100 mt-3"
                                    >

                                        View Product

                                    </a>


                                </div>


                            </div>


                        </div>


                    <?php endwhile; ?>


                <?php else: ?>


                    <!-- NO PRODUCTS -->

                    <div class="col-12">

                        <div class="text-center py-5">

                            <i
                                class="bi bi-box-seam"
                                style="font-size: 60px;"
                            ></i>

                            <h3 class="mt-3">

                                No Products Found

                            </h3>

                            <p>

                                There are currently no products
                                in this category.

                            </p>

                            <a
                                href="products.php"
                                class="btn btn-success"
                            >

                                View All Products

                            </a>

                        </div>

                    </div>


                <?php endif; ?>


            </div>


            <!-- BACK -->

            <div class="text-center mt-5">

                <a
                    href="categories.php"
                    class="btn btn-outline-success"
                >

                    ← All Categories

                </a>

            </div>


        <?php else: ?>


            <!-- =================================================
                 ALL CATEGORIES
            ================================================== -->

            <div class="text-center mb-5">

                <span class="section-label">
                    EXPLORE
                </span>

                <h1>
                    All Categories
                </h1>

                <p>
                    Choose a category to explore agricultural products.
                </p>

            </div>


            <div class="row g-4">


                <?php

                $categories = $conn->query("
                    SELECT *
                    FROM categories
                    ORDER BY id ASC
                ");

                ?>


                <?php if ($categories && $categories->num_rows > 0): ?>


                    <?php while ($cat = $categories->fetch_assoc()): ?>


                        <div class="col-lg-4 col-md-6">


                            <a
                                href="categories.php?id=<?= $cat['id'] ?>"
                                class="category-card"
                            >

                                <div class="category-content py-5">

                                    <span class="category-icon">
                                        🌱
                                    </span>

                                    <h3>

                                        <?= htmlspecialchars(
                                            $cat['name']
                                        ) ?>

                                    </h3>

                                    <p>
                                        Explore Products →
                                    </p>

                                </div>

                            </a>


                        </div>


                    <?php endwhile; ?>


                <?php endif; ?>


            </div>


        <?php endif; ?>


    </div>

</section>


<?php include "includes/footer.php"; ?>