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
// Get context help for this page...
		$contexthelppage = "affiliatecodes";
		include "help.inc.php";

// Open database...
$db = @mysql_connect("$databaseserver", "$databaseuser", "$databasepasswd");
@mysql_select_db("$databasename",$db);


// Handle new and updated link code categories...
if ($submitbutton) {
	if (!$linkcategoryid) {
		$sql="INSERT INTO links (name,linkurl) VALUES ('$linkname','$linkurl')";
	} else {
		$sql = "UPDATE links SET name='$linkname',linkurl='$linkurl' WHERE linkid=$linkid";
	}
	$result = @mysql_query($sql,$db);
}

// Delete link code category...
if ($deletebutton) {
	$sql = "DELETE FROM links WHERE linkid=$linkid";
	$result = @mysql_query($sql,$db);
}

echo "$header<table bgcolor=\"#$adminpanelcolor\" height=\"50\" width=\"100%\"><tr valign=\"middle\" align=\"center\"><td colspan=\"7\" class=\"heading1\">".CONFIGURATION."</td></tr>
  <tr align=\"center\">  
    <td class=\"nav\" width=\"17%\" nowrap><a href=\"configure.php?param=shop\" class=\"nav\">".SHOPPARAMETERS."</a></td>
    <td class=\"nav\" width=\"12%\" nowrap><a href=\"configure.php?param=layout\" class=\"nav\">".LAYOUT."</a></td>
    <td class=\"nav\" width=\"17%\" nowrap><a href=\"configure.php?param=affiliate\" class=\"nav\">".AFFILIATEPROGRAM."</a></td>
    <td class=\"nav\" width=\"14%\" nowrap><a href=\"payoptions.php\" class=\"nav\">".PAYMENT."</a></td>
    <td class=\"nav\" width=\"14%\" nowrap><a href=\"fulfiloptions.php\" class=\"nav\">".FULFILMENT."</a></td>
    <td class=\"nav\" width=\"14%\" nowrap><a href=\"configure.php?param=shipping\" class=\"nav\">".SHIPPING."</a></td>
	<td class=\"nav\" width=\"12%\" nowrap><a href=\"configure.php?param=taxes\" class=\"nav\">".TAXES."</a></td>
<tr>
</table>\n<center><p class=\"heading\">Edit Links</p>
<p><form action=\"editlinks.php\" method=\"post\" name=\"addform\">
      <table width=\"550\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\" bgcolor=\"#E5E5E5\">
	  <tr class=\"formtitle\"><td valign=\"top\" colspan=\"3\">Add a new link:</td></tr>
<tr class=\"formlabel\"><td width=\"200\" valign=\"top\" align=\"right\">Link name:</td><td valign=\"top\"><input type=\"text\" name=\"linkname\" size=\"48\" maxlength=\"100\"></td><td width=\"80\" align=\"left\">&nbsp;</td></tr>
<tr class=\"formlabel\"><td width=\"200\" valign=\"top\" align=\"right\">Link URL:</td><td valign=\"top\"><input type=\"text\" name=\"linkurl\" size=\"48\" maxlength=\"100\"></td><td width=\"80\" align=\"left\"><input type=\"submit\" value=\"Add\" name=\"submitbutton\" ></td></tr></table></form></p>
<p class=\"formtitle\" align=\"center\">Existing Links:</p>";

// Get link information from database...
$sql="SELECT * FROM links ORDER BY linkid ASC";
$result = @mysql_query("$sql",$db);
for ($i = 0; $i < @mysql_num_rows($result); $i++) {
    $thislinkname = @mysql_result($result, $i, "name");
	$thislinkurl = @mysql_result($result, $i, "linkurl");
    $thislinkid = @mysql_result($result, $i, "linkid");
    echo "<p><form action=\"editlinks.php\" method=\"post\"><input type=\"hidden\" name=\"linkid\" value=\"$thislinkid\">
      <table width=\"550\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\" bgcolor=\"#D0D0D0\">
	  <tr class=\"formlabel\"><td width=\"200\" valign=\"top\" align=\"right\">Link name:</td><td valign=\"top\"><input type=\"text\" name=\"linkname\" value=\"$thislinkname\" size=\"48\" maxlength=\"100\"></td><td width=\"70\" align=\"center\">&nbsp;</td></tr>
	  <tr class=\"formlabel\"><td width=\"200\" valign=\"top\" align=\"right\">Link URL:</td><td valign=\"top\"><input type=\"text\" name=\"linkurl\" value=\"$thislinkurl\" size=\"48\" maxlength=\"100\"></td><td width=\"70\" align=\"center\"><input type=\"submit\" value=\"Update\" name=\"submitbutton\"></td></tr></table></form></p>";
}

echo "</center>$footer";
?>