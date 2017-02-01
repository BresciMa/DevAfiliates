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
include "checklogin.inc.php";
include "template.inc.php";
// Get language module...
include "language/$adminlang/affiliates.inc.php";

// Open database...
$db = @mysql_connect("$databaseserver", "$databaseuser", "$databasepasswd");
@mysql_select_db("$databasename",$db);

echo "$header
<script language=\"JavaScript\">
<!--
	function selectPayMethod(paymentform) {
		if (paymentform.paymethod.value=='0') return false;
		else {
			paymentform.action='payaffiliate.php?affiliateid=$affiliateid&paymethod='+paymentform.paymethod.value;
			paymentform.submit();
		}
	}
-->
</script>
";

// Get Traffic Exchange payment options if needed...
if ($ashopcurrency == "tec") {
	$result = @mysql_query("SELECT * FROM payoptions ORDER BY name",$db);
	$paymentoptions = "<select onChange=\"selectPayMethod(paymentform)\" name=\"paymethod\"><option value=\"0\"";
	if (empty($paymethod)) $paymentoptions .= " selected";
	$paymentoptions .= ">".CHOOSE."</option>";
	while ($row = @mysql_fetch_array($result)) {
		$paymentoptions .= "<option value=\"{$row["payoptionid"]}\""; if ($paymethod==$row["payoptionid"]) $paymentoptions .= " selected"; $paymentoptions .= ">{$row["name"]}</option>";
	}
	$paymentoptions .= "</select>";
}

// Get affiliate information from database...
$sql="SELECT * FROM affiliate WHERE affiliateid='$affiliateid'";
$result = @mysql_query("$sql",$db);
$firstname = @mysql_result($result, 0, "firstname");
$lastname = @mysql_result($result, 0, "lastname");
$email = @mysql_result($result, 0, "email");

echo  "<div class=\"heading\">".PAYMENTTO." $firstname $lastname, ".AFFILIATEID." $affiliateid <a href=\"editaffiliate.php?affiliateid=$affiliateid\"><img src=\"images/icon_profile.gif\" alt=\"".PROFILEFORAFFILIATE." $affiliateid\" title=\"".PROFILEFORAFFILIATE." $affiliateid\" border=\"0\"></a>
<a href=\"affiliatedetail.php?affiliateid=$affiliateid\"><img src=\"images/icon_history.gif\" alt=\"".STATISTICSFORAFFILIATE." $affiliateid\" title=\"".STATISTICSFORAFFILIATE." $affiliateid\" border=\"0\"></a>&nbsp;<a href=\"editaffiliate.php?affiliateid=$affiliateid&remove=True&fromstats=True\"><img src=\"images/icon_trash.gif\" alt=\"".DELETEAFFILIATE." $affiliateid ".FROMTHEDATABASE."\" title=\"".DELETEAFFILIATE." $affiliateid ".FROMTHEDATABASE."\" border=\"0\"></a></div><center>";

if ($ashopcurrency == "tec") $selectorderids = "<table width=\"700\" border=\"0\" cellspacing=\"2\" cellpadding=\"1\" align=\"center\" bgcolor=\"#D0D0D0\">
	<tr bgcolor=\"#808080\"><td align=\"center\"><font face=\"Arial, Helvetica, sans-serif\" color=\"#FFFFFF\" size=\"2\"><b>".ORDERID."</b></font></td><td align=\"center\"><font face=\"Arial, Helvetica, sans-serif\" color=\"#FFFFFF\" size=\"2\"><b>".THEWORDDATE."</b></font></td><td align=\"center\"><font face=\"Arial, Helvetica, sans-serif\" color=\"#FFFFFF\" size=\"2\"><b>".TRAFFICEXCHANGE."</b></font></td><td align=\"center\" width=\"50\"><font face=\"Arial, Helvetica, sans-serif\" color=\"#FFFFFF\" size=\"2\"><b>".AMOUNT."</b></font></td><td align=\"center\"><font face=\"Arial, Helvetica, sans-serif\" color=\"#FFFFFF\" size=\"2\"><b>".PAY."</b></font></td></tr>";

else $selectorderids = "<table width=\"600\" border=\"0\" cellspacing=\"2\" cellpadding=\"1\" align=\"center\" bgcolor=\"#D0D0D0\">
	<tr bgcolor=\"#808080\"><td align=\"center\"><font face=\"Arial, Helvetica, sans-serif\" color=\"#FFFFFF\" size=\"2\"><b>".ORDERID."</b></font></td><td align=\"center\"><font face=\"Arial, Helvetica, sans-serif\" color=\"#FFFFFF\" size=\"2\"><b>".THEWORDDATE."</b></font></td><td align=\"center\" width=\"50\"><font face=\"Arial, Helvetica, sans-serif\" color=\"#FFFFFF\" size=\"2\"><b>".AMOUNT."</b></font></td><td align=\"center\"><font face=\"Arial, Helvetica, sans-serif\" color=\"#FFFFFF\" size=\"2\"><b>".PAY."</b></font></td></tr>";


