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

$sessiondb = @mysql_connect("$databaseserver", "$databaseuser", "$databasepasswd");
@mysql_select_db("$databasename",$sessiondb);
if (!empty($affiliatesesid) && !preg_match("/^[0-9a-f]{32}$/", $affiliatesesid)) $affiliatesesid = "";

$date = date("Y/m/d H:i:s");
$sql = "SELECT * FROM affiliate WHERE sessionid = '$affiliatesesid'";
$result = @mysql_query($sql,$sessiondb);
$activity = @mysql_result($result,0,"activity");
if ($activity) $activitytime = strtotime($activity);
else $activitytime = 0;
$inactivitytime = (strtotime($date) - $activitytime)/60;
if ((@mysql_num_rows($result) == 1) && ($inactivitytime < 30)) {
	$sql = "UPDATE affiliate SET activity = '$date' WHERE sessionid = '$affiliatesesid'";
	@mysql_query($sql,$sessiondb);
	@mysql_close($sessiondb);
} else {
    @mysql_close($sessiondb);
	if (!$p3psent) header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');
	$p3psent = TRUE;
	setcookie("affiliatesesid","",time()-10800,"/");
	header("Location: login.php");
}
?>