<?php
// AShop Affiliate
// Copyright 2012 - AShop Software - http://www.ashopsoftware.com
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, see: http://www.gnu.org/licenses/.

error_reporting(E_ALL ^ E_NOTICE);

include "config.inc.php";
if ($msg) $noinactivitycheck = "true";
else $noinactivitycheck = "false";

include "checklogin.inc.php";
include "ashopconstants.inc.php";

// Validate variables...
if (!is_numeric($resultpage)) unset($resultpage);
if (!is_numeric($admindisplayitems)) unset($admindisplayitems);
else {
	$c_admindisplayitems = $admindisplayitems;
	if (!$p3psent) header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');
	$p3psent = TRUE;
	setcookie("c_admindisplayitems","$admindisplayitems");
}
if (!is_numeric($c_admindisplayitems)) unset($c_admindisplayitems);
$namefilter = str_replace("<","",$namefilter);
$namefilter = str_replace(">","",$namefilter);
$emailfilter = str_replace("<","",$emailfilter);
$emailfilter = str_replace(">","",$emailfilter);

include "template.inc.php";
// Get language module...
include "language/$adminlang/customers.inc.php";

// Get context help for this page...
$contexthelppage = "salesadmin";
include "help.inc.php";

// Open database...
$db = @mysql_connect("$databaseserver", "$databaseuser", "$databasepasswd");
@mysql_select_db("$databasename",$db);

// Pause/resume a mailing...
if ($pause) @mysql_query("UPDATE mailing SET paused='1' WHERE type='customer'",$db);
else if ($resume) @mysql_query("UPDATE mailing SET paused=NULL WHERE type='customer'",$db);

