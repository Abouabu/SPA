<?php
session_start();
require 'config.php';
require 'access_token.php'; // Ensure this generates/returns the access token

$error = '';
$success = '';
$amount = 1; // Replace with dynamic amount from your system

// Process M-Pesa payment initiation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['initiate_payment'])) {
    $phone = trim($_POST['phone']);
    
    // Validate phone number
    if (empty($phone) || !preg_match('/^254(7[0-9]{8}|11[0-9]{8})$/', $phone)) {
        $error = "Please enter a valid phone number in format 2547XXXXXXXX or 25411XXXXXXXX";
    } else {
        try {
            // Initiate STK Push
            $access_token = getAccessToken();
            
            $url = MPESA_STK_PUSH_URL;
            $timestamp = date('YmdHis');
            $password = base64_encode(MPESA_SHORTCODE . MPESA_PASSKEY . $timestamp);
            
            $data = [
                'BusinessShortCode' => MPESA_SHORTCODE,
                'Password' => $password,
                'Timestamp' => $timestamp,
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => $amount,
                'PartyA' => $phone,
                'PartyB' => MPESA_SHORTCODE,
                'PhoneNumber' => $phone,
                'CallBackURL' => MPESA_CALLBACK_URL,
                'AccountReference' => 'AppointmentPayment',
                'TransactionDesc' => 'Payment for Appointment'
            ];

            $headers = [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $access_token
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For testing only
            
            $response = curl_exec($ch);
            
            if(curl_errno($ch)) {
                throw new Exception("Curl Error: " . curl_error($ch));
            }
            
            curl_close($ch);

            $response = json_decode($response, true);
            
            // Add this for debugging
            error_log("STK Push Request Data: " . json_encode($data));
            error_log("Full API Response: " . print_r($response, true));

            if (!isset($response['ResponseCode'])) {
                $error = "Invalid API response. Check server logs.";
            }   
                        
                        // Log the response for debugging
            error_log("M-Pesa STK Push Response: " . json_encode($response));
            
            if (isset($response['ResponseCode']) && $response['ResponseCode'] == "0") {
                $_SESSION['CheckoutRequestID'] = $response['CheckoutRequestID'];
                $success = "Payment request sent to your phone. Please complete the payment in your M-Pesa menu.";
            } else {
                $error = "Failed to initiate payment: " . ($response['errorMessage'] ?? 'Unknown error');
                if (isset($response['ResponseDescription'])) {
                    $error .= " - " . $response['ResponseDescription'];
                }
            }
        } catch (Exception $e) {
            error_log("Payment Initiation Error: " . $e->getMessage());
            $error = "Failed to initiate payment: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout - MPesa Payment</title>
    <style>
        
        .phone-input {
            margin-bottom: 15px;
        }
        .phone-input input {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        

<head>
    <meta charset="UTF-8">
    <title>Checkout - MPesa Payment</title>
    <style>
        .body {
            font-family: Arial, sans-serif;
            background-color: #fff5f7;
            margin: 0;
            padding: 0;
        }

        .checkout-container {
            max-width: 500px;
            margin: 6rem auto;
            background-color: rgba(255, 255, 255, 0.28);
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #00796b;
            text-align: center;
        }

        .instruction {
            background-color: #e0f2f1;
            border: 1px solid #b2dfdb;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }

        input[type="text"] {
            width: 100%;
            padding: 8px 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .btna {
            display: block;
            width: 100%;
            padding: 12px;
            color: #ff00c8;
            border: solid 2px #ff00c8;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
        }

        .btna:hover {
            background-color: rgba(255, 0, 200, 0.17);
        }

        .alert {
            padding: 12px 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body class="body">
    <?php include 'navbar.php'; ?>
    <div class="checkout-container">
        <h2>Checkout</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <div id="payment-status" style="text-align: center; margin-top: 20px;">
                <p>Waiting for payment confirmation...</p>
                <!-- Add loading spinner or polling mechanism -->
            </div>
        <?php else: ?>
            <div class="instruction">
                <p>Complete payment using M-Pesa:</p>
                <ol>
                    <li>Enter your M-Pesa registered phone number</li>
                    <li>Check your phone for a payment prompt</li>
                    <li>Enter your M-Pesa PIN to complete payment</li>
                </ol>
            </div>

            <form method="POST">
                <div class="phone-input">
                    <label for="phone">M-Pesa Phone Number (2547XXXXXXXX):</label>
                    <input type="text" id="phone" name="phone" required 
                           pattern="2547[0-9]{8}" 
                           title="Enter phone number in format 2547XXXXXXXX">
                </div>
                <button type="submit" name="initiate_payment" class="btna">
                    Initiate M-Pesa Payment
                </button>
            </form>
        <?php endif; ?>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>