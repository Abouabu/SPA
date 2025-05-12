<?php
require 'config.php';

try {
    $stmt = $pdo->query("SELECT * FROM services");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching services: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Services - Beauty Salon</title>
    <style>
        .body {
            background-color: #fff5f7;
        }

        .service-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1rem;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .service-item {
            background-color: rgba(255, 255, 255, 0.91);
            padding: 0.2rem;
            text-align: center;
            border-radius: 1rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            min-height: 400px;
        }

        .service-item img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 0.8rem 0.8rem 0 0;
            margin-bottom: 15px;
        }

        .service-item h3 {
            margin: 10px 0;
            color: #ff00c8;
            font-size: 1.4em;
            min-height: 50px;
        }

        .service-item p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 15px;
            flex-grow: 1;
            padding: 0 10px;
        }

        .click-button {
            background-color: rgb(249, 219, 243);
            color: #ff00c8;
            padding: 12px 25px;
            border: solid 2px #ff00c8;
            border-radius: 0.8rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-weight: bold;
            transition: background-color 0.3s ease;
            margin-top: auto;
        }

        .click-button:hover {
            background-color: #c0007a;
            color: white;
            transition: background-color 0.3s ease;
            border: none;
        }

        .book-now-container {
            text-align: center;
            margin: 40px 0;
        }

        .book-now-button {
            background-color: #ff00c8;
            color: white;
            padding: 15px 40px;
            border-radius: 0.8rem;
            font-size: 1.1em;
            font-weight: bold;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .book-now-button:hover {
            background-color: #c0007a;
            border-radius: 0.8rem;

        }

        .dashboard-content h1 {
            text-align: center;
            color: #ff00c8;
            font-size: 2.5em;
            margin: 5rem 0 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .dashboard-content>p {
            text-align: center;
            color: #666;
            font-size: 1.1em;
            max-width: 800px;
            margin: 0 auto 40px;
            padding: 0 20px;
        }

        @media (max-width: 768px) {
            .service-container {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                padding: 10px;
            }

            .service-item img {
                height: 200px;
            }
        }
    </style>
</head>

<body class="body">
    <?php include 'navbar.php'; ?>
    <div class="dashboard-content">
        <h1>Our Services</h1>
        <p>Karibu! Welcome to BEAUTY BOOK. Discover a world of beauty and relaxation. Book your perfect service today.
        </p>

        <div class="service-container">
            <?php foreach ($services as $service): ?>
                <div class="service-item">
                    <img src="<?= htmlspecialchars($service['image_url']) ?>"
                        alt="<?= htmlspecialchars($service['name']) ?>">
                    <h3><?= htmlspecialchars($service['name']) ?></h3>
                    <p><?= htmlspecialchars($service['description']) ?></p>

                    <a href="service.php?service_id=<?= $service['id'] ?>" class="click-button">View Details</a>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="book-now-container">
            <a href="book.php" class="book-now-button">Book Appointment Now</a>
        </div>
    </div>
</body>

</html>