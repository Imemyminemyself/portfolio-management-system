<?php

session_start();

require_once "config/database.php";

// Make sure the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

// Get Demat accounts belonging to the logged-in user
$stmt = $conn->prepare(
    "SELECT id, account_name, account_holder, broker_name, boid, created_at
     FROM demat_accounts
     WHERE user_id = ?
     ORDER BY created_at DESC"
);

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Demat Accounts</title>
</head>

<body>

    <h1>My Demat Accounts</h1>

    <a href="dashboard.php">Back to Dashboard</a>

    <p>
    <a href="add_demat.php">+ Add Demat Account</a>
</p>
    <br><br>

    <?php if ($result->num_rows === 0): ?>

        <p>You don't have any Demat accounts yet.</p>

    <?php else: ?>

        <?php while ($demat = $result->fetch_assoc()): ?>

            <div>

                <h2>
                    <?= htmlspecialchars($demat["account_name"]) ?>
                </h2>

                <p>
                    Account Holder:
                    <?= htmlspecialchars($demat["account_holder"]) ?>
                </p>

                <p>
                    Broker:
                    <?= htmlspecialchars($demat["broker_name"]) ?>
                </p>

                <p>
                    BOID:
                    <?= htmlspecialchars($demat["boid"]) ?>
                </p>

                <a href="edit_demat.php?id=<?= $demat["id"] ?>">
    Edit
</a>

<a href="delete_demat.php?id=<?= $demat["id"] ?>"
   onclick="return confirm('Are you sure you want to delete this Demat account?');">
    Delete
</a>

                <hr>

            </div>

        <?php endwhile; ?>

    <?php endif; ?>

</body>

</html>

<?php

$stmt->close();
$conn->close();

?>