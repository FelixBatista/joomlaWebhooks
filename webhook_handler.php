<?php
defined('_JEXEC') or die;

class WebhookHandler
{
    public static function sendWebhook($url, $data)
    {
        $jsonData = json_encode($data);

        // Use cURL to send data
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ]);

        // Execute the request
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
