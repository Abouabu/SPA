<?php
session_start();
require 'config.php';

$error = '';
$success = '';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch services and sub-services
try {
    $stmt = $pdo->query("SELECT id, name FROM services");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $subServiceStmt = $pdo->query("SELECT service_id, id, name, price FROM sub_services");
    $allSubServices = $subServiceStmt->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching services: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book'])) {
    $user_id = $_SESSION['user_id'];
    $service_id = $_POST['service_id'];
    $sub_service_id = $_POST['sub_service_id'];
    $booking_date = $_POST['booking_date'];
    $booking_time = $_POST['booking_time'];

    try {
        if (empty($service_id) || empty($sub_service_id) || empty($booking_date) || empty($booking_time)) {
            throw new Exception("All fields are required");
        }

        // Validate service
        $stmt = $pdo->prepare("SELECT id FROM services WHERE id = ?");
        $stmt->execute([$service_id]);
        if (!$stmt->fetch()) {
            throw new Exception("Invalid service selected");
        }

        // Validate sub-service
        $stmt = $pdo->prepare("SELECT price FROM sub_services WHERE id = ? AND service_id = ?");
        $stmt->execute([$sub_service_id, $service_id]);
        $sub_service = $stmt->fetch();
        if (!$sub_service) {
            throw new Exception("Invalid sub-service selection");
        }

        $price = $sub_service['price'];

        // Insert booking
        $stmt = $pdo->prepare("INSERT INTO bookings (user_id, service_id, sub_service_id, price, booking_date) 
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $service_id, $sub_service_id, $price, $booking_date]);

        $success = "Booking successful!";
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Book Appointment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff5f7;
            margin: 0;
        }

        .booking-container {
            max-width: 600px;
            margin: 50px auto;
            background-color: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #ff00c8;
            text-align: center;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #666;
            font-weight: bold;
        }

        select,
        input[type="date"],
        input[type="time"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #eee;
            border-radius: 8px;
            font-size: 16px;
            margin-bottom: 15px;
        }

        button {
            background-color: #ff00c8;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #c0007a;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .sub-service-group {
            display: none;
        }
    </style>
</head>

<body>
    <div class="booking-container">
        <h2>Book Service</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="service_id">Select Service</label>
                <select name="service_id" id="service_id" required>
                    <option value="">-- Choose a service --</option>
                    <?php foreach ($services as $service): ?>
                        <option value="<?= htmlspecialchars($service['id']) ?>">
                            <?= htmlspecialchars($service['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group sub-service-group">
                <label for="sub_service_id">Select Sub-Service</label>
                <select name="sub_service_id" id="sub_service_id" required>
                    <option value="">Select a service first</option>
                </select>
            </div>

            <div class="form-group">
                <label for="booking_date">Booking Date</label>
                <input type="date" name="booking_date" id="booking_date" required min="<?= date('Y-m-d') ?>">
            </div>

            <div class="form-group">
                <label for="booking_time">Booking Time</label>
                <input type="time" name="booking_time" id="booking_time" required min="09:00" max="18:00">
            </div>

            <button type="submit" name="book">Book Now</button>
        </form>
    </div>

    <script>
        const subServices = <?= json_encode($allSubServices) ?>;

        document.getElementById('service_id').addEventListener('change', function() {
            const serviceId = this.value;
            const subServiceGroup = document.querySelector('.sub-service-group');
            const subServiceSelect = document.getElementById('sub_service_id');

            subServiceSelect.innerHTML = '<option value="">-- Choose sub-service --</option>';

            if (serviceId && subServices[serviceId]) {
                subServiceGroup.style.display = 'block';
                subServices[serviceId].forEach(sub => {
                    const option = document.createElement('option');
                    option.value = sub.id;
                    option.textContent = `${sub.name} (Ksh ${parseFloat(sub.price).toFixed(2)})`;
                    subServiceSelect.appendChild(option);
                });
            } else {
                subServiceGroup.style.display = 'none';
            }
        });
    </script>
</body>
<?php include 'footer.php'; ?>

</html>