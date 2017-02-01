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

include "../admin/config.inc.php";
include "checklogin.inc.php";
include "../admin/ashopfunc.inc.php";

// Apply selected theme...
$templatepath = "/templates";
if ($ashoptheme && $ashoptheme != "none") include "../themes/$ashoptheme/theme.cfg.php";
if ($usethemetemplates == "true") $templatepath = "/themes/$ashoptheme";

// Include language file...
if (!$lang) $lang = $defaultlanguage;
include "../language/$lang/af_affiliate.inc.php";

// Open database...
$db = @mysql_connect("$databaseserver", "$databaseuser", "$databasepasswd");
@mysql_select_db("$databasename",$db);

// Get affiliate information from database...
$sql="SELECT * FROM affiliate WHERE sessionid='$affiliatesesid'";
$result = @mysql_query("$sql",$db);

// Store affiliate information in variables...
$firstname = @mysql_result($result, 0, "firstname");
$lastname = @mysql_result($result, 0, "lastname");
$affiliateid = @mysql_result($result, 0, "affiliateid");
$correctpasswd = @mysql_result($result, 0, "password");
$referralcode = @mysql_result($result, 0, "referralcode");
$username = @mysql_result($result, 0, "user");

// Get number of unread PMs...
$sql="SELECT * FROM affiliatepm WHERE toaffiliateid='$affiliateid' AND (hasbeenread='' OR hasbeenread='0' OR hasbeenread IS NULL)";
$unreadresult = @mysql_query("$sql",$db);
$unreadcount = @mysql_num_rows($unreadresult);

// Print header from template...
if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplateheader("$ashoppath$templatepath/affiliate-$lang.html");
else ashop_showtemplateheader("$ashoppath$templatepath/affiliate.html");

echo "<br><span class=\"ashopaffiliateheader\">".WELCOME." $firstname $lastname! ".AFFILIATEID.": $affiliateid</span>
	<table align=\"center\" width=\"400\"><tr><td align=\"center\"><form action=\"affiliate.php\" method=\"post\"><input class=\"ashopaffiliatebutton\" type=\"submit\" value=\"".STATISTICS."\"></form></td><td align=\"center\"><form action=\"changeprofile.php\" method=\"post\"><input class=\"ashopaffiliatebuttonlarge\" type=\"submit\" value=\"".VIEWPROFILE."\"></form></td><td align=\"center\"><form action=\"changepassword.php\" method=\"post\"><input class=\"ashopaffiliatebuttonlarge\" type=\"submit\" value=\"".CHANGEPASS."\"></form></td><td align=\"center\"><form action=\"login.php?logout\" method=\"post\"><input class=\"ashopaffiliatebutton\" type=\"submit\" value=\"".LOGOUT."\"></form></td></tr></table>
	<table align=\"center\" width=\"400\"><tr><td align=\"center\"><input class=\"ashopaffiliatebutton\" type=\"button\" value=\"".LINKCODES."\" disabled></td><td align=\"center\"><form action=\"orderhistory.php\" method=\"post\"><input class=\"ashopaffiliatebuttonlarge\" type=\"submit\" value=\"".ORDERHISTORY."\"></form></td>";
if ($activateleads) {
	echo "	
	<td align=\"center\"><form action=\"downline.php\" method=\"post\"><input class=\"ashopaffiliatebuttonsmall\" type=\"submit\" value=\"".DOWNLINE."\"></form></td><td align=\"center\"><form action=\"leads.php\" method=\"post\"><input class=\"ashopaffiliatebuttonsmall\" type=\"submit\" value=\"".LEADS."\"></form></td><td align=\"center\"><form action=\"inbox.php\" method=\"post\"><input class=\"ashopaffiliatebuttonsmall\" type=\"submit\" value=\"".INBOX;
	if ($unreadcount) echo " ($unreadcount)";
	echo "\"></form></td>";
} else {
	echo "	
	<td align=\"center\"><form action=\"downline.php\" method=\"post\"><input class=\"ashopaffiliatebuttonlarge\" type=\"submit\" value=\"".DOWNLINE."\"></form></td><td align=\"center\"><form action=\"inbox.php\" method=\"post\"><input class=\"ashopaffiliatebutton\" type=\"submit\" value=\"".INBOX;
	if ($unreadcount) echo " ($unreadcount)";
	echo "\"></form></td>";
}
echo "
	</tr></table>";
