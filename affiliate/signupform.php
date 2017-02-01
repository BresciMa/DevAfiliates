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

// Check for GD...
ob_start(); 
phpinfo(8); 
$phpinfo=ob_get_contents(); 
ob_end_clean();
$phpinfo=strip_tags($phpinfo); 
$phpinfo=stristr($phpinfo,"gd version"); 
$phpinfo=stristr($phpinfo,"version"); 
$end=strpos($phpinfo,"\n"); 
$phpinfo=substr($phpinfo,0,$end);
preg_match ("/[0-9]/", $phpinfo, $version);
if(isset($version[0]) && $version[0]>1) $gdversion = 2;
else $gdversion = 0;

include "../admin/config.inc.php";
include "../admin/ashopfunc.inc.php";
include "../admin/ashopconstants.inc.php";

// If GD is available generate random code for security check...
if (function_exists('imagecreatefromjpeg') && function_exists('imagecreatefromgif') && function_exists('imagecreatetruecolor') && $gdversion == 2) {
	$activatesecuritycheck = TRUE;
	// Generate new random code...
	mt_srand ((double)microtime()*1000000);
	$maxrandom = 1000000;
	$random = mt_rand(0, $maxrandom);
} else $activatesecuritycheck = FALSE;

// Apply selected theme...
$buttonpath = "";
$templatepath = "/templates";
if ($ashoptheme && $ashoptheme != "none" && file_exists("$ashoppath/themes/$ashoptheme/theme.cfg.php")) include "../themes/$ashoptheme/theme.cfg.php";
if ($usethemebuttons == "true") $buttonpath = "themes/$ashoptheme/";
if ($usethemetemplates == "true") $templatepath = "/themes/$ashoptheme";
if ($lang && is_array($themelanguages)) {
	if (!in_array("$lang",$themelanguages)) unset($lang);
}

// Include language file...
if (!$lang) $lang = $defaultlanguage;
include "../language/$lang/af_signupform.inc.php";

// Make sure html code is being parsed...
if (function_exists(html_entity_decode)) {
	$affiliateinfo = html_entity_decode($affiliateinfo);
	$affiliateinfo = str_replace("&#039;","'",$affiliateinfo);
}

// Print header from template...
if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplateheader("$ashoppath$templatepath/affiliate-$lang.html");
else ashop_showtemplateheader("$ashoppath$templatepath/affiliate.html");

