<?php

session_start();

require_once "../config/database.php";

/*
|--------------------------------------------------------------------------
| ADMIN CHECK
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| GET PRODUCT ID
|--------------------------------------------------------------------------
*/

$productId = (int)($_GET['id'] ?? 0);

if ($productId <= 0) {
    header("Location: products.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| GET PRODUCT
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT *
    FROM products
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $productId);
$stmt->execute();

$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    header("Location: products.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| GET CATEGORIES
|--------------------------------------------------------------------------
*/

$categories = $conn->query("
    SELECT id, name
    FROM categories
    ORDER BY name ASC
");

/*
|--------------------------------------------------------------------------
| UPDATE PRODUCT
|--------------------------------------------------------------------------
*/

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST['name'] ?? '');
    $brand = trim($_POST['brand'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $categoryId = (int)($_POST['category_id'] ?? 0);

    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    if ($name === '') {

        $error = "Product name is required.";

    } elseif ($price <= 0) {

        $error = "Price must be greater than 0.";

    } elseif ($stock < 0) {

        $error = "Stock cannot be negative.";

    } elseif ($categoryId <= 0) {

        $error = "Please select a category.";

    } else {

        /*
        |--------------------------------------------------------------------------
        | IMAGE
        |--------------------------------------------------------------------------
        */

        $imageName = $product['image'];

        if (
            isset($_FILES['image']) &&
            $_FILES['image']['error'] === UPLOAD_ERR_OK
        ) {

            $uploadDir = "../assets/images/products/";

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $allowedTypes = [
                'image/jpeg',
                'image/png',
                'image/webp',
                'image/jpg'
            ];

            $fileType = mime_content_type(
                $_FILES['image']['tmp_name']
            );

            if (!in_array($fileType, $allowedTypes)) {

                $error = "Only JPG, PNG and WEBP images are allowed.";

            } else {

                $extension = strtolower(
                    pathinfo(
                        $_FILES['image']['name'],
                        PATHINFO_EXTENSION
                    )
                );

                $newImageName =
                    "product_" .
                    time() .
                    "_" .
                    bin2hex(random_bytes(4)) .
                    "." .
                    $extension;

                $targetPath =
                    $uploadDir . $newImageName;

                if (
                    move_uploaded_file(
                        $_FILES['image']['tmp_name'],
                        $targetPath
                    )
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | DELETE OLD IMAGE
                    |--------------------------------------------------------------------------
                    */

                    if (
                        !empty($product['image']) &&
                        file_exists(
                            $uploadDir . $product['image']
                        )
                    ) {

                        unlink(
                            $uploadDir . $product['image']
                        );
                    }

                    $imageName = $newImageName;

                } else {

                    $error = "Failed to upload new image.";
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE DATABASE
        |--------------------------------------------------------------------------
        */

        if ($error === "") {

            $stmt = $conn->prepare("
                UPDATE products
                SET
                    name = ?,
                    brand = ?,
                    description = ?,
                    price = ?,
                    stock = ?,
                    category_id = ?,
                    image = ?
                WHERE id = ?
            ");

            $stmt->bind_param(
                "sssdiisi",
                $name,
                $brand,
                $description,
                $price,
                $stock,
                $categoryId,
                $imageName,
                $productId
            );

            /*
            | IMPORTANT:
            | Remove the space from "sssdii si"
            */

            $stmt->bind_param(
                "sssdiisi",
                $name,
                $brand,
                $description,
                $price,
                $stock,
                $categoryId,
                $imageName,
                $productId
            );

            if ($stmt->execute()) {

                $success =
                    "Product updated successfully!";

                /*
                | Refresh product data
                */

                $stmt = $conn->prepare("
                    SELECT *
                    FROM products
                    WHERE id = ?
                    LIMIT 1
                ");

                $stmt->bind_param(
                    "i",
                    $productId
                );

                $stmt->execute();

                $product =
                    $stmt->get_result()->fetch_assoc();

            } else {

                $error =
                    "Failed to update product: " .
                    $stmt->error;
            }
        }
    }
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
        Edit Product - KisanSaathi
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body class="bg-light">

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-8">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h2 class="fw-bold">
                        Edit Product
                    </h2>

                    <p class="text-muted mb-0">
                        Update product information
                    </p>

                </div>

                <a
                    href="products.php"
                    class="btn btn-outline-secondary"
                >
                    ← Back
                </a>

            </div>


            <?php if ($success): ?>

                <div class="alert alert-success">

                    <?= htmlspecialchars($success) ?>

                </div>

            <?php endif; ?>


            <?php if ($error): ?>

                <div class="alert alert-danger">

                    <?= htmlspecialchars($error) ?>

                </div>

            <?php endif; ?>


            <div class="card shadow-sm border-0">

                <div class="card-body p-4">

                    <form
                        method="POST"
                        enctype="multipart/form-data"
                    >

                        <!-- PRODUCT NAME -->

                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Product Name
                            </label>

                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                value="<?= htmlspecialchars($product['name']) ?>"
                                required
                            >

                        </div>


                        <!-- BRAND -->

                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Brand
                            </label>

                            <input
                                type="text"
                                name="brand"
                                class="form-control"
                                value="<?= htmlspecialchars($product['brand'] ?? '') ?>"
                            >

                        </div>


                        <!-- CATEGORY -->

                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Category
                            </label>

                            <select
                                name="category_id"
                                class="form-select"
                                required
                            >

                                <option value="">
                                    Select Category
                                </option>

                                <?php while ($category = $categories->fetch_assoc()): ?>

                                    <option
                                        value="<?= $category['id'] ?>"
                                        <?= (
                                            $product['category_id'] == $category['id']
                                        ) ? 'selected' : '' ?>
                                    >

                                        <?= htmlspecialchars($category['name']) ?>

                                    </option>

                                <?php endwhile; ?>

                            </select>

                        </div>


                        <!-- DESCRIPTION -->

                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Description
                            </label>

                            <textarea
                                name="description"
                                rows="5"
                                class="form-control"
                            ><?= htmlspecialchars($product['description'] ?? '') ?></textarea>

                        </div>


                        <div class="row">

                            <!-- PRICE -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-semibold">
                                    Price
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        ₹
                                    </span>

                                    <input
                                        type="number"
                                        name="price"
                                        class="form-control"
                                        step="0.01"
                                        min="0"
                                        value="<?= htmlspecialchars($product['price']) ?>"
                                        required
                                    >

                                </div>

                            </div>


                            <!-- STOCK -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-semibold">
                                    Stock
                                </label>

                                <input
                                    type="number"
                                    name="stock"
                                    class="form-control"
                                    min="0"
                                    value="<?= htmlspecialchars($product['stock']) ?>"
                                    required
                                >

                            </div>

                        </div>


                        <!-- CURRENT IMAGE -->

                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Current Product Image
                            </label>

                            <div>

                                <?php if (!empty($product['image'])): ?>

                                    <img
                                        src="../assets/images/products/<?= htmlspecialchars($product['image']) ?>"
                                        alt="<?= htmlspecialchars($product['name']) ?>"
                                        style="
                                            width:180px;
                                            height:180px;
                                            object-fit:cover;
                                            border-radius:10px;
                                            border:1px solid #ddd;
                                        "
                                    >

                                <?php else: ?>

                                    <p class="text-muted">
                                        No image uploaded.
                                    </p>

                                <?php endif; ?>

                            </div>

                        </div>


                        <!-- NEW IMAGE -->

                        <div class="mb-4">

                            <label class="form-label fw-semibold">
                                Change Product Image
                            </label>

                            <input
                                type="file"
                                name="image"
                                class="form-control"
                                accept=".jpg,.jpeg,.png,.webp"
                            >

                            <small class="text-muted">
                                Leave empty if you don't want to change the image.
                            </small>

                        </div>


                        <!-- BUTTONS -->

                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                class="btn btn-success px-4"
                            >

                                💾 Update Product

                            </button>


                            <a
                                href="products.php"
                                class="btn btn-outline-secondary px-4"
                            >

                                Cancel

                            </a>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

</body>

</html>