$affiliatelink = "$ashopurl/affiliate.php?id=$affiliateid";
$affiliatelinklength = strlen($affiliatelink);
echo "<p><span class=\"ashopaffiliatetext1\">".YOURLINK.":</span> <input id=\"affiliatelink\" type=\"text\" size=\"$affiliatelinklength\" value=\"$ashopurl/affiliate.php?id=$affiliateid\" onclick=\"document.getElementById('affiliatelink').select();\"></p>";
echo "
	<p><span class=\"ashopaffiliatetext1\">".MANUALCODE.":</span><span class=\"ashopaffiliatetext2\"> $referralcode</span></p>
	<p><span class=\"ashopaffiliatetext2\">";

// Show recruitment link if needed...
if ($secondtieractivated) {
	$sql="SELECT * FROM linkcodes WHERE linkid = 1";
	$result = @mysql_query("$sql",$db);
	if (@mysql_num_rows($result)) {
		$thislinktext = @mysql_result($result, 0, "linktext");
		$newlinktext = str_replace("%affiliatelink%","$ashopurl/affiliate.php?id=$affiliateid&redirect=$ashopurl/affiliate/signupform.php",$thislinktext);
		$newlinktext2 = str_replace("&gt;",">",$newlinktext);
		$newlinktext2 = str_replace("&lt;","<",$newlinktext2);
		$thisfilename = @mysql_result($result, 0, "filename");
		$thisalt = @mysql_result($result, $i, "alt");
		echo "<p>".COPYLINK."</p><p><table class=\"ashopaffiliatecodebox\"><tr><td align=\"center\" colspan=\"2\">";
		if ($thisfilename) echo "<img src=\"../banners/$thisfilename\" alt=\"$thisalt\" border=\"0\"><br>";
		echo "<span class=\"ashopaffiliatetext2\">$newlinktext2</span></td></tr><tr><td align=\"right\"><span class=\"ashopaffiliatetext1\">".HTMLCODE."</span></td><td><textarea name=\"linktext\" readonly cols=\"50\" rows=\"5\" align=\"top\">";
		if ($thisfilename) echo "&lt;a href=\"$ashopurl/affiliate.php?id=$affiliateid&redirect=$ashopurl/affiliate/signupform.php\"&gt;&lt;img src=\"$ashopurl/banners/$thisfilename\" alt=\"$thisalt\" border=\"0\"&gt;&lt;/a&gt;&lt;br&gt;";
		echo "$newlinktext</textarea></td></tr></table></form></p>";
	}
}

echo "<p><span class=\"ashopaffiliatetext2\">".COPYPASTE."</span></p><table class=\"ashopaffiliatecodeframe\"><tr><td class=\"ashopaffiliatecategoriesbox\">		<table class=\"ashopcategoriestable\" cellspacing=\"0\">
	  <tr><td class=\"ashopaffiliatecategoriesheader\">&nbsp;&nbsp;".LINKCODES."</td></tr>";

// Set default link code category...
if (empty($linkcat) || !is_numeric($linkcat)) {
	$result = @mysql_query("SELECT * FROM linkcategories ORDER BY linkcategoryid ASC LIMIT 1",$db);
	$linkcat = @mysql_result($result,0,"linkcategoryid");
	$showdefault = TRUE;
} else $showdefault = FALSE;

// Get link code categories...
$result = @mysql_query("SELECT * FROM linkcategories ORDER BY linkcategoryid ASC",$db);
while($row = @mysql_fetch_array($result)) {
	$linkcategoryid = $row["linkcategoryid"];
	$linkcategoryname = $row["linkcategoryname"];
	if ($linkcat == $linkcategoryid) echo "
	  <tr><td class=\"ashopselectedcategory\"><table width=\"100%\" cellpadding=\"2\" cellspacing=\"0\" border=\"0\"><tr><td width=\"16\" valign=\"top\">
			  <img src=\"../images/invisible.gif\" border=\"0\" width=\"12\" vspace=\"3\" alt=\"invisible.gif\"></td><td><a href=\"linkcodes.php?affuser=$affuser&linkcat=$linkcategoryid\" style=\"text-decoration: none\"><span class=\"ashopselectedcategory\">$linkcategoryname</span></a></td></tr></table></td></tr>";
	else echo "
	  <tr><td class=\"ashopaffiliatecategory\"><table width=\"100%\" cellpadding=\"2\" cellspacing=\"0\" border=\"0\"><tr><td width=\"16\" valign=\"top\">
			  <img src=\"../images/invisible.gif\" border=\"0\" width=\"12\" vspace=\"3\" alt=\"invisible.gif\"></td><td><a href=\"linkcodes.php?affuser=$affuser&linkcat=$linkcategoryid\" style=\"text-decoration: none\"><span class=\"ashopcategory\">$linkcategoryname</span></a></td></tr></table></td></tr>";
}

echo "
	</table>
	</td><td>";

