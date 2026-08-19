<?php

session_start();
require_once "../config/database.php";

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header("Location: login.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: products.php");
    exit;
}

$stmt = $conn->prepare("
    SELECT *
    FROM products
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $id);
$stmt->execute();

$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    header("Location: products.php");
    exit;
}

$categories = $conn->query("
    SELECT id, name
    FROM categories
    ORDER BY name
");

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST['name'] ?? '');
    $brand = trim($_POST['brand'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);

    if ($name === '') {
        $error = "Product name is required.";
    } elseif ($price <= 0) {
        $error = "Price must be greater than 0.";
    } elseif ($stock < 0) {
        $error = "Stock cannot be negative.";
    } elseif ($category_id <= 0) {
        $error = "Please select a category.";
    } else {

        $imageName = $product['image'];

        if (
            isset($_FILES['image']) &&
            $_FILES['image']['error'] === UPLOAD_ERR_OK
        ) {

            $allowed = [
                'jpg',
                'jpeg',
                'png',
                'webp'
            ];

            $extension = strtolower(
                pathinfo(
                    $_FILES['image']['name'],
                    PATHINFO_EXTENSION
                )
            );

            if (!in_array($extension, $allowed)) {

                $error = "Only JPG, JPEG, PNG and WEBP images are allowed.";

            } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) {

                $error = "Image must be smaller than 5MB.";

            } else {

                $uploadDir =
                    "../assets/images/products/";

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $newImage =
                    "product_" .
                    time() .
                    "_" .
                    bin2hex(random_bytes(4)) .
                    "." .
                    $extension;

                if (
                    move_uploaded_file(
                        $_FILES['image']['tmp_name'],
                        $uploadDir . $newImage
                    )
                ) {

                    if (
                        !empty($imageName) &&
                        file_exists($uploadDir . $imageName)
                    ) {
                        unlink($uploadDir . $imageName);
                    }

                    $imageName = $newImage;

                } else {

                    $error = "Unable to upload image.";
                }
            }
        }

        if ($error === "") {

            $update = $conn->prepare("
                UPDATE products
                SET
                    category_id = ?,
                    name = ?,
                    description = ?,
                    price = ?,
                    stock = ?,
                    image = ?,
                    brand = ?
                WHERE id = ?
            ");

            $update->bind_param(
                "issdissi",
                $category_id,
                $name,
                $description,
                $price,
                $stock,
                $imageName,
                $brand,
                $id
            );

            if ($update->execute()) {

                $success = "Product updated successfully.";

                $stmt->execute();

                $product =
                    $stmt->get_result()->fetch_assoc();

            } else {

                $error = "Unable to update product.";
            }
        }
    }
}

include "includes/header.php";
include "includes/sidebar.php";
?>

<div class="admin-main">

    <header class="admin-topbar">
        <h2>Edit Product</h2>
    </header>

    <section class="admin-content">

        <div class="admin-card">

            <div class="admin-card-header">
                <h3>Edit Product</h3>
            </div>

            <?php if ($error): ?>

                <div class="alert alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>

            <?php endif; ?>

            <?php if ($success): ?>

                <div class="alert alert-success">
                    <?= htmlspecialchars($success) ?>
                </div>

            <?php endif; ?>

            <form
                method="POST"
                enctype="multipart/form-data"
            >

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Product Name *
                        </label>

                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            value="<?= htmlspecialchars($product['name']) ?>"
                            required
                        >

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Brand
                        </label>

                        <input
                            type="text"
                            name="brand"
                            class="form-control"
                            value="<?= htmlspecialchars($product['brand'] ?? '') ?>"
                        >

                    </div>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Category *
                    </label>

                    <select
                        name="category_id"
                        class="form-select"
                        required
                    >

                        <option value="">
                            Select Category
                        </option>

                        <?php while ($cat = $categories->fetch_assoc()): ?>

                            <option
                                value="<?= $cat['id'] ?>"
                                <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Description
                    </label>

                    <textarea
                        name="description"
                        rows="5"
                        class="form-control"
                    ><?= htmlspecialchars($product['description'] ?? '') ?></textarea>

                </div>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Price *
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            name="price"
                            class="form-control"
                            value="<?= htmlspecialchars($product['price']) ?>"
                            required
                        >

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Stock *
                        </label>

                        <input
                            type="number"
                            name="stock"
                            class="form-control"
                            value="<?= htmlspecialchars($product['stock']) ?>"
                            min="0"
                            required
                        >

                    </div>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Product Image
                    </label>

                    <input
                        type="file"
                        name="image"
                        class="form-control"
                        accept=".jpg,.jpeg,.png,.webp"
                    >

                </div>

                <?php if (!empty($product['image'])): ?>

                    <div class="mb-4">

                        <p>Current Image:</p>

                        <img
                            src="../assets/images/products/<?= htmlspecialchars($product['image']) ?>"
                            style="
                                width:150px;
                                height:150px;
                                object-fit:cover;
                                border-radius:10px;
                            "
                        >

                    </div>

                <?php endif; ?>

                <button
                    type="submit"
                    class="btn btn-success"
                >
                    <i class="bi bi-save"></i>
                    Update Product
                </button>

                <a
                    href="products.php"
                    class="btn btn-secondary"
                >
                    Cancel
                </a>

            </form>

        </div>

    </section>

</div>

<?php include "includes/footer.php"; ?>