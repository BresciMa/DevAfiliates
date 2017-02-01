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

include "../admin/config.inc.php";
include "checklogin.inc.php";
include "../admin/ashopconstants.inc.php";
include "../admin/ashopfunc.inc.php";

// Apply selected theme...
$templatepath = "/templates";
if ($ashoptheme && $ashoptheme != "none") include "../themes/$ashoptheme/theme.cfg.php";
if ($usethemetemplates == "true") $templatepath = "/themes/$ashoptheme";

// Include language file...
if (!$lang) $lang = $defaultlanguage;
include "../language/$lang/af_inbox.inc.php";

// Open database...
$db = @mysql_connect("$databaseserver", "$databaseuser", "$databasepasswd");
@mysql_select_db("$databasename",$db);

// Get affiliate information from database...
$sql="SELECT * FROM affiliate WHERE sessionid='$affiliatesesid'";
$result = @mysql_query("$sql",$db);

// Get the correct password for this affiliate...
$correctpasswd = @mysql_result($result, 0, "password");

// Store affiliate information in variables...
$firstname = @mysql_result($result, 0, "firstname");
$lastname = @mysql_result($result, 0, "lastname");
$affiliateid = @mysql_result($result, 0, "affiliateid");
$referredby = @mysql_result($result, 0, "referedby");

// Validate the read parameter...
if (!empty($read) && is_numeric($read)) {
	$readresult = @mysql_query("SELECT * FROM affiliatepm WHERE affiliatepmid='$read' AND toaffiliateid='$affiliateid'",$db);
	if (!@mysql_num_rows($readresult)) $read = 0;
	else @mysql_query("UPDATE affiliatepm SET hasbeenread='1' WHERE affiliatepmid='$read'",$db);
}

// Delete a PM...
if (!empty($deletepm) && is_numeric($deletepm)) @mysql_query("DELETE FROM affiliatepm WHERE affiliatepmid='$deletepm' AND toaffiliateid='$affiliateid'",$db);

// Get number of unread PMs...
$sql="SELECT * FROM affiliatepm WHERE toaffiliateid='$affiliateid' AND (hasbeenread='' OR hasbeenread='0' OR hasbeenread IS NULL) ORDER BY sentdate DESC";
$unreadresult = @mysql_query("$sql",$db);
$unreadcount = @mysql_num_rows($unreadresult);

// Print header from template...
if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplateheader("$ashoppath$templatepath/affiliate-$lang.html");
else ashop_showtemplateheader("$ashoppath$templatepath/affiliate.html");

echo "<br><span class=\"ashopaffiliateheader\">".WELCOME." $firstname $lastname! ".AFFILIATEID.": $affiliateid</span>
	<table align=\"center\" width=\"400\"><tr><td align=\"center\"><form action=\"affiliate.php\" method=\"post\"><input class=\"ashopaffiliatebutton\" type=\"submit\" value=\"".STATISTICS."\"></form></td><td align=\"center\"><form action=\"changeprofile.php\" method=\"post\"><input class=\"ashopaffiliatebuttonlarge\" type=\"submit\" value=\"".VIEWPROFILE."\"></form></td><td align=\"center\"><form action=\"changepassword.php\" method=\"post\"><input class=\"ashopaffiliatebuttonlarge\" type=\"submit\" value=\"".CHANGEPASS."\"></form></td><td align=\"center\"><form action=\"login.php?logout\" method=\"post\"><input class=\"ashopaffiliatebutton\" type=\"submit\" value=\"".LOGOUT."\"></form></td></tr></table>
	<table align=\"center\" width=\"400\"><tr><td align=\"center\"><form action=\"linkcodes.php\" method=\"post\"><input class=\"ashopaffiliatebutton\" type=\"submit\" value=\"".LINKCODES."\"></form></td><td align=\"center\"><form action=\"orderhistory.php\" method=\"post\"><input class=\"ashopaffiliatebuttonlarge\" type=\"submit\" value=\"".ORDERHISTORY."\"></form></td>";
if ($activateleads) {
	echo "	
	<td align=\"center\"><form action=\"downline.php\" method=\"post\"><input class=\"ashopaffiliatebuttonsmall\" type=\"submit\" value=\"".DOWNLINE."\"></form></td><td align=\"center\"><form action=\"leads.php\" method=\"post\"><input class=\"ashopaffiliatebuttonsmall\" type=\"submit\" value=\"".LEADS."\"></form></td>";
} else {
	echo "	
	<td align=\"center\"><form action=\"downline.php\" method=\"post\"><input class=\"ashopaffiliatebuttonlarge\" type=\"submit\" value=\"".DOWNLINE."\"></form></td>";
}

