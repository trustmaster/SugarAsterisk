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
//prevents directly accessing this file from a web browser
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class AsteriskJS {

    function echoJavaScript($event, $arguments)
    {
        static $called = false;
        if (!$called) {
            // asterisk hack: include ajax callbacks in every sugar page except ajax requests:
            if (empty($_REQUEST["to_pdf"])) {
                if (isset($GLOBALS['current_user']->asterisk_ext_c) && ($GLOBALS['current_user']->asterisk_ext_c != '') && (($GLOBALS['current_user']->asterisk_inbound_c == '1') || ($GLOBALS['current_user']->asterisk_outbound_c == '1'))) {
                    echo '<script type="text/javascript" src="custom/include/javascript/jquery/jquery-1.6.min.js"></script>';
		    echo '<link rel="stylesheet" type="text/css" media="all" href="custom/include/javascript/jquery/jquery.gritter.css">';
		    echo '<script type="text/javascript" src="custom/include/javascript/jquery/jquery.gritter.min.js"></script>';
                    echo '<link rel="stylesheet" type="text/css" media="all" href="custom/modules/Asterisk/include/asterisk.css">';
                    if ($GLOBALS['current_user']->asterisk_inbound_c == '1')
                        echo '<script type="text/javascript" src="custom/modules/Asterisk/include/javascript/dialin.js"></script>';
                    if ($GLOBALS['current_user']->asterisk_outbound_c == '1')
                        echo '<script type="text/javascript" src="custom/modules/Asterisk/include/javascript/dialout.js"></script>';
                    $cUser = new User();
                    $cUser->retrieve($_SESSION['authenticated_user_id']);
                }
            }
            $called = true;
        }
    }
}
?>
