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

// Include configuration file and functions...
if (!$databaseserver || !$databaseuser) include "admin/config.inc.php";
if (!function_exists('ashop_mailsafe')) include "admin/ashopfunc.inc.php";
if (!isset($currencynames)) include "admin/ashopconstants.inc.php";

// Open database...
if (!is_resource($db) || get_resource_type($db) !== 'mysql link') {
	$errorcheck = ashop_opendatabase();
	if ($errorcheck) $error = $errorcheck;
}

// Open database...
$db = @mysql_connect("$databaseserver", "$databaseuser", "$databasepasswd");
@mysql_select_db("$databasename",$db);

echo "---" . $affiliate;

// Get parameters...
$parsed_email=ashop_mailsafe($email);
if (!ashop_is_email($parsed_email)) $parsed_email = "";
$parsed_firstname=ashop_mailsafe($firstname);
$parsed_lastname=ashop_mailsafe($lastname);
$parsed_price=number_format($price,2,'.','');
if (!is_numeric($parsed_price)) $parsed_price = "";
$parsed_customerid=$customerid;
$parsed_affiliate=$affiliate;
if (!is_numeric($parsed_affiliate)) exit;
$parsed_invoice = $invoice;
$parsed_ipnumber = $_SERVER["REMOTE_ADDR"];
$parsed_product = $product;

$dbsafe_firstname = $parsed_firstname;
$dbsafe_lastname = $parsed_lastname;
$dbsafe_email = $parsed_email;
$dbsafe_customerid = $parsed_customerid;
$dbsafe_price = $parsed_price;
$dbsafe_invoice = $parsed_invoice;
$dbsafe_ipnumber = $parsed_ipnumber;
$dbsafe_product = $parsed_product;

// Check if the customer is black listed...
$result = @mysql_query("SELECT * FROM customerblacklist WHERE blacklistitem='$parsed_ipnumber' OR blacklistitem='$dbsafe_email'",$db);
if (@mysql_num_rows($result)) exit;

// Check existing customer records...
$sql = "SELECT affiliateid, customerid FROM customer";
if (!empty($dbsafe_customerid)) $sqlwhere = " WHERE remotecustomerid='$dbsafe_customerid'";
if (!empty($dbsafe_email)) {
	if (!empty($sqlwhere)) $sqlwhere .= " OR email='$dbsafe_email'";
	else $sqlwhere = " WHERE email='$dbsafe_email'";
}
$sql .= $sqlwhere;

$result = @mysql_query($sql,$db);
if (@mysql_num_rows($result)) {
	$parsed_affiliate = @mysql_result($result,0,"affiliateid");
	$customerid = @mysql_result($result,0,"customerid");
}

// Include language file...
if (!$lang) $lang = $defaultlanguage;
include "language/$lang/order.inc.php";

// Check if the order is not a duplicate...
if (!empty($parsed_invoice)) {
	$sql="SELECT * FROM orders WHERE invoice = '$parsed_invoice'";
	$result = @mysql_query($sql,$db);
	if (@mysql_num_rows($result)) exit;
}

// Calculate affiliate commission...
$provision = 0;
$provision2 = 0;
$secondtierprovision = 0;
$secondtierprovision2 = 0;
$tierprovision = array();

// Calculate level 1 affiliate commission...
if (empty($wholesale) || $wholesale != "1") {
	$thisaffcomtype = "percent";
	$thisaffcom = $affiliatepercent;
} else if ($wholesaleaffiliate == "1") {
	$thisaffcomtype = "percent";
	$thisaffcom = $wholesalepercent;
}
if ($thisaffcomtype == "percent") $provision += $parsed_price * ($thisaffcom/100);
else if ($thisaffcomtype == "money") $provision += $thisquantity * $thisaffcom;

