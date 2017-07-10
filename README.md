# joomlaEmailNotification
Tested with Joomla v3.7.x
## Description
Sends an E-mail notification on saving articles in Joomla.

## Instructions
1. Update to e-mail in notify.php:
```php
$to = array("email1@example.com", "email2@example.com");
```
2. Update sender e-mail address in notify.php:
```php
$mailer->setSender('email@example.com');
```

3. Zip files up and use the Joomla Plugin Manager (upload install) to install.

4. Enable Email Notification on Change in the plugin manager.




By W. Ryan parker

MIT License -- 
