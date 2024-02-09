<?php
defined('_JEXEC') or die;
JLog::addLogger(array('text_file' => 'webhooks.log.php'), JLog::ALL, array('webhooks'));


class WebhookHandler
{
    public static function sendWebhook($url, $data, $webhookMethod)
    {
        $jsonData = json_encode($data);

        // Initialize cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $webhookMethod);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ]);

        // Execute the request and capture the response
        $result = curl_exec($ch);
        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Check for cURL errors
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            JLog::add('Error sending webhook: ' . $error_msg, JLog::ERROR, 'webhooks');
        } elseif ($httpStatusCode < 200 || $httpStatusCode >= 300) {
            // Handle HTTP response error
            JLog::add('Webhook POST failed, HTTP status code: ' . $httpStatusCode . ', Response: ' . $result, JLog::ERROR, 'webhooks');
        } else {
            JLog::add('Webhook POST successful, HTTP status code: ' . $httpStatusCode, JLog::INFO, 'webhooks');
        }

        curl_close($ch);

        return $result;
    }
}
