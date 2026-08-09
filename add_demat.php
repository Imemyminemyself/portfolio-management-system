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

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Add Demat Account | Portfolio Manager</title>

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


            <!-- BACK LINK -->

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
                    Link New Account
                </h1>

                <p>
                    Add your Demat account details to manage
                    it alongside your portfolio.
                </p>

            </div>


            <!-- ================= FORM CARD ================= -->

            <div class="add-demat-layout">


                <div class="add-demat-card">


                    <div class="form-card-header">

                        <div class="form-header-icon">
                            +
                        </div>

                        <div>

                            <h2>
                                Account Information
                            </h2>

                            <p>
                                Enter the details associated
                                with your Demat account.
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
                                placeholder="e.g. My Primary Demat"
                                autocomplete="off"
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
                                placeholder="Enter account holder name"
                                autocomplete="name"
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
                                placeholder="e.g. Himalayan Securities"
                                autocomplete="organization"
                                required
                            >

                        </div>


                        <!-- BOID -->

                        <div class="demat-form-group">

                            <label for="boid">
                                BOID
                            </label>

                            <span class="field-hint">
                                Your 16-digit Beneficial Owner ID
                            </span>

                            <input
                                type="text"
                                id="boid"
                                name="boid"
                                placeholder="Enter your BOID"
                                inputmode="numeric"
                                autocomplete="off"
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
                                <span>+</span>
                                Link Account
                            </button>

                        </div>


                    </form>

                </div>


                <!-- ================= SIDE INFO ================= -->

                <aside class="demat-info-card">

                    <div class="info-icon">
                        ◈
                    </div>

                    <h3>
                        Keep your accounts organized
                    </h3>

                    <p>
                        Link multiple Demat accounts and
                        manage their holdings from one place.
                    </p>


                    <div class="info-divider"></div>


                    <div class="info-item">

                        <span class="info-check">
                            ✓
                        </span>

                        <span>
                            View accounts in one dashboard
                        </span>

                    </div>


                    <div class="info-item">

                        <span class="info-check">
                            ✓
                        </span>

                        <span>
                            Track holdings separately
                        </span>

                    </div>


                    <div class="info-item">

                        <span class="info-check">
                            ✓
                        </span>

                        <span>
                            Keep your portfolio organized
                        </span>

                    </div>

                </aside>


            </div>


        </section>

    </main>

</div>

</body>

</html>