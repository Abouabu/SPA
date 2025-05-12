<?php
session_start();
require 'config.php';

// In a real admin system, you would also check if the logged-in user is an administrator.

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid booking ID");
}

$booking_id = (int) $_GET['id'];
$error = '';
$success = '';

// Fetch booking details
try {
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ?");
    $stmt->execute([$booking_id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$booking) {
        die("Booking not found");
    }
} catch (PDOException $e) {
    die("Error fetching booking: " . $e->getMessage());
}

// Fetch all services
try {
    $serviceStmt = $pdo->query("SELECT id, name FROM services");
    $services = $serviceStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching services: " . $e->getMessage());
}

// Fetch all sub-services grouped by service_id
try {
    $subServiceStmt = $pdo->query("SELECT service_id, id, name, price FROM sub_services");
    $allSubServices = $subServiceStmt->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching sub-services: " . $e->getMessage());
}

// Process form submission on POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve values from form
    $service_id   = $_POST['service_id'] ?? '';
    $sub_service_id = $_POST['sub_service_id'] ?? '';
    $booking_date = $_POST['booking_date'] ?? '';
    $booking_time = $_POST['booking_time'] ?? '';
    $status       = $_POST['status'] ?? 'pending'; // expect 'pending', 'completed', or optionally 'cancelled'

    // Validate required fields
    if (empty($service_id) || empty($sub_service_id) || empty($booking_date) || empty($booking_time)) {
        $error = "Please fill in all required fields.";
    } else {
        // Validate that the sub-service belongs to the selected service and retrieve its price
        try {
            $stmt = $pdo->prepare("SELECT price FROM sub_services WHERE id = ? AND service_id = ?");
            $stmt->execute([$sub_service_id, $service_id]);
            $subService = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$subService) {
                throw new Exception("Invalid sub-service selection for the chosen service.");
            }
            $price = $subService['price'];
        } catch (Exception $ex) {
            $error = $ex->getMessage();
        }
    }

    // If no error so far, update the booking record
    if (empty($error)) {
        try {
            $updateStmt = $pdo->prepare("UPDATE bookings 
                SET service_id = ?, sub_service_id = ?, booking_date = ?, booking_time = ?, status = ?, price = ? 
                WHERE id = ?");
            $updateStmt->execute([$service_id, $sub_service_id, $booking_date, $booking_time, $status, $price, $booking_id]);
            $success = "Booking updated successfully.";
            // Reload updated booking data
            $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ?");
            $stmt->execute([$booking_id]);
            $booking = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $error = "Error updating booking: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Booking | Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f7f7;
            font-family: Arial, sans-serif;
            padding-top: 60px;
        }

        .container {
            max-width: 700px;
        }

        .form-control,
        .form-select {
            margin-bottom: 15px;
        }

        .btn-custom {
            background-color: #ff00c8;
            color: #fff;
            border: none;
        }

        .btn-custom:hover {
            background-color: #e600b8;
        }
    </style>
</head>

<body>
    <?php include 'admin_sidebar.php'; ?>
    <div class="container">
        <h1 class="mb-4">Edit Booking #<?= htmlspecialchars($booking_id) ?></h1>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="service_id" class="form-label">Service</label>
                <select name="service_id" id="service_id" class="form-select" required>
                    <option value="">Select Category</option>
                    <?php foreach ($services as $service): ?>
                        <option value="<?= htmlspecialchars($service['id']) ?>" <?= ($service['id'] == $booking['service_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($service['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="sub_service_id" class="form-label">Service</label>
                <select name="sub_service_id" id="sub_service_id" class="form-select" required>
                    <option value="">Select a Service</option>
                    <?php
                    $currentServiceId = $booking['service_id'];
                    if (isset($allSubServices[$currentServiceId])):
                        foreach ($allSubServices[$currentServiceId] as $sub) : ?>
                            <option value="<?= htmlspecialchars($sub['id']) ?>" <?= ($sub['id'] == $booking['sub_service_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($sub['name']) ?> (Ksh <?= number_format($sub['price'], 2) ?>)
                            </option>
                    <?php endforeach;
                    endif;
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="booking_date" class="form-label">Date</label>
                <input type="date" name="booking_date" id="booking_date" class="form-control" value="<?= htmlspecialchars($booking['booking_date']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="booking_time" class="form-label">Time</label>
                <input type="time" name="booking_time" id="booking_time" class="form-control" value="<?= htmlspecialchars($booking['booking_time']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Booking Status</label>
                <select name="status" id="status" class="form-select" required>
                    <!-- Note: Ensure your bookings table allows the 'cancelled' status if needed -->
                    <option value="pending" <?= ($booking['status'] === 'pending') ? 'selected' : '' ?>>Pending</option>
                    <option value="completed" <?= ($booking['status'] === 'completed') ? 'selected' : '' ?>>Completed</option>
                    <option value="cancelled" <?= ($booking['status'] === 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-custom">Update Booking</button>
                <!-- Optionally, you can also have a separate Cancel button that sets the status to cancelled -->
                <a href="cancel_booking.php?id=<?= htmlspecialchars($booking_id) ?>" class="btn btn-outline-danger ms-2">Cancel Booking</a>
            </div>
        </form>
    </div>

    <!-- JavaScript for dynamically loading sub-services based on the selected service -->
    <script>
        const allSubServices = <?= json_encode($allSubServices) ?>;

        document.getElementById('service_id').addEventListener('change', function() {
            const serviceId = this.value;
            const subServiceSelect = document.getElementById('sub_service_id');
            subServiceSelect.innerHTML = '<option value="">Select Sub-Service</option>';
            if (serviceId && allSubServices[serviceId]) {
                allSubServices[serviceId].forEach(function(sub) {
                    const option = document.createElement('option');
                    option.value = sub.id;
                    option.textContent = `${sub.name} (Ksh ${parseFloat(sub.price).toFixed(2)})`;
                    subServiceSelect.appendChild(option);
                });
            }
        });
    </script>
</body>

</html>