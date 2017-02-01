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

// Validate variables...
if (empty($customerid) || !is_numeric($customerid)) {
	header("Location: salesadmin.php");
	exit;
}

// Open database...
$db = @mysql_connect("$databaseserver", "$databaseuser", "$databasepasswd");
@mysql_select_db("$databasename",$db);

if ($remove && $customerid) {
	if ($yes) {
		$sql="DELETE FROM customer WHERE customerid=$customerid";
		$result = @mysql_query($sql,$db);
		$sql="SELECT * FROM orders WHERE customerid=$customerid";
		$result = @mysql_query($sql,$db);
		for ($i = 0; $i < @mysql_num_rows($result); $i++) {
			$orderid = @mysql_result($result,$i,"orderid");
			$sql="DELETE FROM orderaffiliate WHERE orderid='$orderid'";
			$result2 = @mysql_query($sql,$db);
			$sql="DELETE FROM pendingorderaff WHERE orderid='$orderid'";
			$result2 = @mysql_query($sql,$db);
		}
		$sql="DELETE FROM orders WHERE customerid=$customerid";
		$result = @mysql_query($sql,$db);
		header("Location: salesadmin.php");
    }
	elseif ($no) header("Location: salesadmin.php");
	else {
		$sql="SELECT firstname, lastname FROM customer WHERE customerid=$customerid";
		$result = @mysql_query($sql,$db);
		$firstname = @mysql_result($result,0,"firstname");
		$lastname = @mysql_result($result,0,"lastname");
		echo "$header
<div class=\"heading\">".REMOVECUSTOMER."</div><center>
        <p>".AREYOUSURE." $customerid, $firstname $lastname?</font></p>
		<form action=\"editcustomer.php\" method=\"post\">
		<table width=\"440\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\">
		<tr>
        <td width=\"100%\" align=\"center\" valign=\"top\"><input type=\"submit\" name=\"yes\" value=\"".YES."\">
		<input type=\"button\" name=\"no\" value=\"".NO."\" onClick=\"javascript:history.back()\"></td>
		</tr></table><input type=\"hidden\" name=\"customerid\" value=\"$customerid\">
		<input type=\"hidden\" name=\"remove\" value=\"True\"></form>
		</center>
        $footer";
		exit;
	}
} 

// Store updated data...
if ($update) {
	// Avoid duplicate email addresses...
	$result = @mysql_query("SELECT * FROM customer WHERE email='$email' AND customerid!='$customerid'",$db);
	if (@mysql_num_rows($result)) $errormsg = EMAILINUSE;
	else {
		if ($affiliateid == "0") $affiliateid = "";
		$sql="UPDATE customer SET firstname='$firstname', lastname='$lastname', email='$email', affiliateid='$affiliateid' WHERE customerid='$customerid'";
		$result = @mysql_query("$sql",$db);

		header("Location: salesadmin.php"); 
		exit;
	}
}

// Get customer information from database...
$sql="SELECT * FROM customer WHERE customerid='$customerid'";
$result = @mysql_query("$sql",$db);
$firstname = @mysql_result($result, 0, "firstname");
$lastname = @mysql_result($result, 0, "lastname");
$email = @mysql_result($result, 0, "email");
$affiliateid = @mysql_result($result, 0, "affiliateid");
$ipnumber = @mysql_result($result, 0, "ip");

if ($affiliateid) {
	$result = @mysql_query("SELECT firstname, lastname FROM affiliate WHERE affiliateid='$affiliateid'",$db);
	$affiliatefirstname = @mysql_result($result,0,"firstname");
	$affiliatelastname = @mysql_result($result,0,"lastname");
}

// Close database...
@mysql_close($db);


// Show customer page in browser...
	if (strpos($header, "title") != 0) {
	    $newheader = substr($header,1,strpos($header, "title")+5);
	    $newheader .= CUSTOMERDATAFOR.": $firstname $lastname - ".substr($header,strpos($header, "title")+6,strlen($header));
    } else {
		$newheader = substr($header,1,strpos($header, "TITLE")+5);
		$newheader .= CUSTOMERDATAFOR.": $firstname $lastname - ".substr($header,strpos($header, "TITLE")+6,strlen($header));
	}

echo "$newheader
<div class=\"heading\">".PROFILEOF." $firstname $lastname, ".CUSTOMERID." $customerid <a href=\"salesreport.php?customerid=$customerid&generate=true\"><img src=\"images/icon_history.gif\" alt=\"".SALESHISTORYFOR." $customerid\" title=\"".SALESHISTORYFOR." $customerid\" border=\"0\"></a> <a href=\"editcustomer.php?customerid=$customerid&remove=True\"><img src=\"images/icon_trash.gif\" alt=\"".DELETECUSTOMER." $customerid ".FROMDB."\" title=\"".DELETECUSTOMER." $customerid ".FROMDB."\" border=\"0\"></a>
</div><center>
<form action=\"editcustomer.php\" method=\"post\"><input type=\"hidden\" name=\"customerid\" value=\"$customerid\">";
if ($errormsg) echo "<p align=\"center\" class=\"notconfirm\">$errormsg</p>";
if ($activate == "true") echo "<span class=\"confirm\">".ORDERACTIVATIONCOMPLETED."</span><br>";
echo "
    <table width=\"440\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">$processlink
	<tr><td align=\"right\"><font size=\"2\" face=\"Arial, Helvetica, sans-serif\">".REFERREDBY.":</font></td>
    <td align=\"left\"><input type=text name=\"affiliateid\" value=\"$affiliateid\" size=4><font size=\"2\" face=\"Arial, Helvetica, sans-serif\"> <a href=\"editaffiliate.php?affiliateid=$affiliateid\">$affiliatefirstname $affiliatelastname</a></font></td></tr>
    <tr><td align=\"right\"><font size=\"2\" face=\"Arial, Helvetica, sans-serif\">".FIRSTNAME.":</font></td>
    <td align=\"left\"><input type=text name=\"firstname\" value=\"$firstname\" size=40></td></tr>
    <tr><td align=\"right\"><font size=\"2\" face=\"Arial, Helvetica, sans-serif\">".LASTNAME.":</font></td>
    <td align=\"left\"><input type=text name=\"lastname\" value=\"$lastname\" size=40></td></tr>
    <tr><td align=\"right\"><font size=\"2\" face=\"Arial, Helvetica, sans-serif\">".EMAIL.":</font></td>
    <td align=\"left\"><input type=text name=\"email\" value=\"$email\" size=40></td></tr>
	<tr><td colspan=\"2\" align=\"center\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\"><br>".LASTKNOWNIP.": $ipnumber</font></td></tr>
	<tr><td></td><td align=\"right\"><input type=\"submit\" value=\"".UPDATE."\" name=\"update\"></td></tr></table></form>
</font></center>$footer";
?>