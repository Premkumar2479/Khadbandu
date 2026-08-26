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
$success = "";


/*
|--------------------------------------------------------------------------
| REGISTER
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';


    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    if (
        $name === '' ||
        $email === '' ||
        $password === ''
    ) {

        $error =
            "Please fill all required fields.";

    } elseif (
        !filter_var(
            $email,
            FILTER_VALIDATE_EMAIL
        )
    ) {

        $error =
            "Please enter a valid email address.";

    } elseif (
        strlen($password) < 6
    ) {

        $error =
            "Password must contain at least 6 characters.";

    } elseif (
        $password !== $confirmPassword
    ) {

        $error =
            "Passwords do not match.";

    } else {


        /*
        |--------------------------------------------------------------------------
        | CHECK EXISTING EMAIL
        |--------------------------------------------------------------------------
        */

        $check = $conn->prepare("
            SELECT id
            FROM users
            WHERE email = ?
            LIMIT 1
        ");


        $check->bind_param(
            "s",
            $email
        );


        $check->execute();


        $exists =
            $check
            ->get_result()
            ->fetch_assoc();


        if ($exists) {

            $error =
                "An account with this email already exists.";

        } else {


            /*
            |--------------------------------------------------------------------------
            | HASH PASSWORD
            |--------------------------------------------------------------------------
            */

            $hashedPassword =
                password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );


            /*
            |--------------------------------------------------------------------------
            | CREATE CUSTOMER
            |--------------------------------------------------------------------------
            */

            $stmt = $conn->prepare("
                INSERT INTO users
                (
                    name,
                    email,
                    password,
                    phone,
                    role
                )
                VALUES (?, ?, ?, ?, 'user')
            ");


            if (!$stmt) {

                $error =
                    "Database error: " .
                    $conn->error;

            } else {


                $stmt->bind_param(
                    "ssss",
                    $name,
                    $email,
                    $hashedPassword,
                    $phone
                );


                if ($stmt->execute()) {

                    $success =
                        "Account created successfully! You can now login.";

                } else {

                    $error =
                        "Registration failed: " .
                        $stmt->error;

                }

            }

        }

    }

}

?>


<?php

$pageTitle =
    "Create Account - KisanSaathi";

include "includes/header.php";

include "includes/navbar.php";

?>


<section class="auth-page">

    <div class="container">

        <div class="auth-card">


            <!-- HEADER -->

            <div class="auth-header">

                <div class="auth-icon">

                    <i class="bi bi-person-plus"></i>

                </div>


                <h2>
                    Create Account
                </h2>


                <p>
                    Join KisanSaathi today
                </p>

            </div>


            <!-- ERROR -->

            <?php if ($error): ?>

                <div class="alert alert-danger">

                    <?= htmlspecialchars($error) ?>

                </div>

            <?php endif; ?>


            <!-- SUCCESS -->

            <?php if ($success): ?>

                <div class="alert alert-success">

                    <?= htmlspecialchars($success) ?>

                    <a
                        href="login.php"
                        class="ms-2 fw-bold"
                    >
                        Login now
                    </a>

                </div>

            <?php endif; ?>


            <!-- FORM -->

            <form method="POST">


                <!-- NAME -->

                <div class="mb-3">

                    <label class="form-label">

                        Full Name *

                    </label>


                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        placeholder="Enter your full name"
                        required
                    >

                </div>


                <!-- EMAIL -->

                <div class="mb-3">

                    <label class="form-label">

                        Email *

                    </label>


                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        placeholder="Enter your email"
                        required
                    >

                </div>


                <!-- PHONE -->

                <div class="mb-3">

                    <label class="form-label">

                        Phone*

                    </label>


                    <input
                        type="tel"
                        name="phone"
                        class="form-control"
                        placeholder="Enter phone number"
                    >

                </div>


                <!-- PASSWORD -->

                <div class="mb-3">

                    <label class="form-label">

                        Password *

                    </label>


                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        placeholder="Minimum 6 characters"
                        minlength="6"
                        required
                    >

                </div>


                <!-- CONFIRM PASSWORD -->

                <div class="mb-4">

                    <label class="form-label">

                        Confirm Password *

                    </label>


                    <input
                        type="password"
                        name="confirm_password"
                        class="form-control"
                        placeholder="Confirm your password"
                        minlength="6"
                        required
                    >

                </div>


                <!-- BUTTON -->

                <button
                    type="submit"
                    class="btn btn-success w-100 btn-lg"
                >

                    <i class="bi bi-person-plus"></i>

                    Create Account

                </button>


            </form>


            <!-- LOGIN -->

            <p class="auth-footer">

                Already have an account?

                <a href="login.php">

                    Login

                </a>

            </p>


        </div>

    </div>

</section>


<?php

include "includes/footer.php";

?>