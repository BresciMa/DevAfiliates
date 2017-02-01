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

// Open database...
$db = @mysql_connect("$databaseserver", "$databaseuser", "$databasepasswd");
@mysql_select_db("$databasename",$db);

// Check mailing status...
$result = @mysql_query("SELECT * FROM mailing WHERE type='$mailingtype'",$db);
$mailingid = @mysql_result($result,0,"mailingid");
if (is_numeric($mailingid)) {
	$sentresult = @mysql_query("SELECT * FROM maillog WHERE mailingid='$mailingid'",$db);
	if ($mailingtype == "customer") $totalresult = @mysql_query("SELECT customerid FROM customer WHERE customer.firstname != '' AND customer.email != '' AND customer.allowemail='1' AND ((customer.password != '' AND customer.password IS NOT NULL) OR EXISTS (SELECT customerid FROM orders WHERE orders.customerid=customer.customerid AND date IS NOT NULL))",$db);
	else if ($mailingtype == "affiliate") $totalresult =  @mysql_query("SELECT DISTINCT email FROM affiliate",$db);
	else if ($mailingtype == "member") $totalresult =  @mysql_query("SELECT DISTINCT email FROM user WHERE userid!='1' ORDER BY userid",$db);
	else if ($mailingtype == "wholesale") $totalresult =  @mysql_query("SELECT DISTINCT email FROM customer WHERE businesstype IS NOT NULL",$db);
	$sent = @mysql_num_rows($sentresult);
	$total = @mysql_num_rows($totalresult);
	header('Content-type: text/plain');
	echo "$sent|$total";
	exit;
} else {
	echo "-1|-1";
}

@mysql_close($db);
?>