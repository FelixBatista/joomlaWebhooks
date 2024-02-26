# joomlaWebhooks
Tested with Joomla v5.0.2
## Description
"Content Webhooks for Joomla" is a powerful plugin designed for Joomla! websites that automatically sends webhooks when articles are published. It provides real-time integration capabilities, enabling your Joomla site to communicate instantly with external systems or applications whenever content changes.

## Features
- Automatic Webhooks: Sends data to a specified URL via webhooks when an article is published or updated.
- Flexible Configuration: Easily set webhook URLs and methods (GET, POST) through the Joomla administrator interface.
- Detailed Payloads: Includes comprehensive article information in webhook payloads, such as title, link, category, tags, and full article body.
- SEO-Friendly URLs: Generates absolute, SEO-friendly URLs for articles in webhook payloads.

## Requirements
- Joomla! 3.9 or later
- PHP 7.2 or later

## Instructions
1. Download plugin package as zip.

2. Log in your Joomla! administrator panel.

3. Navigate to Extensions -> Manage -> Install.

4. Upload and install the downloaded package.

5. Go to Extensions -> Plugins.

6. Set the "Webhook URL" to the URL where the webhook payloads should be sent.

7. Choose the "Webhook Method" (GET or POST) according to your requirements.

8. Enable Webhooks Plugin.

9. Save. Now you are good to go!

## Usage
Once configured, the plugin will automatically send webhooks in real-time whenever an article's status is changed to published. The webhook payload includes detailed information about the article, formatted in JSON.


By Felix Batista

Inspired by W. Ryan parker on https://github.com/wrparker/joomlaEmailNotification