$totalprovision = 0;
if ($ashopcurrency == "tec") {
	if (!empty($paymethod) && is_numeric($paymethod)) $result = @mysql_query("SELECT orderaffiliate.*, orders.payoptionid, orders.wholesale, orders.date FROM orderaffiliate, orders WHERE orderaffiliate.affiliateid='$affiliateid' AND orderaffiliate.orderid=orders.orderid AND (orderaffiliate.paid=0 OR orderaffiliate.paid IS NULL) AND orders.payoptionid='$paymethod'",$db);
	else $result = @mysql_query("SELECT orders.date, orders.wholesale, orders.payoptionid, orderaffiliate.* FROM orders, orderaffiliate WHERE orderaffiliate.affiliateid='$affiliateid' AND orderaffiliate.orderid=orders.orderid AND (orderaffiliate.paid=0 OR orderaffiliate.paid IS NULL)",$db);
} else $result = @mysql_query("SELECT orders.date, orders.wholesale, orderaffiliate.* FROM orders, orderaffiliate WHERE orderaffiliate.affiliateid='$affiliateid' AND orderaffiliate.orderid=orders.orderid AND (orderaffiliate.paid=0 OR orderaffiliate.paid IS NULL)",$db);
if (@mysql_num_rows($result) != 0) {
  for ($i = 0; $i < @mysql_num_rows($result);$i++) {
	  $provision = @mysql_result($result, $i, "commission");
	  $orderdate = @mysql_result($result, $i, "date");
	  $orderid = @mysql_result($result, $i, "orderid");
	  $wholesale = @mysql_result($result, $i, "wholesale");
	  if ($ashopcurrency == "tec") $payoptionid = @mysql_result($result, $i, "payoptionid");
	  if (!empty($payoptionid) && is_numeric($payoptionid)) {
		  $payoptionresult = @mysql_query("SELECT name FROM payoptions WHERE payoptionid='$payoptionid'",$db);
		  $payoptionname = @mysql_result($payoptionresult,0,"name");
	  }
	  //$chargebackresult = @mysql_query("SELECT orderid FROM orders WHERE reference='$orderid' LIMIT 1",$db);
	  //if (!@mysql_num_rows($chargebackresult) && $provision > 0) {
		  $totalprovision += $provision;
		  $selectorderids .= "<tr><td align=\"center\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\"><a href=\"salesreport.php?generate=true&orderid=$orderid\">$orderid";
		  if ($wholesale) $selectorderids .= " ".WHOLESALESIGN;
		  $selectorderids .= "</a></font></td><td align=\"center\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">$orderdate</font></td>";
		  if ($ashopcurrency == "tec") $selectorderids .= "<td align=\"center\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">$payoptionname</font></td>";
		  $selectorderids .= "<td align=\"right\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">".$currencysymbols[$ashopcurrency]["pre"].number_format($provision,2,$decimalchar,$thousandchar)." ".$currencysymbols[$ashopcurrency]["post"]."</font></td><td align=\"center\"><input type=\"checkbox\" name=\"paid$orderid\" checked></td></tr>";
	  //}
  }
}
$selectorderids .= "</table>";

echo "<form method=\"post\" action=\"affiliatepay.php\" name=\"paymentform\">
<p><font face=\"Arial, Helvetica, sans-serif\" size=\"3\">".TOTALUNPAIDCOMMISSION.": <b>".$currencysymbols[$ashopcurrency]["pre"].number_format($totalprovision,2,$decimalchar,$thousandchar)." ".$currencysymbols[$ashopcurrency]["post"]."</b></font></p>
<p>$selectorderids</p>
<table width=\"400\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
<tr><td width=\"120\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">";
if ($ashopcurrency == "tec") {
	echo PAYCREDITS.":</font></td><td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">$paymentoptions</font></td></tr>";
	if (!empty($paymethod) && is_numeric($paymethod)) echo "
	<tr><td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">".RECIPIENTEMAIL.":</font></td><td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\"><input type=\"text\" name=\"recipientemail\" size=\"43\" value=\"$email\"></font></td></tr><tr><td>&nbsp;</td><td align=\"right\"><input type=\"hidden\" name=\"affiliateid\" value=\"$affiliateid\"><input type=\"hidden\" name=\"paymethod\" value=\"$paymethod\"><input type=\"submit\" name=\"check\" value=\"".PAYNOW."\"></td></tr>";
} else {
	echo PAYSELECTEDBY.":</font></td><td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\"><input type=\"radio\" name=\"paymethod\" value=\"PayPal\"> PayPal <input type=\"radio\" name=\"paymethod\" value=\"Check\" checked> ".CHECK."</font></td></tr>";
}
echo "</table>
<br><input type=\"hidden\" name=\"affiliateid\" value=\"$affiliateid\">";
if ($ashopcurrency != "tec") echo "<input type=\"submit\" name=\"check\" value=\"".MARKASPAID."\">";
echo "</form></center>$footer";
?>