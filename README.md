# Asterisk module for SugarCRM 6 #

This is a modified version of SugarAsterisk module originally made by KINAMU/abcona, which can be found at http://www.sugarforge.org/projects/yaai

## Features ##

* Adds buttons to make calls via Asterisk from SugarCRM in Contacts, Employees, Accounts, Contacts and Users modules.
* Opens a popup with caller info when inbound call is received at Asterisk.
* Registers Asterisk call details in SugarCRM calls module.
* Provides downloads of recorded calls in a browser.

## Requirements ##

* Asterisk 1.8 with Asterisk Manager enabled
* SugarCRM 6.2+
* PHP 5.2+
* MySQL 5.0+

## Installation ##

1. Download this repository as a .zip package.
2. Install the module via SugarCRM Administration / Module Wizard.
3. Copy AsteriskManager/asteriskLogger.php to some folder on your webserver.
4. Copy init.d/asterisk_logger to /etc/init.d on your webserver. Edit this file, find DAEMON path and replace /var/www/crm/AsteriskManager/ with your path to asteriskLogger.php.
5. Make sure asterisk_logger starts on system boot and run it at once: /etc/init.d/asterisk_logger start
6. Go to SugarCRM Administration / ASTERISK Configuration and put your parameters there.

## Important notes ##

* Make sure Asterisk Manager is enabled on asterisk server and is accessible from your webserver, otherwise asteriskLogger.php won't be able to listen to Asterisk events.
* If you want call records (.wav) to be available for download, then make sure Asterisk Monitor is enabled. Then you should mount /var/spool/asterisk/monitor to a directory on your webserver running SugarCRM (by default it is /mnt/asterisk).

## Changelog ##

v1.2 for v6.2 (by Vladimir Sibirov):
* Updated asteriskLogger for Asterisk 1.8 protocol.
* Some fixes to work with SugarCRM 6.2 (tested on 6.2.1 and 6.2.4)
* Improved asteriskLogger persistence (automatically reconnects when MySQL or SOAP connection is lost).
* More fancy popups on inbound calls using jQuery.
* Finetuned details in Calls module.
* Added Listen and Download options for calls.
* Tweaks to work with Russian telephony network.
* Added missing call buttons in Employees module.
* Probably some more which are not mentioned here.
