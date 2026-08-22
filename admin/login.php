<?php

session_start();

require_once "../config/database.php";


/*
|--------------------------------------------------------------------------
| ALREADY LOGGED IN
|--------------------------------------------------------------------------
*/

if (isset($_SESSION['admin_id'])) {

    header("Location: index.php");

    exit;
}

$error = "";


/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email =
        trim($_POST['email'] ?? '');

    $password =
        $_POST['password'] ?? '';


    if (
        empty($email) ||
        empty($password)
    ) {

        $error =
            "Please enter email and password.";

    } else {


        $stmt = $conn->prepare("
            SELECT
                id,
                name,
                email,
                password
            FROM admin_users
            WHERE email = ?
            LIMIT 1
        ");

        $stmt->bind_param(
            "s",
            $email
        );

        $stmt->execute();

        $admin =
            $stmt
            ->get_result()
            ->fetch_assoc();


        if (
            $admin &&
            password_verify(
                $password,
                $admin['password']
            )
        ) {

            session_regenerate_id(true);

            $_SESSION['admin_id'] =
                $admin['id'];

            $_SESSION['admin_name'] =
                $admin['name'];

            $_SESSION['admin_email'] =
                $admin['email'];


            header("Location: dashboard.php");

            exit;

        } else {

            $error =
                "Invalid admin email or password.";

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
        Admin Login - KisanSaathi
    </title>


    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>


<body
    class="bg-light"
>


<div
    class="container"
>

    <div
        class="row justify-content-center align-items-center"
        style="min-height: 100vh;"
    >

        <div
            class="col-md-5 col-lg-4"
        >

            <div
                class="card shadow border-0"
            >

                <div
                    class="card-body p-4"
                >

                    <div
                        class="text-center mb-4"
                    >

                        <h2
                            class="fw-bold text-success"
                        >
                            🌱 KisanSaathi
                        </h2>

                        <p
                            class="text-muted"
                        >
                            Admin Login
                        </p>

                    </div>


                    <?php if ($error): ?>

                        <div
                            class="alert alert-danger"
                        >

                            <?= htmlspecialchars($error) ?>

                        </div>

                    <?php endif; ?>


                    <form
                        method="POST"
                    >

                        <div
                            class="mb-3"
                        >

                            <label
                                class="form-label"
                            >
                                Email
                            </label>

                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                placeholder="admin@KisanSaathi.com"
                                required
                            >

                        </div>


                        <div
                            class="mb-4"
                        >

                            <label
                                class="form-label"
                            >
                                Password
                            </label>

                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                placeholder="Enter password"
                                required
                            >

                        </div>


                        <button
                            type="submit"
                            class="btn btn-success w-100"
                        >

                            Login as Admin

                        </button>

                    </form>


                    <div
                        class="text-center mt-4"
                    >

                        <a
                            href="../index.php"
                            class="text-decoration-none"
                        >

                            ← Back to KisanSaathi

                        </a>

                    </div>


                </div>

            </div>

        </div>

    </div>

</div>


</body>

</html>