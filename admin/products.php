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


/*
|--------------------------------------------------------------------------
| PAGE TITLE
|--------------------------------------------------------------------------
*/

$pageTitle = "Manage Products - KhadBhandu";


/*
|--------------------------------------------------------------------------
| DELETE PRODUCT
|--------------------------------------------------------------------------
*/

$deleteMessage = "";
$deleteError = "";


if ($_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST['delete_product'])) {


    $productId = (int) ($_POST['product_id'] ?? 0);


    if ($productId > 0) {


        /*
        | Get image before deleting product
        */

        $imageStmt = $conn->prepare(
            "SELECT image
             FROM products
             WHERE id = ?
             LIMIT 1"
        );

        $imageStmt->bind_param(
            "i",
            $productId
        );

        $imageStmt->execute();

        $imageResult =
            $imageStmt->get_result();

        $productImage =
            $imageResult->fetch_assoc();


        /*
        | Delete product
        */

        $deleteStmt = $conn->prepare(
            "DELETE FROM products
             WHERE id = ?"
        );

        $deleteStmt->bind_param(
            "i",
            $productId
        );


        if ($deleteStmt->execute()) {


            /*
            | Delete image from server
            */

            if (
                $productImage &&
                !empty($productImage['image'])
            ) {

                $imagePath =
                    "../assets/images/products/"
                    . basename($productImage['image']);


                if (
                    file_exists($imagePath)
                    && is_file($imagePath)
                ) {

                    unlink($imagePath);

                }

            }


            $deleteMessage =
                "Product deleted successfully.";


        } else {

            $deleteError =
                "Unable to delete product.";

        }

    }

}


/*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
*/

$search =
    trim($_GET['search'] ?? '');


/*
|--------------------------------------------------------------------------
| CATEGORY FILTER
|--------------------------------------------------------------------------
*/

$categoryId =
    (int) ($_GET['category'] ?? 0);


/*
|--------------------------------------------------------------------------
| PAGINATION
|--------------------------------------------------------------------------
*/

$limit = 10;

$page =
    max(1, (int) ($_GET['page'] ?? 1));

$offset =
    ($page - 1) * $limit;


/*
|--------------------------------------------------------------------------
| CATEGORY LIST
|--------------------------------------------------------------------------
*/

$categories = [];

$categoryResult = $conn->query(
    "SELECT id, name
     FROM categories
     ORDER BY name ASC"
);


if ($categoryResult) {

    while (
        $category =
        $categoryResult->fetch_assoc()
    ) {

        $categories[] = $category;

    }

}


/*
|--------------------------------------------------------------------------
| BUILD WHERE CONDITION
|--------------------------------------------------------------------------
*/

$where = [];

$params = [];

$types = "";


if ($search !== "") {

    $where[] =
        "(p.name LIKE ?
          OR p.brand LIKE ?)";

    $searchValue =
        "%" . $search . "%";

    $params[] = $searchValue;
    $params[] = $searchValue;

    $types .= "ss";

}


if ($categoryId > 0) {

    $where[] =
        "p.category_id = ?";

    $params[] =
        $categoryId;

    $types .= "i";

}


$whereSQL = "";

if (!empty($where)) {

    $whereSQL =
        "WHERE " . implode(
            " AND ",
            $where
        );

}


/*
|--------------------------------------------------------------------------
| TOTAL PRODUCTS
|--------------------------------------------------------------------------
*/

$countSQL = "
    SELECT COUNT(*) AS total
    FROM products p
    $whereSQL
";


$countStmt =
    $conn->prepare($countSQL);


if (!empty($params)) {

    $countStmt->bind_param(
        $types,
        ...$params
    );

}


$countStmt->execute();

$countResult =
    $countStmt->get_result();

$countRow =
    $countResult->fetch_assoc();

$totalProducts =
    (int) ($countRow['total'] ?? 0);


