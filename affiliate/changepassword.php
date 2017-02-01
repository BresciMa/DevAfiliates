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
include "../language/$lang/af_changepassword.inc.php";

// Open database...
$db = @mysql_connect("$databaseserver", "$databaseuser", "$databasepasswd");
@mysql_select_db("$databasename",$db);

// Get affiliate information from database...
$sql="SELECT * FROM affiliate WHERE sessionid='$affiliatesesid'";
$result = @mysql_query("$sql",$db);

// Get the correct password for this affiliate...
$correctpasswd = @mysql_result($result, 0, "password");
$firstname = @mysql_result($result, 0, "firstname");
$lastname = @mysql_result($result, 0, "lastname");

// Update password...
if ($newpassword1 && $newpassword2 && $oldpassword) {
	if (($newpassword1 == $newpassword2) && ($oldpassword == $correctpasswd)) {

		// Set current date and time...
		$date = date("Y-m-d H:i:s", time()+$timezoneoffset);

		$sql = "UPDATE affiliate SET password='$newpassword1', updated='$date' WHERE sessionid='$affiliatesesid'";
		$result = @mysql_query("$sql",$db);
		header("Location: affiliate.php");
		exit;
	} else if ($newpassword1 != $newpassword2) {
		if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplateheader("$ashoppath$templatepath/affiliate-$lang.html");
		else ashop_showtemplateheader("$ashoppath$templatepath/affiliate.html");
		echo "<table class=\"ashopmessagetable\">
		<tr align=\"center\"><td><br><br><p><span class=\"ashopmessageheader\">".ERROR."</span></p>
		<p><span class=\"ashopmessage\">".DIDNOTMATCH."</span></p>
		<p><span class=\"ashopmessage\"><a 
		href=\"javascript:history.back()\">".TRYAGAIN."</a></span></p></td></tr></table>";
		if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplatefooter("$ashoppath$templatepath/affiliate-$lang.html");
		else ashop_showtemplatefooter("$ashoppath$templatepath/affiliate.html");
		exit;
	} else if ($oldpassword != $correctpasswd) {
		if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplateheader("$ashoppath$templatepath/affiliate-$lang.html");
		else ashop_showtemplateheader("$ashoppath$templatepath/affiliate.html");
		echo "<table class=\"ashopmessagetable\">
		<tr align=\"center\"><td><br><br><p><span class=\"ashopmessageheader\">".WRONGPASS."</span></p>
		<p><span class=\"ashopmessage\">".INCORRECTPASS."</span></p>
		<p><span class=\"ashopmessage\"><a 
		href=\"javascript:history.back()\">".TRYAGAIN."</a></span></p></td></tr></table>";
		if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplatefooter("$ashoppath$templatepath/affiliate-$lang.html");
		else ashop_showtemplatefooter("$ashoppath$templatepath/affiliate.html");
		exit;
	}
}


// Store affiliate information in variables...
$affiliateid = @mysql_result($result, 0, "affiliateid");

// Get number of unread PMs...
$sql="SELECT * FROM affiliatepm WHERE toaffiliateid='$affiliateid' AND (hasbeenread='' OR hasbeenread='0' OR hasbeenread IS NULL)";
$unreadresult = @mysql_query("$sql",$db);
$unreadcount = @mysql_num_rows($unreadresult);

// Close database...
@mysql_close($db);

// Print header from template...
if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplateheader("$ashoppath$templatepath/affiliate-$lang.html");
else ashop_showtemplateheader("$ashoppath$templatepath/affiliate.html");

echo "<br><span class=\"ashopaffiliateheader\">".WELCOME." $firstname $lastname! ".AFFILIATEID.": $affiliateid</span>
	<table align=\"center\" width=\"400\"><tr><td align=\"center\"><form action=\"affiliate.php\" method=\"post\"><input class=\"ashopaffiliatebutton\" type=\"submit\" value=\"".STATISTICS."\"></form></td><td align=\"center\"><form action=\"changeprofile.php\" method=\"post\"><input class=\"ashopaffiliatebuttonlarge\" type=\"submit\" value=\"".VIEWPROFILE."\"></form></td><td align=\"center\"><input class=\"ashopaffiliatebuttonlarge\" type=\"button\" value=\"".CHANGEPASSBTN."\" disabled></td><td align=\"center\"><form action=\"login.php?logout\" method=\"post\"><input class=\"ashopaffiliatebutton\" type=\"submit\" value=\"".LOGOUT."\"></form></td></tr></table>
	<table align=\"center\" width=\"400\"><tr><td align=\"center\"><form action=\"linkcodes.php\" method=\"post\"><input class=\"ashopaffiliatebutton\" type=\"submit\" value=\"".LINKCODES."\"></form></td><td align=\"center\"><form action=\"orderhistory.php\" method=\"post\"><input class=\"ashopaffiliatebuttonlarge\" type=\"submit\" value=\"".ORDERHISTORY."\"></form></td>";
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
?>

<table class="ashopaffiliatesignupframe">
  <tr><td align="center"> 
      <p><span class="ashopaffiliateheader"><?php echo CHANGEPASS ?></span></p></td>
  </tr>
  <tr align="center"> 
    <td> 
      <table class="ashopaffiliatesignupbox">
        <tr align="center"> 
          <td> 
            <form action="changepassword.php" method=post>
              <table border=0 cellspacing=0 cellpadding=3 width="440">
                <tr> 
                  <td align="right" width="159"><span class="ashopaffiliatetext3"><?php echo OLDPASS ?>:</span></td>
                  <td width="269"> 
                    <input type="password" name="oldpassword" size=20>
                  </td>
                </tr>
                <tr> 
                  <td align="right" width="159"><span class="ashopaffiliatetext3"><?php echo NEWPASS ?>:</span></td>
                  <td width="269"> 
                    <input type="password" name="newpassword1" size=20>
                  </td>
                </tr>
                <tr> 
                  <td align="right" width="159"><span class="ashopaffiliatetext3"><?php echo CONFIRM ?>:</span></td>
                  <td width="269"> 
                    <input type="password" name="newpassword2" size=20>
                  </td>
                </tr>
			  </table>
              <br>
              <table>
                <tr> 
                  <td colspan=4 align=center> 
                    <p> 
                      <input type="submit" value="<?php echo UPDATE ?>"  name="Submit">
                  </td>
                </tr>
              </table>
            </form>
      </table>
    </td>
  </tr>
</table>

<?php
// Print footer using template...
if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplatefooter("$ashoppath$templatepath/affiliate-$lang.html");
else ashop_showtemplatefooter("$ashoppath$templatepath/affiliate.html");
?>