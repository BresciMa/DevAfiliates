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

// Apply selected theme...
$templatepath = "/templates";
if ($ashoptheme && $ashoptheme != "none") include "../themes/$ashoptheme/theme.cfg.php";
if ($usethemetemplates == "true") $templatepath = "/themes/$ashoptheme";

// Include language file...
if (!$lang) $lang = $defaultlanguage;
include "../language/$lang/af_affiliate.inc.php";

// Open database...
$db = @mysql_connect("$databaseserver", "$databaseuser", "$databasepasswd");
@mysql_select_db("$databasename",$db);


// Get affiliate information from database...
$sql="SELECT * FROM affiliate WHERE sessionid='$affiliatesesid'";
$result = @mysql_query("$sql",$db);

// Reset statistics...
$click = 0;
$provision = 0;
$ourdebt = 0;

// Store affiliate information in variables...
$firstname = @mysql_result($result, 0, "firstname");
$lastname = @mysql_result($result, 0, "lastname");
$affiliateid = @mysql_result($result, 0, "affiliateid");
$correctpasswd = @mysql_result($result, 0, "password");
$referralcode = @mysql_result($result, 0, "referralcode");
$click = @mysql_result($result, 0, "clicks");
$commissionlevel = @mysql_result($result, 0, "commissionlevel");

// Get statistics from database...
$sql="SELECT * FROM affiliate WHERE referedby='$affiliateid'";
$result = @mysql_query("$sql",$db);
$referrals = @mysql_num_rows($result);
$sql="SELECT orderaffiliate.* FROM orders, orderaffiliate WHERE orderaffiliate.affiliateid='$affiliateid' AND orderaffiliate.orderid=orders.orderid AND orderaffiliate.secondtier IS NULL";
$result = @mysql_query("$sql",$db);
$order = @mysql_num_rows($result);
$sql="SELECT orderaffiliate.* FROM orders, orderaffiliate WHERE orderaffiliate.affiliateid='$affiliateid' AND orderaffiliate.orderid=orders.orderid AND orderaffiliate.secondtier <> 1";
$result = @mysql_query("$sql",$db);
$order += @mysql_num_rows($result);
if (!$order) $order = "0";
$sql="SELECT orderaffiliate.* FROM orders, orderaffiliate WHERE orderaffiliate.affiliateid='$affiliateid' AND orderaffiliate.orderid=orders.orderid";
$result = @mysql_query("$sql",$db);
if (@mysql_num_rows($result)) {
  for ($i = 0; $i < @mysql_num_rows($result); $i++) {
	  $paid = @mysql_result($result, $i, "paid");
	  $commission = @mysql_result($result, $i, "commission");
	  $provision += $commission;
	  if (!$paid) $ourdebt += $commission;
  }
}
$sql="SELECT orderaffiliate.* FROM orders, orderaffiliate WHERE orderaffiliate.affiliateid='$affiliateid' AND orderaffiliate.orderid=orders.orderid AND orderaffiliate.secondtier=1";
$result = @mysql_query("$sql",$db);
$secondtier = @mysql_num_rows($result);
if (!$secondtier) $secondtier = "0";

// Get number of unread PMs...
$sql="SELECT * FROM affiliatepm WHERE toaffiliateid='$affiliateid' AND (hasbeenread='' OR hasbeenread='0' OR hasbeenread IS NULL)";
$unreadresult = @mysql_query("$sql",$db);
$unreadcount = @mysql_num_rows($unreadresult);

// Print header from template...
if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplateheader("$ashoppath$templatepath/affiliate-$lang.html");
else ashop_showtemplateheader("$ashoppath$templatepath/affiliate.html");

echo "<br><span class=\"ashopaffiliateheader\">".WELCOME." $firstname $lastname! ".AFFILIATEID.": $affiliateid</span>
	<table align=\"center\" width=\"400\"><tr><td align=\"center\"><input class=\"ashopaffiliatebutton\" type=\"button\" value=\"".STATISTICS."\" disabled></td><td align=\"center\"><form action=\"changeprofile.php\" method=\"post\"><input class=\"ashopaffiliatebuttonlarge\" type=\"submit\" value=\"".VIEWPROFILE."\"></form></td><td align=\"center\"><form action=\"changepassword.php\" method=\"post\"><input class=\"ashopaffiliatebuttonlarge\" type=\"submit\" value=\"".CHANGEPASS."\"></form></td><td align=\"center\"><form action=\"login.php?logout\" method=\"post\"><input class=\"ashopaffiliatebutton\" type=\"submit\" value=\"".LOGOUT."\"></form></td></tr></table>
	<table align=\"center\" width=\"400\"><tr><td align=\"center\"><form action=\"linkcodes.php\" method=\"post\"><input class=\"ashopaffiliatebutton\" type=\"submit\" value=\"".LINKCODES."\"></form></td><td align=\"center\"><form action=\"orderhistory.php\" method=\"post\"><input class=\"ashopaffiliatebuttonlarge\" type=\"submit\" value=\"".ORDERHISTORY."\"></form></td>";
