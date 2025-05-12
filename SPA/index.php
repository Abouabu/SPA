<?php
session_start();
require 'config.php';


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BEAUTY BOOK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-image: url(images/spa.jpg);
            background-size: cover;
            background-attachment: fixed;
            min-height: 100vh;
            line-height: 1.6;
        }

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

        .hero-section {
            min-height: 100vh;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .about-section {
            min-height: 100vh;
            background-color: rgba(255, 255, 255, 0.8);
        }

        .btn-custom {
            border: solid 2px #ff00c8;
            color: #ff00c8 !important;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            letter-spacing: -0.25px;
            transition: all 0.2s ease;
            text-decoration: none;
        }



        .btn-custom:hover {
            background-color: rgb(255, 208, 245);
            transform: translateY(-2px);
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

            .btn-custom {
                width: 100%;
                margin-top: 1rem;
            }
        }

        .footer {
            background-color: rgba(199, 199, 199, 0.9);
            backdrop-filter: blur(10px);
            padding: 4rem 0 2rem;
            margin-top: auto;
        }

        .footer h5 {
            color: #ff00c8;
            font-weight: 700;
            margin-bottom: 1.5rem;
            letter-spacing: -0.5px;
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 0.8rem;
        }

        .footer-links a {
            color: #666;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .footer-links a:hover {
            color: #c0007a;
            transform: translateX(5px);
        }

        .social-icons {
            display: flex;
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .social-icons a {
            color: #ff00c8;
            font-size: 1.5rem;
            transition: all 0.3s ease;
        }

        .social-icons a:hover {
            transform: translateY(-3px);
            color: #c0007a;
        }

        .copyright {
            text-align: center;
            margin-top: 3rem;
            color: #666;
            font-size: 0.9rem;
            border-top: 1px solid #eee;
            padding-top: 2rem;
        }
    </style>
</head>

<body>
    <!-- Navigation -->

    <?php include 'navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section d-flex align-items-center">
        <div class="container text-center text-white">
            <h1 class="display-3 fw-bold mb-4">Beauty Book</h1>
            <p class="lead fs-3 fst-italic">Enhancing your natural beauty, one style at a time.</p>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="display-5 mb-4 fw-bold">Visit Us</h2>
                    <p class="lead mb-5">For over a decade, Beauty Book has been Mombasa's premier destination for luxury beauty experiences. Our team of certified professionals combines cutting-edge techniques with traditional wellness practices to deliver unparalleled services.</p>
                    <div class="d-flex flex-column flex-md-row gap-3 justify-content-center">
                        <a href="#" class="btn-custom">VISIT MOMBASA</a>
                        <a href="#" class="btn-custom">VISIT BAMBURI-MTAMBO</a>
                        <a href="#" class="btn-custom">+254712345678</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
<?php include 'footer.php'; ?>


<!-- Bootstrap 5 JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>