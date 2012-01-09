<?php

/**
 * Asterisk SugarCRM Integration
 * (c) KINAMU Business Solutions AG 2009
 *
 * Parts of this code are (c) 2006. RustyBrick, Inc.  http://www.rustybrick.com/
 * Parts of this code are (c) 2008 vertico software GmbH
 * Parts of this code are (c) 2009 abcona e. K. Angelo Malaguarnera E-Mail admin@abcona.de
 * http://www.sugarforge.org/projects/yaai/
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 *
 * You can contact KINAMU Business Solutions AG at office@kinamu.com
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 */
if (!defined('sugarEntry'))
    define('sugarEntry', true);

chdir("../");
chdir("../");
chdir("../");
chdir("../");
require_once('include/entryPoint.php');
require_once('modules/Users/User.php');

// get the Asterisk Detail from the Configuration
$server = $system_config->settings['asterisk_host'];
$port = (int) $system_config->settings['asterisk_port'];
$Username = "Username: " . $system_config->settings['asterisk_user'] . "\r\n";
$Secret = "Secret: " . $system_config->settings['asterisk_secret'] . "\r\n";
$context = $system_config->settings['asterisk_context'];

// start the Session ... get the User
session_start();
$cUser = new User();
$cUser->retrieve($_SESSION['authenticated_user_id']);

$extension = $cUser->asterisk_ext_c;
$channel = 'SIP/' . $cUser->asterisk_ext_c;


$socket = fsockopen($server, $port, $errno, $errstr, 20);

if (!$socket) {
    echo "errstr ($errno) <br>\n";
    echo "$server:$port <br>\n";
} else {
    // log on to Asterisk
    fputs($socket, "Action: Login\r\n");
    fputs($socket, $Username);
    fputs($socket, $Secret);
    fputs($socket, "\r\n");
    $result = fgets($socket, 128);
    echo($result);

    //format Phone Number
    $number = $_REQUEST['phoneNr'];
    $prefix = $system_config->settings['asterisk_prefix'];
    //$number = str_replace("+", "00", $number);
    $number = str_replace("+7", "8", $number);
    $number = str_replace("+", "", $number);
    $number = str_replace(array("(", ")", " ", "-", "/", "."), "", $number);
    $number = $prefix . $number;
    var_dump($number);

    // dial number
    fputs($socket, "Action: originate\r\n");
    fputs($socket, "Channel: " . $channel . "\r\n");
    fputs($socket, "Context: " . $context . "\r\n");
    fputs($socket, "Exten: " . $number . "\r\n");
    fputs($socket, "Priority: 1\r\n");
    fputs($socket, "Callerid:" . $_REQUEST['phoneNr'] . "\r\n");
    fputs($socket, "Variable: CALLERID(number)=" . $extension . "\r\n");
    fputs($socket, "Action: Logoff\r\n\r\n");
    fputs($socket, "\r\n");

    $result = fgets($socket, 128);
    var_dump($result);
    var_dump($channel);
    var_dump($context);
    var_dump($number);
    sleep(1);

    // close socket
    fclose($socket);
}
?>