// Calculate level 2 affiliate commission...
if (empty($wholesale) || $wholesale != "1") {
	$thisaffcomtype = "percent";
	$thisaffcom = $affiliatepercent2;
	if ($thisaffcomtype == "percent") $provision2 += $parsed_price * ($thisaffcom/100);
	else if ($thisaffcomtype == "money") $provision2 += $thisquantity * $thisaffcom;

	// Calculate level 1 second tier commissions...
	$thistier2affcomtype = "percent";
	$thistier2affcom = $secondtierpercent;
	if ($thistier2affcomtype == "percent") $secondtierprovision += $parsed_price * ($thistier2affcom/100);
	else if ($thistier2affcomtype == "money") $secondtierprovision += $thisquantity * $thistier2affcom;

	// Calculate multi tier commissions...
	$thisaffiliatetierlowerby = $thisproduct["affiliatetierlowerby"];
	if (!empty($thisaffiliatetierlowerby) && is_numeric($thisaffiliatetierlowerby) && $thisaffiliatetierlowerby > 0) {
		$tier = 3;
		$thistieraffcom = $thistier2affcom - $thisaffiliatetierlowerby;
		if ($thistier2affcomtype == "percent") $thistierprovision = $parsed_price * ($thistieraffcom/100);
		else if ($thistier2affcomtype == "money") $thistierprovision = $thisquantity * $thistieraffcom;
		while ($thistieraffcom > 0) {
			$tierprovision[$tier] += $thistierprovision;
			$tier++;
			$thistieraffcom -= $thisaffiliatetierlowerby;
			if ($thistier2affcomtype == "percent") $thistierprovision = $parsed_price * ($thistieraffcom/100);
			else if ($thistier2affcomtype == "money") $thistierprovision = $thisquantity * $thistieraffcom;
			if (!empty($maxaffiliatetiers) && $maxaffiliatetiers > 0 && $tier > $maxaffiliatetiers) $thistieraffcom = 0;
		}
	}

	// Calculate level 2 second tier commissions...
	$thistier2affcomtype = "percent";
	$thistier2affcom = $secondtierpercent2;
	if ($thistier2affcomtype == "percent") $secondtierprovision2 += $parsed_price * ($thistier2affcom/100);
	else if ($thistier2affcomtype == "money") $secondtierprovision2 += $thisquantity * $thistier2affcom;
}

// Set current date and time...
$date = date("Y-m-d H:i:s", time()+$timezoneoffset);
$dateshort = date("Y-m-d", time()+$timezoneoffset);

// Store customerinfo...
if (!empty($customerid) && $customerid > 0) {
	if (!empty($dbsafe_firstname) || !empty($dbsafe_lastname) || !empty($dbsafe_email) || !empty($dbsafe_ipnumber) || !empty($dbsafe_customerid)) {
		$sqlset = "";
		$sql = "UPDATE customer SET";
		if (!empty($dbsafe_firstname)) $sqlset .= " firstname = '$dbsafe_firstname'";
		if (!empty($dbsafe_lastname)) {
			if (!empty($sqlset)) $sqlset .= ", ";
			$sqlset .= "lastname = '$dbsafe_lastname'";
		}
		if (!empty($dbsafe_email)) {
			if (!empty($sqlset)) $sqlset .= ", ";
			$sqlset .= "email = '$dbsafe_email'";
		}
		if (!empty($dbsafe_ipnumber)) {
			if (!empty($sqlset)) $sqlset .= ", ";
			$sqlset .= "ip = '$dbsafe_ipnumber'";
		}
		if (!empty($dbsafe_customerid)) {
			if (!empty($sqlset)) $sqlset .= ", ";
			$sqlset .= "remotecustomerid = '$dbsafe_customerid'";
		}
		$sql .= $sqlset." WHERE customerid = '$customerid'";
		$result = @mysql_query("$sql",$db);
	}
} else {
	$sql = "INSERT INTO customer (firstname, lastname, email, affiliateid, ip, remotecustomerid) VALUES ('$dbsafe_firstname', '$dbsafe_lastname', '$dbsafe_email','$parsed_affiliate', '$dbsafe_ipnumber', '$dbsafe_customerid')";
	$result = @mysql_query("$sql",$db);
	$customerid = @mysql_insert_id();
}

// Store order...
if (empty($pending) || $pending != 1) $paiddate = $date;
else $paiddate = "";
$sql = "INSERT INTO orders (product, customerid, invoice, date, paid, price, affiliateid) VALUES ('$dbsafe_product', '$customerid', '$dbsafe_invoice', '$date', '$paiddate', '$parsed_price', '$parsed_affiliate')";
$result = @mysql_query("$sql",$db);
$orderid = @mysql_insert_id();

