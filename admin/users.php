<?php

session_start();

require_once "../config/database.php";


if (
    !isset($_SESSION['user_id']) ||
    $_SESSION['user_role'] !== 'admin'
) {

    header("Location: ../login.php");
    exit;

}


$result = $conn->query(
    "SELECT id, name, email, phone, role, created_at
     FROM users
     ORDER BY id DESC"
);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>Users - KhadBhandu Admin</title>

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

        <a href="index.php"
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

    <h2 class="mb-4">
        Users
    </h2>


    <div class="table-responsive">

        <table class="table table-bordered bg-white">

            <thead>

                <tr>

                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Joined</th>

                </tr>

            </thead>


            <tbody>

                <?php while ($user = $result->fetch_assoc()): ?>

                    <tr>

                        <td>
                            <?= $user['id'] ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($user['name']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($user['email']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $user['phone'] ?? ''
                            ) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($user['role']) ?>
                        </td>

                        <td>
                            <?= date(
                                'd M Y',
                                strtotime($user['created_at'])
                            ) ?>
                        </td>

                    </tr>

                <?php endwhile; ?>

            </tbody>

        </table>

    </div>

</div>


</body>

</html>