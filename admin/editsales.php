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
include "ashopconstants.inc.php";

// Open database...
$db = @mysql_connect("$databaseserver", "$databaseuser", "$databasepasswd");
@mysql_select_db("$databasename",$db);

if (!$orderid || !$action) {
	header("Location: salesreport.php?error=noorderselected");
	exit;
}

// Get order information from user input or database...
$sql="SELECT * FROM orders WHERE orderid='$orderid'";
$result = @mysql_query("$sql",$db);
$orderdate = @mysql_result($result, 0, "date");
$customerid = @mysql_result($result, 0, "customerid");
$remoteorderid = @mysql_result($result, 0, "invoice");
$description = trim(@mysql_result($result, 0, "product"));
$description = stripslashes($description);
$date = date("Y-m-d H:i:s", time()+$timezoneoffset);
$shortdate = date("Y-m-d", time()+$timezoneoffset);
$paid = @mysql_result($result, 0, "paid");
if ($_POST["price"]) $price = $_POST["price"];
else $price = @mysql_result($result, 0, "price");
$thisuserid = @mysql_result($result, 0, "userid");
$language = @mysql_result($result, 0, "language");

$sql="SELECT * FROM customer WHERE customerid='$customerid'";
$result = @mysql_query("$sql",$db);
$firstname = @mysql_result($result, 0, "firstname");
$lastname = @mysql_result($result, 0, "lastname");
$email = @mysql_result($result, 0, "email");
$ip = @mysql_result($result, 0, "ip");

// Delete an order...
if ($action == "delete") {
	if ($yes) {
       $sql="DELETE FROM orders WHERE orderid='$orderid'";
       $result = @mysql_query($sql,$db);
	   if ($salesreport) {
		   $reportfields = explode("|", $salesreport);
		   $reporttype = $reportfields[0];
		   $startyear = $reportfields[1];
		   $startmonth = $reportfields[2];
		   $startday = $reportfields[3];
		   $toyear = $reportfields[4];
		   $tomonth = $reportfields[5];
		   $today = $reportfields[6];
		   $orderby = $reportfields[7];
		   $ascdesc = $reportfields[8];
		   $generate = $reportfields[9];
		   if (strstr($SERVER_SOFTWARE, "IIS")) {
			   echo "<html><head><meta http-equiv=\"Refresh\" content=\"0; URL=salesreport.php?msg=deleted&reporttype=$reporttype&startyear=$startyear&startmonth=$startmonth&startday=$startday&toyear=$toyear&tomonth=$tomonth&today=$today&orderby=$orderby&ascdesc=$ascdesc&generate=$generate\"></head></html>";
			   exit;
		   } else header("Location: salesreport.php?msg=deleted&reporttype=$reporttype&startyear=$startyear&startmonth=$startmonth&startday=$startday&toyear=$toyear&tomonth=$tomonth&today=$today&orderby=$orderby&ascdesc=$ascdesc&generate=$generate");
	   } else {
		   header("Location: salesreport.php?msg=deleted");
		   exit;
	   }
    } elseif ($no) {
	   header("Location: salesreport.php");
	   exit;
	} else {
		echo "$header
		 <div class=\"heading\">".DELETEANORDER."</div><center>
        <p>".AREYOUSUREDELETEORDER."</p>
		<table width=\"540\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">
		<tr><td align=\"right\"><font size=\"2\" face=\"Arial, Helvetica, sans-serif\"><b>".ORDERID.":</b></font></td><td><font size=\"2\" face=\"Arial, Helvetica, sans-serif\">$orderid</font></td></tr>";
		if ($remoteorderid) echo "<tr><td align=\"right\"><font size=\"2\" face=\"Arial, Helvetica, sans-serif\"><b>".GATEWAYORDERID.":</b></font></td><td><font size=\"2\" face=\"Arial, Helvetica, sans-serif\">$remoteorderid</font></td></tr>";
		echo "
		<tr><td align=\"right\"><font size=\"2\" face=\"Arial, Helvetica, sans-serif\"><b>".DATEOFSALE.":</b></font></td><td><font size=\"2\" face=\"Arial, Helvetica, sans-serif\">$orderdate</font></td></tr>
		<tr><td align=\"right\"><font size=\"2\" face=\"Arial, Helvetica, sans-serif\"><b>".CUSTOMER.":</b></font></td><td><font size=\"2\" face=\"Arial, Helvetica, sans-serif\">$customerid, $firstname $lastname</font></td></tr>
		<tr><td align=\"right\"><font size=\"2\" face=\"Arial, Helvetica, sans-serif\"><b>".AMOUNT.":</b></font></td><td><font size=\"2\" face=\"Arial, Helvetica, sans-serif\">".$currencysymbols[$ashopcurrency]["pre"].number_format($price,2,$decimalchar,$thousandchar)." ".$currencysymbols[$ashopcurrency]["post"]."</font></td></tr>";
		if ($comment) echo "<tr><td align=\"right\"><font size=\"2\" face=\"Arial, Helvetica, sans-serif\"><b>".YOURCOMMENT.":</b></font></td><td><font size=\"2\" face=\"Arial, Helvetica, sans-serif\">$comment</font></td></tr>";
		echo "
		</table>	    
		<form action=\"editsales.php\" method=\"post\">
		<table width=\"540\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\">
		<tr>
        <td width=\"100%\" align=\"center\" valign=\"top\"><input type=\"submit\" name=\"yes\" value=\"".YES."\">
		<input type=\"button\" name=\"no\" value=\"".NO."\" onClick=\"javascript:history.back()\"></td>
		</tr></table><input type=\"hidden\" name=\"orderid\" value=\"$orderid\">
		<input type=\"hidden\" name=\"action\" value=\"delete\"><input type=\"hidden\" name=\"salesreport\" value=\"$salesreport\"></form>
		</center>
        $footer";
		exit;
	}
} 


