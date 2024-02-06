<?php
defined('_JEXEC') or die;
JLog::addLogger(array('text_file' => 'webhooks.log.php'), JLog::ALL, array('webhooks'));


return [
    'webhookUrl' => 'https://felixbatista.app.n8n.cloud/webhook-test/92775457-6d91-4d11-bd1f-4aaab5aaaa93'
    // You can add more configuration settings as needed
];