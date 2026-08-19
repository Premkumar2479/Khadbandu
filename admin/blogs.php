<?php

session_start();

require_once "../config/database.php";


if (!isset($_SESSION['admin_id'])) {

    header("Location: login.php");

    exit;
}

$message = "";


/*
|--------------------------------------------------------------------------
| ADD BLOG
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $author = trim($_POST['author'] ?? 'Admin');


    if ($title !== '' && $content !== '') {

        $stmt = $conn->prepare(
            "INSERT INTO farming_blogs
            (title, content, author)
            VALUES (?, ?, ?)"
        );

        $stmt->bind_param(
            "sss",
            $title,
            $content,
            $author
        );

        $stmt->execute();

        $message = "Blog article published successfully.";

    }

}


/*
|--------------------------------------------------------------------------
| DELETE BLOG
|--------------------------------------------------------------------------
*/

if (isset($_GET['delete'])) {

    $id = (int)$_GET['delete'];

    $stmt = $conn->prepare(
        "DELETE FROM farming_blogs WHERE id = ?"
    );

    $stmt->bind_param(
        "i",
        $id
    );

    $stmt->execute();

    header("Location: blogs.php");

    exit;
}


$blogs = $conn->query(
    "SELECT *
    FROM farming_blogs
    ORDER BY created_at DESC"
);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>Blogs - KhadBhandu Admin</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        href="../assets/css/style.css"
        rel="stylesheet">

</head>


<body class="admin-body">


<div class="admin-navbar">

    <div class="container-fluid">

        <a
            href="index.php"
            class="admin-brand">

            🌾 KhadBhandu Admin

        </a>

        <a
            href="logout.php"
            class="btn btn-outline-danger btn-sm">

            Logout

        </a>

    </div>

</div>


<div class="container py-5">

    <h2>
        Farming Blog
    </h2>


    <?php if ($message): ?>

        <div class="alert alert-success mt-3">

            <?= htmlspecialchars($message) ?>

        </div>

    <?php endif; ?>


    <div class="admin-panel mt-4">

        <h4>
            Publish New Article
        </h4>


        <form method="POST">

            <div class="mb-3">

                <label class="form-label">
                    Title
                </label>

                <input
                    type="text"
                    name="title"
                    class="form-control"
                    required
                >

            </div>


            <div class="mb-3">

                <label class="form-label">
                    Author
                </label>

                <input
                    type="text"
                    name="author"
                    value="KhadBhandu Team"
                    class="form-control"
                >

            </div>


            <div class="mb-3">

                <label class="form-label">
                    Content
                </label>

                <textarea
                    name="content"
                    rows="8"
                    class="form-control"
                    required
                ></textarea>

            </div>


            <button class="btn btn-success">

                Publish Article

            </button>

        </form>

    </div>


    <div class="admin-panel mt-4">

        <h4>
            Published Articles
        </h4>


        <div class="table-responsive">

            <table class="table">

                <thead>

                    <tr>

                        <th>Title</th>
                        <th>Author</th>
                        <th>Date</th>
                        <th>Action</th>

                    </tr>

                </thead>


                <tbody>

                    <?php while ($blog = $blogs->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars(
                                    $blog['title']
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $blog['author']
                                ) ?>
                            </td>

                            <td>
                                <?= date(
                                    'd M Y',
                                    strtotime($blog['created_at'])
                                ) ?>
                            </td>

                            <td>

                                <a
                                    href="blogs.php?delete=<?= $blog['id'] ?>"
                                    class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Delete this article?');">

                                    Delete

                                </a>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>


</body>

</html>