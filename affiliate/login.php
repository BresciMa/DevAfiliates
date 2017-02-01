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
include "../admin/ashopfunc.inc.php";
if (!empty($affiliatesesid) && !ashop_is_md5($affiliatesesid)) $affiliatesesid = "";

if ($_SERVER['QUERY_STRING'] == "logout") {
	@mysql_query("UPDATE affiliate SET activity=NULL WHERE sessionid='$affiliatesesid'",$db);
	if (!$p3psent) header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');
	$p3psent = TRUE;
	setcookie("affiliatesesid","",time()-10800,"/");
	if (strstr($SERVER_SOFTWARE, "IIS")) {
		echo "<html><head><meta http-equiv=\"Refresh\" content=\"0; URL=login.php\"></head></html>";
		exit;
	} else header("Location: login.php");
}

// Apply selected theme...
$buttonpath = "";
$templatepath = "/templates";
if ($ashoptheme && $ashoptheme != "none") include "../themes/$ashoptheme/theme.cfg.php";
if ($usethemetemplates == "true") $templatepath = "/themes/$ashoptheme";
if ($usethemebuttons == "true") $buttonpath = "themes/$ashoptheme/";
if ($lang && is_array($themelanguages)) {
	if (!in_array("$lang",$themelanguages)) unset($lang);
}

// Include language file...
if (!$lang) $lang = $defaultlanguage;
include "../language/$lang/af_login.inc.php";

if (!$affuser || !$affpassword) {

// Print header from template...
if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplateheader("$ashoppath$templatepath/affiliate-$lang.html");
else ashop_showtemplateheader("$ashoppath$templatepath/affiliate.html");

echo "<br /><table class=\"ashopaffiliateloginframe\"><tr><td align=\"left\"><span class=\"ashopaffiliateheader\">".LOGINMESSAGE."</span>";

if ($_SERVER['QUERY_STRING'] == "retrylogin") echo "<p><span class=\"ashopaffiliatetext2\" style=\"color: $alertcolor;\">".WRONGPASSWORD." ".TRYAGAIN."</span></p>";

if ($newregistered) echo "<p><span class=\"ashopaffiliatetext2\">".SIGNUPMESSAGE1."</span></p>";
   else echo "<p><span class=\"ashopaffiliatetext2\">".SIGNUPMESSAGE2."</span></p>";

echo "<form method=\"post\" action=\"login.php\">
    <table width=\"400\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
      <tr> 
        <td align=\"right\"><span class=\"ashopaffiliatetext2\">".USER.":</span></td>
        <td width=\"160\" align=\"left\">&nbsp;<input type=\"text\" name=\"affuser\" size=\"20\" /></td>
        <td>&nbsp;</td>
      </tr>
      <tr> 
        <td align=\"right\"><span class=\"ashopaffiliatetext2\">".PASSWORD.":</span></td>
        <td align=\"left\">&nbsp;<input type=\"password\" name=\"affpassword\" size=\"20\" /></td>
        <td><input type=\"image\" src=\"../{$buttonpath}images/login-$lang.png\" class=\"ashopbutton\" style=\"border: none;\" alt=\"".LOGIN."\" name=\"Submit\" /></td>
      </tr>
    </table>
  </form>
  <p><span class=\"ashopaffiliatetext2\"><a href=\"sendpass.php\">".FORGOTPASS."</a><br /><a href=\"signupform.php\">".NEWAFFILIATE."</a></span>
  </p></td></tr></table>";

// Print footer using template...
if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplatefooter("$ashoppath$templatepath/affiliate-$lang.html");
else ashop_showtemplatefooter("$ashoppath$templatepath/affiliate.html");
exit;
}

// Open database...
$db = @mysql_connect("$databaseserver", "$databaseuser", "$databasepasswd");
@mysql_select_db("$databasename",$db);

$date = date("Y/m/d H:i:s");

$sql = "SELECT * FROM affiliate WHERE user = '$affuser' AND password = '$affpassword'";
$result = @mysql_query($sql, $db);
if (!@mysql_num_rows($result)) {
	@mysql_close($db);
    header("Location: login.php?retrylogin");
} else {
	$hash = md5($date.$affuser.$affpassword."ashopisgreat");
    $sql = "UPDATE affiliate SET sessionid='$hash', activity='$date', ip='{$_SERVER["REMOTE_ADDR"]}' WHERE user='$affuser'";
    @mysql_query($sql,$db);
    @mysql_close($db);
	if (!$p3psent) header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');
	$p3psent = TRUE;
    setcookie("affiliatesesid","$hash",time()+10800,"/");
    header("Location: affiliate.php");
}
?>