if (!empty($read) && $read > 0) {
	echo "<td align=\"center\"><form action=\"inbox.php\" method=\"post\"><input class=\"";
	if ($activateleads) echo "ashopaffiliatebuttonsmall";
	else echo "ashopaffiliatebutton";
	echo "\" type=\"submit\" value=\"".INBOX;
	if ($unreadcount) echo " ($unreadcount)";
	echo "\"></form></td></tr></table>";
	$received = @mysql_result($readresult, 0, "sentdate");
	$received = substr($received,0,-3);
	$senderid = @mysql_result($readresult, 0, "fromaffiliateid");
	if ($senderid == -1) $sender = SHOPADMIN;
	else {
		$senderresult = @mysql_query("SELECT firstname, lastname, referedby FROM affiliate WHERE affiliateid='$senderid'",$db);
		$senderfirstname = @mysql_result($senderresult,0,"firstname");
		$senderlastname = @mysql_result($senderresult,0,"lastname");
		$senderreferredby = @mysql_result($senderresult,0,"referedby");
		if (!empty($senderfirstname) && !empty($senderlastname)) $sender = $senderfirstname." ".$senderlastname;
		else if (!empty($senderfirstname)) $sender = $senderfirstname;
		else $sender = $senderlastname;
		if ($senderid == $referredby) $sender .= " ".SPONSOR;
		else if ($senderreferredby == $affiliateid) $sender .= " [".DOWNLINE."]";
		else $sender .= " ".UPLINE;
	}
	$subject = @mysql_result($readresult, $i, "subject");
	$message = @mysql_result($readresult, $i, "message");
	echo "
	<p><table class=\"ashopaffiliatemessagebox\" border=\"0\" cellspacing=\"2\" cellpadding=\"1\" align=\"center\">
	<tr><td align=\"right\" width=\"100\"><span class=\"ashopaffiliatemessagestext3\">&nbsp;".RECEIVED.":</span></td><td align=\"left\"><span class=\"ashopaffiliatemessagestext2\">$received</span></td></tr><td align=\"right\"><span class=\"ashopaffiliatemessagestext3\">".FROM.":</span></td><td align=\"left\"><span class=\"ashopaffiliatemessagestext2\">$sender</span></td></tr><tr><td align=\"right\"><span class=\"ashopaffiliatemessagestext3\">&nbsp;".SUBJECT.":</span></td><td align=\"left\"><span class=\"ashopaffiliatemessagestext2\">$subject</span></td></tr><td align=\"right\"><span class=\"ashopaffiliatemessagestext3\">&nbsp;</span></td><td align=\"left\"><span class=\"ashopaffiliatemessagestext2\"><hr>$message<br><br></span></td></tr>
	<tr><td>&nbsp;</td><td align=\"right\"><form action=\"inbox.php\" method=\"post\"><input type=\"submit\" value=\"Delete\"><input type=\"hidden\" name=\"deletepm\" value=\"$read\"></form></table></p>";

} else {
	echo "
	<td align=\"center\"><input class=\"";
	if ($activateleads) echo "ashopaffiliatebuttonsmall";
	else echo "ashopaffiliatebutton";
	echo "\" type=\"button\" value=\"".INBOX;
	if ($unreadcount) echo " ($unreadcount)";
	echo "\" disabled></td></tr></table>";

	$nomessages = TRUE;

	// Get message list from database...
	if (@mysql_num_rows($unreadresult) != 0) {
		$nomessages = FALSE;
		echo "<br><span class=\"ashopaffiliateheader\">".UNREAD."</span>
		<p><table class=\"ashopaffiliatemessagesbox\" border=\"0\" cellspacing=\"2\" cellpadding=\"1\" align=\"center\">
		<tr class=\"ashopaffiliatemessagesrow\"><td align=\"left\" width=\"130\"><span class=\"ashopaffiliatemessagestext1\">&nbsp;".RECEIVED."</span></td><td align=\"left\" width=\"160\"><span class=\"ashopaffiliatemessagestext1\">".FROM."</span></td><td align=\"left\"><span class=\"ashopaffiliatemessagestext1\">&nbsp;".SUBJECT."</span></td></tr>";
		for ($i = 0; $i < @mysql_num_rows($unreadresult);$i++) {
			$received = @mysql_result($unreadresult, $i, "sentdate");
			$received = substr($received,0,-3);
			$pmid = @mysql_result($unreadresult, $i, "affiliatepmid");
			$senderid = @mysql_result($unreadresult, $i, "fromaffiliateid");
			if ($senderid == -1) $sender = SHOPADMIN;
			else {
				$senderresult = @mysql_query("SELECT firstname, lastname, referedby FROM affiliate WHERE affiliateid='$senderid'",$db);
				$senderfirstname = @mysql_result($senderresult,0,"firstname");
				$senderlastname = @mysql_result($senderresult,0,"lastname");
				$senderreferredby = @mysql_result($senderresult,0,"referedby");
				if (!empty($senderfirstname) && !empty($senderlastname)) $sender = $senderfirstname." ".$senderlastname;
				else if (!empty($senderfirstname)) $sender = $senderfirstname;
				else $sender = $senderlastname;
				if ($senderid == $referredby) $sender .= " ".SPONSOR;
				else if ($senderreferredby == $affiliateid) $sender .= " [".DOWNLINE."]";
				else $sender .= " ".UPLINE;
			}
			$subject = @mysql_result($unreadresult, $i, "subject");
			echo "<tr><td align=\"left\"><span class=\"ashopaffiliatemessagestext2\">$received</span></td><td><span class=\"ashopaffiliatemessagestext2\">$sender</span></td><td><span class=\"ashopaffiliatemessagestext2\"><a href=\"inbox.php?read=$pmid\">$subject</a></span></td></tr>";
		}
		echo "</table></p>";
	}
	
	// Get message list from database...
	$sql="SELECT * FROM affiliatepm WHERE toaffiliateid='$affiliateid' AND hasbeenread='1' ORDER BY sentdate DESC";
	$result = @mysql_query("$sql",$db);
	$unreadcount = @mysql_num_rows($result);
	if (@mysql_num_rows($result) != 0) {
		$nomessages = FALSE;
		echo "<br><span class=\"ashopaffiliateheader\">".MESSAGES."</span>
		<p><table class=\"ashopaffiliatemessagesbox\" border=\"0\" cellspacing=\"2\" cellpadding=\"1\" align=\"center\">
		<tr class=\"ashopaffiliatemessagesrow\"><td align=\"left\" width=\"130\"><span class=\"ashopaffiliatemessagestext1\">&nbsp;".RECEIVED."</span></td><td align=\"left\" width=\"160\"><span class=\"ashopaffiliatemessagestext1\">".FROM."</span></td><td align=\"left\"><span class=\"ashopaffiliatemessagestext1\">&nbsp;".SUBJECT."</span></td></tr>";
		for ($i = 0; $i < @mysql_num_rows($result);$i++) {
			$received = @mysql_result($result, $i, "sentdate");
			$received = substr($received,0,-3);
			$pmid = @mysql_result($result, $i, "affiliatepmid");
			$senderid = @mysql_result($result, $i, "fromaffiliateid");
			if ($senderid == -1) $sender = SHOPADMIN;
			else {
				$senderresult = @mysql_query("SELECT firstname, lastname, referedby FROM affiliate WHERE affiliateid='$senderid'",$db);
				$senderfirstname = @mysql_result($senderresult,0,"firstname");
				$senderlastname = @mysql_result($senderresult,0,"lastname");
				$senderreferredby = @mysql_result($senderresult,0,"referedby");
				if (!empty($senderfirstname) && !empty($senderlastname)) $sender = $senderfirstname." ".$senderlastname;
				else if (!empty($senderfirstname)) $sender = $senderfirstname;
				else $sender = $senderlastname;
				if ($senderid == $referredby) $sender .= " ".SPONSOR;
				else if ($senderreferredby == $affiliateid) $sender .= " [".DOWNLINE."]";
				else $sender .= " ".UPLINE;
			}
			$subject = @mysql_result($result, $i, "subject");
			echo "<tr><td align=\"left\"><span class=\"ashopaffiliatemessagestext2\">$received</span></td><td><span class=\"ashopaffiliatemessagestext2\">$sender</span></td><td><span class=\"ashopaffiliatemessagestext2\"><a href=\"inbox.php?read=$pmid\">$subject</a></span></td></tr>";
		}
		echo "</table></p>";
	}

	if ($nomessages) echo "<br><span class=\"ashopaffiliateheader\">".NOMESSAGES."</span>";
}

// Print footer using template...
if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplatefooter("$ashoppath$templatepath/affiliate-$lang.html");
else ashop_showtemplatefooter("$ashoppath$templatepath/affiliate.html");

// Close database...
@mysql_close($db);
?>