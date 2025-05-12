<?php
session_start();
require 'config.php';

// Verify admin access
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$error = '';
$success = '';

// Handle appointment deletion
if (isset($_GET['delete'])) {
    $booking_id = (int)$_GET['delete'];

    try {
        $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
        $stmt->execute([$booking_id]);
        $success = 'Appointment deleted successfully';
    } catch (PDOException $e) {
        $error = 'Error deleting appointment: ' . $e->getMessage();
    }
}

// Fetch all appointments
try {
    $stmt = $pdo->query("
        SELECT 
            b.id,
            u.username AS customer,
            s.name AS service,
            b.booking_date,
            b.booking_time,
            b.created_at
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN services s ON b.service_id = s.id
        ORDER BY b.booking_date DESC, b.booking_time DESC
    ");
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching appointments: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointments - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <?php include 'admin_sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <h2 class="h3 mb-4">Manage Appointments</h2>

                <!-- Alerts -->
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($success) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Appointments Table -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Customer</th>
                                        <th>Service</th>
                                        <th>Appointment Date</th>
                                        <th>Appointment Time</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($appointments)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No appointments found</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($appointments as $appointment): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($appointment['customer']) ?></td>
                                                <td><?= htmlspecialchars($appointment['service']) ?></td>
                                                <td><?= date('M d, Y', strtotime($appointment['booking_date'])) ?></td>
                                                <td><?= date('h:i A', strtotime($appointment['booking_time'])) ?></td>
                                                <td><?= date('M d, Y H:i', strtotime($appointment['created_at'])) ?></td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <a href="view_appointment.php?id=<?= $appointment['id'] ?>"
                                                            class="btn btn-sm btn-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="?delete=<?= $appointment['id'] ?>"
                                                            class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Are you sure you want to delete this appointment?')">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>