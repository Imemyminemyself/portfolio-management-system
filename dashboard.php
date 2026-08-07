<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>

<body>

    <h1>Dashboard</h1>

    <p>
        Welcome,
        <?= htmlspecialchars($_SESSION["user_name"]) ?>!
    </p>

    <p>
        You are logged in successfully.
    </p>

    <a href="logout.php">Logout</a>
</body>

</html>