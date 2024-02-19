<?php
/**
 * @version		$Id: webhooks.php 06/02/2024 FelixBatista $
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GPLv2, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

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
