<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'beauty_book');
define('DB_USER', 'root');
define('DB_PASS', '');

// M-Pesa configuration
define('MPESA_SHORTCODE', '174379'); // Test: 174379, Production: Your shortcode
define('MPESA_PASSKEY', 'bfb279f9aa9bdbcf158e97dd7129070efdf28aab');
define('MPESA_CONSUMER_KEY', 'lZW22w9Bf0fKeWyzQ0jJCGXJhd2NU0y56vxR6hQgwVAOqSC2'); // From Daraja dashboard
define('MPESA_CONSUMER_SECRET', 'OmGzk50EUC8l4YvNbkR9BgUqwZK5qjCzvs36CAYBYgZClneE1qa7TniEV7oA9sij'); // From Daraja dashboard
define('MPESA_CALLBACK_URL', 'https://1370-154-159-254-189.ngrok-free.app/callback.php');
define('MPESA_LOG_FILE', __DIR__.'/mpesa.log');

// API endpoints
define('MPESA_AUTH_URL', 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
define('MPESA_STK_PUSH_URL', 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest');

// Configuration function
function config($key) {
    switch($key) {
        case 'shortcode':
            return MPESA_SHORTCODE;
        case 'passkey':
            return MPESA_PASSKEY;
        case 'callback_url':
            return MPESA_CALLBACK_URL;
        default:
            throw new Exception("Unknown configuration key: $key");
    }
}

try {
    $pdo = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8",
        DB_USER,
        DB_PASS
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}