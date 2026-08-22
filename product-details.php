<?php

session_start();

require_once "config/database.php";


/*
|--------------------------------------------------------------------------
| GET PRODUCT ID
|--------------------------------------------------------------------------
*/

$productId = $_GET['id'] ?? 0;

if (!is_numeric($productId) || $productId <= 0) {

    header("Location: products.php");
    exit;

}


/*
|--------------------------------------------------------------------------
| GET PRODUCT
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        products.*,
        categories.name AS category_name
    FROM products
    LEFT JOIN categories
        ON products.category_id = categories.id
    WHERE products.id = ?
    LIMIT 1
");

$stmt->bind_param("i", $productId);

$stmt->execute();

$result = $stmt->get_result();

$product = $result->fetch_assoc();


/*
|--------------------------------------------------------------------------
| PRODUCT NOT FOUND
|--------------------------------------------------------------------------
*/

if (!$product) {

    header("Location: products.php");
    exit;

}


$pageTitle = $product['name'] . " - KisanSaathi";


/*
|--------------------------------------------------------------------------
| GET REVIEWS
|--------------------------------------------------------------------------
*/

$reviews = [];

$reviewStmt = $conn->prepare("
    SELECT
        r.rating,
        r.review,
        r.created_at,
        u.name
    FROM product_reviews r
    INNER JOIN users u
        ON r.user_id = u.id
    WHERE r.product_id = ?
    ORDER BY r.created_at DESC
");

if ($reviewStmt) {

    $reviewStmt->bind_param("i", $productId);

    $reviewStmt->execute();

    $reviewResult = $reviewStmt->get_result();

    while ($row = $reviewResult->fetch_assoc()) {

        $reviews[] = $row;

    }

    $reviewStmt->close();

}

?>


<?php include "includes/header.php"; ?>

<?php include "includes/navbar.php"; ?>


<!-- =========================================================
     PRODUCT DETAILS
========================================================= -->

<section class="product-details-page py-5">

    <div class="container">


        <!-- BREADCRUMB -->

        <nav aria-label="breadcrumb" class="mb-4">

            <ol class="breadcrumb">

                <li class="breadcrumb-item">

                    <a href="index.php">
                        Home
                    </a>

                </li>


                <li class="breadcrumb-item">

                    <a href="products.php">
                        Products
                    </a>

                </li>


                <li class="breadcrumb-item active">

                    <?= htmlspecialchars($product['name']) ?>

                </li>

            </ol>

        </nav>



        <!-- PRODUCT CARD -->

        <div class="product-detail-card">

            <div class="row g-0">


                <!-- =================================================
                     PRODUCT IMAGE
                ================================================== -->

                <div class="col-lg-6">

                    <div class="product-detail-image">

                        <?php if (!empty($product['image'])): ?>

                            <img
                                src="assets/images/products/<?= htmlspecialchars($product['image']) ?>"
                                alt="<?= htmlspecialchars($product['name']) ?>"
                                onerror="this.style.display='none'; this.parentElement.classList.add('no-detail-image');"
                            >

                        <?php endif; ?>


                        <div class="detail-placeholder">

                            <i class="bi bi-flower1"></i>

                        </div>

                    </div>

                </div>



                <!-- =================================================
                     PRODUCT INFORMATION
                ================================================== -->

                <div class="col-lg-6">

                    <div class="product-detail-content">


                        <!-- CATEGORY -->

                        <span class="detail-category">

                            <?= htmlspecialchars(
                                $product['category_name'] ?? 'Agriculture'
                            ) ?>

                        </span>


                        <!-- BRAND -->

                        <small class="detail-brand">

                            <?= htmlspecialchars(
                                $product['brand'] ?? 'KisanSaathi'
                            ) ?>

                        </small>


                        <!-- NAME -->

                        <h1>

                            <?= htmlspecialchars(
                                $product['name']
                            ) ?>

                        </h1>


                        <!-- DESCRIPTION -->

                        <p class="detail-description">

                            <?= nl2br(
                                htmlspecialchars(
                                    $product['description'] ?? ''
                                )
                            ) ?>

                        </p>


                        <!-- PRICE -->

                        <div class="detail-price">

                            ₹<?= number_format(
                                (float)$product['price'],
                                2
                            ) ?>

                        </div>


                        <!-- STOCK -->

                        <?php if ((int)$product['stock'] > 0): ?>

                            <div class="detail-stock available">

                                <i class="bi bi-check-circle-fill"></i>

                                <?= (int)$product['stock'] ?>
                                units available

                            </div>

                        <?php else: ?>

                            <div class="detail-stock unavailable">

                                <i class="bi bi-x-circle-fill"></i>

                                Currently out of stock

                            </div>

                        <?php endif; ?>



                        <?php if ((int)$product['stock'] > 0): ?>


                            <!-- =================================================
                                 QUANTITY
                            ================================================== -->

                            <div class="quantity-box">

                                <label>
                                    Quantity
                                </label>


                                <div class="quantity-control">


                                    <button
                                        type="button"
                                        onclick="decreaseQuantity()"
                                    >

                                        −

                                    </button>


                                    <input
                                        type="number"
                                        id="quantity"
                                        value="1"
                                        min="1"
                                        max="<?= (int)$product['stock'] ?>"
                                    >


                                    <button
                                        type="button"
                                        onclick="increaseQuantity(
                                            <?= (int)$product['stock'] ?>
                                        )"
                                    >

                                        +

                                    </button>


                                </div>

                            </div>



                            <!-- =================================================
                                 CART + WISHLIST
                            ================================================== -->

                            <div class="detail-buttons">


                                <!-- ADD TO CART -->

                                <form
                                    method="POST"
                                    action="add-to-cart.php"
                                    class="d-inline"
                                >

                                    <input
                                        type="hidden"
                                        name="product_id"
                                        value="<?= (int)$product['id'] ?>"
                                    >


                                    <input
                                        type="hidden"
                                        name="quantity"
                                        id="cartQuantity"
                                        value="1"
                                    >


                                    <button
                                        type="submit"
                                        class="btn btn-success btn-lg"
                                    >

                                        <i class="bi bi-cart-plus"></i>

                                        Add to Cart

                                    </button>

                                </form>



                                <!-- ADD TO WISHLIST -->

                                <form
                                    method="POST"
                                    action="add-to-wishlist.php"
                                    class="d-inline"
                                >

                                    <input
                                        type="hidden"
                                        name="product_id"
                                        value="<?= (int)$product['id'] ?>"
                                    >


                                    <button
                                        type="submit"
                                        class="btn btn-outline-success btn-lg"
                                        title="Add to Wishlist"
                                    >

                                        <i class="bi bi-heart"></i>

                                    </button>

                                </form>


                            </div>


                        <?php endif; ?>



                        <!-- =================================================
                             PRODUCT FEATURES
                        ================================================== -->

                        <div class="product-info-list">


                            <div>

                                <i class="bi bi-shield-check"></i>

                                Quality agricultural product

                            </div>


                            <div>

                                <i class="bi bi-truck"></i>

                                Convenient delivery

                            </div>


                            <div>

                                <i class="bi bi-headset"></i>

                                Farmer support available

                            </div>


                        </div>


                    </div>

                </div>


            </div>

        </div>

    </div>

