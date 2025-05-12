
<?php
require 'config.php';

// First line of callback.php
file_put_contents('callback.log', date('Y-m-d H:i:s')." - ".file_get_contents('php://input')."\n", FILE_APPEND);

// Get the raw callback data
$data = file_get_contents('php://input');

// Log raw callback data
$logEntry = "\n" . str_repeat('-', 80) . "\n";
$logEntry .= "[" . date('Y-m-d H:i:s') . "] Incoming Callback:\n" . $data . "\n";
file_put_contents(log_file, $logEntry, FILE_APPEND);

// Process the callback
$response = json_decode($data, true);

try {
    if(isset($response['Body']['stkCallback'])) {
        $callback = $response['Body']['stkCallback'];
        $checkoutRequestID = $callback['CheckoutRequestID'];
        $resultCode = $callback['ResultCode'];
        $resultDesc = $callback['ResultDesc'];
        
        // Log processed data
        $logEntry = "[" . date('Y-m-d H:i:s') . "] Processed Callback:\n";
        $logEntry .= "CheckoutRequestID: $checkoutRequestID\n";
        $logEntry .= "ResultCode: $resultCode\n";
        $logEntry .= "ResultDesc: $resultDesc\n";
        
        if($resultCode == 0) {
            $metadata = $callback['CallbackMetadata']['Item'];
            $amount = $metadata[0]['Value'];
            $mpesaCode = $metadata[1]['Value'];
            $phone = $metadata[3]['Value'];
            
            $logEntry .= "Successful Payment:\n";
            $logEntry .= "Amount: $amount\n";
            $logEntry .= "MpesaCode: $mpesaCode\n";
            $logEntry .= "Phone: $phone\n";
            
            // Update database here
            // $stmt = $pdo->prepare("UPDATE payments SET status='completed' ...");
            // $stmt->execute([...]);
        }
        
        file_put_contents(log_file, $logEntry, FILE_APPEND);
    }
} catch(Exception $e) {
    $errorLog = "[" . date('Y-m-d H:i:s') . "] Error processing callback: " . $e->getMessage() . "\n";
    file_put_contents(log_file, $errorLog, FILE_APPEND);
}

// Send response
header("Content-Type: application/json");
echo json_encode(["ResultCode" => 0, "ResultDesc" => "Success"]);