<?php

session_start();

require_once "../config/database.php";

if (
    !isset($_SESSION['user_id']) ||
    ($_SESSION['user_role'] ?? '') !== 'admin'
) {
    header("Location: login.php");
    exit;
}

$error = "";
$success = "";


/* ADD CATEGORY */

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST['add_category'])
) {

    $name = trim($_POST['name'] ?? '');

    if ($name === '') {

        $error = "Category name is required.";

    } else {

        $check = $conn->prepare("
            SELECT id
            FROM categories
            WHERE name = ?
            LIMIT 1
        ");

        $check->bind_param("s", $name);
        $check->execute();

        if ($check->get_result()->num_rows > 0) {

            $error = "Category already exists.";

        } else {

            $stmt = $conn->prepare("
                INSERT INTO categories (name)
                VALUES (?)
            ");

            $stmt->bind_param("s", $name);

            if ($stmt->execute()) {
                $success = "Category added successfully.";
            } else {
                $error = "Unable to add category.";
            }
        }
    }
}


/* DELETE CATEGORY */

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST['delete_category'])
) {

    $id = (int)$_POST['category_id'];

    $check = $conn->prepare("
        SELECT COUNT(*) AS total
        FROM products
        WHERE category_id = ?
    ");

    $check->bind_param("i", $id);
    $check->execute();

    $count =
        $check->get_result()->fetch_assoc()['total'];

    if ($count > 0) {

        $error =
            "Cannot delete this category because products are using it.";

    } else {

        $stmt = $conn->prepare("
            DELETE FROM categories
            WHERE id = ?
        ");

        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $success = "Category deleted.";
        }
    }
}

$categories = $conn->query("
    SELECT
        c.id,
        c.name,
        COUNT(p.id) AS product_count
    FROM categories c
    LEFT JOIN products p
        ON p.category_id = c.id
    GROUP BY c.id, c.name
    ORDER BY c.name
");

include "includes/header.php";
include "includes/sidebar.php";
?>

<div class="admin-main">

<header class="admin-topbar">

    <h2>Categories</h2>

</header>

<section class="admin-content">

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


    <div class="admin-card">

        <h3>Add Category</h3>

        <form method="POST">

            <div class="input-group">

                <input
                    type="text"
                    name="name"
                    class="form-control"
                    placeholder="Example: Organic Fertilizers"
                    required
                >

                <button
                    type="submit"
                    name="add_category"
                    class="btn btn-success"
                >
                    Add Category
                </button>

            </div>

        </form>

    </div>


    <div class="admin-card mt-4">

        <h3>All Categories</h3>

        <div class="table-responsive">

            <table class="table">

                <thead>

                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Products</th>
                        <th>Action</th>
                    </tr>

                </thead>

                <tbody>

                    <?php while ($cat = $categories->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?= $cat['id'] ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($cat['name']) ?>
                            </td>

                            <td>
                                <?= $cat['product_count'] ?>
                            </td>

                            <td>

                                <?php if ($cat['product_count'] == 0): ?>

                                    <form
                                        method="POST"
                                        style="display:inline"
                                    >

                                        <input
                                            type="hidden"
                                            name="category_id"
                                            value="<?= $cat['id'] ?>"
                                        >

                                        <button
                                            name="delete_category"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Delete this category?')"
                                        >
                                            Delete
                                        </button>

                                    </form>

                                <?php else: ?>

                                    <span class="text-muted">
                                        In use
                                    </span>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    </div>

</section>

</div>

<?php include "includes/footer.php"; ?>