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
include "template.inc.php";
// Get language module...
include "language/$adminlang/affiliates.inc.php";

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
$urlfilter = str_replace("<","",$urlfilter);
$urlfilter = str_replace(">","",$urlfilter);

// Get context help for this page...
$contexthelppage = "affiliateadmin";
include "help.inc.php";

// Open database...
$db = @mysql_connect("$databaseserver", "$databaseuser", "$databasepasswd");
@mysql_select_db("$databasename",$db);

// Pause/resume a mailing...
if ($pause) @mysql_query("UPDATE mailing SET paused='1' WHERE type='affiliate'",$db);
else if ($resume) @mysql_query("UPDATE mailing SET paused=NULL WHERE type='affiliate'",$db);

// Check if a mailing is being sent...
$mailingresult = @mysql_query("SELECT * FROM mailing WHERE type='affiliate'",$db);
if (@mysql_num_rows($mailingresult)) {
	$mailingid = @mysql_result($mailingresult,0,"mailingid");
	$paused = @mysql_result($mailingresult,0,"paused");
	$sentresult = @mysql_query("SELECT * FROM maillog WHERE mailingid='$mailingid'",$db);
	$totalsent = @mysql_num_rows($sentresult);

	if ($paused) $pauseresumeform = "<p><form action=\"affiliateadmin.php\" method=\"post\"><input type=\"submit\" name=\"resume\" value=\"".RESUME."\"></form></p>";
	else $pauseresumeform = "<p><form action=\"affiliateadmin.php\" method=\"post\"><input type=\"submit\" name=\"pause\" value=\"".PAUSE."\"></form></p>";

	echo "$header
<div class=\"heading\">".MAILINGINPROGRESS."</div><br><br><br><center>
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
			parameters: 'mailingtype=affiliate&dummy='+ new Date().getTime(), 
			onSuccess: reportprogress
		}
	);
}