</section>



<!-- =========================================================
     REVIEWS
========================================================= -->

<section class="reviews-section py-5">

    <div class="container">


        <div class="section-heading mb-4">

            <span>CUSTOMER FEEDBACK</span>

            <h2>
                Product Reviews
            </h2>

        </div>



        <!-- WRITE REVIEW -->

        <?php if (isset($_SESSION['user_id'])): ?>

            <div class="review-form-card mb-5">

                <h5>
                    Write a Review
                </h5>


                <form
                    method="POST"
                    action="submit-review.php"
                >


                    <input
                        type="hidden"
                        name="product_id"
                        value="<?= (int)$productId ?>"
                    >


                    <div class="mb-3">

                        <label class="form-label">
                            Rating
                        </label>


                        <select
                            name="rating"
                            class="form-select"
                            required
                        >

                            <option value="">
                                Select Rating
                            </option>

                            <option value="5">
                                ⭐⭐⭐⭐⭐ - Excellent
                            </option>

                            <option value="4">
                                ⭐⭐⭐⭐ - Very Good
                            </option>

                            <option value="3">
                                ⭐⭐⭐ - Good
                            </option>

                            <option value="2">
                                ⭐⭐ - Average
                            </option>

                            <option value="1">
                                ⭐ - Poor
                            </option>

                        </select>

                    </div>


                    <div class="mb-3">

                        <label class="form-label">
                            Your Review
                        </label>


                        <textarea
                            name="review"
                            class="form-control"
                            rows="4"
                            placeholder="Write your experience with this product..."
                            required
                        ></textarea>

                    </div>


                    <button
                        type="submit"
                        class="btn btn-success"
                    >

                        <i class="bi bi-star"></i>

                        Submit Review

                    </button>


                </form>

            </div>


        <?php else: ?>


            <div class="alert alert-info">

                Please
                <a href="login.php">
                    login
                </a>
                to write a review.

            </div>


        <?php endif; ?>



        <!-- EXISTING REVIEWS -->

        <?php if (count($reviews) > 0): ?>


            <?php foreach ($reviews as $review): ?>


                <div class="card p-4 mb-3 shadow-sm">


                    <div class="d-flex justify-content-between">

                        <strong>

                            <?= htmlspecialchars(
                                $review['name']
                            ) ?>

                        </strong>


                        <small class="text-muted">

                            <?= htmlspecialchars(
                                $review['created_at']
                            ) ?>

                        </small>

                    </div>


                    <div class="my-2">

                        <?php

                        $rating =
                            (int)$review['rating'];

                        for ($i = 1; $i <= 5; $i++):

                        ?>

                            <?php if ($i <= $rating): ?>

                                ⭐

                            <?php else: ?>

                                ☆

                            <?php endif; ?>

                        <?php endfor; ?>

                    </div>


                    <p class="mb-0">

                        <?= nl2br(
                            htmlspecialchars(
                                $review['review']
                            )
                        ) ?>

                    </p>


                </div>


            <?php endforeach; ?>


        <?php else: ?>


            <div class="text-muted">

                No reviews yet.
                Be the first to review this product!

            </div>


        <?php endif; ?>


    </div>

