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

@set_time_limit(0);
include "../admin/config.inc.php";
include "checklogin.inc.php";
include "../admin/ashopfunc.inc.php";

// Apply selected theme...
$templatepath = "/templates";
if ($ashoptheme && $ashoptheme != "none") include "../themes/$ashoptheme/theme.cfg.php";
if ($usethemetemplates == "true") $templatepath = "/themes/$ashoptheme";

// Include language file...
if (!$lang) $lang = $defaultlanguage;
include "../language/$lang/af_downline.inc.php";

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

// Get number of unread PMs...
$sql="SELECT * FROM affiliatepm WHERE toaffiliateid='$affiliateid' AND (hasbeenread='' OR hasbeenread='0' OR hasbeenread IS NULL)";
$unreadresult = @mysql_query("$sql",$db);
$unreadcount = @mysql_num_rows($unreadresult);

// Set current date and time...
$date = date("Y-m-d H:i:s", time()+$timezoneoffset);

// Print header from template...
if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplateheader("$ashoppath$templatepath/affiliate-$lang.html");
else ashop_showtemplateheader("$ashoppath$templatepath/affiliate.html");

echo "<br><span class=\"ashopaffiliateheader\">".WELCOME." $firstname $lastname! ".AFFILIATEID.": $affiliateid</span>
	<table align=\"center\" width=\"400\"><tr><td align=\"center\"><form action=\"affiliate.php\" method=\"post\"><input class=\"ashopaffiliatebutton\" type=\"submit\" value=\"".STATISTICS."\"></form></td><td align=\"center\"><form action=\"changeprofile.php\" method=\"post\"><input class=\"ashopaffiliatebuttonlarge\" type=\"submit\" value=\"".VIEWPROFILE."\"></form></td><td align=\"center\"><form action=\"changepassword.php\" method=\"post\"><input class=\"ashopaffiliatebuttonlarge\" type=\"submit\" value=\"".CHANGEPASS."\"></form></td><td align=\"center\"><form action=\"login.php?logout\" method=\"post\"><input class=\"ashopaffiliatebutton\" type=\"submit\" value=\"".LOGOUT."\"></form></td></tr></table>
	<table align=\"center\" width=\"400\"><tr><td align=\"center\"><form action=\"linkcodes.php\" method=\"post\"><input class=\"ashopaffiliatebutton\" type=\"submit\" value=\"".LINKCODES."\"></form></td><td align=\"center\"><form action=\"orderhistory.php\" method=\"post\"><input class=\"ashopaffiliatebuttonlarge\" type=\"submit\" value=\"".ORDERHISTORY."\"></form></td>";
if ($activateleads) {
	echo "	
	<td align=\"center\"><input class=\"ashopaffiliatebuttonsmall\" type=\"button\" value=\"".DOWNLINE."\" disabled></td><td align=\"center\"><form action=\"leads.php\" method=\"post\"><input class=\"ashopaffiliatebuttonsmall\" type=\"submit\" value=\"".LEADS."\"></form></td><td align=\"center\"><form action=\"inbox.php\" method=\"post\"><input class=\"ashopaffiliatebuttonsmall\" type=\"submit\" value=\"".INBOX;
	if ($unreadcount) echo " ($unreadcount)";
	echo "\"></form></td>";
} else {
	echo "	
	<td align=\"center\"><input class=\"ashopaffiliatebuttonlarge\" type=\"button\" value=\"".DOWNLINE."\" disabled></td><td align=\"center\"><form action=\"inbox.php\" method=\"post\"><input class=\"ashopaffiliatebutton\" type=\"submit\" value=\"".INBOX;
	if ($unreadcount) echo " ($unreadcount)";
	echo "\"></form></td>";
}
echo "
	</tr></table>";

if (!empty($sendpmto) && !empty($pmsubject) && !empty($pmtext)) echo "<br><span class=\"ashopaffiliatetext1\">".MESSAGESENT."</span><br>";

if ($referredby) {
	// Get sponsor details..
	$sponsorresult = @mysql_query("SELECT * FROM affiliate WHERE affiliateid='$referredby'",$db);
	$sponsorfirstname = @mysql_result($sponsorresult,0,"firstname");
	$sponsorlastname = @mysql_result($sponsorresult,0,"lastname");
	echo "<br><span class=\"ashopaffiliatetext1\">".SPONSOR.": </span><span class=\"ashopaffiliatetext2\">$sponsorfirstname $sponsorlastname</span><br>";
	if (!empty($sendpmto) && $sendpmto == "sponsor" && !empty($pmsubject) && !empty($pmtext)) @mysql_query("INSERT INTO affiliatepm (toaffiliateid, fromaffiliateid, sentdate, subject, message) VALUES ('$referredby', '$affiliateid', '$date', '$pmsubject', '$pmtext')",$db);
}
echo "<br><form action=\"downline.php\" method=\"post\">
	<table width=\"450\" cellpadding=\"5\" cellspacing=\"0\" border=\"0\">";
if ($referredby) echo "<tr><td align=\"right\" width=\"100\"><span class=\"ashopaffiliatetext1\">".PMYOUR.": </span></td><td align=\"left\"><select name=\"sendpmto\"><option value=\"sponsor\">".PMSPONSOR."</option><option value=\"downline\">".DOWNLINE."</option></select></td></tr>";
else echo "<tr><td colspan=\"2\"><span class=\"ashopaffiliatetext1\">".PMYOUR." ".DOWNLINE."<input type=\"hidden\" name=\"sendpmto\" value=\"downline\"></span></td></tr>
";
echo "<tr><td align=\"right\" width=\"100\"><span class=\"ashopaffiliatetext1\">".SUBJECT.": </span></td><td align=\"left\"><input type=\"text\" name=\"pmsubject\" size=\"50\"></td></tr>
<tr><td align=\"right\" width=\"100\"><span class=\"ashopaffiliatetext1\">".MESSAGE.": </span></td><td align=\"left\"><textarea name=\"pmtext\" cols=\"38\" rows=\"5\"></textarea></td></tr>
<tr><td align=\"right\" width=\"100\">&nbsp;</td><td align=\"right\"><input type=\"submit\" value=\"".SENDPM."\"></td></tr>
</table>
	";