$totalPages =
    max(
        1,
        (int) ceil(
            $totalProducts / $limit
        )
    );


/*
|--------------------------------------------------------------------------
| GET PRODUCTS
|--------------------------------------------------------------------------
*/

$productSQL = "
    SELECT
        p.id,
        p.name,
        p.brand,
        p.description,
        p.price,
        p.stock,
        p.image,
        p.category_id,
        c.name AS category_name

    FROM products p

    LEFT JOIN categories c
        ON p.category_id = c.id

    $whereSQL

    ORDER BY p.id DESC

    LIMIT ?
    OFFSET ?
";


$productStmt =
    $conn->prepare($productSQL);


/*
|--------------------------------------------------------------------------
| BIND PRODUCT QUERY
|--------------------------------------------------------------------------
*/

$productParams =
    $params;

$productParams[] =
    $limit;

$productParams[] =
    $offset;


$productTypes =
    $types . "ii";


$productStmt->bind_param(
    $productTypes,
    ...$productParams
);


$productStmt->execute();


$productResult =
    $productStmt->get_result();


$products = [];


while (
    $product =
    $productResult->fetch_assoc()
) {

    $products[] =
        $product;

}


include "includes/header.php";

include "includes/sidebar.php";

?>


<div class="admin-main">


    <!-- TOPBAR -->

    <header class="admin-topbar">


        <div>

            <h2 class="admin-page-title">

                Products

            </h2>

        </div>


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
                        Administrator
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


    <!-- CONTENT -->

    <section class="admin-content">


        <!-- PAGE HEADER -->

        <div class="products-page-header">


            <div>

                <h1>

                    Manage Products

                </h1>


                <p>

                    Add, edit and manage your agricultural products.

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


        <!-- SUCCESS MESSAGE -->

        <?php if ($deleteMessage): ?>

            <div class="alert alert-success">

                <i class="bi bi-check-circle"></i>

                <?= htmlspecialchars(
                    $deleteMessage
                ) ?>

            </div>

        <?php endif; ?>


        <!-- ERROR MESSAGE -->

        <?php if ($deleteError): ?>

            <div class="alert alert-danger">

                <i class="bi bi-exclamation-circle"></i>

                <?= htmlspecialchars(
                    $deleteError
                ) ?>

            </div>

        <?php endif; ?>


        <!-- FILTER CARD -->

        <div class="admin-card product-filter-card">


            <form
                method="GET"
                class="product-filter-form"
            >


                <!-- SEARCH -->

                <div class="product-search">

                    <label>

                        Search Product

                    </label>


                    <div class="search-input-wrapper">

                        <i class="bi bi-search"></i>


                        <input
                            type="text"
                            name="search"
                            value="<?= htmlspecialchars(
                                $search
                            ) ?>"
                            placeholder="Search by name or brand..."
                            class="form-control"
                        >

                    </div>

                </div>


                <!-- CATEGORY -->

                <div>

                    <label>

                        Category

                    </label>


                    <select
                        name="category"
                        class="form-select"
                    >

                        <option value="0">

                            All Categories

                        </option>


                        <?php foreach (
                            $categories
                            as $category
                        ): ?>

                            <option
                                value="<?= (int) $category['id'] ?>"
                                <?= $categoryId ===
                                    (int) $category['id']
                                    ? 'selected'
                                    : '' ?>
                            >

                                <?= htmlspecialchars(
                                    $category['name']
                                ) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- BUTTON -->

                <div class="filter-buttons">

                    <button
                        type="submit"
                        class="btn btn-success"
                    >

                        <i class="bi bi-search"></i>

                        Search

                    </button>


                    <a
                        href="products.php"
                        class="btn btn-outline-secondary"
                    >

                        Reset

                    </a>

                </div>


            </form>


        </div>


        <!-- PRODUCT TABLE CARD -->

        <div class="admin-card">


            <div class="admin-card-header">


                <div>

                    <h3>

                        All Products

                    </h3>


                    <small
                        style="
                            color:#98a2b3;
                        "
                    >

                        <?= $totalProducts ?>

                        product(s) found

                    </small>

                </div>


            </div>


            <?php if (
                count($products) > 0
            ): ?>


                <div class="table-responsive">

                    <table class="admin-table products-table">


                        <thead>

                            <tr>

                                <th>
                                    Product
                                </th>

                                <th>
                                    Category
                                </th>

                                <th>
                                    Brand
                                </th>

                                <th>
                                    Price
                                </th>

                                <th>
                                    Stock
                                </th>

                                <th>
                                    Status
                                </th>

                                <th>
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                            <?php foreach (
                                $products
                                as $product
                            ): ?>


                                <tr>


                                    <!-- PRODUCT -->

                                    <td>

                                        <div
                                            class="admin-product-info"
                                        >


                                            <div
                                                class="admin-product-image"
                                            >

                                                <?php

                                                $image =
                                                    $product['image']
                                                    ?? '';

                                                $imagePath =
                                                    "../assets/images/products/"
                                                    . $image;

                                                ?>


                                                <?php if (
                                                    !empty($image)
                                                    &&
                                                    file_exists(
                                                        $imagePath
                                                    )
                                                ): ?>

                                                    <img
                                                        src="<?= htmlspecialchars(
                                                            $imagePath
                                                        ) ?>"
                                                        alt="<?= htmlspecialchars(
                                                            $product['name']
                                                        ) ?>"
                                                    >

                                                <?php else: ?>

                                                    <div
                                                        class="product-image-placeholder"
                                                    >

                                                        <i
                                                            class="bi bi-flower1"
                                                        ></i>

                                                    </div>

                                                <?php endif; ?>


                                            </div>


                                            <div>

                                                <div
                                                    class="product-name"
                                                >

                                                    <?= htmlspecialchars(
                                                        $product['name']
                                                    ) ?>

                                                </div>


                                                <small
                                                    style="
                                                        color:#98a2b3;
                                                    "
                                                >

                                                    ID:
                                                    #<?= (int) $product['id'] ?>

                                                </small>

                                            </div>


                                        </div>

                                    </td>


                                    <!-- CATEGORY -->

                                    <td>

                                        <?php if (
                                            !empty(
                                                $product['category_name']
                                            )
                                        ): ?>

                                            <span
                                                class="category-badge"
                                            >

                                                <?= htmlspecialchars(
                                                    $product['category_name']
                                                ) ?>

                                            </span>

                                        <?php else: ?>

                                            <span
                                                class="text-muted"
                                            >

                                                Uncategorized

                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <!-- BRAND -->

                                    <td>

                                        <?= htmlspecialchars(
                                            $product['brand']
                                            ?? '-'
                                        ) ?>

                                    </td>


                                    <!-- PRICE -->

                                    <td>

                                        <strong>

                                            ₹<?= number_format(
                                                (float)
                                                $product['price'],
                                                2
                                            ) ?>

                                        </strong>

                                    </td>


                                    <!-- STOCK -->

                                    <td>

                                        <?= (int)
                                            $product['stock'] ?>

                                    </td>


                                    <!-- STATUS -->

                                    <td>


                                        <?php if (
                                            (int)
                                            $product['stock']
                                            > 0
                                        ): ?>

                                            <span
                                                class="status-badge status-in-stock"
                                            >

                                                <i
                                                    class="bi bi-check-circle"
                                                ></i>

                                                In Stock

                                            </span>

                                        <?php else: ?>

                                            <span
                                                class="status-badge status-out-stock"
                                            >

                                                <i
                                                    class="bi bi-x-circle"
                                                ></i>

                                                Out of Stock

                                            </span>

                                        <?php endif; ?>


                                    </td>


                                    <!-- ACTIONS -->

                                    <td>


                                        <div
                                            class="product-actions"
                                        >


                                            <!-- VIEW -->

                                            <a
                                                href="../product-details.php?id=<?= (int) $product['id'] ?>"
                                                target="_blank"
                                                class="product-action-btn view"
                                                title="View Product"
                                            >

                                                <i
                                                    class="bi bi-eye"
                                                ></i>

                                            </a>


                                            <!-- EDIT -->

                                            <a
                                                href="edit-product.php?id=<?= (int) $product['id'] ?>"
                                                class="product-action-btn edit"
                                                title="Edit Product"
                                            >

                                                <i
                                                    class="bi bi-pencil"
                                                ></i>

                                            </a>


                                            <!-- DELETE -->

                                            <form
                                                method="POST"
                                                onsubmit="return confirmDelete(
                                                    '<?= htmlspecialchars(
                                                        addslashes(
                                                            $product['name']
                                                        )
                                                    ) ?>'
                                                );"
                                                style="display:inline;"
                                            >

                                                <input
                                                    type="hidden"
                                                    name="product_id"
                                                    value="<?= (int) $product['id'] ?>"
                                                >


                                                <button
                                                    type="submit"
                                                    name="delete_product"
                                                    class="product-action-btn delete"
                                                    title="Delete Product"
                                                >

                                                    <i
                                                        class="bi bi-trash"
                                                    ></i>

                                                </button>

                                            </form>


                                        </div>


                                    </td>


                                </tr>


                            <?php endforeach; ?>


                        </tbody>


                    </table>

                </div>


            <?php else: ?>


                <!-- EMPTY STATE -->

                <div
                    class="products-empty-state"
                >

                    <div
                        class="empty-icon"
                    >

                        <i
                            class="bi bi-box-seam"
                        ></i>

                    </div>


                    <h4>

                        No Products Found

                    </h4>


                    <p>

                        Try changing your search or add a new product.

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


            <!-- PAGINATION -->

            <?php if (
                $totalPages > 1
            ): ?>


                <div class="product-pagination">


                    <?php

                    $queryBase = [];

                    if ($search !== '') {

                        $queryBase['search'] =
                            $search;

                    }

                    if ($categoryId > 0) {

                        $queryBase['category'] =
                            $categoryId;

                    }

                    ?>


                    <!-- PREVIOUS -->

                    <?php if (
                        $page > 1
                    ): ?>


                        <?php

                        $queryBase['page'] =
                            $page - 1;

                        ?>


                        <a
                            href="products.php?<?= http_build_query(
                                $queryBase
                            ) ?>"
                            class="pagination-btn"
                        >

                            <i
                                class="bi bi-chevron-left"
                            ></i>

                        </a>


                    <?php endif; ?>


                    <!-- PAGE NUMBERS -->

                    <?php for (
                        $i = 1;
                        $i <= $totalPages;
                        $i++
                    ): ?>


                        <?php

                        $queryBase['page'] =
                            $i;

                        ?>


                        <a
                            href="products.php?<?= http_build_query(
                                $queryBase
                            ) ?>"
                            class="pagination-btn
                            <?= $page === $i
                                ? 'active'
                                : '' ?>"
                        >

                            <?= $i ?>

                        </a>


                    <?php endfor; ?>


                    <!-- NEXT -->

                    <?php if (
                        $page < $totalPages
                    ): ?>


                        <?php

                        $queryBase['page'] =
                            $page + 1;

                        ?>


                        <a
                            href="products.php?<?= http_build_query(
                                $queryBase
                            ) ?>"
                            class="pagination-btn"
                        >

                            <i
                                class="bi bi-chevron-right"
                            ></i>

                        </a>


                    <?php endif; ?>


                </div>


            <?php endif; ?>


        </div>


    </section>


</div>


<script>

function confirmDelete(productName) {

    return confirm(
        "Are you sure you want to delete \"" +
        productName +
        "\"?\n\nThis action cannot be undone."
    );

}

</script>


<?php

include "includes/footer.php";

?>