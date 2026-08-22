<?php

require_once "config/database.php";


/*
|--------------------------------------------------------------------------
| GET BLOG ID
|--------------------------------------------------------------------------
*/

$id = (int)($_GET['id'] ?? 0);


if ($id <= 0) {

    header("Location: blog.php");

    exit;

}


/*
|--------------------------------------------------------------------------
| GET BLOG
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        id,
        title,
        content,
        image,
        author,
        created_at
    FROM farming_blogs
    WHERE id = ?
    LIMIT 1
");


if (!$stmt) {

    die(
        "Database error: " .
        htmlspecialchars($conn->error)
    );

}


$stmt->bind_param(
    "i",
    $id
);


$stmt->execute();


$result = $stmt->get_result();


$blog = $result->fetch_assoc();


$stmt->close();


/*
|--------------------------------------------------------------------------
| BLOG NOT FOUND
|--------------------------------------------------------------------------
*/

if (!$blog) {

    header("Location: blog.php");

    exit;

}


$pageTitle =
    $blog['title'] .
    " - KisanSaathi";

?>


<?php include "includes/header.php"; ?>

<?php include "includes/navbar.php"; ?>


<section class="py-5">

    <div class="container">


        <!-- BACK -->

        <div class="mb-4">

            <a
                href="blog.php"
                class="text-success text-decoration-none"
            >

                <i class="bi bi-arrow-left"></i>

                Back to Farming Blog

            </a>

        </div>



        <!-- BLOG -->

        <article class="blog-detail">


            <!-- DATE + AUTHOR -->

            <div class="text-muted mb-3">

                <?= date(
                    "d M Y",
                    strtotime($blog['created_at'])
                ) ?>


                <?php if (!empty($blog['author'])): ?>

                    · By
                    <?= htmlspecialchars(
                        $blog['author']
                    ) ?>

                <?php endif; ?>

            </div>



            <!-- TITLE -->

            <h1 class="mb-4">

                <?= htmlspecialchars(
                    $blog['title']
                ) ?>

            </h1>



            <!-- IMAGE -->

            <?php if (!empty($blog['image'])): ?>

                <div class="mb-4">

                    <img
                        src="assets/images/blog/<?= htmlspecialchars($blog['image']) ?>"
                        class="img-fluid rounded shadow-sm"
                        style="
                            width:100%;
                            max-height:500px;
                            object-fit:cover;
                        "
                        alt="<?= htmlspecialchars($blog['title']) ?>"
                        onerror="this.style.display='none';"
                    >

                </div>

            <?php endif; ?>



            <!-- CONTENT -->

            <div
                class="blog-content-full"
                style="
                    font-size:18px;
                    line-height:1.8;
                "
            >

                <?= nl2br(
                    htmlspecialchars(
                        $blog['content']
                    )
                ) ?>

            </div>


        </article>

    </div>

</section>


<?php include "includes/footer.php"; ?>