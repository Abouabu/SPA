<?php
require 'config.php';

// Validate service ID
$service_id = isset($_GET['service_id']) ? (int) $_GET['service_id'] : 0;

try {
    // Get main service details
    $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->execute([$service_id]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$service) {
        header("Location: services.php");
        exit();
    }

    // Get sub-services
    $sub_stmt = $pdo->prepare("SELECT * FROM sub_services WHERE service_id = ?");
    $sub_stmt->execute([$service_id]);
    $sub_services = $sub_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($service['name']) ?> - Beauty Book</title>
    <style>
        /* Keep your existing haircut.php styling */
        body {
            font-family: sans-serif;
            background-color: #fff5f7;

            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .containerr {
            background-color: white;
            padding: 1rem;
            border-radius: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 800px;
            margin: 5rem auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.5rem;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #eee;
        }

        th:first-child {
            border-top-left-radius: 0.8rem;
        }

        th:last-child {
            border-top-right-radius: 0.8rem;
        }

        th {
            background-color: #ff00c8;
            color: white;
            padding: 15px;
            text-align: left;
            border-bottom: 2px solid #ffe6f4;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .back-btn {
            display: inline-block;
            margin-bottom: 1.5rem;
            padding: 0.8rem 1.5rem;
            background-color: #ff00c8;
            color: white;
            text-decoration: none;
            border-radius: 0.5rem;
            transition: background-color 0.3s;
        }

        .back-btn:hover {
            background-color: #c0007a;
        }

        .book-btn {
            display: inline-block;
            margin-bottom: 1.5rem;
            padding: 0.8rem 1.5rem;
            background-color: rgb(255, 219, 247);
            color: #ff00c8;
            font-weight: bold;
            text-decoration: none;
            border-radius: 0.5rem;
            transition: background-color 0.3s;
            border: #ff00c8 solid 0.2rem;
        }

        .book-btn:hover {
            background-color: #c0007a;
            color: white;
            border: #ff00c8 solid 0.2rem;
            transition: background-color 0.3s;
        }

        .top {
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="containerr">
        <div class="top">
            <a href="services.php" class="back-btn">👈🏻 Back to Services</a>
            <a href="book.php" class="book-btn">Book Appointment Now</a>
        </div>
        <h1><?= htmlspecialchars($service['name']) ?></h1>

        <?php if (!empty($sub_services)): ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Service Name</th>
                        <th>Price (Ksh)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sub_services as $index => $sub): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($sub['name']) ?></td>
                            <td><?= number_format($sub['price'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No sub-services currently available</p>
        <?php endif; ?>
    </div>
</body>

<?php include 'footer.php'; ?>

</html>