// Reward affiliate...
if($parsed_affiliate!="") {
	$sql="SELECT * FROM affiliate WHERE affiliateid='$parsed_affiliate'";
	$result = @mysql_query("$sql", $db);
	if (!@mysql_num_rows($result)) $parsed_affiliate = "";
	else {
		$affiliatemail = ashop_mailsafe(@mysql_result($result, 0, "email"));
		$affiliatefirstname = ashop_mailsafe(@mysql_result($result, 0, "firstname"));
		$affiliatelastname = ashop_mailsafe(@mysql_result($result, 0, "lastname"));
		$affiliatereferredby = @mysql_result($result, 0, "referedby");
		$affiliatelevel = @mysql_result($result, 0, "commissionlevel");
		if ($affiliatereferredby) {
			$sql="SELECT * FROM affiliate WHERE affiliateid='$affiliatereferredby'";
			$result = @mysql_query("$sql", $db);
			$referreremail = ashop_mailsafe(@mysql_result($result, 0, "email"));
			$tierreferredby = @mysql_result($result, 0, "referedby");
			$referrerlevel = @mysql_result($result, 0, "commissionlevel");
		}
	}

	// Check that affiliate is not the customer...
	if(!empty($affiliatemail) && $affiliatemail != $parsed_email && $pending != 1 && ($wholesale != 1 || $wholesaleaffiliate == "1")) {
		if ($affiliatelevel == 2) $sql="INSERT INTO orderaffiliate (affiliateid, orderid, paid, secondtier, commission) VALUES ('$parsed_affiliate', '$orderid', 0, 0, '$provision2')";
		else $sql="INSERT INTO orderaffiliate (affiliateid, orderid, paid, secondtier, commission) VALUES ('$parsed_affiliate', '$orderid', 0, 0, '$provision')";
		$result = @mysql_query("$sql", $db);
		$sql = "UPDATE affiliate SET lastdate='$date' WHERE affiliateid='$parsed_affiliate'";
		$result = @mysql_query("$sql",$db);

		// Check if the affiliate should be upgraded...
		if ($affiliatelevel == "1" && !empty($upgradeaffiliate) && $upgradeaffiliate > 0) {
			$sql="SELECT orderid FROM orderaffiliate WHERE affiliateid='$parsed_affiliate'";
			$result = @mysql_query("$sql",$db);
			$affiliateorders = @mysql_num_rows($result);
			if ($affiliateorders >= $upgradeaffiliate) $result = @mysql_query("UPDATE affiliate SET commissionlevel='2' WHERE affiliateid='$parsed_affiliate'",$db);
		}

		// Notify affiliate by email...
		$message="<html><head><title>Your link to $ashopname has generated a sale!</title></head><body><font face=\"$font\"><p>Your link to $ashopname generated a sale on $date</p><p>Thank you for your help!</p><p>You can log in to check how much you have earned at: <a href=\"$ashopurl/affiliate/login.php\">$ashopurl/affiliate/login.php</a></p></font></body></html>";
		$headers = "From: ".un_html($ashopname,1)."<$affiliaterecipient>\nX-Sender: <$affiliaterecipient>\nX-Mailer: PHP\nX-Priority: 3\nReturn-Path: <$affiliaterecipient>\nMIME-Version: 1.0\nContent-Type: text/html; charset=iso-8859-1\n";

		@ashop_mail("$affiliatemail",un_html($ashopname,1)." affiliate notification","$message","$headers");

		// Notify affiliate administrator by email...
		$message="<html><head><title>An affiliate link to $ashopname has generated a sale!</title></head><body><font face=\"$font\"><p>The affiliate $parsed_affiliate $affiliatefirstname $affiliatelastname generated a sale on $date</p></font></body></html>";
		$headers = "From: ".un_html($ashopname,1)."<$affiliaterecipient>\nX-Sender: <$affiliaterecipient>\nX-Mailer: PHP\nX-Priority: 3\nReturn-Path: <$affiliaterecipient>\nMIME-Version: 1.0\nContent-Type: text/html; charset=iso-8859-1\n";

		@ashop_mail("$affiliaterecipient",un_html($ashopname,1)." affiliate sales notification","$message","$headers");

		// Handle secondtier affiliates...
		if ($affiliatereferredby && $secondtieractivated) {
			if ($referrerlevel == 2) $sql="INSERT INTO orderaffiliate (affiliateid, orderid, paid, secondtier, commission) VALUES ('$affiliatereferredby', '$orderid', 0, 1, '$secondtierprovision2')";
			else $sql="INSERT INTO orderaffiliate (affiliateid, orderid, paid, secondtier, commission) VALUES ('$affiliatereferredby', '$orderid', 0, 1, '$secondtierprovision')";
			$result = @mysql_query("$sql", $db);
			$sql = "UPDATE affiliate SET lastdate='$date' WHERE affiliateid='$affiliatereferredby'";
			$result = @mysql_query("$sql",$db);

			// Notify affiliate by email...
			$message="<html><head><title>A link from an affiliate you have referred to $ashopname has generated a sale!</title></head><body><font face=\"$font\"><p>A link from an affiliate you have referred to $ashopname generated a sale on $date</p><p>Thank you for your help!</p><p>You can log in to check how much you have earned at: <a href=\"$ashopurl/affiliate/login.php\">$ashopurl/affiliate/login.php</a></p></font></body></html>";
			$headers = "From: ".un_html($ashopname,1)."<$affiliaterecipient>\nX-Sender: <$affiliaterecipient>\nX-Mailer: PHP\nX-Priority: 3\nReturn-Path: <$affiliaterecipient>\nMIME-Version: 1.0\nContent-Type: text/html; charset=iso-8859-1\n";

			@ashop_mail("$referreremail",un_html($ashopname,1)." affiliate notification","$message","$headers");

			$tier = 3;
			while (!empty($tierreferredby) && !empty($tierprovision[$tier]) && $tierprovision[$tier] > 0) {
				$sql="SELECT * FROM affiliate WHERE affiliateid='$tierreferredby'";
				$result = @mysql_query("$sql", $db);
				if (@mysql_num_rows($result)) {
					$thistieraffid = $tierreferredby;
					$referreremail = ashop_mailsafe(@mysql_result($result, 0, "email"));
					$tierreferredby = @mysql_result($result, 0, "referedby");
					$secondtier = $tier-1;
					$sql="INSERT INTO orderaffiliate (affiliateid, orderid, paid, secondtier, commission) VALUES ('$thistieraffid', '$orderid', 0, '$secondtier', '{$tierprovision[$tier]}')";
					$result = @mysql_query("$sql", $db);
					$sql = "UPDATE affiliate SET lastdate='$date' WHERE affiliateid='$tierreferredby'";
					$result = @mysql_query("$sql",$db);

					// Notify affiliate by email...
					$message="<html><head><title>A link from an affiliate you have referred to $ashopname has generated a sale!</title></head><body><font face=\"$font\"><p>A link from an affiliate you have referred to $ashopname generated a sale on $date</p><p>Thank you for your help!</p><p>You can log in to check how much you have earned at: <a href=\"$ashopurl/affiliate/login.php\">$ashopurl/affiliate/login.php</a></p></font></body></html>";
					$headers = "From: ".un_html($ashopname,1)."<$affiliaterecipient>\nX-Sender: <$affiliaterecipient>\nX-Mailer: PHP\nX-Priority: 3\nReturn-Path: <$affiliaterecipient>\nMIME-Version: 1.0\nContent-Type: text/html; charset=iso-8859-1\n";

					@ashop_mail("$referreremail",un_html($ashopname,1)." affiliate notification","$message","$headers");
					$tier++;
				} else $tierreferredby = "";
			}
		}
	} else if (!empty($affiliatemail) && $affiliatemail != $parsed_email && ($wholesale != 1 || $wholesaleaffiliate == "1")) {
		$sql="INSERT INTO pendingorderaff (affiliateid, orderid, secondtier, commission) VALUES ('$parsed_affiliate', '$orderid', 0, '$provision')";
		$result = @mysql_query("$sql", $db);
		$sql = "UPDATE affiliate SET lastdate='$date' WHERE affiliateid='$parsed_affiliate'";
		$result = @mysql_query("$sql",$db);

		// Notify affiliate administrator by email...
		$message="<html><head><title>An affiliate link to $ashopname has generated a sale!</title></head><body><font face=\"$font\"><p>The affiliate $parsed_affiliate $affiliatefirstname $affiliatelastname generated a sale on $date</p></font></body></html>";
		$headers = "From: ".un_html($ashopname,1)."<$affiliaterecipient>\nX-Sender: <$affiliaterecipient>\nX-Mailer: PHP\nX-Priority: 3\nReturn-Path: <$affiliaterecipient>\nMIME-Version: 1.0\nContent-Type: text/html; charset=iso-8859-1\n";

		@ashop_mail("$affiliaterecipient",un_html($ashopname,1)." affiliate sales notification","$message","$headers");

		// Handle secondtier affiliates...
		if ($affiliatereferredby && $secondtieractivated) {
			$sql="INSERT INTO pendingorderaff (affiliateid, orderid, secondtier, commission) VALUES ('$affiliatereferredby', '$orderid', 1, '$secondtierprovision')";
			$result = @mysql_query("$sql", $db);
			$sql = "UPDATE affiliate SET lastdate='$date' WHERE affiliateid='$affiliatereferredby'";
			$result = @mysql_query("$sql",$db);

			$tier = 3;
			while (!empty($tierreferredby) && !empty($tierprovision[$tier]) && $tierprovision[$tier] > 0) {
				$sql="SELECT * FROM affiliate WHERE affiliateid='$tierreferredby'";
				$result = @mysql_query("$sql", $db);
				if (@mysql_num_rows($result)) {
					$thistieraffid = $tierreferredby;
					$referreremail = ashop_mailsafe(@mysql_result($result, 0, "email"));
					$tierreferredby = @mysql_result($result, 0, "referedby");
					$secondtier = $tier-1;
					$sql="INSERT INTO pendingorderaff (affiliateid, orderid, secondtier, commission) VALUES ('$thistieraffid', '$orderid', '$secondtier', '{$tierprovision[$tier]}')";
					$result = @mysql_query("$sql", $db);
					$sql = "UPDATE affiliate SET lastdate='$date' WHERE affiliateid='$tierreferredby'";
					$result = @mysql_query("$sql",$db);
					$tier++;
				} else $tierreferredby = "";
			}
		}
	}
}

// Close database...
@mysql_close($db);
?>