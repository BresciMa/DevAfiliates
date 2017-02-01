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

if ($userid != 1) {
	header("Location: index.php");
	exit;
}

// Open database...
$db = mysql_connect("$databaseserver", "$databaseuser", "$databasepasswd");
mysql_select_db("$databasename",$db);

if ($remove && $affiliateid) {
	if ($yes) {
       $sql="DELETE FROM affiliate WHERE affiliateid='$affiliateid'";
       $result = @mysql_query($sql,$db);
       $sql="DELETE FROM orderaffiliate WHERE affiliateid='$affiliateid'";
       $result = @mysql_query($sql,$db);
	   if ($fromstats) header("Location: affiliatestats.php");
	   else header("Location: affiliateadmin.php");
    } elseif ($no) {
		if ($fromstats) header("Location: affiliatestats.php");
		else header("Location: affiliateadmin.php");
	} else {
		$sql="SELECT firstname, lastname FROM affiliate WHERE affiliateid='$affiliateid'";
		$result = @mysql_query($sql,$db);
		$firstname = @mysql_result($result,0,"firstname");
		$lastname = @mysql_result($result,0,"lastname");
		echo "$header
<div class=\"heading\">".REMOVEANAFFILIATE."</div><center>
        <p>".AREYOUSUREREMOVE." $affiliateid, $firstname $lastname?</font></p>
		<form action=\"editaffiliate.php\" method=\"post\">
		<table width=\"440\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\">
		<tr>
        <td width=\"100%\" align=\"center\" valign=\"top\"><input type=\"submit\" name=\"yes\" value=\"".YES."\">
		<input type=\"button\" name=\"no\" value=\"".NO."\" onClick=\"javascript:history.back()\"></td>
		</tr></table><input type=\"hidden\" name=\"affiliateid\" value=\"$affiliateid\">
		<input type=\"hidden\" name=\"remove\" value=\"True\">";

		if ($fromstats) echo "<input type=\"hidden\" name=\"fromstats\" value=\"True\">";

		echo "</form></center>$footer";
		exit;
	}
}

// Store updated data...
if ($update) {

	// Set current date and time...
	$date = date("Y-m-d H:i:s", time()+$timezoneoffset);

	$sql="UPDATE affiliate SET user='$user', password='$password', business='$business', firstname='$firstname', lastname='$lastname', email='$email', address='$address', state='$state', zip='$zip', city='$city', country='$country', url='$url', phone='$phone', paypalid='$paypalid', updated='$date', referralcode='$referralcode', commissionlevel='$commissionlevel', extrainfo='$extrainfo', referedby='$referredby' WHERE affiliateid=$affiliateid";
    $result = @mysql_query("$sql",$db);
	if ($fromstats) header("Location: affiliatestats.php");
	else header("Location: affiliateadmin.php"); 
	exit;
}

// Get affiliate information from database...
$sql="SELECT * FROM affiliate WHERE affiliateid='$affiliateid'";
$result = @mysql_query("$sql",$db);
$user = @mysql_result($result, 0, "user");
$password = @mysql_result($result, 0, "password");
$business = @mysql_result($result, 0, "business");
$firstname = @mysql_result($result, 0, "firstname");
$lastname = @mysql_result($result, 0, "lastname");
$email = @mysql_result($result, 0, "email");
$address = @mysql_result($result, 0, "address");
$state = @mysql_result($result, 0, "state");
$zip = @mysql_result($result, 0, "zip");
$city = @mysql_result($result, 0, "city");
$country = @mysql_result($result, 0, "country");
$url = @mysql_result($result, 0, "url");
$phone = @mysql_result($result, 0, "phone");
$paypalid = @mysql_result($result, 0, "paypalid");
$signedup = @mysql_result($result, 0, "signedup");
$lastupdated = @mysql_result($result, 0, "updated");
$lastdate = @mysql_result ($result, 0, "lastdate");
$referralcode = @mysql_result ($result, 0, "referralcode");
$commissionlevel = @mysql_result ($result, 0, "commissionlevel");
$extrainfo = @mysql_result ($result, 0, "extrainfo");
$referredby = @mysql_result ($result, 0, "referedby");
if (!empty($referredby)) {
	$result2 = @mysql_query("SELECT * FROM affiliate WHERE affiliateid='$referredby'",$db);
	$sponsorfirstname = @mysql_result($result2, 0, "firstname");
	$sponsorlastname = @mysql_result($result2, 0, "lastname");
}

