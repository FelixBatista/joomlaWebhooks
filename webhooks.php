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

//Import extensions
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use Joomla\CMS\Uri\Uri;
 
 // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
//jimport('joomla.plugin.plugin');
JLog::addLogger(array('text_file' => 'webhooks.log.php'), JLog::ALL, array('webhooks'));


 // Include the WebhookHandler class
 require_once(__DIR__ . '/webhook_handler.php');

class plgContentWebhooks extends JPlugin {

    private $config;

    /**
     * Check if the category ID is allowed based on plugin configuration.
     *
     * @param int|string $catId
     * @return bool
     */
    private function isCategoryAllowed($catId) {
        $allowedCategories = $this->params->get('categories');

        // If no categories are configured (or setting is empty), allow all categories
        if (empty($allowedCategories)) {
            return true;
        }

        if (!is_array($allowedCategories)) {
            $allowedCategories = array($allowedCategories);
        }

        // Filter out empty string/null values (e.g. "All Categories" option selected)
        $allowedCategories = array_filter($allowedCategories, function($val) {
            return $val !== '' && $val !== null;
        });

        if (empty($allowedCategories)) {
            return true;
        }

        return in_array((string)$catId, array_map('strval', $allowedCategories), true);
    }

    private function sendWebhookForArticle($articleId) {
        // Access plugin parameters
        $webhookUrl = $this->params->get('webhook_url');
        $webhookMethod = $this->params->get('webhook_method');

        // Get Website URL
        $baseUrl = JUri::root();

        // Load the article object
        $article = JTable::getInstance('content');
        $article->load($articleId);

        // Check if article state is published
        if (!isset($article->state) || (int) $article->state !== 1) {
            JLog::add('Article (ID: ' . $articleId . ') state is not published. Webhook skipped.', JLog::INFO, 'webhooks');
            return true;
        }

        // Check if category is allowed before preparing payload and cURL request
        if (!$this->isCategoryAllowed($article->catid)) {
            JLog::add('Article (ID: ' . $articleId . ') category (' . $article->catid . ') is not in allowed categories. Webhook skipped.', JLog::INFO, 'webhooks');
            return true;
        }

        // Get Article URL
        $relativeUrl = RouteHelper::getArticleRoute($article->id . ':' . $article->alias, $article->catid, $article->language);
        $link = rtrim($baseUrl, '/') . '/' . ltrim($relativeUrl, '/');
        JLog::add('Link' . $link, JLog::INFO, 'webhooks');

        // Fetch category title
        $category = JTable::getInstance('category');
        $category->load($article->catid);
        $categoryTitle = $category->title;

        // Fetch tags
        $tagsHelper = new JHelperTags();
        $tagList = $tagsHelper->getItemTags('com_content.article', $article->id);
        $tagNames = array();
        foreach ($tagList as $tag) {
            $tagNames[] = $tag->title;
        }

        // Prepare Article body
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
        WebhookHandler::sendWebhook($webhookUrl, $data, $webhookMethod);
    }


    /* I Noticed that if you opne an article, change the status there and then save it, the onContentChangeSate does not detect as a state change and then nothing happens.
        Here we then track the save events as well. */

    // Detect save events
    public function onContentAfterSave($context, $article, $isNew) {
        if ($context !== 'com_content.article') {
            return true;
        }

        // Check if article is published (state == 1)
        if (!isset($article->state) || (int) $article->state !== 1) {
            return true;
        }

        // Check if article category is allowed
        if (!$this->isCategoryAllowed($article->catid)) {
            JLog::add('Article category (' . $article->catid . ') is not in allowed categories. Webhook skipped.', JLog::INFO, 'webhooks');
            return true;
        }

        $webhookUrl = $this->params->get('webhook_url');
        JLog::add('Detected a save event to a published state. Sending data to ' . $webhookUrl, JLog::INFO, 'webhooks');
        $this->sendWebhookForArticle($article->id);

        return true;
    }

    // Detect state change events, ie. Publish, unpublish
    public function onContentChangeState($context, $pks, $value)
    {   
        if ($context !== 'com_content.article' || (int) $value !== 1) {
            return true;
        }

        $webhookUrl = $this->params->get('webhook_url');
        JLog::add('Detected a status change to published. Sending data to ' . $webhookUrl, JLog::INFO, 'webhooks');
        foreach ($pks as $pk) {
            $this->sendWebhookForArticle($pk);
        }

        return true;
    }
}
