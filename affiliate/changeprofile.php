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
include "../language/$lang/af_changeprofile.inc.php";

// Open database...
$db = @mysql_connect("$databaseserver", "$databaseuser", "$databasepasswd");
@mysql_select_db("$databasename",$db);

// Get affiliate information from database...
$sql="SELECT * FROM affiliate WHERE sessionid='$affiliatesesid'";
$result = @mysql_query("$sql",$db);

// Get the correct password for this affiliate...
$correctpasswd = @mysql_result($result, 0, "password");

// Set current date and time...
$date = date("Y-m-d H:i:s", time()+$timezoneoffset);

// Update profile...
if($Submit) {
	$sql = "UPDATE affiliate SET business='$business', firstname='$firstname', lastname='$lastname', email='$email', address='$address', state='$state', zip='$zip', city='$city', url='$url', phone='$phone', country='$country', paypalid='$paypalid', updated='$date' WHERE user='$affuser'";
	@mysql_query("$sql",$db);
}

// Store affiliate information in variables...
$business = @mysql_result($result, 0, "business");
$firstname = @mysql_result($result, 0, "firstname");
$lastname = @mysql_result($result, 0, "lastname");
$email = @mysql_result($result, 0, "email");
$address = @mysql_result($result, 0, "address");
$state = @mysql_result($result, 0, "state");
$zip = @mysql_result($result, 0, "zip");
$city = @mysql_result($result, 0, "city");
$url = @mysql_result($result, 0, "url");
$phone = @mysql_result($result, 0, "phone");
$country = @mysql_result($result, 0, "country");
$paypalid = @mysql_result($result, 0, "paypalid");
$affiliateid = @mysql_result($result, 0, "affiliateid");

// Get custom fields...
$customfields = "";
$customfieldsresult = @mysql_query("SELECT * FROM affiliatetags ORDER BY fieldname ASC",$db);
if (@mysql_num_rows($customfieldsresult)) {
	while ($customfieldrow = @mysql_fetch_array($customfieldsresult)) {
		$tagid = $customfieldrow["affiliatetagid"];
		$fieldname = $customfieldrow["fieldname"];
		$htmlfieldname = str_replace(" ","__",$fieldname);
		$rows = $customfieldrow["rows"];
		$affinfo = "";
		if($Submit) {
			$affinfo = $_POST["$htmlfieldname"];
			$customfieldinforesult = @mysql_query("SELECT * FROM affiliatetaginfo WHERE affiliateid='$affiliateid' AND affiliatetagid='$tagid'",$db);
			if (@mysql_num_rows($customfieldinforesult)) @mysql_query("UPDATE affiliatetaginfo SET value='$affinfo' WHERE affiliateid='$affiliateid' AND affiliatetagid='$tagid'",$db);
			else @mysql_query("INSERT INTO affiliatetaginfo (affiliateid,affiliatetagid,value) VALUES ('$affiliateid','$tagid','$affinfo')",$db);
		} else {
			$customfieldinforesult = @mysql_query("SELECT * FROM affiliatetaginfo WHERE affiliateid='$affiliateid' AND affiliatetagid='$tagid'",$db);
			if (@mysql_num_rows($customfieldinforesult)) $affinfo = @mysql_result($customfieldinforesult,0,"value");
		}
		if ($rows == "1") $customfields .= "<tr><td align=\"right\"><span class=\"ashopaffiliatetext3\">$fieldname:</span></td>
		<td><input type=text name=\"$htmlfieldname\" value=\"$affinfo\" size=40></td></tr>";
		else $customfields .= "<tr><td align=\"right\"><span class=\"ashopaffiliatetext3\">$fieldname:</span></td>
		<td><textarea name=\"$htmlfieldname\" cols=\"30\" rows=\"$rows\">$affinfo</textarea></td></tr>";
	}
}