</section>



<!-- =========================================================
     QUANTITY JAVASCRIPT
========================================================= -->

<script>

function syncCartQuantity() {

    const quantityInput =
        document.getElementById("quantity");

    const cartQuantity =
        document.getElementById("cartQuantity");


    if (quantityInput && cartQuantity) {

        cartQuantity.value =
            quantityInput.value;

    }

}



function increaseQuantity(maxStock) {

    const input =
        document.getElementById("quantity");


    if (!input) {
        return;
    }


    let value =
        parseInt(input.value) || 1;


    if (value < maxStock) {

        value++;

    }


    input.value = value;


    syncCartQuantity();

}



function decreaseQuantity() {

    const input =
        document.getElementById("quantity");


    if (!input) {
        return;
    }


    let value =
        parseInt(input.value) || 1;


    if (value > 1) {

        value--;

    }


    input.value = value;


    syncCartQuantity();

}



document.addEventListener(
    "DOMContentLoaded",
    function () {


        const quantity =
            document.getElementById("quantity");


        if (quantity) {

            quantity.addEventListener(
                "input",
                function () {

                    let value =
                        parseInt(this.value) || 1;


                    const max =
                        parseInt(this.max);


                    if (value < 1) {

                        value = 1;

                    }


                    if (
                        max &&
                        value > max
                    ) {

                        value = max;

                    }


                    this.value = value;


                    syncCartQuantity();

                }
            );


            syncCartQuantity();

        }

    }
);

</script>



<?php include "includes/footer.php"; ?>