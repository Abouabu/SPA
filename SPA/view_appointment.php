<?php
session_start();
require 'config.php';

// Verify admin access
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Get appointment ID from URL
$appointment_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

// Fetch appointment details
try {
    $stmt = $pdo->prepare("
    SELECT 
        b.*,
        u.username AS customer_name,
        u.email AS customer_email,
        u.phone AS customer_phone,
        s.name AS service_name,
        s.description AS service_description,
        ss.name AS sub_service_name
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN services s ON b.service_id = s.id
    LEFT JOIN sub_services ss ON b.sub_service_id = ss.id
    WHERE b.id = ?
");

    $stmt->execute([$appointment_id]);
    $appointment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$appointment) {
        throw new Exception('Appointment not found');
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Appointment - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <?php include 'admin_sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h3">Appointment Details</h2>
                    <a href="appointments.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Appointments
                    </a>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <!-- Customer Information -->
                            <div class="col-md-6 mb-4">
                                <h4 class="mb-3 text-primary">
                                    <i class="fas fa-user me-2"></i>Customer Details
                                </h4>
                                <dl class="row">
                                    <dt class="col-sm-4">Name:</dt>
                                    <dd class="col-sm-8"><?= htmlspecialchars($appointment['customer_name']) ?></dd>

                                    <dt class="col-sm-4">Email:</dt>
                                    <dd class="col-sm-8"><?= htmlspecialchars($appointment['customer_email']) ?></dd>

                                    <dt class="col-sm-4">Phone:</dt>
                                    <dd class="col-sm-8"><?= htmlspecialchars($appointment['customer_phone']) ?></dd>
                                </dl>
                            </div>

                            <!-- Service Information -->
                            <div class="col-md-6 mb-4">
                                <h4 class="mb-3 text-primary">
                                    <i class="fas fa-concierge-bell me-2"></i>Service Details
                                </h4>
                                <dl class="row">
                                    <dt class="col-sm-4">Category:</dt>
                                    <dd class="col-sm-8"><?= htmlspecialchars($appointment['service_name']) ?></dd>

                                    <dt class="col-sm-4">Description:</dt>
                                    <dd class="col-sm-8"><?= htmlspecialchars($appointment['service_description']) ?></dd>
                                    <dt class="col-sm-4">Service:</dt>
                                    <dd class="col-sm-8"><?= htmlspecialchars($appointment['sub_service_name']) ?: 'N/A' ?></dd>
                                    <dt class="col-sm-4">Amount:</dt>
                                    <dd class="col-sm-8">KSh <?= number_format($appointment['price'], 2) ?></dd>

                                </dl>
                            </div>
                        </div>

                        <!-- Appointment Details -->
                        <div class="row">
                            <div class="col-12">
                                <h4 class="mb-3 text-primary">
                                    <i class="fas fa-calendar-alt me-2"></i>Appointment Information
                                </h4>
                                <dl class="row">
                                    <dt class="col-sm-2">Appointment Date:</dt>
                                    <dd class="col-sm-4">
                                        <?= date('l, F j, Y', strtotime($appointment['booking_date'])) ?>
                                    </dd>

                                    <dt class="col-sm-2">Appointment Time:</dt>
                                    <dd class="col-sm-4">
                                        <?= date('h:i A', strtotime($appointment['booking_time'])) ?>
                                    </dd>

                                    <dt class="col-sm-2">Booked On:</dt>
                                    <dd class="col-sm-4">
                                        <?= date('M j, Y H:i', strtotime($appointment['created_at'])) ?>
                                    </dd>

                                    <dt class="col-sm-2">Status:</dt>
                                    <dd class="col-sm-4">
                                        <span class="badge bg-<?= $appointment['status'] === 'confirmed' ? 'success' : 'warning' ?>">
                                            <?= ucfirst($appointment['status']) ?>
                                        </span>
                                    </dd>
                                </dl>
                            </div>
                        </div>

                        <!-- Additional Notes -->
                        <?php if (!empty($appointment['notes'])): ?>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h4 class="mb-3 text-primary">
                                        <i class="fas fa-sticky-note me-2"></i>Additional Notes
                                    </h4>
                                    <div class="border p-3 rounded">
                                        <?= nl2br(htmlspecialchars($appointment['notes'])) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>