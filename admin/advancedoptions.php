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

if ($cancel) {
	header("Location: settings.php");
	exit;
}
include "template.inc.php";
// Get language module...
include "language/$adminlang/configure.inc.php";
include "ashopconstants.inc.php";

// Open database connection...
$db = @mysql_connect("$databaseserver", "$databaseuser", "$databasepasswd");
@mysql_select_db("$databasename",$db);

if (!$changeconfig) {
        echo "$header
<div class=\"heading\">
        ".ADVANCEDSHOPPARAMETERS."</div>
        <table align=\"center\" cellpadding=\"10\"><tr><td>
        <form action=\"advancedoptions.php?changeconfig=1\" method=\"post\" name=\"configurationform\" enctype=\"multipart/form-data\">
		<table width=\"500\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\" bgcolor=\"#F0F0F0\">";
}

if (!$changeconfig) {
	// Get context help for this page...
		$contexthelppage = "advancedoptions";
		include "help.inc.php";
	echo "<font face=\"Arial, Helvetica, sans-serif\" color=\"#FF0000\" size=\"2\">".ONLYCHANGEIFYOUKNOW."</font><br><br>
		<tr bgcolor=\"#D0D0D0\"><td align=\"right\" class=\"formlabel\"><a href=\"javascript:;\" onMouseOut=\"MM_swapImgRestore()\" onMouseOver=\"MM_swapImage('Image1','','images/contexthelpicon_over.gif',1)\"><img src=\"images/contexthelpicon.gif\" width=\"14\" height=\"15\" border=\"0\" name=\"Image1\" align=\"absmiddle\" onclick=\"return overlib('$tip1');\" onmouseout=\"return nd();\"></a> ".SHOPURL.":</td><td><input type=\"text\" name=\"nashopurl\" size=\"35\" value=\"$ashopurl\"><script language=\"JavaScript\">document.configurationform.nashopurl.focus();</script></td></tr>
		<tr bgcolor=\"#D0D0D0\"><td align=\"right\" class=\"formlabel\"><a href=\"javascript:;\" onMouseOut=\"MM_swapImgRestore()\" onMouseOver=\"MM_swapImage('Image2','','images/contexthelpicon_over.gif',1)\"><img src=\"images/contexthelpicon.gif\" width=\"14\" height=\"15\" border=\"0\" name=\"Image2\" align=\"absmiddle\" onclick=\"return overlib('$tip2');\" onmouseout=\"return nd();\"></a> ".FILESYSTEMPATH.":</td><td><input type=\"text\" name=\"nashoppath\" size=\"35\" value=\"$ashoppath\"></td></tr>
		<tr bgcolor=\"#D0D0D0\"><td align=\"right\" class=\"formlabel\"><a href=\"javascript:;\" onMouseOut=\"MM_swapImgRestore()\" onMouseOver=\"MM_swapImage('Image3','','images/contexthelpicon_over.gif',1)\"><img src=\"images/contexthelpicon.gif\" width=\"14\" height=\"15\" border=\"0\" name=\"Image3\" align=\"absmiddle\" onclick=\"return overlib('$tip3');\" onmouseout=\"return nd();\"></a> ".TIMEZONEOFFSET.":</td><td><input type=\"text\" name=\"ntimezoneoffset\" size=\"35\" value=\"$timezoneoffset\"></td></tr>
		<tr bgcolor=\"#D0D0D0\"><td align=\"right\" class=\"formlabel\">".MAILERTYPE.":</td><td><select name=\"nmailertype\"><option value=\"mailfunction\""; if ($mailertype == "mailfunction") echo " selected"; echo ">".MAILFUNCTION."</option><option value=\"smtp\""; if ($mailertype == "smtp") echo " selected"; echo ">SMTP</option></select></td></tr>
		<tr bgcolor=\"#D0D0D0\"><td align=\"right\" class=\"formlabel\">".MAILERSERVER.":</td><td><input type=\"text\" name=\"nmailerserver\" size=\"35\" value=\"$mailerserver\"></td></tr>
		<tr bgcolor=\"#D0D0D0\"><td align=\"right\" class=\"formlabel\">".MAILERPORT.":</td><td><input type=\"text\" name=\"nmailerport\" size=\"35\" value=\"$mailerport\"></td></tr>
		<tr bgcolor=\"#D0D0D0\"><td align=\"right\" class=\"formlabel\">".MAILERUSER.":</td><td><input type=\"text\" name=\"nmaileruser\" size=\"35\" value=\"$maileruser\"></td></tr>
		<tr bgcolor=\"#D0D0D0\"><td align=\"right\" class=\"formlabel\">".MAILERPASS.":</td><td><input type=\"text\" name=\"nmailerpass\" size=\"35\" value=\"$mailerpass\"></td></tr>";
} else {
	@mysql_query("UPDATE preferences SET prefvalue='$nashopurl' WHERE prefname='ashopurl'");
	@mysql_query("UPDATE preferences SET prefvalue='$nashopsurl' WHERE prefname='ashopsurl'");
	@mysql_query("UPDATE preferences SET prefvalue='$nashoppath' WHERE prefname='ashoppath'");
	@mysql_query("UPDATE preferences SET prefvalue='$ntimezoneoffset' WHERE prefname='timezoneoffset'");
	@mysql_query("UPDATE preferences SET prefvalue='$nmailertype' WHERE prefname='mailertype'");
	@mysql_query("UPDATE preferences SET prefvalue='$nmailerserver' WHERE prefname='mailerserver'");
	@mysql_query("UPDATE preferences SET prefvalue='$nmailerport' WHERE prefname='mailerport'");
	@mysql_query("UPDATE preferences SET prefvalue='$nmaileruser' WHERE prefname='maileruser'");
	@mysql_query("UPDATE preferences SET prefvalue='$nmailerpass' WHERE prefname='mailerpass'");
}

if (!$changeconfig) {
	echo "<tr bgcolor=\"#FFFFFF\"><td>&nbsp;</td><td align=\"right\"><input type=\"hidden\" name=\"cancel\" value=\"\"><input type=\"button\" value=\"".CANCEL."\" onClick=\"document.configurationform.cancel.value='true';document.configurationform.submit();\"> <input type=\"submit\" value=\"".SUBMIT."\"></td></tr></table></form></table>$footer";
} else {
	@mysql_close($db);
	header("Location: settings.php");
}
?>