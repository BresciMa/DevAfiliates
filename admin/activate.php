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
if ($activate) $orderid = $activate;
include "ashopfunc.inc.php";
include "ashopconstants.inc.php";

// Open database...
$db = @mysql_connect("$databaseserver", "$databaseuser", "$databasepasswd");
@mysql_select_db("$databasename",$db);

// Get order info...
if (substr($orderid, 0, 2) == "ws") {
	$orderid = substr($orderid, 2);
	$wholesaleorder = TRUE;
} else $wholesaleorder = FALSE;

$sql="SELECT * FROM orders WHERE orderid='$orderid'";
$result = @mysql_query("$sql",$db);
$customerid = @mysql_result($result, 0, "customerid");

// Mark the order as paid...
$date = date("Y-m-d H:i:s", time()+$timezoneoffset);
$sql="UPDATE orders SET paid='$date' WHERE orderid='$orderid'";
$result = @mysql_query("$sql",$db);

// Give affiliate credit...
$sql = "SELECT * FROM pendingorderaff WHERE orderid='$orderid' AND secondtier=0";
$result = @mysql_query("$sql",$db);
$affiliateid = @mysql_result($result,0,"affiliateid");
$commission = @mysql_result($result,0,"commission");
$affiliatelevel = @mysql_result($result,0,"commissionlevel");
if ($affiliateid && $commission) {
	$sql="INSERT INTO orderaffiliate (affiliateid, orderid, paid, secondtier, commission) VALUES ($affiliateid, $orderid, 0, 0, '$commission')";
	$result = @mysql_query("$sql", $db);

	$sql="DELETE FROM pendingorderaff WHERE orderid=$orderid AND secondtier=0";
	$result = @mysql_query("$sql", $db);
	
	$sql="SELECT email FROM affiliate WHERE affiliateid=$affiliateid";
	$result = @mysql_query("$sql", $db);
	$affiliatemail = ashop_mailsafe(@mysql_result($result, 0, "email"));

	// Check if the affiliate should be upgraded...
	if ($affiliatelevel == "1" && !empty($upgradeaffiliate) && $upgradeaffiliate > 0) {
		$sql="SELECT orderid FROM orderaffiliate WHERE affiliateid='$affiliateid'";
		$result = @mysql_query("$sql",$db);
		$affiliateorders = @mysql_num_rows($result);
		if ($affiliateorders >= $upgradeaffiliate) $result = @mysql_query("UPDATE affiliate SET commissionlevel='2' WHERE affiliateid='$affiliateid'",$db);
	}

	// Notify affiliate by email...
	$message="<html><head><title>Your link to $ashopname has generated a sale!</title></head><body><font face=\"$font\"><p>Your link to $ashopname generated a sale</p><p>Thank you for your help!</p><p>You can log in to check how much you have earned at: <a href=\"$ashopurl/affiliate/login.php\">$ashopurl/affiliate/login.php</a></p></font></body></html>";
	$headers = "From: ".un_html($ashopname)."<$affiliaterecipient>\nX-Sender: <$affiliaterecipient>\nX-Mailer: PHP\nX-Priority: 3\nReturn-Path: <$affiliaterecipient>\nMIME-Version: 1.0\nContent-Type: text/html; charset=iso-8859-1\n";

	@ashop_mail("$affiliatemail",un_html($ashopname)." affiliate notification","$message","$headers");
}

// Give referring affiliates credit if necessary...
$sql = "SELECT * FROM pendingorderaff WHERE orderid='$orderid' AND secondtier!='0' AND secondtier IS NOT NULL";
$result = @mysql_query("$sql",$db);
while ($row = @mysql_fetch_array($result)) {
	$affiliateid = $row["affiliateid"];
	$commission = $row["commission"];
	$secondtier = $row["secondtier"];
	if ($affiliateid && $commission) {
		$sql="DELETE FROM pendingorderaff WHERE orderid=$orderid AND secondtier=1";
		$result = @mysql_query("$sql", $db);

		if ($secondtieractivated) {
			$sql="INSERT INTO orderaffiliate (affiliateid, orderid, paid, secondtier, commission) VALUES ($affiliateid, $orderid, 0, '$secondtier', '$commission')";
			$result = @mysql_query("$sql", $db);

			$sql="SELECT email FROM affiliate WHERE affiliateid=$affiliateid";
			$result = @mysql_query("$sql", $db);
			$affiliatemail = ashop_mailsafe(@mysql_result($result, 0, "email"));
			
			// Notify affiliate by email...
			$message="<html><head><title>A link from an affiliate you have referred to $ashopname has generated a sale!</title></head><body><font face=\"$font\"><p>A link from an affiliate you have referred to $ashopname generated a sale</p><p>Thank you for your help!</p><p>You can log in to check how much you have earned at: <a href=\"$ashopurl/affiliate/login.php\">$ashopurl/affiliate/login.php</a></p></font></body></html>";
			$headers = "From: ".un_html($ashopname)."<$affiliaterecipient>\nX-Sender: <$affiliaterecipient>\nX-Mailer: PHP\nX-Priority: 3\nReturn-Path: <$affiliaterecipient>\nMIME-Version: 1.0\nContent-Type: text/html; charset=iso-8859-1\n";
			
			@ashop_mail("$affiliatemail",un_html($ashopname)." affiliate notification","$message","$headers");
		}
	}
}

if($salesreport && ($unlockkeystring || $subscriptionlinks || $downloadable)) {
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
	header("Location: salesreport.php?msg=activated&reporttype=$reporttype&startyear=$startyear&startmonth=$startmonth&startday=$startday&toyear=$toyear&tomonth=$tomonth&today=$today&orderby=$orderby&ascdesc=$ascdesc&generate=$generate");
	exit;
}

if ($tocustomer == "true") {
	header("Location: editcustomer.php?customerid=$customerid&activate=true");
	exit;
}

// Close database...
@mysql_close($db);

if ($unlockkeystring || $subscriptionlinks || $downloadable) header("Location: salesadmin.php?activate=true&salesreport=$salesreport");
else header("Location: salesadmin.php?activate=$orderid&salesreport=$salesreport");
?>