echo "
<script language=\"JavaScript\" src=\"../includes/switchstates.js.php\" type=\"text/javascript\"></script>
<br /><table class=\"ashopaffiliatesignupframe\">
  <tr><td align=\"center\"> 

      <p><span class=\"ashopaffiliateheader\">".SIGNUPHERE." $ashopname ".AFFILIATEPROGRAM."</span></p>
	  <p><span class=\"ashopaffiliatetext2\">".ALREADYSIGNEDUP." <a href=\"login.php\">".LOGINHERE."</a></span></p>
      <p align=\"left\"><span class=\"ashopaffiliatetext2\">$affiliateinfo</span></p>
      <p align=\"left\"><span class=\"ashopaffiliatetext2\">".AFFILIATEMESSAGE."</span></p>
      </td>
  </tr>
  <tr align=\"center\"> 
    <td> 
      <table class=\"ashopaffiliatesignupbox\">
        <tr align=\"center\"> 
          <td> 
            <form action=\"signup.php\" method=\"post\" name=\"signupform\">
              <table border=\"0\" cellspacing=\"0\" cellpadding=\"3\" width=\"440\">
                <tr> 
                  <td align=\"right\" width=\"159\"><span class=\"ashopaffiliatetext3\">".USERNAME.":</span></td>
                  <td width=\"269\" class=\"ashopaffiliatefield\"> 
                    <input type=\"text\" name=\"affuser\" size=\"15\" />
                    <span class=\"ashopaffiliatenotice\"> ".MAXCHARS."</span> </td>
                </tr>
                <tr> 
                  <td align=\"right\"><span class=\"ashopaffiliatetext3\">".BUSINESS.":</span></td>
                  <td class=\"ashopaffiliatefield\"> 
                    <input type=\"text\" name=\"business\" size=\"30\" />
                  </td>
                </tr>
                <tr> 
                  <td align=\"right\"><span class=\"ashopaffiliatetext3\">".FIRSTNAME.":</span></td>
                  <td class=\"ashopaffiliatefield\"> 
                    <input type=\"text\" name=\"firstname\" size=\"30\" />
                  </td>
                </tr>
                <tr> 
                  <td align=\"right\"><span class=\"ashopaffiliatetext3\">".LASTNAME.":</span></td>
                  <td class=\"ashopaffiliatefield\"> 
                    <input type=\"text\" name=\"lastname\" size=\"30\" />
                  </td>
                </tr>
                <tr> 
                  <td align=\"right\"><span class=\"ashopaffiliatetext3\">".EMAIL.":</span></td>
                  <td class=\"ashopaffiliatefield\"> 
                    <input type=\"text\" name=\"email\" size=\"30\" />
                  </td>
                </tr>
                <tr> 
                  <td align=\"right\"><span class=\"ashopaffiliatetext3\">".ADDRESS.":</span></td>
                  <td class=\"ashopaffiliatefield\"> 
                    <input type=\"text\" name=\"address\" size=\"30\" />
                  </td>
                </tr>
                <tr>
                  <td align=\"right\"><span class=\"ashopaffiliatetext3\">".CITY.":</span></td>
                  <td class=\"ashopaffiliatefield\"> 
                    <input type=\"text\" name=\"city\" size=\"20\" />
                  </td>
                </tr>
                <tr> 
                  <td align=\"right\"><span class=\"ashopaffiliatetext3\">".ZIP.":</span></td>
                  <td class=\"ashopaffiliatefield\"> 
                    <input type=\"text\" name=\"zip\" size=\"10\" />
                  </td>
                </tr>
                <tr> 
                  <td align=\"right\"><span class=\"ashopaffiliatetext3\">".COUNTRY.":</span></td>
                  <td class=\"ashopaffiliatefield\"> 
                    <select name=\"country\" onchange=\"switchStates(document.signupform.state,document.signupform.province,document.signupform.country.value);\"><option  value=\"none\">choose country</option>";
					foreach ($countries as $shortcountry => $longcountry) {
						if (strlen($longcountry) > 30) $longcountry = substr($longcountry,0,27)."...";
						echo "<option value=\"$shortcountry\">$longcountry</option>\n";
					}
					echo "</select>
                  </td>
                </tr>
                <tr id=\"stateselector\" style=\"display:none\"> 
                  <td align=\"right\"><span class=\"ashopaffiliatetext3\">".STATE.":</span></td>
                  <td class=\"ashopaffiliatefield\"> 
                    <select name=\"state\"><option value=\"none\">".CHOOSESTATE."</option></select>
                  </td>
                </tr>
                <tr id=\"regionrow\" style=\"display:none\"> 
                  <td align=\"right\"><span class=\"ashopaffiliatetext3\">".PROVINCE.":</span></td>
                  <td class=\"ashopaffiliatefield\"> 
                    <input type=\"text\" name=\"province\" size=\"20\" />
                  </td>
                </tr>

                <tr> 
                  <td align=\"right\"><span class=\"ashopaffiliatetext3\">".PHONE.":</span></td>
                  <td class=\"ashopaffiliatefield\"> 
                    <input type=\"text\" name=\"phone\" size=\"20\" />
                  </td>
                </tr>
                <tr> 
                  <td align=\"right\"><span class=\"ashopaffiliatetext3\">".URL.":</span></td>
                  <td valign=\"top\" class=\"ashopaffiliatefield\"> 
                    <input type=\"text\" name=\"url\" size=\"30\" />
                  </td>
                </tr>
                <tr> 
                  <td align=\"right\"><span class=\"ashopaffiliatetext3\">".PAYPAL.":</span></td>
                  <td valign=\"top\" class=\"ashopaffiliatefield\"> 
                    <input type=\"text\" name=\"paypalid\" size=\"30\" />
                  </td>
                </tr>";
				if (!$requirepaypalid) echo "<tr><td></td><td><span class=\"ashopaffiliatenotice\">".OPTIONAL."</span></td></tr>";
				/*
				<tr>
                  <td align=\"left\" colspan=\"2\"><span class=\"ashopaffiliatetext3\">".PLEASEPROVIDE." </span></td>
                </tr>
                <tr> 
                  <td align=\"right\"><span class=\"ashopaffiliatetext3\">".DESCRIPTION.":</span></td>
                  <td valign=\"top\" class=\"ashopaffiliatefield\"> 
                    <textarea name=\"extrainfo\" cols=\"30\" rows=\"5\"></textarea>
                  </td>
                </tr>":
				*/
				if ($activatesecuritycheck) {
					echo "<tr><td align=\"right\" width=\"159\"><span class=\"ashopaffiliatetext3\">".SECURITYCODE.":</span></td><td width=\"269\" valign=\"top\" class=\"ashopaffiliatefield\"><img src=\"../admin/afsignuppic.php?action=generatecode&amp;random=$random\" border=\"1\" alt=\"Security Code\" title=\"Security Code\" /></td></tr><tr><td align=\"right\" width=\"159\"><span class=\"ashopaffiliatetext3\">".TYPESECURITYCODE.":</span></td><td width=\"269\" valign=\"top\" class=\"ashopaffiliatefield\"><input type=\"text\" name=\"securitycheck\" size=\"10\" /><input type=\"hidden\" name=\"random\" value=\"$random\" /></td></tr>";
			    }
			    echo "
                <tr> 
                  <td colspan=\"2\" align=\"center\"> 
                      <p><input type=\"image\" src=\"../{$buttonpath}images/submit-$lang.png\" class=\"ashopbutton\" style=\"border: none;\" alt=\"".SUBMIT."\" name=\"Submit\" /></p>
                  </td>
                </tr>
              </table>
            </form>
			</td>
			</tr>
      </table>
    </td>
  </tr>
</table>";

// Print footer using template...
if ($lang != $defaultlanguage && file_exists("$ashoppath$templatepath/affiliate-$lang.html")) ashop_showtemplatefooter("$ashoppath$templatepath/affiliate-$lang.html");
else ashop_showtemplatefooter("$ashoppath$templatepath/affiliate.html");
?>