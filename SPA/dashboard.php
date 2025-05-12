<?php
session_start();
require 'config.php';


if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit();
}


try {
  $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
  $stmt->execute([$_SESSION['user_id']]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die("Error fetching user data: " . $e->getMessage());
}

// Fetch bookings
try {
  $stmt = $pdo->prepare("SELECT * FROM bookings WHERE user_id = ? ORDER BY booking_date DESC");
  $stmt->execute([$_SESSION['user_id']]);
  $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die("Error fetching bookings: " . $e->getMessage());
}

// Get booking counts
try {
  // Upcoming bookings
  $stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE user_id = ? AND booking_date >= CURDATE()");
  $stmt->execute([$_SESSION['user_id']]);
  $upcomingCount = $stmt->fetchColumn();

  // Total bookings
  $stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE user_id = ?");
  $stmt->execute([$_SESSION['user_id']]);
  $totalCount = $stmt->fetchColumn();
} catch (PDOException $e) {
  $upcomingCount = 0;
  $totalCount = 0;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | Beauty Book</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Self-hosted SF Pro -->
  <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/sf-pro@4.0.0/index.css"> -->

  <!-- Apple San Francisco CDN -->
  <link rel="stylesheet" href="https://cdn.apple-livephotoskit.com/sf-fonts/v3.0/sf-font.css">
  <style>
    .body {
      background-color: #fff5f7;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .main-content {
      flex: 1;
      padding-bottom: 60px;
    }

    .footer {
      background-color: #ffffff;
      padding: 20px 0;
      margin-top: auto;
      box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
      position: relative;
      z-index: 100;
    }

    @media (max-width: 768px) {
      .container.pt-5 {
        padding-top: 2rem !important;
      }
    }

    .dashboard-nav {
      background-color: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(10px);
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .brand-text {
      color: #ff00c8 !important;
      font-weight: 700;
      letter-spacing: -0.5px;
    }

    .card-service {
      border: none;
      border-radius: 15px;
      transition: transform 0.3s ease;
      background-color: rgba(255, 156, 220, 0.19);
    }

    .card-service:hover {
      transform: translateY(-5px);
    }

    .btn-pink {
      border: solid 2px #ff00c8;
      color: #ff00c8 !important;
      padding: 0.8rem 1.5rem;
      border-radius: 8px;
      text-decoration: none;
    }

    .btna {
      border: solid 2px #ff00c8;
      color: #ff00c8 !important;
      padding: 0.1rem 0.2rem;
      border-radius: 8px;
      text-decoration: none;
    }

    .btnar {
      border: solid 2px rgb(255, 0, 0) !important;
      color: rgb(255, 0, 0) !important;
      padding: 0.1rem 0.2rem;
      border-radius: 8px;
      text-decoration: none;
    }

    .btn-pink:hover {
      background-color: rgb(255, 127, 208);
      color: white !important;
    }
  </style>
</head>

<body class="body">
  <!-- Navigation -->
  <?php include 'navbar.php'; ?>

  <div class="main-content">
    <div class="container pt-5 mt-5">
      <!-- Dashboard Header -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="d-flex justify-content-between align-items-center">
            <h1 class="brand-text">Your Dashboard</h1>
            <a href="book.php" class="btn-pink">
              <i class="fas fa-plus me-2"></i>Book Service
            </a>
          </div>
        </div>
      </div>

      <!-- Stats Overview -->
      <div class="row mb-4">
        <div class="col-md-4 mb-3">
          <div class="card-service p-3">
            <h5>Upcoming Appointments</h5>
            <h2 class="brand-text"><?= $upcomingCount ?></h2>
          </div>
        </div>
        <div class="col-md-4 mb-3">
          <div class="card-service p-3">
            <h5>Total Appointments</h5>
            <h2 class="brand-text"><?= $totalCount ?></h2>
          </div>
        </div>
        <div class="col-md-4 mb-3">
          <div class="card-service p-3">
            <h5>Loyalty Points</h5>
            <h2 class="brand-text">450</h2>
          </div>
        </div>
      </div>

      <!-- Appointments List -->
      <div class="row">
        <div class="col-12">
          <div class="card-service p-4">
            <h4 class="mb-4">Upcoming Appointments</h4>
            <?php if (count($bookings) > 0): ?>
              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th>Date</th>
                      <th>Time</th>
                      <th>Service</th>
                      <th>Location</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($bookings as $booking): ?>
                      <tr>
                        <td><?= date('M d, Y', strtotime($booking['booking_date'])) ?></td>
                        <td><?= date('h:i A', strtotime($booking['booking_time'])) ?></td>
                        <td><?= htmlspecialchars($booking['service_type']) ?></td>
                        <td><?= htmlspecialchars($booking['location'] ?? 'Bamburi-Mtambo') ?></td>
                        <td>
                          <span class="badge bg-<?= $booking['status'] === 'confirmed' ? 'success' : 'warning' ?>">
                            <?= ucfirst($booking['status']) ?>
                          </span>
                        </td>
                        <td>
                          <a href="checkout.php" class="btna">Checkout</a><br
                          <a href="#" class="btnar">Cancel</a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <div class="alert alert-info">No upcoming appointments found.</div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome (replace with your kit code) -->
    <script src="https://kit.fontawesome.com/your-kit-code.js" crossorigin="anonymous"></script>
  </div>
</body>

<?php include 'footer.php'; ?>

</html>