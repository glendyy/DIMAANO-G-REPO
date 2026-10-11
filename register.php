<?php
session_start();
require_once __DIR__ . "/includes/validation.php";
$pageTitle = "Register";
$errors = [];
$username = "";
$email = "";

if (!isset($_SESSION["users"])) {
    $_SESSION["users"] = [];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = clean_input($_POST["username"] ?? "");
    $email = clean_input($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    if ($username === "") {
        $errors[] = "Username is required.";
    } elseif (!valid_username($username)) {
        $errors[] = "Username must be 3–20 characters and contain only letters, numbers, or underscores.";
    } elseif (isset($_SESSION["users"][$username])) {
        $errors[] = "That username is already registered in this session.";
    }

    if ($email === "") {
        $errors[] = "Email address is required.";
    } elseif (!valid_email($email)) {
        $errors[] = "Please enter a valid email address.";
    }

    if ($password === "") {
        $errors[] = "Password is required.";
    } elseif (!valid_password($password)) {
        $errors[] = "Password must be at least 8 characters and include at least one letter and one number.";
    }

    if ($confirmPassword === "") {
        $errors[] = "Please confirm your password.";
    } elseif ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }

    if (!$errors) {
        $_SESSION["users"][$username] = [
            "email" => $email,
            "password" => password_hash($password, PASSWORD_DEFAULT)
        ];
        header("Location: login.php?registered=1");
        exit;
    }
}
include __DIR__ . "/includes/header.php";
include __DIR__ . "/includes/navbar.php";
?>
<main class="container narrow">
    <section class="form-card">
        <p class="eyebrow">GET STARTED</p>
        <h1>Create an account</h1>
        <p class="muted">Complete the form. Validation is handled by PHP on the server.</p>

        <?php if ($errors): ?>
            <div class="alert alert-error" role="alert">
                <strong>Please fix these items:</strong>
                <ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul>
            </div>
        <?php endif; ?>

        <form method="post" action="register.php" novalidate>
            <label for="username">Username</label>
            <input id="username" name="username" type="text" value="<?= e($username) ?>" minlength="3" maxlength="20" autocomplete="username" required>
            <small>3–20 characters; letters, numbers, and underscores only.</small>

            <label for="email">Email address</label>
            <input id="email" name="email" type="email" value="<?= e($email) ?>" autocomplete="email" required>

            <label for="password">Password</label>
            <input id="password" name="password" type="password" autocomplete="new-password" required>
            <small>At least 8 characters, with at least one letter and one number.</small>

            <label for="confirm_password">Confirm password</label>
            <input id="confirm_password" name="confirm_password" type="password" autocomplete="new-password" required>

            <button class="button full-width" type="submit">Register</button>
        </form>
        <p class="form-note">Already registered? <a href="login.php">Login here</a>.</p>
    </section>
</main>
<?php include __DIR__ . "/includes/footer.php"; ?>