if ($Submit) {
	header("Location: affiliate.php");
	exit;
}

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
	<table align=\"center\" width=\"400\"><tr><td align=\"center\"><form action=\"affiliate.php\" method=\"post\"><input class=\"ashopaffiliatebutton\" type=\"submit\" value=\"".STATISTICS."\"></form></td><td align=\"center\"><input class=\"ashopaffiliatebuttonlarge\" type=\"button\" value=\"".VIEWPROFILE."\" disabled></td><td align=\"center\"><form action=\"changepassword.php\" method=\"post\"><input class=\"ashopaffiliatebuttonlarge\" type=\"submit\" value=\"".CHANGEPASS."\"></form></td><td align=\"center\"><form action=\"login.php?logout\" method=\"post\"><input class=\"ashopaffiliatebutton\" type=\"submit\" value=\"".LOGOUT."\"></form></td></tr></table>
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
      <p><span class="ashopaffiliateheader"><?php echo CHANGEBELOW ?></span></p></td>
  </tr>
  <tr align="center"> 
    <td> 
      <table class="ashopaffiliatesignupbox">
        <tr align="center"> 
          <td> 
            <form action="changeprofile.php" method=post name="orderform">
              <table border=0 cellspacing=0 cellpadding=3 width="440">
                <tr> 
                  <td align="right" width="159"><span class="ashopaffiliatetext3"><?php echo BUSINESS ?>:</span></td>
                  <td width="269"> 
                    <input type=text name="business" value="<?php echo $business ?>" size=40>
                  </td>
                </tr>
                <tr> 
                  <td align="right" width="159"><span class="ashopaffiliatetext3"><?php echo FIRSTNAME ?>:</span></td>
                  <td width="269"> 
                    <input type=text name="firstname" value="<?php echo $firstname ?>" size=40>
                  </td>
                </tr>
                <tr> 
                  <td align="right" width="159"><span class="ashopaffiliatetext3"><?php echo LASTNAME ?>:</span></td>
                  <td width="269"> 
                    <input type=text name="lastname" value="<?php echo $lastname ?>" size=40>
                  </td>
                </tr>
                <tr> 
                  <td align="right" width="159"><span class="ashopaffiliatetext3"><?php echo EMAIL ?>:</span></td>
                  <td width="269"> 
                    <input type=text name="email" value="<?php echo $email ?>" size=40>
                  </td>
                </tr>
                <tr> 
                  <td align="right" width="159"><span class="ashopaffiliatetext3"><?php echo ADDRESS ?>:</span></td>
                  <td width="269"> 
                    <input type=text name="address" value="<?php echo $address ?>" size=40>
                  </td>
                </tr>
                <tr>
                  <td align="right" height="25" width="159"><span class="ashopaffiliatetext3"><?php echo CITY ?>:</span></td>
                  <td width="269" height="25"> 
                    <input type=text name="city" value="<?php echo $city ?>" size=40>
                  </td>
                </tr>
                <tr> 
                  <td align="right" width="159"><span class="ashopaffiliatetext3"><?php echo STATE ?>:</span></td>
                  <td width="269"> 
                    <input type=text name="state" value="<?php echo $state ?>" size=40>
                  </td>
                </tr>
                <tr> 
                  <td align="right" width="159"><span class="ashopaffiliatetext3"><?php echo ZIP ?>:</span></td>
                  <td width="269"> 
                    <input type=text name="zip" value="<?php echo $zip ?>" size=40>
                  </td>
                </tr>
                <tr> 
                  <td align="right" width="159"><span class="ashopaffiliatetext3"><?php echo COUNTRY ?>:</span></td>
                  <td width="269"> 
                    <input type=text name="country" value="<?php echo $country ?>" size=40>
                  </td>
                </tr>
                <tr> 
                  <td align="right" width="159"><span class="ashopaffiliatetext3"><?php echo PHONE ?>:</span></td>
                  <td width="269"> 
                    <input type=text name="phone" value="<?php echo $phone ?>" size=40>
                  </td>
                </tr>
                <tr> 
                  <td align="right" width="159"><span class="ashopaffiliatetext3"><?php echo URL ?>:</span></td>
                  <td width="269" valign="top"> 
                    <input type="text" name="url" value="<?php echo $url ?>" size="40">
                  </td>
                </tr>
                <tr> 
                  <td align="right" width="159"><span class="ashopaffiliatetext3"><?php echo PAYPAL ?>:</span></td>
                  <td width="269" valign="top"> 
                    <input type="text" name="paypalid" value="<?php echo $paypalid ?>" size="40">
                  </td>
                </tr>
				<tr>
				  <td></td><td><span class="ashopaffiliatenotice"><?php echo OPTIONAL ?></span></td>
				</tr>
				<?php if ($customfields) echo $customfields; ?>
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