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
include "../admin/ashopfunc.inc.php";

if (!$activateleads) {
	header("Location: affiliate.php");
	exit;
}

// Apply selected theme...
$templatepath = "/templates";
if ($ashoptheme && $ashoptheme != "none") include "../themes/$ashoptheme/theme.cfg.php";
if ($usethemetemplates == "true") $templatepath = "/themes/$ashoptheme";

// Include language file...
if (!$lang) $lang = $defaultlanguage;
include "../language/$lang/af_leads.inc.php";

// Open database...
$db = @mysql_connect("$databaseserver", "$databaseuser", "$databasepasswd");
@mysql_select_db("$databasename",$db);

// Get affiliate information from database...
$sql="SELECT * FROM affiliate WHERE sessionid='$affiliatesesid'";
$result = @mysql_query("$sql",$db);

// Store affiliate information in variables...
$firstname = @mysql_result($result, 0, "firstname");
$lastname = @mysql_result($result, 0, "lastname");
$affiliateid = @mysql_result($result, 0, "affiliateid");

// Download leads as a CSV...
if (!empty($download) || !empty($view)) {
$leadsinterests = @mysql_real_escape_string($leadsinterests,$db);
$leadsinterests = strtoupper($leadsinterests);
if (!empty($leadsinterests)) {
	$sql="SELECT DISTINCT customer.customerid FROM customer, orders WHERE customer.affiliateid='$affiliateid' AND customer.customerid=orders.customerid AND UPPER(orders.product) LIKE '%$leadsinterests%' AND customer.email != '' AND customer.email IS NOT NULL";
	$sql.=" ORDER BY customer.lastname";
} else {
	$sql="SELECT DISTINCT customerid FROM customer WHERE affiliateid='$affiliateid' AND email != '' AND email IS NOT NULL ORDER BY lastname";
}
$result = @mysql_query("$sql",$db);
$leadslist = "";
if (@mysql_num_rows($result) != 0) {
	if (!empty($download)) {
		header ("Content-Type: application/octet-stream");
		header ("Content-Disposition: attachment; filename=leads.csv");
		echo NAME.";".EMAIL.";".ORDERS."\n";
	} else $leadslist = "<p><table class=\"ashopaffiliateleadsbox\" width=\"800\" border=\"0\" cellspacing=\"2\" cellpadding=\"1\" align=\"center\"><tr class=\"ashopaffiliateleadsrow\"><td><span class=\"ashopaffiliateleadstext1\">".NAME."</span></td><td><span class=\"ashopaffiliateleadstext1\">".EMAIL."</span></td><td><span class=\"ashopaffiliateleadstext1\">".ORDERS."</span></td></tr>";
	for ($i = 0; $i < @mysql_num_rows($result);$i++) {
		$customerid = @mysql_result($result, $i, "customerid");
		$customerresult = @mysql_query("SELECT * FROM customer WHERE customerid='$customerid'",$db);
		$firstname = @mysql_result($customerresult, 0, "firstname");
		$lastname = @mysql_result($customerresult, 0, "lastname");
		if (!empty($firstname) && !empty($lastname)) $fullname = "$firstname $lastname";
		else if (!empty($firstname)) $fullname = $firstname;
		else if (!empty($lastname)) $fullname = $lastname;
		else $fullname = "Unknown";
		$email = @mysql_result($customerresult, 0, "email");
		$orderresult = @mysql_query("SELECT orderid FROM orders WHERE customerid='$customerid' AND paid!='' AND paid IS NOT NULL",$db);
		$ordercount = @mysql_num_rows($orderresult);
		if (!empty($download)) echo "$fullname;$email;$phone;$ordercount\n";
		else $leadslist .= "<tr><td><span class=\"ashopaffiliatetext3\">$fullname</span></td><td><span class=\"ashopaffiliatetext3\"><a href=\"mailto:$email\">$email</a></span></td><td align=\"center\"><span class=\"ashopaffiliatetext3\">$ordercount</span></td></tr>";
	}
	if (!empty($download)) exit;
	else $leadslist .= "</table></p>";
} else $msg = "noleads";
}

