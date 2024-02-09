<?php
/**
 * @version		$Id: webhooks.php 06/02/2024 FelixBatista $
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

//Import extensions
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;
 
 // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
//jimport('joomla.plugin.plugin');
JLog::addLogger(array('text_file' => 'webhooks.log.php'), JLog::ALL, array('webhooks'));


 // Include the WebhookHandler class
 require_once(__DIR__ . '\webhook_handler.php');

class plgContentWebhooks extends JPlugin {

    private $config;

    public function onContentChangeState($context, $pks, $value)
    {
        // Access plugin parameters
        $webhookUrl = $this->params->get('webhook_url');
        $webhookMethod = $this->params->get('webhook_method');

        JLog::addLogger(array('text_file' => 'webhooks.log.php'), JLog::ALL, array('webhooks'));
        JLog::add('Detected a status change. Sending data to ' . $webhookUrl, JLog::INFO, 'webhooks');



        if ($context == 'com_content.article' && $value == 1) {  // here we check if its published
            JLog::add('Article was published', JLog::INFO, 'webhooks');
            foreach ($pks as $pk) {
                // Load the article object
                $article = JTable::getInstance('content');
                $article->load($pk);

                // Get Article URL
                $link = JRoute::_(ContentHelperRoute::getArticleRoute($article->id . ':' . $article->alias, $article->catid));

                // Fetch category title
                $category = JTable::getInstance('category');
                $category->load($article->catid);
                $categoryTitle = $category->title;

                // Fetch tags
                $tags = new JHelperTags;
                $tagList = $tags->getItemTags('com_content.article', $article->id);
                $tagNames = array();
                foreach ($tagList as $tag) {
                    $tagNames[] = $tag->title;
                }

                //Prepare Article body
                $body = $article->introtext . $article->fulltext;

                // Prepare your webhook data
                $data = [
                    'title' => $article->title,
                    'link' => $link,
                    'category' => $categoryTitle,
                    'tags' => $tagNames,
                    'body' => $body
                ];

                // Send the webhook
                $result = WebhookHandler::sendWebhook($webhookUrl, $data, $webhookMethod);
                JLog::add('POST sent: ' . $result, JLog::INFO, 'webhooks');
            }
        }
    } 
}