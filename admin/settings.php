<?php

require_once "includes/admin-auth.php";

require_once "../config/database.php";

$pageTitle = "Settings - KhadBhandu";

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
        Settings - KhadBhandu
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
            Settings
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

            <h4>
                KhadBhandu Admin Settings
            </h4>

            <hr>

            <div class="mb-3">

                <label class="form-label">
                    Store Name
                </label>

                <input
                    type="text"
                    class="form-control"
                    value="KhadBhandu"
                >

            </div>


            <div class="mb-3">

                <label class="form-label">
                    Store Email
                </label>

                <input
                    type="email"
                    class="form-control"
                    value="admin@khadbhandu.com"
                >

            </div>


            <button
                type="button"
                class="btn btn-success"
            >
                Save Settings
            </button>

        </div>

    </div>

</div>

</body>

</html>