// Check if this affiliate exists as a Sales Office user too...
$salesrep = FALSE;
if (file_exists("$ashoppath/emerchant/quote.php")) {
	$result3 = @mysql_query("SELECT * FROM emerchant_user WHERE username='$user'",$db);
	if (@mysql_num_rows($result3)) $salesrep = TRUE;
}

// Copy the affiliate to the Sales Office...
if ($makesalesrep && $affiliateid) {
	$result3 = @mysql_query("SELECT * FROM emerchant_user WHERE username='$user'",$db);
	if (@mysql_num_rows($result3)) $msg = "<p align=\"center\" class=\"notconfirm\">".THEREISALREADYAUSER."</p>";
	else {
		@mysql_query("INSERT INTO emerchant_user (username,password) VALUES ('$user','$password')",$db);
		$msg = "<p align=\"center\" class=\"confirm\">".THISAFFILIATEADDEDTOSALESOFFICE."</p>";
	}
	$salesrep = TRUE;
}

// Close database...
@mysql_close($db);


// Show affiliate page in browser...
	if (strpos($header, "title") != 0) {
	    $newheader = substr($header,1,strpos($header, "title")+5);
	    $newheader .= AFFILIATEDATAFOR.": $firstname $lastname - ".substr($header,strpos($header, "title")+6,strlen($header));
    } else {
		$newheader = substr($header,1,strpos($header, "TITLE")+5);
		$newheader .= AFFILIATEDATAFOR.": $firstname $lastname - ".substr($header,strpos($header, "TITLE")+6,strlen($header));
	}

echo "$newheader
<div class=\"heading\">".PROFILEOF." $firstname $lastname, ".AFFILIATEID." $affiliateid <a href=\"affiliatedetail.php?affiliateid=$affiliateid\"><img src=\"images/icon_history.gif\" alt=\"".STATISTICSFORAFFILIATE." $affiliateid\" title=\"".STATISTICSFORAFFILIATE." $affiliateid\" border=\"0\"></a>&nbsp;<a href=\"editaffiliate.php?affiliateid=$affiliateid&remove=True&fromstats=True\"><img src=\"images/icon_trash.gif\" alt=\"".DELETEAFFILIATE." $affiliateid ".FROMTHEDATABASE."\" title=\"".DELETEAFFILIATE." $affiliateid ".FROMTHEDATABASE."\" border=\"0\"></a></div><center>
<font size=\"2\" face=\"Arial, Helvetica, sans-serif\">".SIGNUPDATE.": $signedup
<br>".PROFILELASTUPDATED.": $lastupdated<br>
".DATEOFLASTACTIVITY.": $lastdate</font>
</p>";
if (file_exists("$ashoppath/emerchant/quote.php") && !$salesrep) echo "
<form action=\"editaffiliate.php\" method=\"post\"><input type=\"hidden\" name=\"affiliateid\" value=\"$affiliateid\"><input type=\"submit\" name=\"makesalesrep\" value=\"".COPYTOSALESOFFICE."\" class=\"widebutton\"></form>";
else if ($msg) echo $msg;
echo "
    <form action=\"editaffiliate.php\" method=\"post\"><input type=\"hidden\" name=\"affiliateid\" value=\"$affiliateid\">
    <table width=\"440\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">

