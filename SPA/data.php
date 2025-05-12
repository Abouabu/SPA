<?php
$servername = "localhost";
$username = "your_username";
$password = "your_password";
$dbname = "your_database";

$conn = new mysqli("localhost", "root", "", "spa_salon");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Total Customers
$sql = "SELECT COUNT(*) AS total FROM customers";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$totalCustomers = $row['total'];

// Total Appointments
$sql = "SELECT COUNT(*) AS total FROM appointments";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$totalAppointments = $row['total'];

// Accepted Appointments
$sql = "SELECT COUNT(*) AS total FROM appointments WHERE status = 'accepted'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$acceptedAppointments = $row['total'];

// Rejected Appointments
$sql = "SELECT COUNT(*) AS total FROM appointments WHERE status = 'rejected'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$rejectedAppointments = $row['total'];

// Today Sales
$today = date('Y-m-d');
$sql = "SELECT SUM(amount) AS total FROM sales WHERE date = '$today'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$todaySales = $row['total'] ?? 0; // Use null coalescing operator

// Yesterday Sales
$yesterday = date('Y-m-d', strtotime('-1 day'));
$sql = "SELECT SUM(amount) AS total FROM sales WHERE date = '$yesterday'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$yesterdaySales = $row['total'] ?? 0;

// Last Seven Days Sales
$sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
$sql = "SELECT SUM(amount) AS total FROM sales WHERE date >= '$sevenDaysAgo'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$lastSevenDaysSales = $row['total'] ?? 0;

// Total Sales
$sql = "SELECT SUM(amount) AS total FROM sales";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$totalSales = $row['total'] ?? 0;

$conn->close();
?>