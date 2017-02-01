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

include "config.inc.php";
include "ashopconstants.inc.php";
include "checklogin.inc.php";
include "template.inc.php";
// Get language module...
include "language/$adminlang/affiliates.inc.php";
// Get context help for this page...
$contexthelppage = "affiliatestats";
include "help.inc.php";

// Open database...
$db = @mysql_connect("$databaseserver", "$databaseuser", "$databasepasswd");
@mysql_select_db("$databasename",$db);

echo "$header
<div class=\"heading\">".STATISTICSANDPAYMENT." <a href=\"$help1\" target=\"_blank\"><img src=\"images/icon_helpsm.gif\" width=\"15\" height=\"15\" border=\"0\"></a></div><center>
<form action=\"affiliatestats.php?resultpage=$resultpage&admindisplayitems=$admindisplayitems\" method=\"post\" name=\"affiliatefilterform\" style=\"margin-bottom: 0px;\"><span class=\"text\">".FILTERBYNAME.": <input type=\"text\" name=\"namefilter\" value=\"$namefilter\" size=\"10\"> <input type=\"submit\" value=\"".FILTER."\"></span><br><br>
      <table width=\"80%\" border=\"0\" cellspacing=\"1\" cellpadding=\"0\" align=\"center\" bgcolor=\"#D0D0D0\">
      <tr class=\"reporthead\"><td align=\"left\">".IDNAME."</td><td align=\"center\" width=\"60\">".CLICKS."</td><td align=\"center\" width=\"80\">".ORDERS."</td><td align=\"center\" width=\"150\">".DOWNLINEORDERS."</td><td align=\"center\" width=\"70\">".EARNED."</td><td align=\"center\" width=\"70\">".UNPAID."</td><td align=\"center\" width=\"90\">".ACTION."</td></tr>";

// Reset statistics...
$totalclicks = 0;
$totalorders = 0;
$totalprovision = 0;
$totalourdebt = 0;
$totaldownline = 0;

// Get affiliate information from database...
$sql="SELECT * FROM affiliate";
if ($namefilter) $sql .= " WHERE firstname  LIKE '%$namefilter%' OR lastname LIKE '%$namefilter%'";
$sql .=" ORDER BY affiliateid";
$result = @mysql_query("$sql",$db);
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
	$thisrow++;
	$provision = 0;
	$ourdebt = 0;
    $firstname = $row["firstname"];
    $lastname = $row["lastname"];
	$email = $row["email"];
    $affiliateid = $row["affiliateid"];
	$clicks = $row["clicks"];
	$totalclicks += $clicks;
	if (!$clicks) $clicks = 0;
	$sql="SELECT orderaffiliate.commission, orderaffiliate.paid FROM orders, orderaffiliate WHERE orderaffiliate.affiliateid='$affiliateid' AND (orderaffiliate.secondtier=0 OR orderaffiliate.secondtier IS NULL) AND orders.orderid=orderaffiliate.orderid";
	$result2 = @mysql_query("$sql",$db);
	$orders = @mysql_num_rows($result2);
	$totalorders += $orders;
	if (@mysql_num_rows($result2) != 0) {
		for ($j = 0; $j < @mysql_num_rows($result2);$j++) {
			$commission = @mysql_result($result2, $j, "commission");
			$paid = @mysql_result($result2, $j, "paid");
			$provision += $commission;
			if (!$paid) $ourdebt += $commission;
		}
	}
	$sql="SELECT orderaffiliate.commission, orderaffiliate.paid FROM orders, orderaffiliate WHERE orderaffiliate.affiliateid='$affiliateid' AND orderaffiliate.secondtier!=0 AND orderaffiliate.secondtier IS NOT NULL AND orders.orderid=orderaffiliate.orderid";
	$result2 = @mysql_query("$sql",$db);
	$secondtier = @mysql_num_rows($result2);
	if (@mysql_num_rows($result2) != 0) {
		for ($j = 0; $j < @mysql_num_rows($result2);$j++) {
			$commission = @mysql_result($result2, $j, "commission");
			$paid = @mysql_result($result2, $j, "paid");
			$provision += $commission;
			if (!$paid) $ourdebt += $commission;
		}
	}
	if (!$secondtier) $secondtier = "0";
	$totaldownline += $secondtier;
	echo "<tr class=\"reportline\"><td align=\"left\">$affiliateid, <a href=\"editaffiliate.php?affiliateid=$affiliateid&fromstats=True\">$firstname $lastname</a></td><td align=\"center\">$clicks</td><td align=\"center\">$orders</td><td align=\"center\">$secondtier</td><td align=\"right\">".$currencysymbols[$ashopcurrency]["pre"].number_format($provision,2,$decimalchar,$thousandchar)." ".$currencysymbols[$ashopcurrency]["post"]."</td><td align=\"right\">";
	if ($ourdebt) echo $currencysymbols[$ashopcurrency]["pre"].number_format($ourdebt,2,$decimalchar,$thousandchar)." ".$currencysymbols[$ashopcurrency]["post"];
	echo "</td><td align=\"center\">";
	if ($ourdebt) echo "<a href=\"payaffiliate.php?affiliateid=$affiliateid\"><img src=\"images/icon_pay.gif\" alt=\"".PAYAFFILIATE." $affiliateid.\" title=\"".PAYAFFILIATE." $affiliateid.\" border=\"0\"></a>&nbsp;";
	else echo "<img src=\"images/spacer.gif\" width=\"15\" border=\"0\"></a>&nbsp;";
	echo "<a href=\"editaffiliate.php?affiliateid=$affiliateid\"><img src=\"images/icon_profile.gif\" alt=\"".PROFILEFORAFFILIATE." $affiliateid\" title=\"".PROFILEFORAFFILIATE." $affiliateid\" border=\"0\"></a>&nbsp;<a href=\"affiliatedetail.php?affiliateid=$affiliateid\"><img src=\"images/icon_history.gif\" alt=\"".STATISTICSFORAFFILIATE." $affiliateid\" title=\"".STATISTICSFORAFFILIATE." $affiliateid\" border=\"0\"></a>&nbsp;<a href=\"editaffiliate.php?affiliateid=$affiliateid&remove=True&fromstats=True\"><img src=\"images/icon_trash.gif\" alt=\"".DELETEAFFILIATE." $affiliateid ".FROMTHEDATABASE."\" title=\"".DELETEAFFILIATE." $affiliateid ".FROMTHEDATABASE."\" border=\"0\"></a></td></tr>";
	$totalprovision += $provision;
	$totalourdebt += $ourdebt;
}

