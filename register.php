<?php

require_once "config/database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($name) || empty($email) || empty($password)) {

        $message = "Please fill in all fields.";

    } else {

        // Check whether the email already exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {

            $message = "An account with this email already exists.";

        } else {

            // Hash the password before storing it
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert the new user
            $stmt = $conn->prepare(
                "INSERT INTO users (name, email, password)
                 VALUES (?, ?, ?)"
            );

            $stmt->bind_param("sss", $name, $email, $hashedPassword);

            if ($stmt->execute()) {

                $message = "Registration successful!";

            } else {

                $message = "Registration failed. Please try again.";
            }

            $stmt->close();
        }

        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create Account | Portfolio Management System</title>

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <div class="auth-page">

        <div class="auth-card">

            <div class="auth-logo">
                <h1>Portfolio Manager</h1>
                <p>Create your account and start managing your Demat accounts</p>
            </div>

            <?php if (!empty($message)): ?>

                <div class="alert <?= str_contains($message, 'successful') ? 'alert-success' : 'alert-error' ?>">
                    <?= htmlspecialchars($message) ?>
                </div>

            <?php endif; ?>

           <form method="POST" onsubmit="return validateRegisterForm()">

    <div class="form-group">

        <label for="name">
            Full Name
        </label>

        <input
            type="text"
            id="name"
            name="name"
            placeholder="Enter your full name"
            autocomplete="name"
            required
        >

    </div>


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


    <div class="form-group">

        <label for="password">
            Password
        </label>

        <div class="password-wrapper">

            <input
                type="password"
                id="password"
                name="password"
                placeholder="Create a password"
                autocomplete="new-password"
                required
            >

            <button
                type="button"
                class="password-toggle"
                onclick="togglePassword('password', this)"
                aria-label="Show password"
            >
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12z"></path>
                    <circle cx="12" cy="12" r="2.5"></circle>
                </svg>
            </button>

        </div>

    </div>


    <div class="form-group">

        <label for="confirm_password">
            Confirm Password
        </label>

        <div class="password-wrapper">

            <input
                type="password"
                id="confirm_password"
                name="confirm_password"
                placeholder="Confirm your password"
                autocomplete="new-password"
                required
            >

            <button
                type="button"
                class="password-toggle"
                onclick="togglePassword('confirm_password', this)"
                aria-label="Show password"
            >
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12z"></path>
                    <circle cx="12" cy="12" r="2.5"></circle>
                </svg>
            </button>

        </div>

    </div>


    <p id="password-error" class="password-error"></p>


    <button
        type="submit"
        class="btn btn-primary"
    >
        Create Account
    </button>

</form>


            <div class="auth-footer">

                Already have an account?

                <a href="login.php">
                    Login
                </a>

            </div>

        </div>

    </div>

    <script>

function togglePassword(inputId, button) {

    const input = document.getElementById(inputId);

    if (input.type === "password") {

        input.type = "text";

        button.innerHTML = `
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M3 3l18 18"></path>
                <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"></path>
                <path d="M9.9 5.2A10.7 10.7 0 0 1 12 5c6.5 0 10 7 10 7a18.3 18.3 0 0 1-3.2 3.9"></path>
                <path d="M6.2 6.2C3.5 8.2 2 12 2 12s3.5 7 10 7c1.2 0 2.3-.2 3.3-.6"></path>
            </svg>
        `;

    } else {

        input.type = "password";

        button.innerHTML = `
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12z"></path>
                <circle cx="12" cy="12" r="2.5"></circle>
            </svg>
        `;
    }
}


function validateRegisterForm() {

    const password =
        document.getElementById("password").value;

    const confirmPassword =
        document.getElementById("confirm_password").value;

    const error =
        document.getElementById("password-error");


    if (password !== confirmPassword) {

        error.textContent =
            "Passwords do not match.";

        return false;
    }


    error.textContent = "";

    return true;
}

</script>

</body>

</html>