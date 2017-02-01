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

// Open database...
$db = mysql_connect("$databaseserver", "$databaseuser", "$databasepasswd");
mysql_select_db("$databasename",$db);

// Delete paid commission...
if ($delete && $affiliateid) {
	if ($payment == "true") $sql="UPDATE orderaffiliate SET paid='', paymethod='' WHERE orderid='$delete'";
	else if ($chargeback == "true") $sql="DELETE FROM orderaffiliate WHERE orderid='$delete' AND commission<0";
	else $sql="DELETE FROM orderaffiliate WHERE orderid='$delete'";
	$result = @mysql_query("$sql",$db);
}

// Get affiliate information from database...
$sql="SELECT * FROM affiliate WHERE affiliateid='$affiliateid'";
$result = @mysql_query("$sql",$db);
if (@mysql_num_rows($result) == 0) {
    echo "<html><head><title>".ERRORNOSUCHAFFILIATE."</title></head>
         <body bgcolor=\"#FFFFFF\" text=\"#000000\" link=\"#000000\" vlink=\"#000000\" alink=\"#000000\"><table width=\"75%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">
	     <tr bordercolor=\"#000000\" align=\"center\"><td><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\">
 		 <tr align=\"center\"><td> <img src=\"../images/logo.gif\"><br><hr size=\"0\" noshade>
		 </td></tr></table><p><font face=\"Arial, Helvetica, sans-serif\" size=\"5\">".ERRORNOSUCHAFFILIATE."</p>
		 <p><a href=\"javascript:history.back()\">".TRYAGAIN."</a></p></font></td></tr></table></body></html>";
    exit;
}

// Store affiliate information in variables...
$firstname = mysql_result($result, 0, "firstname");
$lastname = mysql_result($result, 0, "lastname");
$click = mysql_result($result, 0, "clicks");
$lastdate = @mysql_result ($result, 0, "lastdate");

// Get statistics from database...
$selectorderids = "	<p><table width=\"60%\" border=\"0\" cellspacing=\"2\" cellpadding=\"1\" align=\"center\" bgcolor=\"#D0D0D0\">
	<tr class=\"reporthead\"><td align=\"center\">".DATETIME."</td><td align=\"center\">".AMOUNT."</td><td align=\"center\">".REFERENCE."</td><td width=\"15\"></td></tr>";


$totalprovision = 0;
$totalourdebt = 0;
$sql="SELECT orders.date, orders.orderid, orders.wholesale, orderaffiliate.* FROM orders, orderaffiliate WHERE orderaffiliate.affiliateid='$affiliateid' AND orderaffiliate.orderid=orders.orderid";
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
	  if ($secondtier) $secondtier++;
	  $totalprovision += $provision;
	  if ($provision < 0) {
		  $chargebackresult = @mysql_query("SELECT orderid FROM orders WHERE reference='$orderid' LIMIT 1",$db);
		  $linkorderid = @mysql_result($chargebackresult,0,"orderid");
	  } else $linkorderid = $orderid;

	  $selectorderids .= "<tr class=\"reportline\"><td align=\"center\">$orderdate</td><td align=\"right\"><font ";
	  if ($provision < 0) $selectorderids .= " color=\"#FF0000\">- ".$currencysymbols[$ashopcurrency]["pre"].number_format(-$provision,2,$decimalchar,$thousandchar)." ".$currencysymbols[$ashopcurrency]["post"];
	  else $selectorderids .= ">".$currencysymbols[$ashopcurrency]["pre"].number_format($provision,2,$decimalchar,$thousandchar)." ".$currencysymbols[$ashopcurrency]["post"];
	  $selectorderids .= "</font></td><td align=\"left\">&nbsp;&nbsp;<a href=\"salesreport.php?generate=true&orderid=$linkorderid\">";
	  if ($provision < 0) $selectorderids .= CHARGEBACK." ";
	  $selectorderids .= ORDERID.": $orderid";
	  if ($wholesale) $selectorderids .= " ".WHOLESALESIGN;
	  $selectorderids .= "</a>";
	  if ($secondtier) $selectorderids .= ", ".TIER." $secondtier";
	  $selectorderids .= "</td><td align=\"center\" width=\"15\">";
	  if ($provision > 0) $selectorderids .= "<a href=\"affiliatedetail.php?affiliateid=$affiliateid&delete=$orderid\"><img src=\"images/icon_trash.gif\" border=\"0\" alt=\"".DELETETRANSACTION."\" title=\"".DELETETRANSACTION."\"></a>";
	  else $selectorderids .= "<a href=\"affiliatedetail.php?affiliateid=$affiliateid&delete=$orderid&chargeback=true\"><img src=\"images/icon_trash.gif\" border=\"0\" alt=\"".DELETETRANSACTION."\" title=\"".DELETETRANSACTION."\"></a>";
	  $selectorderids .= "</td></tr>";

	  if ($paid && $provision > 0) {
		  $selectorderids .= "<tr class=\"reportline\"><td align=\"center\">$paid</td><td align=\"right\"><font color=\"#FF0000\">- ".$currencysymbols[$ashopcurrency]["pre"].number_format($provision,2,$decimalchar,$thousandchar)." ".$currencysymbols[$ashopcurrency]["post"]."</font></td><td>&nbsp;&nbsp;".PAIDBY." $paymethod</td><td><a href=\"affiliatedetail.php?affiliateid=$affiliateid&delete=$orderid&payment=true\"><img src=\"images/icon_trash.gif\" border=\"0\" alt=\"".DELETETRANSACTION."\" title=\"".DELETETRANSACTION."\"></a></td></tr>";
	  }
  }
}
$selectorderids .= "</table></p>";


// Show affiliate stats in browser...
	if (strpos($header, "title") != 0) {
		$newheader = substr($header,1,strpos($header, "title")+5);
		$newheader .= AFFILIATEDATAFOR.": $firstname $lastname - ".substr($header,strpos($header, "title")+6,strlen($header));
    } else {
		$newheader = substr($header,1,strpos($header, "TITLE")+5);
		$newheader .= AFFILIATEDATAFOR.": $firstname $lastname - ".substr($header,strpos($header, "TITLE")+6,strlen($header));
	}

echo "$header
<div class=\"heading\">".STATISTICSFOR." $firstname $lastname, ".AFFILIATEID." $affiliateid\n <a href=\"editaffiliate.php?affiliateid=$affiliateid\"><img src=\"images/icon_profile.gif\" alt=\"".PROFILEFORAFFILIATE." $affiliateid\" title=\"".PROFILEFORAFFILIATE." $affiliateid\" border=\"0\"></a>&nbsp;<a href=\"editaffiliate.php?affiliateid=$affiliateid&remove=True&fromstats=True\"><img src=\"images/icon_trash.gif\" alt=\"".DELETEAFFILIATE." $affiliateid ".FROMTHEDATABASE."\" title=\"".DELETEAFFILIATE." $affiliateid ".FROMTHEDATABASE."\" border=\"0\"></a></div><center>
	<p><font size=\"2\" face=\"Arial, Helvetica, sans-serif\">".TOTALNUMBEROFCLICKS.": $click<br>
	".TOTALNUMBEROFORDERS.": $order<br>
	".TOTALEARNINGS.": ".$currencysymbols[$ashopcurrency]["pre"].number_format($totalprovision,2,$decimalchar,$thousandchar)." ".$currencysymbols[$ashopcurrency]["post"]."<br>
	".DATEOFLASTACTIVITY.": $lastdate</font></p>
	<p><b>".COMMISSIONHISTORY."</b></p>$selectorderids</center>$footer";

// Close database...
@mysql_close($db);
?>