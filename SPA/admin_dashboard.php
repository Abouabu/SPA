<?php
session_start();
require 'config.php';

// Check admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Fetch dashboard metrics (example queries - modify as needed)
try {
    // Total Customers
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $totalCustomers = $stmt->fetchColumn();

    // Total Appointments
    $stmt = $pdo->query("SELECT COUNT(*) FROM bookings");
    $totalAppointments = $stmt->fetchColumn();

    // ... Add other metric queries ...

} catch (PDOException $e) {
    die("Error fetching metrics: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Beauty Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-width: 280px;
            --primary-color: #ff00c8;
            --hover-color: #c0007a;
        }

        body {
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            background: #343a40;
            color: white;
            padding: 20px;
            overflow-y: auto;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            padding: 20px;
            transition: margin 0.3s;
        }

        .metric-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .metric-card:hover {
            transform: translateY(-5px);
        }

        .metric-value {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        @media (max-width: 768px) {
            .sidebar {
                position: static;
                width: 100%;
                height: auto;
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
    <?php include 'admin_sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Header -->
        <header class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Dashboard Overview</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item active">Admin Dashboard</li>
                    </ol>
                </nav>
            </div>

            <div class="d-flex gap-2">
                <form class="d-flex">
                    <input type="search" class="form-control" placeholder="Search appointments...">
                    <button class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </header>

        <!-- Metrics Grid -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card metric-card h-100">
                    <div class="card-body text-center">
                        <div class="metric-value"><?= $totalCustomers ?></div>
                        <h5 class="text-muted mb-0">Total Customers</h5>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card metric-card h-100">
                    <div class="card-body text-center">
                        <div class="metric-value"><?= $totalAppointments ?></div>
                        <h5 class="text-muted mb-0">Total Appointments</h5>
                    </div>
                </div>
            </div>

            <!-- Add other metrics following the same pattern -->
        </div>

        <!-- Recent Appointments Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Appointments</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Customer</th>
                                <th>Service</th>
                                <th>Booking Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                // Get bookings with user and service information
                                $stmt = $pdo->query("
                SELECT 
                    b.id,
                    u.username AS customer,
                    s.name AS service,
                    b.booking_date
                FROM bookings b
                JOIN users u ON b.user_id = u.id
                JOIN services s ON b.service_id = s.id
                ORDER BY b.booking_date DESC
            ");
                                $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                if (empty($bookings)) {
                                    echo '<tr><td colspan="5" class="text-center">No bookings found</td></tr>';
                                } else {
                                    foreach ($bookings as $booking) {
                            ?>
                                        <tr>
                                            <td><?= htmlspecialchars($booking['id']) ?></td>
                                            <td><?= htmlspecialchars($booking['customer']) ?></td>
                                            <td><?= htmlspecialchars($booking['service']) ?></td>
                                            <td><?= date('M d, Y h:i A', strtotime($booking['booking_date'])) ?></td>
                                            <td>
                                                <div class="btn-group">

                                                    <a href="edit_booking.php?id=<?= $booking['id'] ?>" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit">&emsp;Edit</i>
                                                    </a>
                                                    <a href="delete_booking.php?id=<?= $booking['id'] ?>" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Are you sure you want to delete this booking?')">
                                                        <i class="fas fa-trash">&emsp; Delete</i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                            <?php
                                    }
                                }
                            } catch (PDOException $e) {
                                echo '<tr><td colspan="5" class="text-center text-danger">Error loading bookings</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>