// Credit an order...
if ($action == "chargeback") {
	if ($yes) {
	   $result = @mysql_query("SELECT * FROM orderaffiliate WHERE orderid='$orderid'",$db);
	   while ($row = @mysql_fetch_array($result)) {
		   $affiliateid = $row["affiliateid"];
		   $secondtier = $row["secondtier"];
		   $commission = $row["commission"];
		   $commission -= $commission*2;
		   @mysql_query("INSERT INTO orderaffiliate (affiliateid, orderid, secondtier, commission) VALUES ('$affiliateid', '$orderid', '$secondtier', '$commission')",$db);
	   }
	   $result = @mysql_query("SELECT * FROM pendingorderaff WHERE orderid='$orderid'",$db);
	   while ($row = @mysql_fetch_array($result)) {
		   $affiliateid = $row["affiliateid"];
		   $secondtier = $row["secondtier"];
		   $commission = $row["commission"];
		   $commission -= $commission*2;
		   @mysql_query("INSERT INTO pendingorderaff (affiliateid, orderid, secondtier, commission) VALUES ('$affiliateid', '$orderid', '$secondtier', '$commission')",$db);
	   }
	   $price -= $price*2;
	   $description = @mysql_real_escape_string($description,$db);
	   $sql="INSERT INTO orders (customerid, reference, invoice, product, date, paid, price, comment) VALUES ('$customerid', '$orderid', '$remoteorderid', '$description', '$date', '$paid', '$price', '$chargebackcomment')";

	   $result = @mysql_query($sql,$db);

	   $chargebackorderid = @mysql_insert_id();

	   if($salesreport) {
		   $reportfields = explode("|", $salesreport);
		   $reporttype = $reportfields[0];
		   $startyear = $reportfields[1];
		   $startmonth = $reportfields[2];
		   $startday = $reportfields[3];
		   $toyear = $reportfields[4];
		   $tomonth = $reportfields[5];
		   $today = $reportfields[6];
		   $orderby = $reportfields[7];
		   $ascdesc = $reportfields[8];
		   $generate = $reportfields[9];
		   header("Location: salesreport.php?msg=chargeback&reporttype=$reporttype&startyear=$startyear&startmonth=$startmonth&startday=$startday&toyear=$toyear&tomonth=$tomonth&today=$today&orderby=$orderby&ascdesc=$ascdesc&generate=$generate");
		   exit;
	   } else {
		   header("Location: salesreport.php?msg=chargeback");
		   exit;
	   }
    } elseif ($no) {
		if($salesreport) {
			$reportfields = explode("|", $salesreport);
			$reporttype = $reportfields[0];
			$startyear = $reportfields[1];
			$startmonth = $reportfields[2];
			$startday = $reportfields[3];
			$toyear = $reportfields[4];
			$tomonth = $reportfields[5];
			$today = $reportfields[6];
			$orderby = $reportfields[7];
			$ascdesc = $reportfields[8];
			$generate = $reportfields[9];
			header("Location: salesreport.php?reporttype=$reporttype&startyear=$startyear&startmonth=$startmonth&startday=$startday&toyear=$toyear&tomonth=$tomonth&today=$today&orderby=$orderby&ascdesc=$ascdesc&generate=$generate");
			exit;
		} else {
			header("Location: salesreport.php");
			exit;
		}
	} else {
	   	echo "$header
		<div class=\"heading\">".WILLREVERSEORDER."</div><center>
		<form action=\"editsales.php\" method=\"post\">
		<table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">
		<tr><td align=\"right\"><font size=\"2\" face=\"Arial, Helvetica, sans-serif\"><b>".ORDERID.":</b></font></td><td><font size=\"2\" face=\"Arial, Helvetica, sans-serif\">$orderid</font></td></tr>";
		if ($remoteorderid) echo "<tr><td align=\"right\"><font size=\"2\" face=\"Arial, Helvetica, sans-serif\"><b>".GATEWAYORDERID.":</b></font></td><td><font size=\"2\" face=\"Arial, Helvetica, sans-serif\">$remoteorderid</font></td></tr>";
		echo "
		<tr><td align=\"right\"><font size=\"2\" face=\"Arial, Helvetica, sans-serif\"><b>".DATEOFSALE.":</b></font></td><td><font size=\"2\" face=\"Arial, Helvetica, sans-serif\">$orderdate</font></td></tr>
		<tr><td align=\"right\"><font size=\"2\" face=\"Arial, Helvetica, sans-serif\"><b>".CUSTOMER.":</b></font></td><td><font size=\"2\" face=\"Arial, Helvetica, sans-serif\">$customerid, $firstname $lastname</font></td></tr>
		<tr><td align=\"right\"><font size=\"2\" face=\"Arial, Helvetica, sans-serif\"><b>".TOTALAMOUNTREPAID.":</b></font></td><td><input type=\"text\" name=\"price\" value=\"$price\" size=\"8\"></td></tr>
		<tr><td align=\"right\"><font size=\"2\" face=\"Arial, Helvetica, sans-serif\"><b>".RETURNEDPRODUCTS.":</b></font></td><td><font size=\"2\" face=\"Arial, Helvetica, sans-serif\">$description</font></td></tr>
		<tr><td align=\"right\"><font size=\"2\" face=\"Arial, Helvetica, sans-serif\"><b>".YOURCOMMENT.":</b></font></td><td><font size=\"2\" face=\"Arial, Helvetica, sans-serif\"><textarea name=\"chargebackcomment\" cols=\"30\" rows=\"5\"></textarea></font></td></tr>
		</table>
		<table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\">
		<tr>
        <td width=\"100%\" align=\"center\" valign=\"top\"><input type=\"submit\" name=\"yes\" value=\"".SUBMIT."\">
		<input type=\"button\" name=\"no\" value=\"".CANCEL."\" onClick=\"javascript:history.back()\"></td>
		</tr></table><input type=\"hidden\" name=\"orderid\" value=\"$orderid\">
		<input type=\"hidden\" name=\"action\" value=\"chargeback\"><input type=\"hidden\" name=\"salesreport\" value=\"$salesreport\"></form>
		</center>
        $footer";
		exit;
	}
}

// Close database...
@mysql_close($db);
?>