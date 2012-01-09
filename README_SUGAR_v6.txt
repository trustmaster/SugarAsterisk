*********************************************************************************

SugarCRM 6.1.2 adjustments

I editted some stuff in the code in an attempt to make it work on v6.1.2
I also tried to make it upgradesafe. I take no responsibility for system crashes, loss of data or nuclear warfare by installing this package.
Only tested it on 6.1.2 if you want to install it on a different version of Sugar 6, edit the manifest.php to include your version.
The manifest also includes a view.list.php to sort the calls on start date instead of the default settings, if you don't want this remove this 
				
				array (
                                    'from' => '<basepath>/SugarModules/modules/Calls/views/view.list.php',
                                    'to' => 'custom/modules/Calls/views/view.list.php',
                                    ), from the manifest.php

If it works then you're lucky, if it doesn't don't complain to me.(this package works on my configuration, you may have different results)
I also editted the asteriskLogger.php to make it compatible with AsteriskManager 1.1(Asterisk 1.6 and 1.8).
There are a bunch of straight to database delete queries in the logger.
If you are worried about that, don't use this package. 
If you are running AsteriskManager 1.0, you will need to replace the asteriskLogger.php with the one found in /asteriskManager 1.0
Follow the instructions from the YAAI installation and configuration.pdf for installation and configuration.
If you are planning on editting the calls and want to keep the correct duration, use the dropdown editor in studio and add 1 minute intervals to duration_interval. Or copy(the contents of) en_us.lang.php to custom/include/language/ (NOTE: if this file already exists, PLEASE copy the contents or you will lose your custom/editted dropdown lists.) 

Functions added:
- Open the memo attached to a call, with outgoing calls just hit the memo button any time you like.
With incoming calls only use this when the state is connected.Or when your extensions is going to pick up the call(this only applies if you have multiple extensions).
- close button on the popup
- check to see if a call is internal and then ignore the call and delete corresponding call record.

I want to thank the people @ abcona/kinamu for making this connector so that I didn't need to start from scratch to make asterisk work with Sugar 6.
There are some files(view files mostly) in the package that I made for back-up, it's the original files from the package in case you want to view them or alter them in any way.

**********************************************************************************
Asterisk 1.6 config.

In manager.conf put the user you created for asterisk in sugar.
Add hud to read and originate to write priveleges.
If your Sugar instance is running on a different server don't forget to permit that ip-adress

**********************************************************************************

SugarCRM 6.1.2 and Asterisk 1.6 adjustments made by
Sebastiaan Tieland

**********************************************************************************