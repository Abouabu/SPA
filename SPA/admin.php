<?php include 'data.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Contact Us - Beauty Salon</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .contact-content {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            line-height: 1.6;
        }

        .contact-content h2 {
            color: #007bff;
            margin-bottom: 20px;
        }

        .contact-content p {
            color: #6c757d;
            margin-bottom: 20px;
        }

        .contact-content ul {
            list-style: none;
            padding: 0;
        }

        .contact-content ul li {
            margin-bottom: 10px;
        }

        .contact-content ul li i {
            margin-right: 10px;
            color: #007bff;
        }

        .contact-form {
            margin-top: 30px;
        }

        .contact-form label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        .contact-form input[type="text"],
        .contact-form input[type="email"],
        .contact-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .contact-form button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <nav class="navbar">
            <ul class="nav-left">
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="services.php">Services</a></li>
                <li class="active"><a href="contact.php">Contact</a></li>
            </ul>
            <ul class="nav-right">
                <li><a href="admin.php">Admin</a></li>
            </ul>
        </nav>

        <div class="sidebar">
            </div>

        <div class="dashboard-content">
            <h1>Contact Our Beauty Salon</h1>

            <div class="contact-content">
                <h2>Get in Touch</h2>
                <p>We'd love to hear from you! Please feel free to contact us with any questions or to book an appointment.</p>

                <ul>
                    <li><i class="fas fa-map-marker-alt"></i> [Your Salon Address], [City], Kenya</li>
                    <li><i class="fas fa-phone"></i> +254 [Your Phone Number]</li>
                    <li><i class="fas fa-envelope"></i> [Your Email Address]</li>
                </ul>

                <div class="contact-form">
                    <h2>Send Us a Message</h2>
                    <form action="send_email.php" method="post">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" required>

                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>

                        <label for="message">Message:</label>
                        <textarea id="message" name="message" rows="4" required></textarea>

                        <button type="submit">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>