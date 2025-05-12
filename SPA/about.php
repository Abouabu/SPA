<?php
session_start();
require 'config.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Beauty BOOK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-image: url('images/spa.jpg');
            background-size: cover;
            background-attachment: fixed;
            min-height: 100vh;
        }

        .about-content {
            backdrop-filter: blur(2rem);
            background-color: rgba(255, 255, 255, 0.79);
            padding: 1rem;
            border-radius: 1rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            line-height: 1.6;
            margin: 2rem 0;
        }

        .about-content h1,
        .about-content h2 {
            color: #ff00c8;
            margin-bottom: 1.5rem;
        }

        .about-content p {
            color: #6c757d;
            margin-bottom: 1.5rem;
        }

        .about-content img {
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">
    <?php include 'navbar.php'; ?>
    <div style="margin-bottom: 2rem;"></div>
    <main class="flex-grow-1">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-12 col-md-10 col-lg-8">
                    <h1 class="text-center mb-4 text-white">About Beauty Book</h1>

                    <div class="about-content">
                        <img src="images/salon.jpg" alt="Our Salon" class="img-fluid w-100">

                        <h2>Our Story</h2>
                        <p class="lead">Welcome to <strong>BEAUTY BOOK</strong>, a premier beauty destination in Mombasa, Kenya. We are passionate about providing exceptional beauty services and creating a relaxing and rejuvenating experience for our clients.</p>

                        <h2>Our Mission</h2>
                        <p>Our mission is to deliver high-quality beauty services with a focus on customer satisfaction. We strive to create a welcoming and comfortable environment where our clients can unwind and indulge in our wide range of services.</p>

                        <div class="row">
                            <div class="col-md-6">
                                <img src="images/team.jpg" alt="Our Team" class="img-fluid mb-3">
                            </div>
                            <div class="col-md-6">
                                <h2>Our Team</h2>
                                <p>Our team consists of experienced and talented stylists, estheticians, and therapists who are passionate about their craft. We are dedicated to providing personalized services and ensuring that each client receives the attention and care they deserve.</p>
                            </div>
                        </div>

                        <h2>Why Choose Us?</h2>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">Holistic beauty approach</li>
                                    <li class="list-group-item">Eco-friendly practices</li>
                                    <li class="list-group-item">Latest beauty trends</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">Premium quality products</li>
                                    <li class="list-group-item">Personalized service</li>
                                    <li class="list-group-item">Certified professionals</li>
                                </ul>
                            </div>
                        </div>

                        <div class="text-center mt-5">
                            <h2>Visit Us Today!</h2>
                            <p class="lead">Book your appointment and experience the Beauty Book difference!</p>
                            <a href="book.php" class="btn btn-primary btn-lg">
                                <i class="fas fa-calendar-check me-2"></i>Book Appointment
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>