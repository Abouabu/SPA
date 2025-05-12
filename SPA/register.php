<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $email = trim($_POST['email']);
  $phone = trim($_POST['phone']);

  try {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);

    if ($stmt->rowCount() > 0) {
      $error = 'Username or email already exists';
    } else {
      $stmt = $pdo->prepare("INSERT INTO users (username, password, email, phone) VALUES (?, ?, ?, ?)");
      $stmt->execute([$username, $password, $email, $phone]);

      header('Location: login.php');
      exit();
    }
  } catch (PDOException $e) {
    $error = "Registration failed: " . $e->getMessage();
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register | Beauty Book</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background-image: url(images/spa.jpg);
      background-size: cover;
      background-attachment: fixed;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .register-container {
      max-width: 500px;
      margin: auto;
      background-color: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(10px);
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      padding: 2.5rem;
    }

    .brand-text {
      color: #ff00c8;
      font-weight: 700;
    }

    .btn-pink {
      background-color: #ff00c8;
      color: white;
      padding: 0.8rem 1.5rem;
      border-radius: 8px;
      border: none;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .btn-pink:hover {
      background-color: #c0007a;
      transform: translateY(-2px);
    }

    .form-control:focus {
      border-color: #ff00c8;
      box-shadow: 0 0 0 0.25rem rgba(255, 0, 200, 0.25);
    }

    .back-link {
      color: #ff00c8;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .back-link:hover {
      color: #c0007a;
      transform: translateX(-3px);
    }
  </style>
</head>

<body class="d-flex align-items-center py-4">

  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-6">
        <div class="register-container">
          <h1 class="text-center mb-4 brand-text">BEAUTY BOOK</h1>
          <h2 class="text-center mb-4">Create Your Account</h2>

          <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <?php echo $error; ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>

          <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="mb-3">
              <label for="username" class="form-label">Username</label>
              <input type="text" class="form-control form-control-lg" id="username" name="username" required>
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control form-control-lg" id="email" name="email" required>
            </div>

            <div class="mb-3">
              <label for="phone" class="form-label">Phone</label>
              <input type="text" class="form-control form-control-lg" id="phone" name="phone" required>
            </div>

            <div class="mb-4">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control form-control-lg" id="password" name="password" required>
            </div>

            <button type="submit" name="register" class="btn btn-pink w-100 btn-lg mb-3">
              <i class="fas fa-user-plus me-2"></i> Register
            </button>

            <div class="text-center mt-3">
              <p class="mb-1">Already have an account? <a href="login.php" class="text-decoration-none brand-text">Login here</a></p>
              <a href="index.php" class="back-link">
                <i class="fas fa-arrow-left me-1"></i> Back to Homepage
              </a>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap 5 JS Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>