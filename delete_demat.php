<?php

session_start();

require_once "config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: my_demat.php");
    exit;
}

$demat_id = (int) $_GET["id"];
$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare(
    "DELETE FROM demat_accounts
     WHERE id = ? AND user_id = ?"
);

$stmt->bind_param("ii", $demat_id, $user_id);

$stmt->execute();

$stmt->close();
$conn->close();

header("Location: my_demat.php");
exit;