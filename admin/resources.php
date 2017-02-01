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
 
echo $header."<div class=\"heading\">AShop Affiliate Resources</div><table cellpadding=\"0\" align=\"center\"><tr><td>";

if ($userid != "1") {
	header("Location: index.php");
	exit;
}

// Open database...
$db = @mysql_connect("$databaseserver", "$databaseuser", "$databasepasswd");
@mysql_select_db("$databasename",$db);

$result = @mysql_query("SELECT licensekey FROM user WHERE userid='1'",$db);
$licensekey = @mysql_result($result, 0, "licensekey");
$securitycheck = md5($licensekey."ashopresources");

$resquerystring = "url=$ashopurl&key=$licensekey&product=1&version=$ashopversion&hash=$securitycheck";
$header = "POST /resources/index.php HTTP/1.0\r\nHost: www.ashopsoftware.com\r\nContent-Type: application/x-www-form-urlencoded\r\nContent-Length: ".strlen ($resquerystring)."\r\n\r\n";
$fp = @fsockopen ("www.ashopsoftware.com", 80, $errno, $errstr, 30);
if ($fp) {
	fputs ($fp, $header . $resquerystring);
	$res = "";
	while (!feof($fp)) {
		$res .= fgets ($fp, 1024);
	}
	fclose ($fp);
	if (!$res || strpos($res, "404 Not Found")) $error = "2";
	else $res = explode("<!-- AShopstart -->", $res);
	if ($res[1]) $res = $res[1];
	else $res = $res[0];
	$res = explode("<!-- AShopend -->", $res);
	$res = $res[0];
} else $error = "1";
if (!$res) $error = "2";

if ($error == 1) echo "<p><font face=\"Arial, Helvetica, sans-serif\" color=\"#900000\"><b>Error! Could not establish a connection with resources server!</font></p>";
else if ($error == 2) echo "<p><font face=\"Arial, Helvetica, sans-serif\" color=\"#900000\"><b>There is currently no information available for this version of AShop Affiliate!</font></p>";
else echo $res;

echo "</td></tr></table></center>$footer";
?>