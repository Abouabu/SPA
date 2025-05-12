<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <style>
        body{
    background-image: url('images/spa.jpg');
    background-size: cover;
}

        .logo {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    margin-right: 15px;
}
        .login-form {
            width: 300px;
            margin: 100px auto;
            background-color: gray;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .login-form input[type="text"],
        .login-form input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .login-form button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container">
        
    <div class="login-form">
    <div class="sidebar">
            <div class="sidebar-header">
                <img src="images/logo.png" alt="Beauty Saloon Logo" class="logo">
                <span class="salon-name">Beauty Book</span>
            </div>
        <h2>Admin Login</h2>
        <form action="admin-dashboard.php" method="post">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>