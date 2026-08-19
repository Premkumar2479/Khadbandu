<?php

require_once "config/database.php";

$pageTitle = "Farming Blog - KhadBhandu";

include "includes/header.php";
include "includes/navbar.php";


/*
|--------------------------------------------------------------------------
| GET BLOGS
|--------------------------------------------------------------------------
*/

$result = $conn->query("
    SELECT
        id,
        title,
        content,
        image,
        author,
        created_at
    FROM farming_blogs
    ORDER BY created_at DESC
");

?>

<section class="py-5">

    <div class="container">

        <!-- HEADER -->

        <div class="text-center mb-5">

            <span class="text-success fw-bold">
                FARMING BLOG
            </span>

            <h1 class="mt-2">
                Learn Better Farming
            </h1>

            <p class="text-muted">
                Tips, guides and agriculture knowledge for better farming.
            </p>

        </div>


        <!-- BLOGS -->

        <div class="row g-4">

            <?php if ($result && $result->num_rows > 0): ?>

                <?php while ($blog = $result->fetch_assoc()): ?>

                    <div class="col-md-6 col-lg-4">

                        <article class="card h-100 shadow-sm border-0">


                            <!-- IMAGE -->

                            <?php if (!empty($blog['image'])): ?>

                                <img
                                    src="assets/images/blog/<?= htmlspecialchars($blog['image']) ?>"
                                    class="card-img-top"
                                    style="
                                        height:220px;
                                        object-fit:cover;
                                    "
                                    alt="<?= htmlspecialchars($blog['title']) ?>"
                                    onerror="this.style.display='none';"
                                >

                            <?php else: ?>

                                <div
                                    class="d-flex align-items-center justify-content-center bg-light"
                                    style="height:220px;"
                                >

                                    <i
                                        class="bi bi-flower1 text-success"
                                        style="font-size:60px;"
                                    ></i>

                                </div>

                            <?php endif; ?>


                            <!-- CONTENT -->

                            <div class="card-body d-flex flex-column">


                                <small class="text-success mb-2">

                                    <?= date(
                                        "d M Y",
                                        strtotime($blog['created_at'])
                                    ) ?>

                                    <?php if (!empty($blog['author'])): ?>

                                        · By
                                        <?= htmlspecialchars($blog['author']) ?>

                                    <?php endif; ?>

                                </small>


                                <h4 class="card-title">

                                    <?= htmlspecialchars(
                                        $blog['title']
                                    ) ?>

                                </h4>


                                <p class="text-muted">

                                    <?= htmlspecialchars(
                                        mb_substr(
                                            strip_tags(
                                                $blog['content']
                                            ),
                                            0,
                                            150
                                        )
                                    ) ?>...

                                </p>


                                <!-- READ MORE -->

                                <div class="mt-auto">

                                    <a
                                        href="blog_details.php?id=<?= (int)$blog['id'] ?>"
                                        class="btn btn-success"
                                    >

                                        Read More

                                        <i class="bi bi-arrow-right"></i>

                                    </a>

                                </div>


                            </div>

                        </article>

                    </div>

                <?php endwhile; ?>


            <?php else: ?>


                <!-- NO BLOG -->

                <div class="col-12">

                    <div class="text-center py-5">

                        <i
                            class="bi bi-journal-text text-muted"
                            style="font-size:60px;"
                        ></i>

                        <h3 class="mt-3">
                            No Blog Posts Yet
                        </h3>

                        <p class="text-muted">
                            Farming articles will appear here.
                        </p>

                    </div>

                </div>


            <?php endif; ?>

        </div>

    </div>

</section>


<?php include "includes/footer.php"; ?>