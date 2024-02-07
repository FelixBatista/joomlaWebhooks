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
 
 // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
//jimport('joomla.plugin.plugin');
JLog::addLogger(array('text_file' => 'webhooks.log.php'), JLog::ALL, array('webhooks'));


 // Include the WebhookHandler class
 require_once(__DIR__ . '\webhook_handler.php');

class plgContentWebhooks extends JPlugin {

    private $config;

    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
        
        // Load the configuration settings
        $this->config = include(__DIR__ . '\webhook_config.php');
    }

    public function onContentChangeState($context, $pks, $value)
    {
        JLog::add('Detected a status change', JLog::INFO, 'webhooks');

        if ($context == 'com_content.article' && $value == 1) {  // here we check if its published
            JLog::add('Article was published', JLog::INFO, 'webhooks');
            foreach ($pks as $pk) {
                // Load the article object
                $article = JTable::getInstance('content');
                $article->load($pk);

                // Prepare your webhook data
                $data = [
                    'title' => $article->title,
                    'id' => $article->id,
                    'state' => $article->state
                ];

                // Send the webhook
                $result = WebhookHandler::sendWebhook($this->config['webhookUrl'], $data);
                JLog::add('POST sent' . $result, JLog::INFO, 'webhooks');
            }
        }
    } 
    
    
    /* FREEZING THIS PART OF THE CODE FOR NOW
    //protected $autoloadLanguage = true;
    function onContentChangeState($context, $pks, $value) {
        $config = JFactory::getConfig();
        $user =& JFactory::getUser();   
        

        //$title = $article->title;       TODO: article was part of the onContentAfterSave, but it does not come with the OnContentChangesState. I need to find a way to add a title.
        $uri = & JFactory::getURI(); 
        $url = $uri->toString(); 
        $url = strtok($url, '?');
        
        
            $user =& JFactory::getUser();                             // TODO: is this relevant to keep?
            $user_eid = $user->get('username'); //This SHOULD be EID
            $user_name = $user->get('name');
            $user_email = $user->get('email');
        
        $body   = "Attention!  A Content change has been made on". $config->get( 'sitename' ).
                  "\n URL:".$url.
                  "\nUser:" .$user_name."\n".
                  "EID: " . $user_eid."\n".
                  "E-mail: " . $user_email."\n".
                  "Page Title:" . $title. "\n"
                  ."To stop receiving this e-mail please contact webmaster@cns.utexas.edu\n"
                  ."E-mail generated by Custom Plugin developed by CNS Web Team: 'Email Notification on Change'";  
        $subject = "Alert!  Change on: ". $config->get( 'sitename' );
        
        $to = array("email1@example.com", "email2@example.com");
        //set it all
        $mailer =& JFactory::getMailer();
		$mailer->setSender('email@example.com'); //from the person requesting access
		$mailer->addRecipient($to);
		$mailer->setSubject($subject);
		$mailer->setBody($body);
		$sent = $mailer->Send();
        
        if($sent !== true){
        JError::raiseNotice( 100, 'ERROR SENDING EMAIL TO COMMUNICATIONS COORDINATOR.' );    
        }
        else{
        JError::raiseNotice( 100, 'Communications Coordinator notified of changes.' );    
        }                
    }
    */
}