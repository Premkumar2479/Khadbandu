<?php

session_start();

require_once "config/database.php";


/*
|--------------------------------------------------------------------------
| ALREADY LOGGED IN
|--------------------------------------------------------------------------
*/

if (isset($_SESSION['user_id'])) {

    header("Location: index.php");
    exit;

}


$error = "";


/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';


    if ($email === '' || $password === '') {

        $error = "Please enter email and password.";

    } else {

        $stmt = $conn->prepare("
            SELECT
                id,
                name,
                email,
                password,
                role
            FROM users
            WHERE email = ?
            LIMIT 1
        ");


        if (!$stmt) {

            $error = "Database error: " . $conn->error;

        } else {

            $stmt->bind_param(
                "s",
                $email
            );

            $stmt->execute();

            $result =
                $stmt->get_result();

            $user =
                $result->fetch_assoc();


            /*
            |--------------------------------------------------------------------------
            | CHECK PASSWORD
            |--------------------------------------------------------------------------
            */

            if (
                $user &&
                password_verify(
                    $password,
                    $user['password']
                )
            ) {

                session_regenerate_id(true);


                $_SESSION['user_id'] =
                    $user['id'];

                $_SESSION['user_name'] =
                    $user['name'];

                $_SESSION['user_email'] =
                    $user['email'];

                $_SESSION['user_role'] =
                    $user['role'];


                header("Location: index.php");

                exit;

            } else {

                $error =
                    "Invalid email or password.";

            }

        }

    }

}

?>


<?php

$pageTitle = "Login - KisanSaathi";

include "includes/header.php";

include "includes/navbar.php";

?>


<section class="auth-page">

    <div class="container">

        <div class="auth-card">


            <div class="auth-header">

                <div class="auth-icon">

                    <i class="bi bi-person"></i>

                </div>


                <h2>
                    Welcome Back
                </h2>


                <p>
                    Login to your KisanSaathi account
                </p>

            </div>


            <?php if ($error): ?>

                <div class="alert alert-danger">

                    <?= htmlspecialchars($error) ?>

                </div>

            <?php endif; ?>


            <form method="POST">


                <div class="mb-3">

                    <label class="form-label">

                        Email Address

                    </label>


                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        placeholder="Enter your email"
                        required
                    >

                </div>


                <div class="mb-3">

                    <label class="form-label">

                        Password

                    </label>


                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        placeholder="Enter your password"
                        required
                    >

                </div>


                <button
                    type="submit"
                    class="btn btn-success w-100 btn-lg"
                >

                    <i class="bi bi-box-arrow-in-right"></i>

                    Login

                </button>


            </form>


            <p class="auth-footer">

                Don't have an account?

                <a href="register.php">

                    Create Account

                </a>

            </p>


        </div>

    </div>

</section>


<?php

include "includes/footer.php";

?>