// Get referral statistics...
$tier1referrals = 0;
$tier2referrals = 0;
$tier1result = @mysql_query("SELECT * FROM affiliate WHERE referedby='$affiliateid' ORDER BY signedup DESC",$db);
if (@mysql_num_rows($tier1result)) {
	echo "<br><span class=\"ashopaffiliateheader\">".DOWNLINE."</span></center>
	<p><table class=\"ashopaffiliatehistorybox\" border=\"0\" cellspacing=\"2\" cellpadding=\"1\" align=\"center\"><tr class=\"ashopaffiliatehistoryrow\"><td width=\"20\">&nbsp;</td><td><span class=\"ashopaffiliatehistorytext1\">".REFERRALLEVEL."</span></td><td><span class=\"ashopaffiliatehistorytext1\">".NAME."</span></td><td><span class=\"ashopaffiliatehistorytext1\">".ORDERS."</span></td><td><span class=\"ashopaffiliatehistorytext1\">".REGISTRATIONDATE."</span></td><td><span class=\"ashopaffiliatehistorytext1\">".ACTIVITY."</span></td></tr>";
	$tier1referrals = @mysql_num_rows($tier1result);
	while ($tier1row = @mysql_fetch_array($tier1result)) {
		$sql="SELECT orderid FROM orderaffiliate WHERE affiliateid='{$tier1row["affiliateid"]}'";
		$tier1ordersresult = @mysql_query("$sql",$db);
		$tier1orders = @mysql_num_rows($tier1ordersresult);
		$tier1signeduparray = explode(" ",$tier1row["signedup"]);
		$tier1signedup = $tier1signeduparray[0];
		$tier1lastdatearray = explode(" ",$tier1row["lastdate"]);
		$tier1lastdate = $tier1signeduparray[0];
		$tier1affiliateid = $tier1row["affiliateid"];
		echo "<tr><td align=\"center\"><input type=\"checkbox\" name=\"affiliate$tier1affiliateid\"></td><td align=\"center\"><span class=\"ashopaffiliatetext3\">1</span></td><td><span class=\"ashopaffiliatetext3\">{$tier1row["firstname"]} {$tier1row["lastname"]}</span></td><td align=\"center\"><span class=\"ashopaffiliatetext3\">$tier1orders</span></td><td><span class=\"ashopaffiliatetext3\">$tier1signedup</span></td><td><span class=\"ashopaffiliatetext3\">$tier1lastdate</span></td></tr>";
		$checkpmstring = "affiliate$tier1affiliateid";
		if (!empty($sendpmto) && $sendpmto == "downline" && !empty($pmsubject) && !empty($pmtext) && $$checkpmstring == "on") @mysql_query("INSERT INTO affiliatepm (toaffiliateid, fromaffiliateid, sentdate, subject, message) VALUES ('$tier1affiliateid', '$affiliateid', '$date', '$pmsubject', '$pmtext')",$db);
		$tier2result = @mysql_query("SELECT * FROM affiliate WHERE referedby='$tier1affiliateid' ORDER BY signedup DESC",$db);
		$tier2referrals += @mysql_num_rows($tier2result);
		while ($tier2row = @mysql_fetch_array($tier2result)) {
			$tier2affiliateid = $tier2row["affiliateid"];
			$sql="SELECT orderid FROM orderaffiliate WHERE affiliateid='$tier2affiliateid'";
			$tier2ordersresult = @mysql_query("$sql",$db);
			$tier2orders = @mysql_num_rows($tier1ordersresult);
			$tier2signeduparray = explode(" ",$tier2row["signedup"]);
			$tier2signedup = $tier2signeduparray[0];
			$tier2lastdatearray = explode(" ",$tier2row["lastdate"]);
			$tier2lastdate = $tier2signeduparray[0];
			echo "<tr><td align=\"center\"><input type=\"checkbox\" name=\"affiliate$tier2affiliateid\"></td><td align=\"center\"><span class=\"ashopaffiliatetext3\">2</span></td><td><span class=\"ashopaffiliatetext3\">{$tier2row["firstname"]} {$tier2row["lastname"]}</span></td><td align=\"center\"><span class=\"ashopaffiliatetext3\">$tier2orders</span></td><td><span class=\"ashopaffiliatetext3\">$tier2signedup</span></td><td><span class=\"ashopaffiliatetext3\">$tier2lastdate</span></td></tr>";
			$checkpmstring = "affiliate$tier2affiliateid";
			if (!empty($sendpmto) && $sendpmto == "downline" && !empty($pmsubject) && !empty($pmtext) && $$checkpmstring == "on") @mysql_query("INSERT INTO affiliatepm (toaffiliateid, fromaffiliateid, sentdate, subject, message) VALUES ('$tier2affiliateid', '$affiliateid', '$date', '$pmsubject', '$pmtext')",$db);
		}
	}
}

echo "</form></table></p>";

// Print footer using template...
if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplatefooter("$ashoppath$templatepath/affiliate-$lang.html");
else ashop_showtemplatefooter("$ashoppath$templatepath/affiliate.html");

// Close database...
@mysql_close($db);
?>