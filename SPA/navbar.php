<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .navbar {
            background-color: rgba(255, 255, 255, 0.5) !important;
            backdrop-filter: blur(2rem);
            padding: 1rem 0;
            border-bottom: 1px solid #000;

        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.25rem;
            letter-spacing: -0.5px;
            color: #ff00c8 !important;
        }

        .nav-link {
            color: #ff00c8 !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            transform: translateY(-2px);
            text-decoration: none;
        }

        .navbar-collapse {
            flex-grow: 0;
        }

        .navbar-nav {
            gap: 0.5rem;
        }

        .navbar-toggler {
            border: none;
            padding: 0.5rem;
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }

        @media (max-width: 992px) {
            .navbar-brand {
                position: absolute;
                left: 50%;
                transform: translateX(-50%);
            }

            .navbar-toggler {
                order: -1;
            }

            .navbar-nav {
                padding-top: 1rem;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand mx-auto" href="#">BEAUTY BOOK</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <?php else: ?>
                        <?php
                        $dashboardLink = 'dashboard.php';
                        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
                            $dashboardLink = 'admin_dashboard.php';
                        }
                        ?>
                        <li class="nav-item"><a class="nav-link" href="<?= $dashboardLink ?>">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Bootstrap JS and Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>