// Get number of unread PMs...
$sql="SELECT * FROM affiliatepm WHERE toaffiliateid='$affiliateid' AND (hasbeenread='' OR hasbeenread='0' OR hasbeenread IS NULL)";
$unreadresult = @mysql_query("$sql",$db);
$unreadcount = @mysql_num_rows($unreadresult);

// Print header from template...
if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplateheader("$ashoppath$templatepath/affiliate-$lang.html");
else ashop_showtemplateheader("$ashoppath$templatepath/affiliate.html");

echo "
<script language=\"JavaScript\" src=\"../includes/switchstates.js.php\" type=\"text/javascript\"></script>
<br><span class=\"ashopaffiliateheader\">".WELCOME." $firstname $lastname! ".AFFILIATEID.": $affiliateid</span>
	<table align=\"center\" width=\"400\"><tr><td align=\"center\"><form action=\"affiliate.php\" method=\"post\"><input class=\"ashopaffiliatebutton\" type=\"submit\" value=\"".STATISTICS."\"></form></td><td align=\"center\"><form action=\"changeprofile.php\" method=\"post\"><input class=\"ashopaffiliatebuttonlarge\" type=\"submit\" value=\"".VIEWPROFILE."\"></form></td><td align=\"center\"><form action=\"changepassword.php\" method=\"post\"><input class=\"ashopaffiliatebuttonlarge\" type=\"submit\" value=\"".CHANGEPASS."\"></form></td><td align=\"center\"><form action=\"login.php?logout\" method=\"post\"><input class=\"ashopaffiliatebutton\" type=\"submit\" value=\"".LOGOUT."\"></form></td></tr></table>
	<table align=\"center\" width=\"400\"><tr><td align=\"center\"><form action=\"linkcodes.php\" method=\"post\"><input class=\"ashopaffiliatebutton\" type=\"submit\" value=\"".LINKCODES."\"></form></td><td align=\"center\"><form action=\"orderhistory.php\" method=\"post\"><input class=\"ashopaffiliatebuttonlarge\" type=\"submit\" value=\"".ORDERHISTORY."\"></form></td><td align=\"center\"><form action=\"downline.php\" method=\"post\"><input class=\"ashopaffiliatebuttonsmall\" type=\"submit\" value=\"".DOWNLINE."\"></form></td><td align=\"center\"><input class=\"ashopaffiliatebuttonsmall\" type=\"button\" value=\"".LEADS."\" disabled></td><td align=\"center\"><form action=\"inbox.php\" method=\"post\"><input class=\"ashopaffiliatebuttonsmall\" type=\"submit\" value=\"".INBOX;
	if ($unreadcount) echo " ($unreadcount)";
	echo "\"></form></td></tr></table>
	<br><span class=\"ashopaffiliateheader\">".YOURLEADS."</span>";

	if ($msg == "noleads") echo "<br><br><span class=\"ashopaffiliatetext2\">".NOLEADSFOUND."</span>";

// Check if this affiliate has any leads...
$sql="SELECT customerid FROM customer WHERE affiliateid='$affiliateid' ORDER BY lastname";
$result = @mysql_query("$sql",$db);
if (@mysql_num_rows($result) != 0) {
	echo "
	<form action=\"leads.php\" method=\"post\" name=\"signupform\">
	<p><table width=\"450\" cellpadding=\"5\" cellspacing=\"0\" border=\"0\">
	<tr><td align=\"right\" width=\"100\"><span class=\"ashopaffiliatetext1\">".INTERESTS.": </span></td><td align=\"left\"><input type=\"text\" name=\"leadsinterests\" value=\"$leadsinterests\" size=\"50\"></td></tr>
	<tr><td align=\"right\" width=\"100\">&nbsp;</td><td align=\"right\"><input type=\"submit\" name=\"download\" value=\"".DOWNLOAD."\"> <input type=\"submit\" name=\"view\" value=\"".VIEW."\"></td></tr>
	</table></p>
	</form>";
} else echo "
<br><br><span class=\"ashopaffiliatetext2\">".NOLEADS."</span>";

if ($leadslist) echo $leadslist;


// Print footer using template...
if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplatefooter("$ashoppath$templatepath/affiliate-$lang.html");
else ashop_showtemplatefooter("$ashoppath$templatepath/affiliate.html");

// Close database...
@mysql_close($db);
?>