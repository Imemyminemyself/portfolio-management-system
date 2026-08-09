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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard | Portfolio Management System</title>

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="dashboard-page">

<div class="dashboard-layout">

    <!-- Sidebar -->
    <aside class="sidebar">

        <div class="sidebar-brand">
            <div class="brand-icon">P</div>

            <div>
                <h2>Portfolio</h2>
                <span>Manager</span>
            </div>
        </div>


        <nav class="sidebar-nav">

            <p class="nav-title">MENU</p>

            <a href="dashboard.php" class="nav-link active">
                <span class="nav-icon">⌂</span>
                Dashboard
            </a>

            <a href="my_demat.php" class="nav-link">
                <span class="nav-icon">▣</span>
                My Demat
            </a>

            <a href="#" class="nav-link">
                <span class="nav-icon">◈</span>
                Holdings
            </a>

            <a href="#" class="nav-link">
                <span class="nav-icon">◆</span>
                Companies
            </a>

            <a href="#" class="nav-link">
                <span class="nav-icon">▤</span>
                IPO News
            </a>

        </nav>


        <div class="sidebar-bottom">

            <a href="#" class="nav-link">
                <span class="nav-icon">⚙</span>
                Settings
            </a>

            <a href="logout.php" class="nav-link logout-link">
                <span class="nav-icon">↪</span>
                Logout
            </a>

        </div>

    </aside>


    <!-- Main Content -->
    <main class="dashboard-main">

        <!-- Top Bar -->
        <header class="dashboard-header">

            <div>
                <p class="page-label">OVERVIEW</p>

                <h1>Dashboard</h1>
            </div>

            <div class="user-profile">

                <div class="user-avatar">
                    <?= strtoupper(substr($_SESSION["user_name"], 0, 1)) ?>
                </div>

                <div class="user-info">
                    <strong>
                        <?= htmlspecialchars($_SESSION["user_name"]) ?>
                    </strong>

                    <span>Portfolio User</span>
                </div>

            </div>

        </header>


        <!-- Welcome -->
        <section class="welcome-section">

            <div>
                <p class="welcome-label">WELCOME BACK</p>

                <h2>
                    Hello, <?= htmlspecialchars($_SESSION["user_name"]) ?> 👋
                </h2>

                <p>
                    Here's an overview of your portfolio and Demat accounts.
                </p>
            </div>

        </section>


        <!-- Summary Cards -->
        <section class="summary-grid">

            <div class="summary-card">

                <div class="card-icon">
                    ◉
                </div>

                <div>
                    <span>Demat Accounts</span>
                    <strong>—</strong>
                    <small>Accounts connected</small>
                </div>

            </div>


            <div class="summary-card">

                <div class="card-icon">
                    ◆
                </div>

                <div>
                    <span>Companies Held</span>
                    <strong>—</strong>
                    <small>Companies in portfolio</small>
                </div>

            </div>


            <div class="summary-card">

                <div class="card-icon">
                    ◈
                </div>

                <div>
                    <span>Total Holdings</span>
                    <strong>—</strong>
                    <small>Across all accounts</small>
                </div>

            </div>

        </section>


        <!-- Main Dashboard Grid -->
        <section class="dashboard-grid">

            <!-- Demat Accounts -->
            <div class="dashboard-card demat-card">

                <div class="card-header">

                    <div>
                        <p class="section-label">ACCOUNTS</p>
                        <h3>My Demat Accounts</h3>
                    </div>

                    <a href="my_demat.php" class="view-link">
                        View all →
                    </a>

                </div>


                <div class="empty-state">

                    <div class="empty-icon">
                        ◉
                    </div>

                    <h4>Your Demat accounts</h4>

                    <p>
                        Add your Demat accounts to start managing your portfolio.
                    </p>

                    <a href="my_demat.php" class="primary-button">
                        Manage Demat Accounts
                    </a>

                </div>

            </div>


            <!-- Quick Actions -->
            <div class="dashboard-card">

                <div class="card-header">

                    <div>
                        <p class="section-label">QUICK ACCESS</p>
                        <h3>Portfolio</h3>
                    </div>

                </div>


                <div class="quick-actions">

                    <a href="#" class="quick-action">
                        <span class="quick-icon">◈</span>

                        <div>
                            <strong>Holdings</strong>
                            <small>View your stocks</small>
                        </div>

                        <span class="arrow">→</span>
                    </a>


                    <a href="#" class="quick-action">
                        <span class="quick-icon">◆</span>

                        <div>
                            <strong>Companies</strong>
                            <small>Browse companies</small>
                        </div>

                        <span class="arrow">→</span>
                    </a>


                    <a href="#" class="quick-action">
                        <span class="quick-icon">▤</span>

                        <div>
                            <strong>IPO News</strong>
                            <small>Latest IPO updates</small>
                        </div>

                        <span class="arrow">→</span>
                    </a>

                </div>

            </div>

        </section>


        <!-- Portfolio Placeholder -->
        <section class="dashboard-card portfolio-preview">

            <div class="card-header">

                <div>
                    <p class="section-label">PORTFOLIO</p>
                    <h3>Portfolio Overview</h3>
                </div>

            </div>


            <div class="table-placeholder">

                <div class="placeholder-row placeholder-header">
                    <span>Company</span>
                    <span>Quantity</span>
                    <span>Current Price</span>
                    <span>Status</span>
                </div>

                <div class="placeholder-message">
                    <span>No holdings to display yet.</span>
                    <small>
                        Your holdings will appear here once they are added.
                    </small>
                </div>

            </div>

        </section>

    </main>

</div>

</body>
</html>