// Check if a mailing is being sent...
$mailingresult = @mysql_query("SELECT * FROM mailing WHERE type='customer'",$db);
if (@mysql_num_rows($mailingresult)) {
	$mailingid = @mysql_result($mailingresult,0,"mailingid");
	$paused = @mysql_result($mailingresult,0,"paused");
	$sentresult = @mysql_query("SELECT * FROM maillog WHERE mailingid='$mailingid'",$db);
	$totalsent = @mysql_num_rows($sentresult);

	if ($paused) $pauseresumeform = "<p><form action=\"salesadmin.php\" method=\"post\"><input type=\"submit\" name=\"resume\" value=\"".RESUME."\"></form></p>";
	else $pauseresumeform = "<p><form action=\"salesadmin.php\" method=\"post\"><input type=\"submit\" name=\"pause\" value=\"".PAUSE."\"></form></p>";

	echo "$header
<div class=\"heading\">".MAILINGINPROGRESS."</div><center><br><br><br>
<script language=\"JavaScript\" src=\"../includes/prototype.js\" type=\"text/javascript\"></script>
<script language=\"JavaScript\" type=\"text/javascript\">
function reportprogress(ajaxRequest) {
	parameters = ajaxRequest.responseText;
	parametersarray = parameters.split('|');
	sent = parseInt(parametersarray[0]);
	total = parseInt(parametersarray[1]);
	sentmsgs = sent;
	totalmsgs = total;
	if (sent == -1) $('mailingprogress').update('".MESSAGESENT." <a href=\"../previews/'+logfile+'\" target=\"_blank\">".VIEWLOG."</a>');
	$('sentmails').update(sent);
	$('totalmails').update(total);
}

function setlogfile(ajaxRequest) {
	templog = ajaxRequest.responseText;
	if (templog) logfile = templog;
}

function checkprogress() {
	var myAjax = new Ajax.Request(
		'mailstatus.php', 
		{
			method: 'get',
			parameters: 'mailingtype=customer&dummy='+ new Date().getTime(), 
			onSuccess: reportprogress
		}
	);
}

function startmailing() {
	var myAjax = new Ajax.Request(
		'mailcustomer.php', 
		{
			method: 'get',
			parameters: 'mailall=true&dummy='+ new Date().getTime(),
			onSuccess: setlogfile
		}
	);
}
window.setInterval(\"checkprogress()\",3000);
</script>

	<div id=\"mailingprogress\" class=\"confirm\">".SENT.": <span id=\"sentmails\">0</span> ".THEWORDOF.": <span id=\"totalmails\"></span> ".MESSAGES.".$pauseresumeform</div>
<script language=\"JavaScript\" type=\"text/javascript\">
var logfile = '';
var totalmsgs = 0;
var checkmsgs = 0;
checkprogress();
startmailing();
function unstall() {
	if (totalmsgs == checkmsgs && totalmsgs != -1) startmailing();
	else checkmsgs = totalmsgs;
}
window.setInterval(\"unstall()\",15000);
</script>
</td></tr></table></center>$footer
	";
	exit;
}

echo "$header
<script language=\"JavaScript\" type=\"text/javascript\">
<!--
function newWindow(newContent)
{
	winContent = window.open(newContent, 'c572bf4', 'top=109,width=800,height=550, toolbar=no,scrollbars=yes, resizable=no');
	x = (screen.availWidth-800)/2;
	y = (screen.availHeight-550)/2;
	winContent.moveTo(x,y);
}
function selectAll()
{
	if (document.customermailform.switchall.checked == true) {
		for (var i = 0; i < document.customermailform.elements.length; i++) {
			if (document.customermailform.elements[i].checked != true) {
				document.customermailform.elements[i].checked = true;
			}
		}
	} else {
		for (var i = 0; i < document.customermailform.elements.length; i++) {
			if (document.customermailform.elements[i].checked == true) {
				document.customermailform.elements[i].checked = false;
			}
		}
	}
}
-->
</script>
<div class=\"heading\">".CUSTOMERSANDMESSAGING." <a href=\"$help1\" target=\"_blank\"><img src=\"images/icon_helpsm.gif\" width=\"15\" height=\"15\" border=\"0\"></a></div><center><br>";
if ($msg == "sent") {
	echo "<br><span class=\"confirm\">".MESSAGESENT;
	if ($log) echo " <a href=\"../previews/$log\" target=\"_blank\">".VIEWLOG."</a>";
	echo "</span><br>";
}
if ($activate) echo "<br><span class=\"confirm\">".ORDERACTIVATIONCOMPLETED."</span><br>";
echo "<table width=\"700\"><tr><td><form action=\"salesadmin.php?resultpage=$resultpage&admindisplayitems=$admindisplayitems\" method=\"post\" name=\"customerfilterform\" style=\"margin-bottom: 0px;\"><span class=\"text\">".FILTERBYNAME.": <input type=\"text\" name=\"namefilter\" value=\"$namefilter\" size=\"10\"> ".ANDOREMAIL.": <input type=\"text\" name=\"emailfilter\" value=\"$emailfilter\" size=\"10\"> <input type=\"submit\" value=\"".FILTER."\"></span></form></td>
<td align=\"right\"><span class=\"text\">".EXPORTTOCSV.": </span></td><td><form action=\"exportcustomers.php\" method=\"post\" style=\"margin-bottom: 0px;\"><input type=\"submit\" value=\"".DOWNLOAD."\"></form></td>
</tr></table><br>
      <table width=\"700\" border=\"0\" cellspacing=\"1\" cellpadding=\"0\" align=\"center\" bgcolor=\"#D0D0D0\">
      <tr class=\"reporthead\"><td align=\"left\">".IDNAME."</td><td align=\"left\">".EMAIL."</td><td width=\"90\" align=\"center\">".ACTION."</td></tr>";

// Get customer information from database...
$sql = "SELECT * FROM customer WHERE customer.email != ''";
if ($namefilter || $emailfilter) $sql .= " AND ";
if ($namefilter) {
	$sql .= "(firstname  LIKE '%$namefilter%' OR lastname LIKE '%$namefilter%')";
	if ($emailfilter) $sql .= " AND email LIKE '%$emailfilter%'";
} else if ($emailfilter) $sql .= " email LIKE '%$emailfilter%'";
if ($remind) $sql .= " AND customerid='$remindcustomer'";
else if ($activate) $sql .= " AND customerid='$activatecustomer'";
$sql .= " ORDER BY customerid ASC";
$result = @mysql_query($sql,$db);
$numberofrows = intval(@mysql_num_rows($result));
if (!$admindisplayitems) {
	if ($c_admindisplayitems) $admindisplayitems = $c_admindisplayitems;
	else $admindisplayitems = 10;
}
$numberofpages = ceil($numberofrows/$admindisplayitems);
if ($resultpage > 1) $startrow = (intval($resultpage)-1) * $admindisplayitems;
else {
	$resultpage = 1;
	$startrow = 0;
}
$startpage = $resultpage - 9;
if ($numberofpages - $resultpage < 10) {
	$pagesleft = $numberofpages - $resultpage;
	$startpage = $startpage - (10 - $pagesleft);
}
if ($startpage < 1) $startpage = 1;
$stoprow = $startrow + $admindisplayitems;
@mysql_data_seek($result, $startrow);
$thisrow = $startrow;
while (($row = @mysql_fetch_array($result)) && ($thisrow < $stoprow)) {
    $firstname = $row["firstname"];
    $lastname = $row["lastname"];
    $customerid = $row["customerid"];
    $email = $row["email"];
	$allowemail = $row["allowemail"];
	$password = $row["password"];
	$thisrow++;
	echo "<tr class=\"reportline\"><td nowrap align=\"left\"><a href=\"editcustomer.php?customerid=$customerid\">$customerid</a>, $firstname $lastname</td><td align=\"left\">
	<a href=\"mailto:$email\">$email</a>
	</td><td width=\"90\" nowrap align=\"center\"><a href=\"editcustomer.php?customerid=$customerid\"><img src=\"images/icon_profile.gif\" alt=\"".PROFILEFOR." $customerid\" title=\"".PROFILEFOR." $customerid\" border=\"0\"></a>&nbsp;<a href=\"salesreport.php?customerid=$customerid&generate=true\"><img src=\"images/icon_history.gif\" alt=\"".SALESHISTORYFOR." $customerid\" title=\"".SALESHISTORYFOR." $customerid\" border=\"0\"></a>&nbsp;<a href=\"editcustomer.php?customerid=$customerid&remove=True\"><img src=\"images/icon_trash.gif\" alt=\"".DELETECUSTOMER." $customerid ".FROMDB."\" title=\"".DELETECUSTOMER." $customerid ".FROMDB."\" border=\"0\"></a>";
	echo "</td></tr>";
}

echo "</table>\n";
if ($numberofrows > 5) {
	echo "<table width=\"100%\" border=\"0\" cellpadding=\"5\" cellspacing=\"0\"><tr><td align=\"center\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">";
	if ($numberofpages > 1) {
		echo "<b>".PAGE.": </b>";
		if ($resultpage > 1) {
			$previouspage = $resultpage-1;
			echo "<<<a href=\"salesadmin.php?recurring=$recurring&resultpage=$previouspage&admindisplayitems=$admindisplayitems&namefilter=$namefilter&emailfilter=$emailfilter\"><b>".PREVIOUS."</b></a>&nbsp;&nbsp;";
		}
		$page = 1;
		for ($i = $startpage; $i <= $numberofpages; $i++) {
			if ($page > 20) break;
			if ($i != $resultpage) echo "<a href=\"salesadmin.php?recurring=$recurring&resultpage=$i&admindisplayitems=$admindisplayitems&namefilter=$namefilter&emailfilter=$emailfilter\">";
			echo "$i";
			if ($i != $resultpage) echo "</a>";
			echo "&nbsp;&nbsp;";
			$page++;
		}
		if ($resultpage < $numberofpages) {
			$nextpage = $resultpage+1;
			echo "<a href=\"salesadmin.php?recurring=$recurring&resultpage=$nextpage&admindisplayitems=$admindisplayitems&namefilter=$namefilter&emailfilter=$emailfilter\"><b>".NEXTPAGE."</b></a>>>";
		}
	}
	echo " ".DISPLAY.": <select name=\"admindisplayitems\" onChange=\"document.location.href='salesadmin.php?recurring=$recurring&resultpage=$resultpage&namefilter=$namefilter&emailfilter=$emailfilter&admindisplayitems='+customermailform.admindisplayitems.value;\"><option value=\"$numberofrows\">".SELECT."</option><option value=\"5\">5</option><option value=\"10\">10</option><option value=\"20\">20</option><option value=\"40\">40</option><option value=\"$numberofrows\">".ALL."</option></select> ".CUSTOMERS2."</td></tr></table>
	";
}
	
echo "</p></td></tr></table></center>$footer";
?>