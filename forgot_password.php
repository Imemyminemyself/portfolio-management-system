<?php

date_default_timezone_set("Asia/Kathmandu");
session_start();

require_once "config/database.php";

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");

    if (empty($email)) {

        $message = "Please enter your email address.";
        $message_type = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $message_type = "error";

    } else {

        /*
         * Find the user.
         */
        $stmt = $conn->prepare(
            "SELECT id FROM users WHERE email = ? LIMIT 1"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        $stmt->close();


        /*
         * Don't reveal whether the email exists.
         */
        if ($user) {

            $user_id = $user["id"];

            /*
             * Generate a secure random token.
             */
            $token = bin2hex(random_bytes(32));

            /*
             * Store only the hash of the token.
             */
            $token_hash = hash("sha256", $token);

            /*
             * Token expires after 30 minutes.
             */
            $expires_at = date(
                "Y-m-d H:i:s",
                time() + (30 * 60)
            );


            /*
             * Remove old reset tokens for this user.
             */
            $stmt = $conn->prepare(
                "DELETE FROM password_resets WHERE user_id = ?"
            );

            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->close();


            /*
             * Store the new reset token.
             */
            $stmt = $conn->prepare(
                "INSERT INTO password_resets
                (user_id, token_hash, expires_at)
                VALUES (?, ?, ?)"
            );

            $stmt->bind_param(
                "iss",
                $user_id,
                $token_hash,
                $expires_at
            );

            $stmt->execute();
            $stmt->close();


            /*
             * Temporary localhost reset link.
             *
             * We'll replace this with actual email
             * sending later.
             */
            $reset_link =
                "http://localhost/portfolio-management-system/reset_password.php?token="
                . urlencode($token);

            $message =
                "A password reset link has been generated. "
                . "For development, use the link below.";

            $message_type = "success";

        } else {

            /*
             * Same response even if the email doesn't exist.
             */
            $message =
                "If an account exists with that email, "
                . "a password reset link has been generated.";

            $message_type = "success";
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

    <title>Forgot Password | Portfolio Management System</title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>

<body>

<div class="auth-page">

    <div class="auth-card">

        <div class="auth-logo">

            <h1>Reset Password</h1>

            <p>
                Enter your email address and we'll help you
                reset your password.
            </p>

        </div>


        <?php if (!empty($message)): ?>

    <div class="alert <?= $message_type === "success"
        ? "alert-success"
        : "alert-error" ?>">

        <?= htmlspecialchars($message) ?>

    </div>

    <?php if (!empty($reset_link)): ?>

        <div class="reset-link-box">

            <p>
                Development reset link:
            </p>

            <a
                href="<?= htmlspecialchars($reset_link) ?>"
                target="_self"
            >
                Reset your password
            </a>

        </div>

    <?php endif; ?>

<?php endif; ?>


        <form method="POST">

            <div class="form-group">

                <label for="email">
                    Email Address
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    autocomplete="email"
                    required
                >

            </div>


            <button
                type="submit"
                class="btn btn-primary"
            >
                Continue
            </button>

        </form>


        <div class="auth-footer">

            <a href="login.php">
                ← Back to Login
            </a>

        </div>

    </div>

</div>

</body>

</html>