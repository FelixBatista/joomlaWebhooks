<?php
defined('_JEXEC') or die;

class WebhookHandler
{
    public static function sendWebhook($url, $data)
    {
        $jsonData = json_encode($data);

        // Initialize cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ]);

        // Execute the request and capture the response
        $result = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            // Handle the error as needed, e.g., log it or send an alert
        }

        curl_close($ch);

        if (isset($error_msg)) {
            // Return or handle the error message as needed
            return $error_msg;
        }

        return $result;
    }
}
