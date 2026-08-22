<?php

require_once "includes/admin-auth.php";

require_once "../config/database.php";

$pageTitle = "Reviews - KisanSaathi";


$result = $conn->query("
    SELECT
        r.id,
        r.rating,
        r.comment,
        r.created_at,
        u.name AS user_name,
        p.name AS product_name

    FROM reviews r

    LEFT JOIN users u
        ON u.id = r.user_id

    LEFT JOIN products p
        ON p.id = r.product_id

    ORDER BY r.created_at DESC
");

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>
        Reviews - KisanSaathi
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body class="bg-light">

<div class="container py-5">

    <div class="d-flex justify-content-between mb-4">

        <h2>
            Customer Reviews
        </h2>

        <a
            href="index.php"
            class="btn btn-success"
        >
            ← Dashboard
        </a>

    </div>


    <div class="card shadow-sm">

        <div class="card-body">

            <?php if ($result && $result->num_rows > 0): ?>

                <div class="table-responsive">

                    <table class="table table-hover">

                        <thead>

                            <tr>
                                <th>User</th>
                                <th>Product</th>
                                <th>Rating</th>
                                <th>Review</th>
                                <th>Date</th>
                            </tr>

                        </thead>

                        <tbody>

                        <?php while ($review = $result->fetch_assoc()): ?>

                            <tr>

                                <td>
                                    <?= htmlspecialchars(
                                        $review['user_name'] ?? 'Unknown'
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $review['product_name'] ?? 'Unknown'
                                    ) ?>
                                </td>

                                <td>

                                    <?php
                                    $rating = (int)$review['rating'];

                                    for ($i = 1; $i <= 5; $i++) {

                                        echo $i <= $rating
                                            ? '⭐'
                                            : '☆';
                                    }
                                    ?>

                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $review['comment'] ?? ''
                                    ) ?>
                                </td>

                                <td>
                                    <?= date(
                                        'd M Y',
                                        strtotime(
                                            $review['created_at']
                                        )
                                    ) ?>
                                </td>

                            </tr>

                        <?php endwhile; ?>

                        </tbody>

                    </table>

                </div>

            <?php else: ?>

                <div class="text-center py-5">

                    <i
                        class="bi bi-star"
                        style="font-size:50px;"
                    ></i>

                    <h4 class="mt-3">
                        No Reviews Yet
                    </h4>

                    <p class="text-muted">
                        Customer reviews will appear here.
                    </p>

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

</body>

</html>