//**
// * Asterisk SugarCRM Integration
// * (c) KINAMU Business Solutions AG 2009
// *
// * Parts of this code are (c) 2006. RustyBrick, Inc.  http://www.rustybrick.com/
// * Parts of this code are (c) 2008 vertico software GmbH
// * Parts of this code are (c) 2009 abcona e. K. Angelo Malaguarnera E-Mail admin@abcona.de
// * http://www.sugarforge.org/projects/yaai/
// *
// * This program is free software; you can redistribute it and/or modify it under
// * the terms of the GNU General Public License version 3 as published by the
// * Free Software Foundation with the addition of the following permission added
// * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
// * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
// * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
// *
// * This program is distributed in the hope that it will be useful, but WITHOUT
// * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
// * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
// * details.
// *
// * You should have received a copy of the GNU General Public License along with
// * this program; if not, see http://www.gnu.org/licenses or write to the Free
// * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
// * 02110-1301 USA.
// *
// * You can contact KINAMU Business Solutions AG at office@kinamu.com
// *
// * The interactive user interfaces in modified source and object code versions
// * of this program must display Appropriate Legal Notices, as required under
// * Section 5 of the GNU General Public License version 3.
// *
// */

var asterGritter = null;

// look for new events logged from asterisk
function checkForNewStates(){
    $.getJSON('custom/modules/Asterisk/include/callListener.php', function(data){
	checkData(data);/*alert(data)*/
    });
    setTimeout('checkForNewStates()', 1000);
}

function checkData(data){
    if(data == "."){
	if (asterGritter != null) {
	    $.gritter.removeAll();
	    asterGritter = null;
	}
	return;
    }
    $.each(data, function(entryIndex, entry){
	// response is not empty, lets walk through the json array
	var sfDiv = $("div[id='" + entry['asterisk_id'] + "']");
	if(!sfDiv.is("div")){
	    var subs = /<h4>(.+?)<\/h4>/mig.exec(entry['html']);
	    asterGritter = $.gritter.add({
		title: subs[1],
		text: entry['html'],
		sticky: true
	    });
	    sfDiv = $("div[id='" + entry['asterisk_id'] + "']");
	    $('.asterisk_open_memo', sfDiv).click(function(){
		var newHREF = "index.php?module=Calls&action=DetailView&return_module=Calls&return_action=DetailView&parent_type=Contacts";
		newHREF += "&record=" + entry['call_record_id'];
		//newHREF += "&direction=" + entry['direction'];
		//newHREF += "&status=Held";
		//newHREF += "&parent_id=" + entry['contact_id'];
		//newHREF += "&parent_name=" + entry['full_name'];
		location.href = newHREF;
	    });

	    $('.asterisk_close', sfDiv).click(function(){
		$.gritter.removeAll();
		asterGritter = null;
	    });
	}else{
	    $(".asterisk_state", sfDiv).text(entry['state']);
	}
    });
}

$(document).ready(function(){
    // no checking for the login page
    if(location.href.indexOf('action=Login') == -1){
	$('<div id="asterisk_ajaxContent" style="display:none;"></div>').prependTo('#main');
	checkForNewStates();
    }
});

