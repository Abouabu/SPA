<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>

<nav class="sidebar bg-dark text-white p-3">
    <div class="d-flex flex-column h-100">
        <!-- Brand Header -->
        <div class="text-center mb-4">
            <h2 class="text-pink mb-0">BEAUTY BOOK</h2>
            <small class="text-muted">Admin Panel</small>
        </div>

        <!-- Navigation Menu -->
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item mb-2">
                <a href="admin_dashboard.php"
                    class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? 'active bg-pink' : '' ?>">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="admin_services.php"
                    class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'admin_services.php' ? 'active bg-pink' : '' ?>">
                    <i class="fas fa-concierge-bell me-2"></i>
                    Manage Services
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="admin_users.php"
                    class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'admin_users.php' ? 'active bg-pink' : '' ?>">
                    <i class="fas fa-users-cog me-2"></i>
                    Manage Users
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="appointments.php"
                    class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'appointments.php' ? 'active bg-pink' : '' ?>">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Appointments
                </a>
            </li>
            <!-- <li class="nav-item mb-2">
                <a href="settings.php"
                    class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active bg-pink' : '' ?>">
                    <i class="fas fa-cog me-2"></i>
                    Settings
                </a>
            </li> -->
        </ul>

        <!-- User Profile & Logout -->
        <div class="mt-auto border-top pt-3">
            <div class="d-flex align-items-center mb-3">
                <div class="flex-shrink-0">
                    <img src="images/profile.jpg"
                        alt="Admin Profile"
                        class="rounded-circle"
                        width="40"
                        height="40">
                </div>
                <div class="flex-grow-1 ms-3">
                    <div class="text-pink"><?= htmlspecialchars($_SESSION['username']) ?></div>
                    <small class="text-muted">Administrator</small>
                </div>
            </div>
            <a href="logout.php" class="btn btn-pink w-100">
                <i class="fas fa-sign-out-alt me-2"></i>
                Logout
            </a>
        </div>
    </div>
</nav>

<style>
    .sidebar {
        width: 280px;
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        z-index: 1000;
        overflow-y: auto;
    }

    .nav-link {
        border-radius: 5px;
        transition: all 0.3s ease;
    }

    .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1) !important;
    }

    .nav-link.active {
        background-color: #ff00c8 !important;
        color: white !important;
    }

    .text-pink {
        color: #ff00c8;
    }

    .btn-pink {
        background-color: #ff00c8;
        color: white;
        border: none;
    }

    .btn-pink:hover {
        background-color: #c0007a;
    }

    @media (max-width: 768px) {
        .sidebar {
            position: static;
            width: 100%;
            height: auto;
        }
    }
</style>