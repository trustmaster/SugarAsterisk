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


// Fetch Asterisk dialprefix - must strip this from inbound callerIDs if set
$calloutPrefix = $system_config->settings['asterisk_prefix'];

// Clear old entries from the log
$cUser->db->query("DELETE FROM asterisk_log WHERE timestampCall + INTERVAL 1 HOUR < NOW()", false);

// query log
$query = " SELECT * FROM asterisk_log WHERE (callstate = 'Dial' OR callstate = 'Connected') AND (channel LIKE 'SIP/{$cUser->asterisk_ext_c}%')";

$resultSet = $cUser->db->query($query, false);
if($cUser->db->checkError()){
	trigger_error("checkForNewStates-Query failed");
}

$response = array();
while($row = $cUser->db->fetchByAssoc($resultSet)){
	$cUser->db->query("INSERT INTO test_log VALUES(NOW, '".mysql_real_escape_string(serialize($row))."')");
	$item = array();
	$item['asterisk_id'] = $row['asterisk_id'];

	$item['state'] = isset($mod_strings[$row['callstate']]) ? $mod_strings[$row['callstate']] : $row['callstate'];
	$item['state'] = "'" . $item['state'] . "'";

	$item['id'] = $row['id'];
	//for opening the relevant phone record when call has been answered
	$item['call_record_id'] = $row['call_record_id'];

	if($row['direction'] == 'I'){

		// this call is coming in from a remote phone partner
		$item['call_type'] = "ASTERISKLBL_COMING_IN";
		$item['direction'] = "Inbound";

	}

	if($row['direction'] == 'O'){

		// this call is coming in from a remote phone partner
		$item['call_type'] = "ASTERISKLBL_GOING_OUT";
		$item['direction'] = "Outbound";
		#$item['phone_number'] = $row['callerID'];
		#$item['asterisk_name'] = $row['callerName'];

	}

	// Remove prepending dialout prefix if present

	$tmpCallerID = trim($row['callerID']);
	if ( (strlen($calloutPrefix) > 0)  && (strpos($tmpCallerID, $calloutPrefix) === 0) )
	{
		$tmpCallerID = substr($tmpCallerID, strlen($calloutPrefix));
	}
	$item['phone_number'] = $tmpCallerID;

	#$item['phone_number'] = $row['callerID'];
	$item['asterisk_name'] = $row['callerName'];
	$item['asterisk_id'] = $row['asterisk_id'];


	// prepare phone number passed in
	$phoneToFind =  preg_replace('#\D#', '', $item['phone_number']);

	// delete leading zeros
	$phoneToFind = ltrim($phoneToFind, '0');

	$found = array();
	if(strlen($phoneToFind) > 0){
		$sqlReplace = "
			    replace(
			    replace(
			    replace(
			    replace(
			    replace(
			    replace(
			    replace(
			    replace(
			      %s,
			        ' ', ''),
			        '+', ''),
			        '/', ''),
			        '(', ''),
			        ')', ''),
			        '[', ''),
			        ']', ''),
			        '-', '')
			        LIKE '%s'
			";



		$queryContact = "SELECT c.id as contact_id, first_name,	last_name,phone_work, phone_home, phone_mobile, phone_other, a.name as account_name, account_id
                   FROM contacts c left join accounts_contacts ac on (c.id=ac.contact_id and ac.deleted=0) left join accounts a on (ac.account_id=a.id) WHERE ";
		$queryContact .= sprintf($sqlReplace, "phone_work", $phoneToFind) . " OR ";
		$queryContact .= sprintf($sqlReplace, "phone_home", $phoneToFind) . " OR ";
		$queryContact .= sprintf($sqlReplace, "phone_other", $phoneToFind) . " OR ";
		$queryContact .= sprintf($sqlReplace, "assistant_phone", $phoneToFind) . " OR ";
		$queryContact .= sprintf($sqlReplace, "phone_mobile", $phoneToFind) . "";
//                $queryContact .= "phone_work = '$phoneToFind' OR phone_mobile = '$phoneToFind'";

		$innerResultSet = $cUser->db->query($queryContact, false);
		while($contactRow = $cUser->db->fetchByAssoc($innerResultSet)){

			$found['$contactFullName'] = $contactRow['first_name'] . " " . $contactRow['last_name'];
			$found['$company'] = $contactRow['account_name'];
			$found['$contactId'] = $contactRow['contact_id'];
			$found['$companyId'] = $contactRow['account_id'];
		}
	}
	$item['full_name'] = isset($found['$contactFullName']) ? $found['$contactFullName'] : "";

	$item['company'] = isset($found['$company']) ? $found['$company'] : "";
	$item['contact_id'] = isset($found['$contactId']) ? $found['$contactId'] : "";
	$item['company_id'] = isset($found['$companyId']) ? $found['$companyId'] : "";

	$response[] = $item;
	}

header("Content-Type: application/json");
$responseArray = array();
if(count($response) == 0){
	print json_encode(array("."));
}else{
	foreach($response as $item){
		ob_start();
		require("custom/modules/Asterisk/include/ShowCall.html");
		$item['html'] = ob_get_contents();
		$item['html'] = str_replace("\n", "", $item['html']);
		$item['html'] = str_replace("\t", "", $item['html']);
		$item['html'] = str_replace("\r", "", $item['html']);
		ob_clean();

		$responseArray[] = $item;
	}
	print json_encode($responseArray);
	ob_flush();
}

sugar_cleanup();

?>