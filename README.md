# joomlaEmailNotification
## Tested with Joomla v3.7.x
Sends an E-mail notification on saving content.  

Zip files up and use the Joomla Plugin Manager (upload install) to install.

To change where the e-mail goes update line 47

$to = array("email1@example.com", "email2@example.com");

To update sender, change line 50:

$mailer->setSender('email@example.com');

By W. Ryan parker

MIT License -- 