// Get link code information from database...
$sql="SELECT * FROM linkcodes WHERE linkid > 1 AND linkcategoryid='$linkcat'";
$result = @mysql_query("$sql",$db);
if (!@mysql_num_rows($result) && $showdefault) echo "<table class=\"ashopaffiliatecodebox\"><tr><td align=\"center\" colspan=\"2\"><span class=\"ashopaffiliatetext2\"><u>$ashopname</u></span></td></tr><tr><td align=\"right\"><span class=\"ashopaffiliatetext1\">".HTMLCODE."</span></td><td><textarea name=\"linktext\" readonly cols=\"50\" rows=\"5\" align=\"top\">&lt;a href=\"$ashopurl/affiliate.php?id=$affiliateid\"&gt;$ashopname&lt/a&gt;</textarea></td></tr></table></form><br><br>";
for ($i = 0; $i < @mysql_num_rows($result); $i++) { 
    $thisredirect = @mysql_result($result, $i, "redirect");
    $thislinktext = @mysql_result($result, $i, "linktext");
	$thisredirect = str_replace("%affiliateuser%",$username,$thisredirect);
	$isreplicatedsite = FALSE;
	if (!empty($thisredirect) && empty($thislinktext)) $isreplicatedsite = TRUE;
	else {
		$thisredirect = str_replace($ashopurl,"",$thisredirect);
		$thisredirect = str_replace($ashopsurl,"",$thisredirect);
		if(substr($thisredirect,0,1) == "/") $thisredirect = substr($thisredirect,1);
	}
	if ($thisredirect) {
		$newlinktext = str_replace("%affiliatelink%","$ashopurl/affiliate.php?id=$affiliateid&redirect=$thisredirect",$thislinktext);
		$newlinktext = str_replace("%affiliatecloaklink%","&lt;a href=\"$ashopurl\" onClick=\"window.open('$ashopurl/affiliate.php?id=$affiliateid&redirect=$thisredirect', 'PGM', 'scrollbars=yes, toolbar=yes, status=yes, menubar=yes location=yes resizable=yes'); return false;\"&gt;",$newlinktext);
	} else {
		$newlinktext = str_replace("%affiliatelink%","$ashopurl/affiliate.php?id=$affiliateid",$thislinktext);
		$newlinktext = str_replace("%affiliatecloaklink%","&lt;a href=\"$ashopurl\" onClick=\"window.open('$ashopurl/affiliate.php?id=$affiliateid', 'PGM', 'scrollbars=yes, toolbar=yes, status=yes, menubar=yes location=yes resizable=yes'); return false;\"&gt;",$newlinktext);
	}
	$newlinktext2 = str_replace("&gt;",">",$newlinktext);
	$newlinktext2 = str_replace("&lt;","<",$newlinktext2);
    $thisfilename = @mysql_result($result, $i, "filename");
    $thislinkid = @mysql_result($result, $i, "linkid");
    $thisalt = @mysql_result($result, $i, "alt");
    echo "<table class=\"ashopaffiliatecodebox\"><tr><td align=\"center\" colspan=\"2\">";
	if ($thisfilename) echo "<img src=\"../banners/$thisfilename\" alt=\"$thisalt\" border=\"0\"><br>";
	if ($isreplicatedsite) echo "&nbsp;</td><tr><td><td align=\"right\" width=\"90\"><span class=\"ashopaffiliatetext1\">".URL."</span></td><td><textarea name=\"linktext\" readonly cols=\"50\" rows=\"2\" align=\"top\">$thisredirect</textarea></td></tr>";
	else {
		echo "<span class=\"ashopaffiliatetext2\">$newlinktext2</span></td></tr><tr><td align=\"right\" width=\"90\"><span class=\"ashopaffiliatetext1\">".HTMLCODE."</span></td><td><textarea name=\"linktext\" readonly cols=\"50\" rows=\"5\" align=\"top\">";
		if ($thisfilename) {
			echo "&lt;a href=\"$ashopurl/affiliate.php?id=$affiliateid";
			if ($thisredirect) echo "&redirect=$thisredirect";
			echo "\"&gt;&lt;img src=\"$ashopurl/banners/$thisfilename\" alt=\"$thisalt\" border=\"0\"&gt;&lt;/a&gt;&lt;br&gt;";
		}
		echo "$newlinktext</textarea></td></tr>";
	}
	echo "</table></form><br>";
}

	echo "</td></tr></table></font></font><br>";

// Close database...

@mysql_close($db);

// Print footer using template...
if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplatefooter("$ashoppath$templatepath/affiliate-$lang.html");
else ashop_showtemplatefooter("$ashoppath$templatepath/affiliate.html");
?>