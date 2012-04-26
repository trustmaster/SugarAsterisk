<?php
/**
 * Asterisk SugarCRM Integration
 * (c) KINAMU Business Solutions AG 2009
 *
 * Parts of this code are (c) 2006. RustyBrick, Inc.  http://www.rustybrick.com/
 * Parts of this code are (c) 2008 vertico software GmbH
 * Parts of this code are (c) 2009 abcona e. K. Angelo Malaguarnera E-Mail admin@abcona.de
 * http://www.sugarforge.org/projects/yaai/
 * Parts of this code are (c) 2011 Vladimir Sibirov contact@kodigy.com
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

if(!defined('sugarEntry'))define('sugarEntry', true);

chdir("../");
chdir("../");
chdir("../");
chdir("../");

require_once('include/entryPoint.php');
require_once('modules/Contacts/Contact.php');
require_once('modules/Users/User.php');

session_start();

//�include language

$current_language = $_SESSION['authenticated_user_language'];
if(empty($current_language)) {
	$current_language = $sugar_config['default_language'];
}
require("custom/modules/Asterisk/language/" . $current_language . ".lang.php");
$cUser = new User();
$cUser->retrieve($_SESSION['authenticated_user_id']);

if (!$cUser) {
    header('403 Forbidden');
    echo '<h1>Forbidden</h1>';
    exit;
}

// Find the file
if (is_numeric($_GET['id'])) {
    $callID = $_GET['id'];
    // Load asterisk config from DB
    $sql_res = mysql_query("SELECT * FROM config WHERE category = 'asterisk'");
    while ($row = mysql_fetch_assoc($sql_res)) {
		$sugar_config['asterisk_' . $row['name']] = $row['value'];
    }
	$found = false;
    $files_wav = glob($sugar_config['asterisk_recordings'] . '/*' . $callID . '.wav');
	$files_mp3 = glob($sugar_config['asterisk_recordings'] . '/*' . $callID . '.mp3');
	if (count($files_wav) == 1 && filesize($files_wav[0]) > 0) {
		$path = $files_wav[0];
		$content_type = 'audio/wav';
		$file_ext = 'wav';
		$found = true;
	} elseif (count($files_mp3) == 1 && filesize($files_mp3[0]) > 0) {
		$path = $files_mp3[0];
		$content_type = 'audio/mpeg';
		$file_ext = 'mp3';
		$found = true;
	}
	
    if ($found) {
		header('Content-Type: ' . $content_type);
		header('Content-Length: ' . filesize($path));
		if ($_GET['dl'])
		{
			header('Content-Disposition: attachment; filename="call_'.$callID.'.'.$file_ext.'"');
		}
		readfile($path);
		exit;
    }
}

header('404 Not Found');
echo '<h1>File Not Found</h1>';
exit;

?>
