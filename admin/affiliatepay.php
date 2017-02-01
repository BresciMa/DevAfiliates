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
include "ashopfunc.inc.php";

if (!$paymethod && !$affiliateid) {
	header ("Location: affiliatestats.php");	
	exit;
}

// Open database...
$db = @mysql_connect("$databaseserver", "$databaseuser", "$databasepasswd");
@mysql_select_db("$databasename",$db);

// Set current date and time...
$date = date("Y-m-d H:i:s", time()+$timezoneoffset);

// Get affiliate information from database...
$sql="SELECT * FROM affiliate WHERE affiliateid='$affiliateid'";
$result = @mysql_query("$sql",$db);
$firstname = @mysql_result($result, 0, "firstname");
$lastname = @mysql_result($result, 0, "lastname");
$address = @mysql_result($result, 0, "address");
$state = @mysql_result($result, 0, "state");
$zip = @mysql_result($result, 0, "zip");
$city = @mysql_result($result, 0, "city");
$paypalid = @mysql_result($result, 0, "paypalid");

// Set affiliate commission as paid...
$provision = 0;
$sql="SELECT orderaffiliate.orderid, orderaffiliate.commission FROM orders, orderaffiliate WHERE orderaffiliate.affiliateid='$affiliateid' AND (orderaffiliate.paid=0 OR orderaffiliate.paid IS NULL) AND orders.orderid=orderaffiliate.orderid";
$result = @mysql_query("$sql",$db);
$updatepaid = "off";
for ($i = 0; $i < @mysql_num_rows($result); $i++) {
	$orderid = @mysql_result($result, $i, "orderid");
	$thiscommission = @mysql_result($result, $i, "commission");
    eval ("if (\$paid$orderid == \"on\") \$updatepaid = \"on\";");
    if ($updatepaid == "on") {
		$sql="UPDATE orderaffiliate SET paid='$date', paymethod='$paymethod' WHERE orderid=$orderid AND affiliateid='$affiliateid'";
		$subresult = @mysql_query("$sql",$db);
		$updatepaid = "off";
		$provision += $thiscommission;
	}
}

$item_name = urlencode("Affiliate commission from $ashopname");
$return = "$ashopurl/admin/affiliatestats.php";
$urlprovision = urlencode(number_format(round($provision,2),2,'.',''));

// Send the shop administrator back to affiliate stats or to PayPal...
if ($paymethod == "Check") echo "$header
<div class=\"heading\">".PAYMENTTO." $firstname $lastname</div><center>
<table width=\"50%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
<tr><td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">".AMOUNTONCHECK.":</font></td><td><input type=\"text\" value=\"".number_format(round($provision,2),2,$decimalchar,$thousandchar)."\" readonly></td></tr>
<tr><td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">".CHECKPAYABLETO.":</font></td><td><input type=\"text\" value=\"$firstname $lastname\" readonly></td></tr>
<tr><td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">".SENDCHECKTO.":</font></td><td><textarea rows=\"7\" readonly>$firstname $lastname
$address
$city
$state
$zip
$country
</textarea></td></tr></table></center>$footer";


else if ($paymethod == "PayPal") {
	$paypalcurrency = "&currency_code=".strtoupper($ashopcurrency);	
	header ("Location: https://www.paypal.com/cgi-bin/webscr?business=$paypalid&item_name=$item_name&cmd=_xclick&undefined_quantity=0&no_shipping=1&no_note=1$paypalcurrency&return=$return&amount=$urlprovision&currency_code=".strtoupper($ashopcurrency));
} else if ($ashopcurrency == "tec" && !empty($paymethod) && is_numeric($paymethod)) {
	$result = @mysql_query("SELECT * FROM payoptions WHERE payoptionid='$paymethod'",$db);
	$gateway = @mysql_result($result,0,"gateway");
	$merchantid = @mysql_result($result,0,"merchantid");
	$secret = @mysql_result($result, 0, "secret");
	$hash = md5($secret."ashopcommissionpayment".$urlprovision);
	if (file_exists("$ashoppath/admin/gatewaystec/$gateway.gw")) {
		$fp = fopen ("$ashoppath/admin/gatewaystec/$gateway.gw", "r");
		while (!feof($fp)) {
			$line = fgets($fp,4096);
			if (substr($line,1,10) == "paymenturl") {
				$gatewayurl = str_replace("\$paymenturl = ","",$line);
				$gatewayurl = str_replace("=","",$gatewayurl);
				$gatewayurl = str_replace(";","",$gatewayurl);
				$gatewayurl = str_replace("\"","",$gatewayurl);
				$gatewayurl = str_replace("transaction.php","send.php",$gatewayurl);
				$gatewayurl = trim($gatewayurl);
			}
		}
		fclose($fp);
	}
	if (!empty($gatewayurl)) {
		$gatewayurl .= "?tec_vendor=$merchantid&tec_hash=$hash&amount=$urlprovision&recipientemail=$recipientemail";
		header ("Location: $gatewayurl");
	}
}
?>