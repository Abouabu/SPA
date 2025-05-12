<?php
session_start();
require 'config.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Beauty Book</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-image: url('images/spa.jpg');
            background-size: cover;
            background-attachment: fixed;
            min-height: 100vh;
        }

        .contact-content {
            background-color: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .contact-icon {
            color: #ff00c8;
            font-size: 1.2rem;
            margin-right: 10px;
        }

        .btn-primary {
            background-color: #ff00c8;
            border-color: #ff00c8;
        }

        .btn-primary:hover {
            background-color: #c0007a;
            border-color: #c0007a;
        }

        .modal-content {
            border-radius: 15px;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div style="margin-bottom: 2rem;"></div>
    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="contact-content p-4 mb-5">
                    <h1 class="text-center mb-4 fw-bold">Contact Beauty Book</h1>

                    <div class="mb-5">
                        <h2 class="mb-3">Get in Touch</h2>
                        <p class="lead">We'd love to hear from you! Please feel free to contact us with any questions or to book an appointment.</p>

                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <i class="fas fa-map-marker-alt contact-icon"></i>
                                Bamburi-mtambo, Mombasa, Kenya
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-phone contact-icon"></i>
                                +254712345678
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-envelope contact-icon"></i>
                                beautybook@gmail.com
                            </li>
                        </ul>
                    </div>

                    <div class="contact-form">
                        <h2 class="mb-4">Send Us a Message</h2>
                        <form action="send_email.php" method="post">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name:</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email:</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>

                            <div class="mb-3">
                                <label for="message" class="form-label">Message:</label>
                                <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Send Message</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap Modal -->
    <div class="modal fade" id="successModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Beauty Book says</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Record created successfully!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Modal handling
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($_GET['success'])): ?>
                var myModal = new bootstrap.Modal(document.getElementById('successModal'))
                myModal.show()
            <?php endif; ?>
        });
    </script>
</body>

</html>