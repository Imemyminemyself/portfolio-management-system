<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once "config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

// Check whether a Demat ID was provided
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: my_demat.php");
    exit;
}

$demat_id = (int) $_GET["id"];

$message = "";

// Get the Demat account
$stmt = $conn->prepare(
    "SELECT id, account_name, account_holder, broker_name, boid
     FROM demat_accounts
     WHERE id = ? AND user_id = ?"
);

$stmt->bind_param("ii", $demat_id, $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: my_demat.php");
    exit;
}

$demat = $result->fetch_assoc();

$stmt->close();


// Handle update
if ($_SERVER["REQUEST_METHOD"] === "POST") {

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

        // Check whether another Demat account already uses this BOID
        $check = $conn->prepare(
            "SELECT id
             FROM demat_accounts
             WHERE boid = ? AND id != ?"
        );

        $check->bind_param("si", $boid, $demat_id);
        $check->execute();

        $check_result = $check->get_result();

        if ($check_result->num_rows > 0) {

            $message = "Another Demat account already uses this BOID.";

        } else {

            $update = $conn->prepare(
                "UPDATE demat_accounts
                 SET account_name = ?,
                     account_holder = ?,
                     broker_name = ?,
                     boid = ?
                 WHERE id = ? AND user_id = ?"
            );

            $update->bind_param(
                "ssssii",
                $account_name,
                $account_holder,
                $broker_name,
                $boid,
                $demat_id,
                $user_id
            );

            if ($update->execute()) {

                header("Location: my_demat.php");
                exit;

            } else {

                $message = "Failed to update Demat account.";
            }

            $update->close();
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

    <title>Edit Demat Account</title>
</head>

<body>

    <h1>Edit Demat Account</h1>

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
            value="<?= htmlspecialchars($demat["account_name"]) ?>"
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
            value="<?= htmlspecialchars($demat["account_holder"]) ?>"
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
            value="<?= htmlspecialchars($demat["broker_name"]) ?>"
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
            value="<?= htmlspecialchars($demat["boid"]) ?>"
            required
        >

        <br><br>

        <button type="submit">
            Update Demat
        </button>

    </form>

</body>

</html>