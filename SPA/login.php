<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $password = $_POST['password'];

  try {
    $stmt = $pdo->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
      // Set session variables
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['user_role'] = $user['role']; // This is the new line

      // Redirect to appropriate dashboard
      header('Location: ' . ($user['role'] === 'admin' ? 'admin_dashboard.php' : 'dashboard.php'));
      exit();
    } else {
      $error = "Invalid username or password";
    }
  } catch (PDOException $e) {
    $error = "Login failed: " . $e->getMessage();
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Beauty Book</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-image: url(images/spa.jpg);
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background-size: cover;
      min-height: 100vh;
      display: flex;
      align-items: center;
    }

    .login-container {
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(10px);
      border-radius: 1rem;
      padding: 2rem;
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .brand-text {
      color: #ff00c8;
      font-weight: 700;
      letter-spacing: -0.5px;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-md-8 col-lg-6">
        <div class="login-container">
          <h1 class="text-center mb-4 brand-text">BEAUTY BOOK</h1>
          <h2 class="text-center mb-4">Welcome Back</h2>

          <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
          <?php endif; ?>

          <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="mb-3">
              <label for="username" class="form-label">Username</label>
              <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-4">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100" style="background-color: #ff00c8; border: none;">Login</button>
          </form>

          <div class="mt-4 text-center">
            <p class="mb-2">New user? <a href="register.php" class="text-decoration-none" style="color: #ff00c8;">Create account</a></p>
            <a href="index.php" class="text-decoration-none" style="color: #ff00c8;">← Back to Homepage</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>