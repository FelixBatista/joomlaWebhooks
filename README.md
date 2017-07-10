# joomlaEmailNotification
## Tested with Joomla v3.7.x
Sends an E-mail notification on saving content.  

Zip files up and use the Joomla Plugin Manager (upload install) to install.

To change where the e-mail goes update line 47

$to = array("webmaster@cns.utexas.edu", "vabagiu@austin.utexas.edu");

To update sender, change line 50:

$mailer->setSender('webmaster@cns.utexas.edu');

By W. Ryan parker

MIT License -- 
