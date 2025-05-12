<?php
function getAccessToken() {
    $consumerKey = MPESA_CONSUMER_KEY;
    $consumerSecret = MPESA_CONSUMER_SECRET;
    $credentials = base64_encode("$consumerKey:$consumerSecret");

    // After $credentials = base64_encode(...)
    error_log("Consumer Key: ".MPESA_CONSUMER_KEY);
    error_log("Consumer Secret: ".MPESA_CONSUMER_SECRET);
    error_log("Base64 Credentials: ".$credentials);

    $url = MPESA_AUTH_URL;
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, ["Authorization: Basic $credentials"]);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // For testing only
    $response = curl_exec($curl);
    
    if(curl_errno($curl)) {
        error_log("Curl Error in getAccessToken: " . curl_error($curl));
        throw new Exception("Failed to get access token: " . curl_error($curl));
    }
    
    curl_close($curl);
    
    $result = json_decode($response);
    if (!$result || !isset($result->access_token)) {
        error_log("Invalid response from M-Pesa auth: " . $response);
        throw new Exception("Failed to get access token: Invalid response from M-Pesa");
    }
    
    return $result->access_token;
}
?> 