<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once "config/database.php";

// Make sure the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $user_id = $_SESSION["user_id"];

    $account_name = trim($_POST["account_name"]);
    $account_holder = trim($_POST["account_holder"]);
    $broker_name = trim($_POST["broker_name"]);
    $boid = trim($_POST["boid"]);

    if (
        empty($account_name) ||
        empty($account_holder) ||
        empty($broker_name) ||
        empty($boid)
    ) {

        $message = "Please fill in all fields.";

    } else {

        // Check whether this BOID already exists
        $check = $conn->prepare(
            "SELECT id FROM demat_accounts WHERE boid = ?"
        );

        $check->bind_param("s", $boid);
        $check->execute();

        $result = $check->get_result();

        if ($result->num_rows > 0) {

            $message = "A Demat account with this BOID already exists.";

        } else {

            // Insert the Demat account
            $stmt = $conn->prepare(
                "INSERT INTO demat_accounts
                (user_id, account_name, account_holder, broker_name, boid)
                VALUES (?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "issss",
                $user_id,
                $account_name,
                $account_holder,
                $broker_name,
                $boid
            );

            if ($stmt->execute()) {

                header("Location: my_demat.php");
                exit;

            } else {

                $message = "Failed to add Demat account.";
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

    <title>Add Demat Account</title>
</head>

<body>

    <h1>Add Demat Account</h1>

    <a href="my_demat.php">Back to My Demat Accounts</a>

    <br><br>

    <?php if (!empty($message)): ?>

        <p>
            <?= htmlspecialchars($message) ?>
        </p>

    <?php endif; ?>

    <form method="POST">

        <label for="account_name">
            Account Name:
        </label>

        <input
            type="text"
            id="account_name"
            name="account_name"
            required
        >

        <br><br>

        <label for="account_holder">
            Account Holder:
        </label>

        <input
            type="text"
            id="account_holder"
            name="account_holder"
            required
        >

        <br><br>

        <label for="broker_name">
            Broker Name:
        </label>

        <input
            type="text"
            id="broker_name"
            name="broker_name"
            required
        >

        <br><br>

        <label for="boid">
            BOID:
        </label>

        <input
            type="text"
            id="boid"
            name="boid"
            required
        >

        <br><br>

        <button type="submit">
            Add Demat
        </button>

    </form>

</body>

</html>