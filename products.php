<?php

$pageTitle = "Products - KhadBhandu";

require_once "config/database.php";

?>

<?php include "includes/header.php"; ?>

<?php include "includes/navbar.php"; ?>


<section class="products-page py-5">

    <div class="container">

        <!-- PAGE HEADER -->

        <div class="section-heading text-center mb-5">

            <span>KHADBhandu STORE</span>

            <h1>Our Agricultural Products</h1>

            <p>
                Quality products for healthier crops and better harvests.
            </p>

        </div>


        <!-- SEARCH -->

        <div class="row mb-5">

            <div class="col-lg-6 mx-auto">

                <form method="GET" action="products.php">

                    <div class="input-group">

                        <input
                            type="text"
                            name="search"
                            class="form-control form-control-lg"
                            placeholder="Search fertilizers, seeds, pesticides..."
                            value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                        >

                        <button
                            class="btn btn-success px-4"
                            type="submit">

                            <i class="bi bi-search"></i>
                            Search

                        </button>

                    </div>

                </form>

            </div>

        </div>


        <!-- PRODUCTS -->

        <div class="row g-4">

            <?php

            $search = $_GET['search'] ?? '';

            if (!empty($search)) {

                $searchTerm = "%" . $search . "%";

                $stmt = $conn->prepare(
                    "SELECT products.*, categories.name AS category_name
                     FROM products
                     LEFT JOIN categories
                     ON products.category_id = categories.id
                     WHERE products.name LIKE ?
                     OR products.description LIKE ?
                     ORDER BY products.id DESC"
                );

                $stmt->bind_param(
                    "ss",
                    $searchTerm,
                    $searchTerm
                );

                $stmt->execute();

                $result = $stmt->get_result();

            } else {

                $sql = "
                    SELECT products.*, categories.name AS category_name
                    FROM products
                    LEFT JOIN categories
                    ON products.category_id = categories.id
                    ORDER BY products.id DESC
                ";

                $result = $conn->query($sql);
            }


            if ($result && $result->num_rows > 0):

                while ($product = $result->fetch_assoc()):

            ?>

                    <div class="col-md-6 col-lg-3">

                        <div class="product-card">

                            <div class="product-image">

                                <img
                                    src="assets/images/products/<?= htmlspecialchars($product['image']) ?>"
                                    alt="<?= htmlspecialchars($product['name']) ?>"
                                >

                                <span class="product-category">
                                    <?= htmlspecialchars($product['category_name']) ?>
                                </span>
                    <Div>   dfsf 
                            </div>


                            <div class="product-content">

                                <small class="product-brand">
                                    <?= htmlspecialchars($product['brand']) ?>
                                </small>

                                <h5>
                                    <?= htmlspecialchars($product['name']) ?>
                                </h5>

                                <p>
                                    <?= htmlspecialchars(
                                        substr($product['description'], 0, 80)
                                    ) ?>...
                                </p>

                                <div class="product-bottom">

                                    <strong>
                                        ₹<?= number_format($product['price'], 2) ?>
                                    </strong>

                                    <a
                                        href="product-details.php?id=<?= $product['id'] ?>"
                                        class="btn btn-success btn-sm">

                                        View

                                    </a>

                                </div>

                            </div>

                        </div>

                    </div>

            <?php

                endwhile;

            else:

            ?>

                <div class="col-12 text-center">

                    <div class="alert alert-warning">

                        No products found.

                    </div>

                </div>

            <?php endif; ?>

        </div>

    </div>
<div>
    </div>
</section>


<?php include "includes/footer.php"; ?>