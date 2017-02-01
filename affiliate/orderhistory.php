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
include "../language/$lang/af_orderhistory.inc.php";

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

// Get statistics from database...
$selectorderids = "	<p><table class=\"ashopaffiliatehistorybox\" border=\"0\" cellspacing=\"2\" cellpadding=\"1\" align=\"center\">
	<tr class=\"ashopaffiliatehistoryrow\"><td align=\"left\"><span class=\"ashopaffiliatehistorytext1\">&nbsp;".REFERENCE."</span></td><td align=\"left\" width=\"150\"><span class=\"ashopaffiliatehistorytext1\">&nbsp;".DATETIME."</span></td><td align=\"center\" width=\"100\"><span class=\"ashopaffiliatehistorytext1\">".AMOUNT."</span></td></tr>";

$totalprovision = 0;
$totalourdebt = 0;
$sql="SELECT orders.date, orders.wholesale, orders.orderid, orderaffiliate.* FROM orders, orderaffiliate WHERE orderaffiliate.affiliateid='$affiliateid' AND orderaffiliate.orderid=orders.orderid";
$result = @mysql_query("$sql",$db);
$order = @mysql_num_rows($result);
if (@mysql_num_rows($result) != 0) {
  for ($i = 0; $i < @mysql_num_rows($result);$i++) {
	  $orderdate = @mysql_result($result, $i, "date");
	  $orderid = @mysql_result($result, $i, "orderid");
	  $wholesale = @mysql_result($result, $i, "wholesale");
	  $paid = @mysql_result($result, $i, "paid");
	  $paymethod = @mysql_result($result, $i, "paymethod");
	  $provision = @mysql_result($result, $i, "commission");
	  $secondtier = @mysql_result($result, $i, "secondtier");
	  $tierlevel = $secondtier+1;
	  if (!$paid) $ourdebt += $provision;

	  $selectorderids .= "<tr><td align=\"left\"><span class=\"ashopaffiliatetext3\">";
	  if ($provision < 0) $selectorderids .= "Chargeback ";
	  $selectorderids .= "Order ID: $orderid";
	  if ($wholesale) $selectorderids .= " W";
	  if ($secondtier) $selectorderids .= ", ".TIER2." $tierlevel";
	  $selectorderids .= "</span></td><td><span class=\"ashopaffiliatetext3\">$orderdate</span></td><td align=\"right\"><span class=\"ashopaffiliatetext2\">";
	  if ($provision < 0) $selectorderids .= "<font color=\"red\">- ".$currencysymbols[$ashopcurrency]["pre"].number_format(-$provision,2,'.','')." ".$currencysymbols[$ashopcurrency]["post"];
	  else $selectorderids .= $currencysymbols[$ashopcurrency]["pre"].number_format($provision,2,'.','')." ".$currencysymbols[$ashopcurrency]["post"];
	  if ($provision < 0) $selectorderids .= "</font>";
	  $selectorderids .= "</span></td></tr>";

	  if ($paid && $provision > 0) {
		  $chargebackresult = @mysql_query("SELECT orderid FROM orderaffiliate WHERE orderid='$orderid' AND commission<0 AND paid>0 AND paid IS NOT NULL",$db);
		  if (!@mysql_num_rows($chargebackresult)) {
			  $selectorderids .= "<tr><td align=\"left\"><span class=\"ashopaffiliatetext3\">".PAIDBY." $paymethod</span></td><td><span class=\"ashopaffiliatetext3\">$paid</span></td><td align=\"right\"><span class=\"ashopaffiliatehistorytext2\">- ".$currencysymbols[$ashopcurrency]["pre"].number_format($provision,2,'.','')." ".$currencysymbols[$ashopcurrency]["post"]."</span></td></tr>";
		  }
	  }
  }
}
$selectorderids .= "<tr class=\"ashopaffiliatehistoryrow\"><td colspan=\"2\" align=\"right\"><span class=\"ashopaffiliatehistorytext1\">".TOTALUNPAID.":</span></td><td align=\"right\"><span class=\"ashopaffiliatehistorytext1\">".$currencysymbols[$ashopcurrency]["pre"].number_format($ourdebt,2,'.','')." ".$currencysymbols[$ashopcurrency]["post"]."</span></td></tr></table></p>";

// Get number of unread PMs...
$sql="SELECT * FROM affiliatepm WHERE toaffiliateid='$affiliateid' AND (hasbeenread='' OR hasbeenread='0' OR hasbeenread IS NULL)";
$unreadresult = @mysql_query("$sql",$db);
$unreadcount = @mysql_num_rows($unreadresult);

// Print header from template...
if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplateheader("$ashoppath$templatepath/affiliate-$lang.html");
else ashop_showtemplateheader("$ashoppath$templatepath/affiliate.html");

echo "<br><span class=\"ashopaffiliateheader\">".WELCOME." $firstname $lastname! ".AFFILIATEID.": $affiliateid</span>
	<table align=\"center\" width=\"400\"><tr><td align=\"center\"><form action=\"affiliate.php\" method=\"post\"><input class=\"ashopaffiliatebutton\" type=\"submit\" value=\"".STATISTICS."\"></form></td><td align=\"center\"><form action=\"changeprofile.php\" method=\"post\"><input class=\"ashopaffiliatebuttonlarge\" type=\"submit\" value=\"".VIEWPROFILE."\"></form></td><td align=\"center\"><form action=\"changepassword.php\" method=\"post\"><input class=\"ashopaffiliatebuttonlarge\" type=\"submit\" value=\"".CHANGEPASS."\"></form></td><td align=\"center\"><form action=\"login.php?logout\" method=\"post\"><input class=\"ashopaffiliatebutton\" type=\"submit\" value=\"".LOGOUT."\"></form></td></tr></table>
	<table align=\"center\" width=\"400\"><tr><td align=\"center\"><form action=\"linkcodes.php\" method=\"post\"><input class=\"ashopaffiliatebutton\" type=\"submit\" value=\"".LINKCODES."\"></form></td><td align=\"center\"><input class=\"ashopaffiliatebuttonlarge\" type=\"button\" value=\"".ORDERHISTORY."\" disabled></td>";
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
echo "
	</tr></table>
	<br><span class=\"ashopaffiliateheader\">".COMMISSIONHISTORY."</span>$selectorderids";

// Print footer using template...
if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplatefooter("$ashoppath$templatepath/affiliate-$lang.html");
else ashop_showtemplatefooter("$ashoppath$templatepath/affiliate.html");

// Close database...
@mysql_close($db);
?>