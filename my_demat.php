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

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>My Demat Accounts | Portfolio Manager</title>

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


            <a
                href="my_demat.php"
                class="active"
            >

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
                    placeholder="Search accounts..."
                    id="accountSearch"
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


        <!-- PAGE CONTENT -->

        <section class="demat-content">


            <!-- PAGE HEADER -->

            <div class="page-heading">

                <div>

                    <span class="eyebrow">
                        PORTFOLIO
                    </span>

                    <h1>
                        Demat Management
                    </h1>

                    <p>
                        Manage your linked Demat accounts and
                        Depository Participants.
                    </p>

                </div>


                <a
                    href="add_demat.php"
                    class="add-account-btn"
                >

                    <span>+</span>

                    Link New Account

                </a>

            </div>


            <!-- ================= SUMMARY ================= -->

            <div class="demat-summary">


                <div class="demat-summary-card summary-primary">

                    <div class="summary-icon">
                        ◉
                    </div>

                    <div class="summary-content">

                        <span>
                            TOTAL DEMAT ACCOUNTS
                        </span>

                        <strong>
                            <?= $result->num_rows ?>
                        </strong>

                        <small>
                            Accounts connected
                        </small>

                    </div>

                </div>


                <div class="demat-summary-card">

                    <div class="summary-icon">
                        ◈
                    </div>

                    <div class="summary-content">

                        <span>
                            ACCOUNT STATUS
                        </span>

                        <strong>
                            <?= $result->num_rows > 0 ? "Active" : "—" ?>
                        </strong>

                        <small>
                            <?= $result->num_rows > 0
                                ? "Your accounts are available"
                                : "No accounts linked yet"
                            ?>
                        </small>

                    </div>

                </div>


            </div>


            <!-- ================= ACCOUNTS ================= -->

            <div class="accounts-section">


                <div class="section-heading">

                    <div>

                        <span class="eyebrow">
                            ACCOUNTS
                        </span>

                        <h2>
                            Linked Accounts
                        </h2>

                    </div>


                    <?php if ($result->num_rows > 0): ?>

                        <span class="account-count">
                            <?= $result->num_rows ?>
                            <?= $result->num_rows === 1
                                ? "account"
                                : "accounts"
                            ?>
                        </span>

                    <?php endif; ?>

                </div>


                <div
                    class="accounts-grid"
                    id="accountsGrid"
                >


                    <?php if ($result->num_rows === 0): ?>


                        <!-- EMPTY STATE -->

                        <div class="empty-state">

                            <div class="empty-icon">
                                ◉
                            </div>

                            <h3>
                                No Demat accounts yet
                            </h3>

                            <p>
                                Link your first Demat account to
                                start managing your portfolio.
                            </p>

                            <a
                                href="add_demat.php"
                                class="add-account-btn"
                            >
                                + Add Demat Account
                            </a>

                        </div>


                    <?php else: ?>


                        <?php while ($demat = $result->fetch_assoc()): ?>


                            <!-- ACCOUNT CARD -->

                            <article class="demat-card">


                                <div class="demat-card-top">


                                    <div class="broker-icon">
                                        ◉
                                    </div>


                                    <div class="account-title">

                                        <h3>
                                            <?= htmlspecialchars(
                                                $demat["account_name"]
                                            ) ?>
                                        </h3>

                                        <span class="status-badge">
                                            <i></i>
                                            Active
                                        </span>

                                    </div>


                                    <div class="account-menu">
                                        ⋮
                                    </div>

                                </div>


                                <div class="account-details">


                                    <div class="detail-item">

                                        <span>
                                            ACCOUNT HOLDER
                                        </span>

                                        <strong>
                                            <?= htmlspecialchars(
                                                $demat["account_holder"]
                                            ) ?>
                                        </strong>

                                    </div>


                                    <div class="detail-item">

                                        <span>
                                            BROKER / DP
                                        </span>

                                        <strong>
                                            <?= htmlspecialchars(
                                                $demat["broker_name"]
                                            ) ?>
                                        </strong>

                                    </div>


                                    <div class="detail-item boid-detail">

                                        <span>
                                            BOID
                                        </span>

                                        <strong>
                                            <?= htmlspecialchars(
                                                $demat["boid"]
                                            ) ?>
                                        </strong>

                                    </div>


                                </div>


                                <div class="demat-card-footer">


                                    <span class="account-label">
                                        Linked Demat Account
                                    </span>


                                    <div class="account-actions">

                                        <a
                                            href="edit_demat.php?id=<?= (int) $demat["id"] ?>"
                                            class="action-btn edit-btn"
                                        >
                                            Edit
                                        </a>


                                        <a
                                            href="delete_demat.php?id=<?= (int) $demat["id"] ?>"
                                            class="action-btn delete-btn"
                                            onclick="return confirm('Are you sure you want to delete this Demat account?');"
                                        >
                                            Delete
                                        </a>

                                    </div>

                                </div>


                            </article>


                        <?php endwhile; ?>


                    <?php endif; ?>


                </div>

            </div>


        </section>

    </main>

</div>


<script>

const searchInput =
    document.getElementById("accountSearch");

const accountCards =
    document.querySelectorAll(".demat-card");


if (searchInput) {

    searchInput.addEventListener("input", function () {

        const query =
            this.value.toLowerCase().trim();


        accountCards.forEach(function (card) {

            const text =
                card.textContent.toLowerCase();

            card.style.display =
                text.includes(query)
                    ? ""
                    : "none";

        });

    });

}

</script>

</body>

</html>