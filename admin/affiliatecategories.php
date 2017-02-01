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
// Get context help for this page...
$contexthelppage = "affiliatecodes";
include "help.inc.php";

// Open database...
$db = @mysql_connect("$databaseserver", "$databaseuser", "$databasepasswd");
@mysql_select_db("$databasename",$db);


// Handle new and updated link code categories...
if ($submitbutton) {
	if (!$linkcategoryid) {
		$sql="INSERT INTO linkcategories (linkcategoryname) VALUES ('$linkcategoryname')";
	} else {
		$sql = "UPDATE linkcategories SET linkcategoryname='$linkcategoryname' WHERE linkcategoryid=$linkcategoryid";
	}
	$result = @mysql_query($sql,$db);
}

// Delete link code category...
if ($deletebutton) {
	$sql = "DELETE FROM linkcategories WHERE linkcategoryid=$linkcategoryid";
	$result = @mysql_query($sql,$db);
}

echo "$header
<div class=\"heading\">".LINKCODECATEGORIES." <a href=\"$help2\" target=\"_blank\"><img src=\"images/icon_helpsm.gif\" width=\"15\" height=\"15\" border=\"0\"></a></div><center>
<p><form action=\"affiliatecategories.php\" method=\"post\" name=\"addform\">
      <table width=\"550\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\" bgcolor=\"#E5E5E5\">
	  <tr class=\"formtitle\"><td valign=\"top\" colspan=\"3\" align=\"left\">".ADDNEWLINKCATEGORY.":</td></tr>
<tr class=\"formlabel\"><td width=\"300\" valign=\"top\" align=\"right\">".CATEGORYNAME.":</td><td valign=\"top\" align=\"left\"><input type=\"text\" name=\"linkcategoryname\" size=\"40\" maxlength=\"100\"></td><td width=\"250\" align=\"left\"><input type=\"submit\" value=\"".ADD."\" name=\"submitbutton\"></td></tr></table></form></p>
<p class=\"formtitle\" align=\"center\">".CATEGORIES.":</p>";

// Get link code information from database...
$sql="SELECT * FROM linkcategories ORDER BY linkcategoryid ASC";
$result = @mysql_query("$sql",$db);
for ($i = 0; $i < @mysql_num_rows($result); $i++) {
    $thislinkcategoryname = @mysql_result($result, $i, "linkcategoryname");
    $thislinkcategoryid = @mysql_result($result, $i, "linkcategoryid");
    echo "<p><form action=\"affiliatecategories.php\" method=\"post\"><input type=\"hidden\" name=\"linkcategoryid\" value=\"$thislinkcategoryid\">
      <table width=\"550\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\" bgcolor=\"#D0D0D0\">
	  <tr class=\"formlabel\"><td valign=\"top\" align=\"left\"><input type=\"text\" name=\"linkcategoryname\" value=\"$thislinkcategoryname\" size=\"40\" maxlength=\"100\"></td><td width=\"80\" align=\"center\"><input type=\"submit\" value=\"".UPDATE."\" name=\"submitbutton\"></td><td width=\"90\" class=\"formlabel\" align=\"center\">";
	  $result2 = @mysql_query("SELECT * FROM linkcodes WHERE linkcategoryid='$thislinkcategoryid'",$db);
	  if (!@mysql_num_rows($result2)) echo "<input type=\"submit\" value=\"".DELETELINK."\" name=\"deletebutton\">";
	  else echo INUSE;
	  echo "</td></tr></table></form></p>";
}

echo "</center>$footer";
?>