function startmailing() {
	var myAjax = new Ajax.Request(
		'mailaffiliate.php', 
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
function selectAll()
{
	if (document.affiliatemailform.switchall.checked == true) {
		for (var i = 0; i < document.affiliatemailform.elements.length; i++) {
			if (document.affiliatemailform.elements[i].checked != true) {
				document.affiliatemailform.elements[i].checked = true;
			}
		}
	} else {
		for (var i = 0; i < document.affiliatemailform.elements.length; i++) {
			if (document.affiliatemailform.elements[i].checked == true) {
				document.affiliatemailform.elements[i].checked = false;
			}
		}
	}
}
-->
</script>
<div class=\"heading\">".MESSAGINGANDMANAGEMENT." <a href=\"$help1\" target=\"_blank\"><img src=\"images/icon_helpsm.gif\" width=\"15\" height=\"15\" border=\"0\"></a></div><center>";
if ($msg == "sent") {
	echo "<span class=\"confirm\">".MESSAGESENT;
	if ($log) echo " <a href=\"../previews/$log\" target=\"_blank\">".VIEWLOG."</a>";
	echo "</span><br><br>";
}
echo "<form action=\"affiliateadmin.php?resultpage=$resultpage&admindisplayitems=$admindisplayitems\" method=\"post\" name=\"affiliatefilterform\" style=\"margin-bottom: 0px;\"><span class=\"text\">".FILTERBYNAME.": <input type=\"text\" name=\"namefilter\" value=\"$namefilter\" size=\"10\"> ".ANDORURL.": <input type=\"text\" name=\"urlfilter\" value=\"$urlfilter\" size=\"10\"> <input type=\"submit\" value=\"".FILTER."\"></span></form><br><form action=\"mailaffiliate.php\" method=\"post\" name=\"affiliatemailform\">
      <table width=\"700\" border=\"0\" cellspacing=\"1\" cellpadding=\"0\" align=\"center\" bgcolor=\"#D0D0D0\">
      <tr class=\"reporthead\"><td width=\"20\"><input type=\"checkbox\" name=\"switchall\" onClick=\"selectAll();\"></td><td width=\"200\" nowrap align=\"left\">".IDNAME."</td><td align=\"left\">".URL."</td><td width=\"100\" align=\"center\">".ACTION."</td></tr>";

// Get affiliate information from database...
if ($userid > "1") {
	$sql = "SELECT DISTINCT orderaffiliate.affiliateid FROM orderaffiliate, orders, affiliate WHERE orders.userid LIKE '%|$userid|%' AND orders.orderid=orderaffiliate.orderid AND orderaffiliate.affiliateid=affiliate.affiliateid";
	if ($namefilter || $urlfilter) $sql .= " AND ";
	if ($namefilter) {
		$sql .= "(affiliate.firstname  LIKE '%$namefilter%' OR affiliate.lastname LIKE '%$namefilter%')";
		if ($urlfilter) $sql .= " AND affiliate.url LIKE '%$urlfilter%'";
	} else if ($urlfilter) $sql .= " affiliate.url LIKE '%$urlfilter%'";
	$sql .= " ORDER BY orderaffiliate.affiliateid";
} else {
	$sql = "SELECT * FROM affiliate";
	if ($namefilter || $urlfilter) $sql .= " WHERE ";
	if ($namefilter) {
		$sql .= "(firstname  LIKE '%$namefilter%' OR lastname LIKE '%$namefilter%')";
		if ($urlfilter) $sql .= " AND url LIKE '%$urlfilter%'";
	} else if ($urlfilter) $sql .= " url LIKE '%$urlfilter%'";
	$sql .= "  ORDER BY affiliateid";
}
$result = @mysql_query($sql,$db);
$numberofrows = intval(@mysql_num_rows($result));
if (empty($admindisplayitems)) {
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
	$thisrow++;
	$affiliateid = $row["affiliateid"];
	$sql="SELECT * FROM affiliate WHERE affiliateid='$affiliateid'";
	$affiliateresult = @mysql_query("$sql",$db);
	$firstname = @mysql_result($affiliateresult, 0, "firstname");
    $lastname = @mysql_result($affiliateresult, 0, "lastname");
    $email = @mysql_result($affiliateresult, 0, "email");
    $url = @mysql_result($affiliateresult, 0, "url");
	echo "<tr class=\"reportline\"><td width=\"20\"><input type=\"checkbox\" name=\"affiliate$affiliateid\"></td><td align=\"left\">$affiliateid, <a href=\"editaffiliate.php?affiliateid=$affiliateid\">$firstname $lastname</a></td><td align=\"left\"><a href=\"$url\" target=\"_blank\">$url</a></td><td align=\"center\"><a href=\"editaffiliate.php?affiliateid=$affiliateid\"><img src=\"images/icon_profile.gif\" alt=\"".PROFILEFORAFFILIATE.": $firstname $lastname\" title=\"".PROFILEFORAFFILIATE.": $firstname $lastname\" border=\"0\"></a>&nbsp;<a href=\"affiliatedetail.php?affiliateid=$affiliateid\"><img src=\"images/icon_history.gif\" alt=\"".STATISTICSFORAFFILIATE.": $firstname $lastname\" title=\"".STATISTICSFORAFFILIATE.": $firstname $lastname\" border=\"0\"></a>&nbsp;<a href=\"editaffiliate.php?affiliateid=$affiliateid&remove=True\"><img src=\"images/icon_trash.gif\" alt=\"".DELETEAFFILIATE." $firstname $lastname ".FROMTHEDATABASE."\" title=\"".DELETEAFFILIATE." $firstname $lastname ".FROMTHEDATABASE."\" border=\"0\"></a></td></tr>";
}

echo "</table>\n";

if ($numberofrows > 5) {
	echo "<table width=\"100%\" border=\"0\" cellpadding=\"5\" cellspacing=\"0\"><tr><td align=\"center\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">";
	if ($numberofpages > 1) {
		echo "<b>".PAGE.": </b>";
		if ($resultpage > 1) {
			$previouspage = $resultpage-1;
			echo "<<<a href=\"affiliateadmin.php?resultpage=$previouspage&admindisplayitems=$admindisplayitems&namefilter=$namefilter&urlfilter=$urlfilter\"><b>".PREVIOUS."</b></a>&nbsp;&nbsp;";
		}
		$page = 1;
		for ($i = $startpage; $i <= $numberofpages; $i++) {
			if ($page > 20) break;
			if ($i != $resultpage) echo "<a href=\"affiliateadmin.php?resultpage=$i&admindisplayitems=$admindisplayitems&namefilter=$namefilter&urlfilter=$urlfilter\">";
			echo "$i";
			if ($i != $resultpage) echo "</a>";
			echo "&nbsp;&nbsp;";
			$page++;
		}
		if ($resultpage < $numberofpages) {
			$nextpage = $resultpage+1;
			echo "<a href=\"affiliateadmin.php?resultpage=$nextpage&admindisplayitems=$admindisplayitems&namefilter=$namefilter&urlfilter=$urlfilter\"><b>".NEXTPAGE."</b></a>>>";
		}
	}
	echo " ".DISPLAY.": <select name=\"admindisplayitems\" onChange=\"document.location.href='affiliateadmin.php?resultpage=$resultpage&namefilter=$namefilter&urlfilter=$urlfilter&admindisplayitems='+affiliatemailform.admindisplayitems.value;\"><option value=\"$numberofrows\">".SELECT."</option><option value=\"5\">5</option><option value=\"10\">10</option><option value=\"20\">20</option><option value=\"40\">40</option><option value=\"$numberofrows\">".ALL."</option></select> ".AFFILIATES2."</td></tr></table>
	";
}
	
echo "<table align=\"center\" cellpadding=\"10\"><tr class=\"formtitle\"><td><tr><td align=\"center\"><p>".SUBJECT.": <input type=\"text\" name=\"subject\" size=\"40\"></p><p>".MESSAGE.":<br><textarea name=\"message\" cols=\"60\" rows=\"10\"></textarea><br><span class=\"sm\">[".SUPPORTSCODES.": %affiliatelink%, %affiliateid%, %referralcode%, %firstname%,<br>%lastname%, %email%, %address%, %state%, %zip%, %city%, %country%, %url%,<br>%phone%, %password%, %username%]</p><p><input type=\"radio\" name=\"mailformat\" value=\"html\"";
if ($prefaffmailformat == "html") echo " checked";
echo "> ".HTMLFORMAT." <input type=\"radio\" name=\"mailformat\" value=\"text\"";
if ($prefaffmailformat == "text" || !$prefaffmailformat) echo "checked";
echo "> ".PLAINTEXT." <input type=\"radio\" name=\"mailformat\" value=\"pm\"";
if ($prefaffmailformat == "pm" || !$prefaffmailformat) echo "checked";
echo "> ".PM."</p><p><input type=\"hidden\" name=\"resultpage\" value=\"$resultpage\"><input type=\"hidden\" name=\"displayitems\" value=\"$admindisplayitems\"><input type=\"hidden\" name=\"urlfilter\" value=\"$urlfilter\"><input type=\"hidden\" name=\"namefilter\" value=\"$namefilter\"><input type=\"submit\" class=\"widebutton\" name=\"mail\" value=\"".MAILTOSELECTED."\"> <input type=\"submit\" class=\"widebutton\" name=\"mailall\" value=\"".MAILTOALL."\"></p></form></td></tr></table></center>$footer";
?>