<tr><td align=\"right\"><font size=\"2\" face=\"Arial, Helvetica, sans-serif\">".REFERREDBYAFFILIATE.":</font></td>
    <td align=\"left\"><input type=text name=\"referredby\" value=\"$referredby\" size=4><font size=\"2\" face=\"Arial, Helvetica, sans-serif\"> <a href=\"editaffiliate.php?affiliateid=$referredby\">$sponsorfirstname $sponsorlastname</a></font></td></tr>

    <tr><td align=\"right\"><font size=\"2\" face=\"Arial, Helvetica, sans-serif\">".COMMISSIONLEVEL.":</font></td>
    <td align=\"left\"><font size=\"2\" face=\"Arial, Helvetica, sans-serif\"><input type=radio name=\"commissionlevel\" value=\"1\"";
	if ($commissionlevel == "1") echo " checked";
	echo "> ".NORMAL." <input type=radio name=\"commissionlevel\" value=\"2\"";
	if ($commissionlevel == "2") echo " checked";
	echo "> ".UPGRADED."</font></td></tr>
    <tr><td align=\"right\"><font size=\"2\" face=\"Arial, Helvetica, sans-serif\">".MANUALREFERRALCODE.":</font></td>
    <td align=\"left\"><input type=text name=\"referralcode\" value=\"$referralcode\" size=15></td></tr>
    <tr><td align=\"right\"><font size=\"2\" face=\"Arial, Helvetica, sans-serif\">".USERNAME.":</font></td>
    <td align=\"left\"><input type=text name=\"user\" value=\"$user\" size=15><font size=\"1\" face=\"Arial, Helvetica, sans-serif\"> [".MAXTENCHARS."]</font>
    </td></tr>
    <tr><td align=\"right\"><font size=\"2\" face=\"Arial, Helvetica, sans-serif\">".PASSWORD.":</font></td>
    <td align=\"left\"><input type=text name=\"password\" value=\"$password\" size=15><font size=\"1\" face=\"Arial, Helvetica, sans-serif\"> [".MAXSEVENCHARS."]</font>
    </td></tr>
    <tr><td align=\"right\"><font size=\"2\" face=\"Arial, Helvetica, sans-serif\">".BUSINESSNAME.":</font></td>
    <td align=\"left\"><input type=text name=\"business\" value=\"$business\" size=40></td></tr>
    <tr><td align=\"right\"><font size=\"2\" face=\"Arial, Helvetica, sans-serif\">".FIRSTNAME.":</font></td>
    <td align=\"left\"><input type=text name=\"firstname\" value=\"$firstname\" size=40></td></tr>
    <tr><td align=\"right\"><font size=\"2\" face=\"Arial, Helvetica, sans-serif\">".LASTNAME.":</font></td>
    <td align=\"left\"><input type=text name=\"lastname\" value=\"$lastname\" size=40></td></tr>
    <tr><td align=\"right\"><font size=\"2\" face=\"Arial, Helvetica, sans-serif\">".EMAIL.":</font></td>
    <td align=\"left\"><input type=text name=\"email\" value=\"$email\" size=40></td></tr>
    <tr><td align=\"right\"><font size=\"2\" face=\"Arial, Helvetica, sans-serif\">".ADDRESS.":</font></td>
    <td align=\"left\"><input type=text name=\"address\" value=\"$address\" size=40></td></tr>
    <tr><td align=\"right\"><font size=\"2\" face=\"Arial, Helvetica, sans-serif\">".CITY.":</font></td>
    <td align=\"left\"><input type=text name=\"city\" value=\"$city\" size=40></td></tr>
	<tr><td align=\"right\"><font size=\"2\" face=\"Arial, Helvetica, sans-serif\">".STATEPROVINCE.":</font></td>
    <td align=\"left\"><input type=text name=\"state\" value=\"$state\" size=40></td></tr>
	<tr><td align=\"right\"><font size=\"2\" face=\"Arial, Helvetica, sans-serif\">".ZIP.":</font></td>
    <td align=\"left\"><input type=text name=\"zip\" value=\"$zip\" size=40></td></tr>
    <tr><td align=\"right\"><font size=\"2\" face=\"Arial, Helvetica, sans-serif\">".COUNTRY.":</font></td>
    <td align=\"left\"><input type=text name=\"country\" value=\"$country\" size=40></td></tr>
	<tr><td align=\"right\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">".PHONE.":</font></td>
    <td align=\"left\"><input type=text name=\"phone\" value=\"$phone\" size=40></td></tr>
    <tr><td align=\"right\"><font size=\"2\" face=\"Arial, Helvetica, sans-serif\">".URL.":</font></td>
    <td align=\"left\"><input type=\"text\" name=\"url\" value=\"$url\" size=\"40\"></td></tr>
    <tr><td align=\"right\"><font size=\"2\" face=\"Arial, Helvetica, sans-serif\">".PAYPALID.":</font></td>
    <td align=\"left\"><input type=\"text\" name=\"paypalid\" value=\"$paypalid\" size=\"40\"></td></tr>";
	/*<tr><td align=\"right\"><font size=\"2\" face=\"Arial, Helvetica, sans-serif\">Additional<br>information:</font></td>
    <td><textarea name=\"extrainfo\" cols=\"30\" rows=\"5\">$extrainfo</textarea></td></tr>*/
	echo "<tr><td></td><td align=\"right\">";
	if ($fromstats) echo "<input type=\"hidden\" name=\"fromstats\" value=\"True\">";
	echo "<input type=\"submit\" value=\"".UPDATE."\" name=\"update\"></td></tr>
    </table></form>
	</font></center>
	$footer";
?>