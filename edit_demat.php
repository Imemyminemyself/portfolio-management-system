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

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Edit Demat Account | Portfolio Manager</title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>

<body class="demat-page">

<div class="demat-layout">


    <!-- ================= SIDEBAR ================= -->

    <aside class="demat-sidebar">

        <div class="sidebar-brand">

            <div class="brand-icon">
                P
            </div>

            <div>
                <h2>Portfolio</h2>
                <span>Manager</span>
            </div>

        </div>


        <div class="sidebar-section-title">
            MENU
        </div>


        <nav class="sidebar-nav">

            <a href="dashboard.php">
                <span class="nav-icon">⌂</span>
                <span>Dashboard</span>
            </a>

            <a href="my_demat.php" class="active">
                <span class="nav-icon">▣</span>
                <span>My Demat</span>
            </a>

            <a href="#">
                <span class="nav-icon">◇</span>
                <span>Holdings</span>
            </a>

            <a href="#">
                <span class="nav-icon">◆</span>
                <span>Companies</span>
            </a>

            <a href="#">
                <span class="nav-icon">▤</span>
                <span>IPO News</span>
            </a>

        </nav>


        <div class="sidebar-bottom">

            <a href="logout.php">
                <span class="nav-icon">↪</span>
                <span>Logout</span>
            </a>

        </div>

    </aside>


    <!-- ================= MAIN ================= -->

    <main class="demat-main">


        <!-- TOP BAR -->

        <header class="demat-topbar">

            <div class="topbar-search">

                <span>⌕</span>

                <input
                    type="text"
                    placeholder="Search..."
                    aria-label="Search"
                >

            </div>


            <div class="topbar-actions">

                <button
                    type="button"
                    class="topbar-icon"
                    aria-label="Notifications"
                >
                    ♧
                </button>

                <button
                    type="button"
                    class="topbar-icon"
                    aria-label="Settings"
                >
                    ⚙
                </button>

            </div>

        </header>


        <!-- ================= CONTENT ================= -->

        <section class="demat-content add-demat-content">


            <!-- BACK -->

            <a
                href="my_demat.php"
                class="back-link"
            >
                ← Back to My Demat
            </a>


            <!-- HEADER -->

            <div class="add-demat-heading">

                <span class="eyebrow">
                    MY DEMAT
                </span>

                <h1>
                    Edit Account
                </h1>

                <p>
                    Update the information associated
                    with this Demat account.
                </p>

            </div>


            <!-- ================= FORM ================= -->

            <div class="add-demat-layout">


                <div class="add-demat-card">


                    <div class="form-card-header">

                        <div class="form-header-icon edit-icon">
                            ✎
                        </div>

                        <div>

                            <h2>
                                Account Information
                            </h2>

                            <p>
                                Make changes to your account details below.
                            </p>

                        </div>

                    </div>


                    <?php if (!empty($message)): ?>

                        <div class="demat-message">

                            <?= htmlspecialchars($message) ?>

                        </div>

                    <?php endif; ?>


                    <form
                        method="POST"
                        class="demat-form"
                    >


                        <!-- ACCOUNT NAME -->

                        <div class="demat-form-group">

                            <label for="account_name">
                                Account Name
                            </label>

                            <span class="field-hint">
                                A name to help you identify this account
                            </span>

                            <input
                                type="text"
                                id="account_name"
                                name="account_name"
                                value="<?= htmlspecialchars($demat["account_name"]) ?>"
                                required
                            >

                        </div>


                        <!-- ACCOUNT HOLDER -->

                        <div class="demat-form-group">

                            <label for="account_holder">
                                Account Holder
                            </label>

                            <span class="field-hint">
                                Name registered with your broker
                            </span>

                            <input
                                type="text"
                                id="account_holder"
                                name="account_holder"
                                value="<?= htmlspecialchars($demat["account_holder"]) ?>"
                                required
                            >

                        </div>


                        <!-- BROKER -->

                        <div class="demat-form-group">

                            <label for="broker_name">
                                Broker / DP Name
                            </label>

                            <span class="field-hint">
                                The broker or Depository Participant
                            </span>

                            <input
                                type="text"
                                id="broker_name"
                                name="broker_name"
                                value="<?= htmlspecialchars($demat["broker_name"]) ?>"
                                required
                            >

                        </div>


                        <!-- BOID -->

                        <div class="demat-form-group">

                            <label for="boid">
                                BOID
                            </label>

                            <span class="field-hint">
                                Your Beneficial Owner ID
                            </span>

                            <input
                                type="text"
                                id="boid"
                                name="boid"
                                value="<?= htmlspecialchars($demat["boid"]) ?>"
                                required
                            >

                        </div>


                        <!-- ACTIONS -->

                        <div class="demat-form-actions">

                            <a
                                href="my_demat.php"
                                class="form-cancel-btn"
                            >
                                Cancel
                            </a>

                            <button
                                type="submit"
                                class="form-submit-btn"
                            >
                                <span>✓</span>
                                Save Changes
                            </button>

                        </div>


                    </form>

                </div>


                <!-- ================= INFO ================= -->

                <aside class="demat-info-card">

                    <div class="info-icon edit-info-icon">
                        ✎
                    </div>

                    <h3>
                        Updating your account
                    </h3>

                    <p>
                        Changes made here will update the
                        information displayed throughout
                        your portfolio.
                    </p>


                    <div class="info-divider"></div>


                    <div class="info-item">

                        <span class="info-check">
                            ✓
                        </span>

                        <span>
                            Review your details before saving
                        </span>

                    </div>


                    <div class="info-item">

                        <span class="info-check">
                            ✓
                        </span>

                        <span>
                            Your holdings remain connected
                        </span>

                    </div>


                    <div class="info-item">

                        <span class="info-check">
                            ✓
                        </span>

                        <span>
                            Changes apply immediately
                        </span>

                    </div>

                </aside>


            </div>

        </section>

    </main>

</div>

</body>

</html>