<?php
// footer.php
?>
<footer class="footer">
    <style>
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

    <div class="container">
        <div class="row g-4">
            <!-- Contact Column -->
            <div class="col-md-4">
                <h5>Contact Us</h5>
                <ul class="footer-links">
                    <li><i class="fas fa-map-marker-alt me-2"></i>Bamburi-Mtambo, Mombasa</li>
                    <li><i class="fas fa-phone me-2"></i>+254 712 345 678</li>
                    <li><i class="fas fa-envelope me-2"></i>info@beautybook.co.ke</li>
                </ul>
            </div>

            <!-- Quick Links Column -->
            <div class="col-md-4">
                <h5>Quick Links</h5>
                <ul class="footer-links">
                    <li><a href="services.php"><i class="fas fa-chevron-right me-2"></i>Services</a></li>
                    <li><a href="about.php"><i class="fas fa-chevron-right me-2"></i>About Us</a></li>
                    <li><a href="contact.php"><i class="fas fa-chevron-right me-2"></i>Contact</a></li>
                    <li><a href="login.php"><i class="fas fa-chevron-right me-2"></i>Client Login</a></li>
                </ul>
            </div>

            <!-- Social Media Column -->
            <div class="col-md-4">
                <h5>Follow Us</h5>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
        </div>

        <!-- Copyright -->
        <div class="row">
            <div class="col-12">
                <p class="copyright">
                    © 2025 Beauty Book. All rights reserved.<br>
                    Designed with <i class="fas fa-heart text-danger"></i> in Mombasa
                </p>
            </div>
        </div>
    </div>
</footer>