if ($activateleads) {
	echo "	
	<td align=\"center\"><form action=\"downline.php\" method=\"post\"><input class=\"ashopaffiliatebuttonsmall\" type=\"submit\" value=\"".DOWNLINE."\"></form></td><td align=\"center\"><form action=\"leads.php\" method=\"post\"><input class=\"ashopaffiliatebuttonsmall\" type=\"submit\" value=\"".LEADS."\"></form></td><td align=\"center\"><form action=\"inbox.php\" method=\"post\"><input class=\"ashopaffiliatebuttonsmall\" type=\"submit\" value=\"".INBOX;
	if ($unreadcount) echo " ($unreadcount)";
	echo "\"></form></td>";
} else {
	echo "	
	<td align=\"center\"><form action=\"downline.php\" method=\"post\"><input class=\"ashopaffiliatebuttonlarge\" type=\"submit\" value=\"".DOWNLINE."\"></form></td><td align=\"center\"><form action=\"inbox.php\" method=\"post\"><input class=\"ashopaffiliatebutton\" type=\"submit\" value=\"".INBOX;
	if ($unreadcount) echo " ($unreadcount)";
	echo "\"></form></td>";
}
$affiliatelink = "$ashopurl/affiliate.php?id=$affiliateid";
$affiliatelinklength = strlen($affiliatelink);
echo "
	</tr></table><p><span class=\"ashopaffiliatetext1\">".YOURLINK.":</span> <input id=\"affiliatelink\" type=\"text\" size=\"$affiliatelinklength\" value=\"$ashopurl/affiliate.php?id=$affiliateid\" onclick=\"document.getElementById('affiliatelink').select();\"></p>";
if ($commissionlevel == "2") echo "<p><span class=\"ashopaffiliatetext1\">".ACCOUNTLEVEL.":</span><span class=\"ashopaffiliatetext2\"> ".UPGRADED."</span></p>";
echo "
	<p><span class=\"ashopaffiliatetext1\">".STATISTICS.":</span></p><span class=\"ashopaffiliatetext2\">
	<p>".CLICKS.": $click<br>".ORDERS.": $order";

if ($upgradeaffiliate && $commissionlevel != "2") {
	$ordersleft = $upgradeaffiliate - $order;
	echo "<br>".ORDERSLEFT.": $ordersleft";
}

if ($secondtieractivated) echo "<br>".RECRUITED.": $referrals<br>".TWOTIERORDERS.": $secondtier";

echo "<br>".TOTALEARNINGS.": ".$currencysymbols[$ashopcurrency]["pre"].number_format($provision,2,'.','')." ".$currencysymbols[$ashopcurrency]["post"]."<br>".OWEYOU.": ".$currencysymbols[$ashopcurrency]["pre"].number_format($ourdebt,2,'.','')." ".$currencysymbols[$ashopcurrency]["post"]."</p></span></font></font>";

// Get top 5 referers...
$result = @mysql_query("SELECT * FROM affiliatereferer WHERE affiliateid='$affiliateid' ORDER BY clicks DESC LIMIT 5",$db);
if (@mysql_num_rows($result)) {
	echo "
	<p><span class=\"ashopaffiliatetext1\">".TOPREFERERS.":</span><span class=\"ashopaffiliatetext2\"><br>";
	while ($row = @mysql_fetch_array($result)) {
		$referer = $row["referer"];
		$clicks = $row["clicks"];
		echo "<br>$referer - $clicks ".REFERERCLICKS;
	}
	echo "</p>";
}

// Close database...

@mysql_close($db);

// Print footer using template...
if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplatefooter("$ashoppath$templatepath/affiliate-$lang.html");
else ashop_showtemplatefooter("$ashoppath$templatepath/affiliate.html");
?>