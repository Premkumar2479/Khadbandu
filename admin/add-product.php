<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

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


$error = "";
$success = "";


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
| ADD PRODUCT
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $brand = trim($_POST["brand"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $price = (float)($_POST["price"] ?? 0);
    $stock = (int)($_POST["stock"] ?? 0);
    $categoryId = (int)($_POST["category_id"] ?? 0);

    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    if ($name === "") {

        $error = "Product name is required.";

    } elseif ($description === "") {

        $error = "Product description is required.";

    } elseif ($price <= 0) {

        $error = "Price must be greater than 0.";

    } elseif ($stock < 0) {

        $error = "Stock cannot be negative.";

    } elseif ($categoryId <= 0) {

        $error = "Please select a category.";

    }


    /*
    |--------------------------------------------------------------------------
    | IMAGE UPLOAD
    |--------------------------------------------------------------------------
    */

    $imageName = "";


    if ($error === "" && isset($_FILES["image"])) {

        if ($_FILES["image"]["error"] === UPLOAD_ERR_OK) {

            $originalName = $_FILES["image"]["name"];

            $extension = strtolower(
                pathinfo(
                    $originalName,
                    PATHINFO_EXTENSION
                )
            );


            $allowedExtensions = [
                "jpg",
                "jpeg",
                "png",
                "webp"
            ];


            if (!in_array(
                $extension,
                $allowedExtensions,
                true
            )) {

                $error =
                    "Only JPG, JPEG, PNG and WEBP images are allowed.";

            } else {

                /*
                |--------------------------------------------------------------------------
                | CREATE UNIQUE IMAGE NAME
                |--------------------------------------------------------------------------
                */

                $imageName =
                    "product_" .
                    time() .
                    "_" .
                    bin2hex(random_bytes(4)) .
                    "." .
                    $extension;


                /*
                |--------------------------------------------------------------------------
                | IMAGE DIRECTORY
                |--------------------------------------------------------------------------
                */

                $uploadDirectory =
                    "../assets/images/products/";


                if (!is_dir($uploadDirectory)) {

                    mkdir(
                        $uploadDirectory,
                        0777,
                        true
                    );
                }


                $uploadPath =
                    $uploadDirectory .
                    $imageName;


                /*
                |--------------------------------------------------------------------------
                | MOVE IMAGE
                |--------------------------------------------------------------------------
                */

                if (!move_uploaded_file(
                    $_FILES["image"]["tmp_name"],
                    $uploadPath
                )) {

                    $error =
                        "Image upload failed.";

                }

            }

        } elseif (
            $_FILES["image"]["error"] !== UPLOAD_ERR_NO_FILE
        ) {

            $error =
                "There was a problem uploading the image.";

        }

    }


    /*
    |--------------------------------------------------------------------------
    | INSERT PRODUCT
    |--------------------------------------------------------------------------
    */

    if ($error === "") {

        $stmt = $conn->prepare("
            INSERT INTO products
            (
                name,
                brand,
                description,
                price,
                stock,
                category_id,
                image
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");


        if (!$stmt) {

            $error =
                "Database error: " .
                $conn->error;

        } else {

            /*
            |--------------------------------------------------------------------------
            | CORRECT bind_param TYPES
            |--------------------------------------------------------------------------
            |
            | s = string
            | d = decimal/double
            | i = integer
            |
            */

            $stmt->bind_param(
                "sssdiis",
                $name,
                $brand,
                $description,
                $price,
                $stock,
                $categoryId,
                $imageName
            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | EXECUTE INSERT
    |--------------------------------------------------------------------------
    */

    if ($error === "" && isset($stmt)) {

        /*
        |--------------------------------------------------------------------------
        | IMPORTANT
        |--------------------------------------------------------------------------
        | Remove the accidental space from the type string.
        */

        $stmt = $conn->prepare("
            INSERT INTO products
            (
                name,
                brand,
                description,
                price,
                stock,
                category_id,
                image
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");


        if (!$stmt) {

            $error =
                "Could not prepare database query: " .
                $conn->error;

        } else {

            $stmt->bind_param(
                "sssdiis",
                $name,
                $brand,
                $description,
                $price,
                $stock,
                $categoryId,
                $imageName
            );


            /*
            |--------------------------------------------------------------------------
            | FIX TYPE STRING
            |--------------------------------------------------------------------------
            */

            $stmt->close();


            $stmt = $conn->prepare("
                INSERT INTO products
                (
                    name,
                    brand,
                    description,
                    price,
                    stock,
                    category_id,
                    image
                )
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");


            $stmt->bind_param(
                "sssdiis",
                $name,
                $brand,
                $description,
                $price,
                $stock,
                $categoryId,
                $imageName
            );


            if ($stmt->execute()) {

                $success =
                    "Product added successfully!";

                /*
                |--------------------------------------------------------------------------
                | CLEAR FORM
                |--------------------------------------------------------------------------
                */

                $name = "";
                $brand = "";
                $description = "";
                $price = 0;
                $stock = 0;
                $categoryId = 0;

            } else {

                /*
                |--------------------------------------------------------------------------
                | REMOVE IMAGE IF DATABASE INSERT FAILS
                |--------------------------------------------------------------------------
                */

                if (
                    $imageName !== "" &&
                    file_exists($uploadPath)
                ) {

                    unlink($uploadPath);

                }


                $error =
                    "Product could not be added: " .
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
        Add Product - KisanSaathi Admin
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


<body class="bg-light">


<!-- =========================================================
     NAVBAR
========================================================= -->

<nav class="navbar navbar-dark bg-success">

    <div class="container">

        <a
            href="dashboard.php"
            class="navbar-brand fw-bold"
        >

            🌱 KisanSaathi Admin

        </a>


        <div class="d-flex gap-2">

            <a
                href="dashboard.php"
                class="btn btn-light btn-sm"
            >

                Dashboard

            </a>


            <a
                href="logout.php"
                class="btn btn-outline-light btn-sm"
            >

                Logout

            </a>

        </div>

    </div>

</nav>


<!-- =========================================================
     MAIN
========================================================= -->

<div class="container py-5">


    <div class="row justify-content-center">

        <div class="col-lg-8">


            <!-- HEADER -->

            <div class="mb-4">

                <h1>

                    <i class="bi bi-plus-circle text-success"></i>

                    Add Product

                </h1>

                <p class="text-muted">

                    Add a new agricultural product to KisanSaathi.

                </p>

            </div>


            <!-- SUCCESS -->

            <?php if ($success !== ""): ?>

                <div
                    class="alert alert-success alert-dismissible fade show"
                >

                    <i class="bi bi-check-circle-fill"></i>

                    <?= htmlspecialchars($success) ?>


                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                    ></button>

                </div>

            <?php endif; ?>


            <!-- ERROR -->

            <?php if ($error !== ""): ?>

                <div
                    class="alert alert-danger alert-dismissible fade show"
                >

                    <i class="bi bi-exclamation-triangle-fill"></i>

                    <?= htmlspecialchars($error) ?>


                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                    ></button>

                </div>

            <?php endif; ?>


            <!-- FORM -->

            <div class="card border-0 shadow-sm">

                <div class="card-body p-4">


                    <form
                        method="POST"
                        enctype="multipart/form-data"
                    >


                        <!-- PRODUCT NAME -->

                        <div class="mb-3">

                            <label
                                class="form-label fw-semibold"
                            >

                                Product Name
                                <span class="text-danger">*</span>

                            </label>


                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                placeholder="Example: Urea Fertilizer"
                                value="<?= htmlspecialchars($name ?? "") ?>"
                                required
                            >

                        </div>


                        <!-- BRAND -->

                        <div class="mb-3">

                            <label
                                class="form-label fw-semibold"
                            >

                                Brand

                            </label>


                            <input
                                type="text"
                                name="brand"
                                class="form-control"
                                placeholder="Example: IFFCO"
                                value="<?= htmlspecialchars($brand ?? "") ?>"
                            >

                        </div>


                        <!-- CATEGORY -->

                        <div class="mb-3">

                            <label
                                class="form-label fw-semibold"
                            >

                                Category
                                <span class="text-danger">*</span>

                            </label>


                            <select
                                name="category_id"
                                class="form-select"
                                required
                            >

                                <option value="">

                                    Select Category

                                </option>


                                <?php if (
                                    $categories &&
                                    $categories->num_rows > 0
                                ): ?>


                                    <?php while (
                                        $category =
                                        $categories->fetch_assoc()
                                    ): ?>

                                        <option
                                            value="<?= $category['id'] ?>"
                                            <?= (
                                                isset($categoryId) &&
                                                $categoryId ==
                                                $category['id']
                                            )
                                                ? "selected"
                                                : ""
                                            ?>
                                        >

                                            <?= htmlspecialchars(
                                                $category['name']
                                            ) ?>

                                        </option>

                                    <?php endwhile; ?>


                                <?php else: ?>

                                    <option value="">

                                        No categories found

                                    </option>

                                <?php endif; ?>

                            </select>

                        </div>


                        <!-- DESCRIPTION -->

                        <div class="mb-3">

                            <label
                                class="form-label fw-semibold"
                            >

                                Description
                                <span class="text-danger">*</span>

                            </label>


                            <textarea
                                name="description"
                                class="form-control"
                                rows="5"
                                placeholder="Describe the product..."
                                required
                            ><?= htmlspecialchars($description ?? "") ?></textarea>

                        </div>


                        <!-- PRICE + STOCK -->

                        <div class="row">


                            <!-- PRICE -->

                            <div class="col-md-6 mb-3">

                                <label
                                    class="form-label fw-semibold"
                                >

                                    Price (₹)
                                    <span class="text-danger">*</span>

                                </label>


                                <input
                                    type="number"
                                    name="price"
                                    class="form-control"
                                    min="0.01"
                                    step="0.01"
                                    placeholder="499.00"
                                    value="<?= (
                                        isset($price) &&
                                        $price > 0
                                    )
                                        ? htmlspecialchars($price)
                                        : ""
                                    ?>"
                                    required
                                >

                            </div>


                            <!-- STOCK -->

                            <div class="col-md-6 mb-3">

                                <label
                                    class="form-label fw-semibold"
                                >

                                    Stock Quantity
                                    <span class="text-danger">*</span>

                                </label>


                                <input
                                    type="number"
                                    name="stock"
                                    class="form-control"
                                    min="0"
                                    step="1"
                                    placeholder="100"
                                    value="<?= (
                                        isset($stock) &&
                                        $stock > 0
                                    )
                                        ? htmlspecialchars($stock)
                                        : ""
                                    ?>"
                                    required
                                >

                            </div>

                        </div>


                        <!-- IMAGE -->

                        <div class="mb-4">

                            <label
                                class="form-label fw-semibold"
                            >

                                Product Image

                            </label>


                            <input
                                type="file"
                                name="image"
                                id="image"
                                class="form-control"
                                accept=".jpg,.jpeg,.png,.webp"
                            >


                            <div
                                class="form-text"
                            >

                                JPG, JPEG, PNG or WEBP only.

                            </div>


                            <!-- IMAGE PREVIEW -->

                            <div
                                class="mt-3"
                                id="imagePreviewContainer"
                                style="display:none;"
                            >

                                <p class="mb-2 fw-semibold">

                                    Image Preview

                                </p>


                                <img
                                    id="imagePreview"
                                    src=""
                                    alt="Preview"
                                    style="
                                        max-width: 250px;
                                        max-height: 250px;
                                        object-fit: cover;
                                        border-radius: 10px;
                                    "
                                >

                            </div>

                        </div>


                        <!-- BUTTONS -->

                        <div class="d-flex gap-2">


                            <button
                                type="submit"
                                class="btn btn-success px-4"
                            >

                                <i class="bi bi-plus-circle"></i>

                                Add Product

                            </button>


                            <a
                                href="dashboard.php"
                                class="btn btn-outline-secondary"
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


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


<script>

/*
|--------------------------------------------------------------------------
| IMAGE PREVIEW
|--------------------------------------------------------------------------
*/

document
    .getElementById("image")
    .addEventListener(
        "change",
        function(event) {

            const file =
                event.target.files[0];

            const preview =
                document.getElementById(
                    "imagePreview"
                );

            const container =
                document.getElementById(
                    "imagePreviewContainer"
                );


            if (file) {

                preview.src =
                    URL.createObjectURL(file);

                container.style.display =
                    "block";

            } else {

                preview.src = "";

                container.style.display =
                    "none";

            }

        }
    );

</script>


</body>

</html>