echo "<tr><td class=\"reporttotal\" align=\"right\">".TOTALS." </td><td class=\"reporttotal\" align=\"center\">$totalclicks</td><td class=\"reporttotal\" align=\"center\">$totalorders</td><td class=\"reporttotal\" align=\"center\">$totaldownline</td><td class=\"reporttotal\" align=\"right\"> ".$currencysymbols[$ashopcurrency]["pre"].number_format($totalprovision,2,$decimalchar,$thousandchar)." ".$currencysymbols[$ashopcurrency]["post"]."</td><td class=\"reporttotal\" align=\"right\"> ".$currencysymbols[$ashopcurrency]["pre"].number_format($totalourdebt,2,$decimalchar,$thousandchar)." ".$currencysymbols[$ashopcurrency]["post"]."</td><td class=\"reporttotal\">&nbsp;</td></tr></table>";
if ($numberofrows > 5) {
	echo "<table width=\"100%\" border=\"0\" cellpadding=\"5\" cellspacing=\"0\"><tr><td align=\"center\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">";
	if ($numberofpages > 1) {
		echo "<b>".PAGE.": </b>";
		if ($resultpage > 1) {
			$previouspage = $resultpage-1;
			echo "<<<a href=\"affiliatestats.php?resultpage=$previouspage&admindisplayitems=$admindisplayitems&namefilter=$namefilter\"><b>".PREVIOUS."</b></a>&nbsp;&nbsp;";
		}
		$page = 1;
		for ($i = $startpage; $i <= $numberofpages; $i++) {
			if ($page > 20) break;
			if ($i != $resultpage) echo "<a href=\"affiliatestats.php?resultpage=$i&admindisplayitems=$admindisplayitems&namefilter=$namefilter\">";
			echo "$i";
			if ($i != $resultpage) echo "</a>";
			echo "&nbsp;&nbsp;";
			$page++;
		}
		if ($resultpage < $numberofpages) {
			$nextpage = $resultpage+1;
			echo "<a href=\"affiliatestats.php?resultpage=$nextpage&admindisplayitems=$admindisplayitems&namefilter=$namefilter\"><b>".NEXTPAGE."</b></a>>>";
		}
	}
	echo " ".DISPLAY.": <select name=\"admindisplayitems\" onChange=\"document.location.href='affiliatestats.php?resultpage=$resultpage&namefilter=$namefilter&admindisplayitems='+affiliatefilterform.admindisplayitems.value;\"><option value=\"$numberofrows\">".SELECT."</option><option value=\"5\">5</option><option value=\"10\">10</option><option value=\"20\">20</option><option value=\"40\">40</option><option value=\"$numberofrows\">".ALL."</option></select> ".AFFILIATES2."</form></td></tr></table>
	";
}
echo "</center>$footer";
?>