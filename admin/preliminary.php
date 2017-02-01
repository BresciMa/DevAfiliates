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
include "language/$adminlang/customers.inc.php";
include "ashopfunc.inc.php";
include "ashopconstants.inc.php";

// Convert translated buttons...
if ($generate == "Redigera") $generate = "Edit";
if ($generate == "Töm") $generate = "Clear";

// Open database...
$db = @mysql_connect("$databaseserver", "$databaseuser", "$databasepasswd");
@mysql_select_db("$databasename",$db);

if ($generate == "Clear") {
	$result = @mysql_query("SELECT * FROM orders WHERE date IS NULL ORDER BY orderid DESC LIMIT 10",$db);
	$keepfrom = @mysql_result($result, 9, "orderid");
	@mysql_query("DELETE FROM orders WHERE date IS NULL AND orderid<'$keepfrom'",$db);
	echo "$header<center><p align=\"center\"><font face=\"Arial, Helvetica, sans-serif\" size=\"3\" color=\"#009000\"><b>".INCOMPLETEORDERSCLEARED."</b><br><font size=\"2\">".LASTTENKEPT."</font></font></p></center>$footer";
	exit;
}

echo "$header
<center>";

// Show "Please wait" page while completing the search...
if(!$showresult) {
	foreach ($_POST as $field => $value) $getquerystring .= "&$field=$value";
	foreach ($_GET as $field => $value) $getquerystring .= "&$field=$value";
	echo "<p class=\"heading\">".GENERATINGREPORT."</p><br></center><meta http-equiv=\"Refresh\" content=\"0; URL=preliminary.php?showresult=true$getquerystring\"></td></tr></table></center>$footer";
	exit;
}
ob_start();

echo "<div class=\"heading\">".SALESREPORT."</div><p>".INCOMPLETEORDERS."</p>
<table width=\"96%\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\" align=\"center\" bgcolor=\"#C0C0C0\">
<tr class=\"reportheadsm\"><td nowrap>".THEWORDDATE."</td>
<td nowrap>".ORDERID."</td>
<td>".PRODUCTS."</td><td>".AMOUNT."</td>
<td>".CUSTOMER."</td>";
if ($reporttype != "wholesale") echo "<td>&nbsp;</td></tr>";
$subtotal = 0;

// Get order information from database...
$sql="SELECT * FROM orders WHERE date IS NULL ORDER BY orderid DESC";
$result = @mysql_query("$sql",$db);
$rowcolor = "#E0E0E0";
for ($i = 0; $i < @mysql_num_rows($result); $i++) {
	$orderid = @mysql_result($result, $i, "orderid");
	$price = @mysql_result($result, $i, "price");
	$tax = @mysql_result($result, $i, "tax");
	$shipping = @mysql_result($result, $i, "shipping");
	$timedate = explode(" ", @mysql_result($result, $i, "tempdate"));
	$tempdate = $timedate[0];
	$temptime = explode(":",$timedate[1]);
	$subtotal += $price;
	$descriptionstring = @mysql_result($result, $i, "description");
	if (!$descriptionstring) {
		$productsincart = ashop_parseproductstring($db, @mysql_result($result, $i, "products"));
		if ($productsincart) foreach($productsincart as $productnumber => $thisproduct) {
			$descriptionstring .= $thisproduct["quantity"].": ".$thisproduct["name"].$thisproduct["parameters"];
			if (count($productsincart) > 1 && $productnumber < count($productsincart)-1) $descriptionstring .= ", ";
		}
	}
	$displaydescr = str_replace(",", "<br>", $descriptionstring);
	$thiscustomerid = @mysql_result($result, $i, "customerid");
	if ($thiscustomerid == "0") $customername = "Unknown";
	else {
		$sql = "SELECT * FROM shipping WHERE shippingid='$thiscustomerid'";
		$result3 = @mysql_query("$sql", $db);
		$shippingfirstname = @mysql_result($result3, 0, "shippingfirstname");
		$shippinglastname = @mysql_result($result3, 0, "shippinglastname");
		if (!$shippingfirstname && !$shippinglastname) $customername = UNKNOWN;
		else $customername = "$shippingfirstname $shippinglastname";
	}
	echo "<tr bgcolor=\"$rowcolor\"><td><font face=\"Arial, Helvetica, sans-serif\" size=\"1\">";
	if ($tempdate) echo "$tempdate<br>{$temptime[0]}:{$temptime[1]}";
	else echo "Unknown";
	echo "</font></td><td><font face=\"Arial, Helvetica, sans-serif\" size=\"1\">$orderid</font></td><td><font face=\"Arial, Helvetica, sans-serif\" size=\"1\">$displaydescr</font></td><td><font face=\"Arial, Helvetica, sans-serif\" size=\"1\">".$currencysymbols[$ashopcurrency]["pre"].number_format($price,2,$decimalchar,$thousandchar).$currencysymbols[$ashopcurrency]["post"]."</font></td><td><font face=\"Arial, Helvetica, sans-serif\" size=\"1\">$customername</font></td>";
	if ($reporttype != "wholesale") echo "<td align=\"center\"><font face=\"Arial, Helvetica, sans-serif\" size=\"1\">";
	if ($reporttype != "wholesale") {
		echo "<a href=\"editpreliminary.php?orderid=$orderid&action=complete\"><img src=\"images/icon_activatem.gif\" alt=\"".COMPLETEORDER."\" title=\"".COMPLETEORDER."\" border=\"0\"></a>&nbsp;<a href=\"editpreliminary.php?orderid=$orderid&action=delete\"><img src=\"images/icon_trash.gif\" alt=\"".DELETEORDERID." $orderid ".FROMDB."\" title=\"".DELETEORDERID." $orderid ".FROMDB."\" border=\"0\"></a>";
	}
	echo "</font></a></td></tr>";
	if ($rowcolor == "#C0C0C0") $rowcolor = "#E0E0E0";
	else $rowcolor = "#C0C0C0";
}
echo "</table><br><br></center>$footer";
ob_end_flush();
?>