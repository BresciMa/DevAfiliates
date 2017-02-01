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


// Handle new and updated affiliate tags...
if ($submitbutton) {
	if (!$affiliatetagid) {
		$sql="INSERT INTO affiliatetags (fieldname,tagname,rows) VALUES ('$fieldname','$tagname','$rows')";
	} else {
		$sql = "UPDATE affiliatetags SET fieldname='$fieldname',tagname='$tagname',rows='$rows' WHERE affiliatetagid='$affiliatetagid'";
	}
	$result = @mysql_query($sql,$db);
}

// Delete affiliate tag...
if ($deletebutton) {
	$sql = "DELETE FROM affiliatetags WHERE affiliatetagid='$affiliatetagid'";
	$result = @mysql_query($sql,$db);
}

echo "$header
<div class=\"heading\">".CUSTOMTAGS." <a href=\"$help3\" target=\"_blank\"><img src=\"images/icon_helpsm.gif\" width=\"15\" height=\"15\" border=\"0\"></a></div><center>
<p><form action=\"affiliatetags.php\" method=\"post\" name=\"addform\">
      <table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\" bgcolor=\"#E5E5E5\">
	  <tr class=\"formtitle\"><td colspan=\"3\" width=\"100\">".ADDNEWCUSTOMTAG.":</td></tr>
<tr class=\"formlabel\"><td align=\"right\" width=\"150\">".FIELDNAME.":</td><td valign=\"top\" colspan=\"2\" align=\"left\"><input type=\"text\" name=\"fieldname\" size=\"40\" maxlength=\"100\"></td></tr>
<tr class=\"formlabel\"><td align=\"right\">".HTMLTAG.":</td><td valign=\"top\" colspan=\"2\" align=\"left\">&lt;!-- AShop_affiliate_<input type=\"text\" name=\"tagname\" size=\"21\" maxlength=\"40\"> --&gt;</td></tr>
<tr class=\"formlabel\"><td valign=\"top\" align=\"right\">".NUMBEROFROWS.":</td><td valign=\"top\" align=\"left\"><input type=\"text\" name=\"rows\" size=\"5\" maxlength=\"10\" value=\"1\"></td><td width=\"250\" align=\"right\"><input type=\"submit\" value=\"".ADD."\" name=\"submitbutton\"></td></tr></table></form></p>";

// Get tag information from database...
$sql="SELECT * FROM affiliatetags ORDER BY fieldname ASC";
$result = @mysql_query("$sql",$db);
if (@mysql_num_rows($result)) echo "<p class=\"formtitle\" align=\"center\">Available Tags:</p>";
for ($i = 0; $i < @mysql_num_rows($result); $i++) {
    $thisfieldname = @mysql_result($result, $i, "fieldname");
    $thisaffiliatetagid = @mysql_result($result, $i, "affiliatetagid");
	$thistagname = @mysql_result($result, $i, "tagname");
	$thisrows = @mysql_result($result, $i, "rows");
    echo "<p><form action=\"affiliatetags.php\" method=\"post\"><input type=\"hidden\" name=\"affiliatetagid\" value=\"$thisaffiliatetagid\">
      <table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\" bgcolor=\"#D0D0D0\">
	  <tr class=\"formlabel\"><td align=\"right\" width=\"150\">".FIELDNAME.":</td><td valign=\"top\" colspan=\"2\" align=\"left\"><input type=\"text\" name=\"fieldname\" value=\"$thisfieldname\" size=\"40\" maxlength=\"100\"></td></tr>
	  <tr class=\"formlabel\"><td align=\"right\">".HTMLTAG.":</td><td valign=\"top\" colspan=\"2\" align=\"left\">&lt;!-- AShop_affiliate_<input type=\"text\" name=\"tagname\" value=\"$thistagname\" size=\"21\" maxlength=\"40\"> --&gt;</td></tr>
	  <tr class=\"formlabel\"><td align=\"right\">".NUMBEROFROWS.":</td><td valign=\"top\" align=\"left\"><input type=\"text\" name=\"rows\" size=\"5\" maxlength=\"10\" value=\"$thisrows\"></td><td width=\"250\" align=\"right\"><input type=\"submit\" value=\"".UPDATE."\" name=\"submitbutton\"> <input type=\"submit\" value=\"".DELETELINK."\" name=\"deletebutton\"></td></tr></table></form></p>";
}

echo "</center>$footer";
?>