<?php

session_start();

require_once "config/database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {

        $message = "Please fill in all fields.";

    } else {

        // Find the user by email
        $stmt = $conn->prepare(
            "SELECT id, name, password, role
             FROM users
             WHERE email = ?"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            // Check the entered password against the stored hash
            if (password_verify($password, $user["password"])) {

                // Login successful
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["user_name"] = $user["name"];
                $_SESSION["role"] = $user["role"];

                header("Location: dashboard.php");
                exit;

            } else {

                $message = "Invalid email or password.";
            }

        } else {

            $message = "Invalid email or password.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | Portfolio Management System</title>

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <div class="auth-page">

        <div class="auth-card">

            <div class="auth-logo">
                <h1>Portfolio Manager</h1>
                <p>Manage your Demat accounts and investments</p>
            </div>

            <?php if (!empty($message)): ?>

                <div class="alert alert-error">
                    <?= htmlspecialchars($message) ?>
                </div>

            <?php endif; ?>

            <form method="POST">

                <div class="form-group">

                    <label for="email">
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Enter your email"
                        required
                    >

                </div>

                <div class="form-group">

                    <label for="password">
                        Password
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        required
                    >

                </div>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Login
                </button>

            </form>

            <div class="auth-footer">

                Don't have an account?

                <a href="register.php">
                    Create one
                </a>

            </div>

        </div>

    </div>

</body>

</html>