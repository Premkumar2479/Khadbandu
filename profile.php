<?php

session_start();

require_once "config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId =
    (int)$_SESSION['user_id'];

$error = "";
$success = "";

$stmt = $conn->prepare("
    SELECT
        id,
        name,
        email,
        phone,
        address
    FROM users
    WHERE id = ?
");

$stmt->bind_param("i", $userId);
$stmt->execute();

$user =
    $stmt->get_result()->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name =
        trim($_POST['name'] ?? '');

    $phone =
        trim($_POST['phone'] ?? '');

    $address =
        trim($_POST['address'] ?? '');

    if ($name === '') {

        $error =
            "Name is required.";

    } else {

        $update = $conn->prepare("
            UPDATE users
            SET
                name = ?,
                phone = ?,
                address = ?
            WHERE id = ?
        ");

        $update->bind_param(
            "sssi",
            $name,
            $phone,
            $address,
            $userId
        );

        if ($update->execute()) {

            $_SESSION['user_name'] =
                $name;

            $success =
                "Profile updated successfully.";

            $user['name'] =
                $name;

            $user['phone'] =
                $phone;

            $user['address'] =
                $address;
        }
    }
}

include "includes/header.php";
include "includes/navbar.php";
?>

<section class="py-5">

<div class="container">

<div class="row justify-content-center">

<div class="col-lg-7">

<div class="card p-4">

<h2>
My Profile 👤
</h2>

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

<form method="POST">

<div class="mb-3">

<label>
Name
</label>

<input
    type="text"
    name="name"
    class="form-control"
    value="<?= htmlspecialchars($user['name']) ?>"
    required
>

</div>

<div class="mb-3">

<label>
Email
</label>

<input
    type="email"
    class="form-control"
    value="<?= htmlspecialchars($user['email']) ?>"
    disabled
>

</div>

<div class="mb-3">

<label>
Phone
</label>

<input
    type="text"
    name="phone"
    class="form-control"
    value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
>

</div>

<div class="mb-4">

<label>
Address
</label>

<label>
Address 2
</label>

<textarea
    name="address"
    class="form-control"
    rows="4"
><?= htmlspecialchars($user['address'] ?? '') ?></textarea>

</div>

<button
    class="btn btn-success"
>
    Save Changes
</button>

</form>

</div>

</div>

</div>

</div>

</section>

<?php include "includes/footer.php"; ?>