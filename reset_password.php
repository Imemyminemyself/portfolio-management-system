<?php

date_default_timezone_set("Asia/Kathmandu");

session_start();

require_once "config/database.php";

$message = "";
$message_type = "";

$token = $_GET["token"] ?? "";

$reset = null;

if (empty($token)) {

    $message = "Invalid password reset link.";
    $message_type = "error";

} else {

    $token_hash = hash("sha256", $token);

    /*
     * Use PHP time so it matches the time
     * used when the token was created.
     */
    $current_time = date("Y-m-d H:i:s");

    $stmt = $conn->prepare(
        "SELECT id, user_id
         FROM password_resets
         WHERE token_hash = ?
         AND expires_at > ?
         LIMIT 1"
    );

    $stmt->bind_param(
        "ss",
        $token_hash,
        $current_time
    );

    $stmt->execute();

    $result = $stmt->get_result();

    $reset = $result->fetch_assoc();

    $stmt->close();


    if (!$reset) {

        $message =
            "This password reset link is invalid or has expired.";

        $message_type = "error";

    } else {

        $reset_id = $reset["id"];
        $user_id = $reset["user_id"];


        /*
         * Process new password.
         */
        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $password = $_POST["password"] ?? "";
            $confirm_password = $_POST["confirm_password"] ?? "";


            if (strlen($password) < 8) {

                $message =
                    "Password must be at least 8 characters long.";

                $message_type = "error";

            } elseif ($password !== $confirm_password) {

                $message =
                    "Passwords do not match.";

                $message_type = "error";

            } else {

                /*
                 * Hash the new password.
                 */
                $password_hash = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );


                /*
                 * Update user's password.
                 */
                $stmt = $conn->prepare(
                    "UPDATE users
                     SET password = ?
                     WHERE id = ?"
                );

                $stmt->bind_param(
                    "si",
                    $password_hash,
                    $user_id
                );

                $stmt->execute();

                $stmt->close();


                /*
                 * Delete the reset token so
                 * it cannot be used again.
                 */
                $stmt = $conn->prepare(
                    "DELETE FROM password_resets
                     WHERE id = ?"
                );

                $stmt->bind_param(
                    "i",
                    $reset_id
                );

                $stmt->execute();

                $stmt->close();


                $message =
                    "Your password has been reset successfully.";

                $message_type = "success";

                
                $reset = null;
            }
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

    <title>Reset Password | Portfolio Management System</title>

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
                Create a new password for your account.
            </p>

        </div>


        <?php if (!empty($message)): ?>

            <div class="alert <?= $message_type === "success"
                ? "alert-success"
                : "alert-error" ?>">

                <?= htmlspecialchars($message) ?>

            </div>

        <?php endif; ?>


        <?php if (!empty($reset) && empty($message) || 
                  (!empty($reset) && $message_type === "error")): ?>

            <form method="POST">

                <div class="form-group">

                    <label for="password">
                        New Password
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your new password"
                        autocomplete="new-password"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="confirm_password">
                        Confirm Password
                    </label>

                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        placeholder="Confirm your new password"
                        autocomplete="new-password"
                        required
                    >

                </div>


                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Reset Password
                </button>

            </form>

        <?php elseif ($message_type === "success"): ?>

            <div class="auth-footer">

                <a href="login.php">
                    Back to Login
                </a>

            </div>

        <?php endif; ?>

    </div>

</div>

</body>

</html>