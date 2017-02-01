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
include "ashopfunc.inc.php";
$adminlang = "en";
include "language/$adminlang/login.inc.php";
if (!$adminpanelcolor) $adminpanelcolor = "7589e7";
else $adminpanelcolor = str_replace("#","","$adminpanelcolor");
unset($shop);
unset($userid);
if (!$p3psent) header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');
$p3psent = TRUE;
SetCookie($userid,"");

// Initiate password hashing...
include "$ashoppath/includes/PasswordHash.php";
$passhasher = new PasswordHash(8, FALSE);

// Open database...
$db = @mysql_connect("$databaseserver", "$databaseuser", "$databasepasswd");
@mysql_select_db("$databasename",$db);

// Handle password reset requests...
if (!empty($r) && !empty($u) && is_numeric($u)) {
	// Check the reset code...
	$result = @mysql_query("SELECT passwordreset FROM user WHERE userid='$u'",$db);
	$resethash = @mysql_result($result,0,"passwordreset");
	$resetcheck = $passhasher->CheckPassword($r, $resethash);
	if (!$resetcheck) {
		echo "<HTML><HEAD>".CHARSET."<title>$ashopname - ".ADMINPANELLOGIN."</title><meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\"><link rel=\"stylesheet\" href=\"admin.css\" type=\"text/css\"></HEAD>
	    <BODY bgcolor=\"#FFFFFF\" text=\"#FFFFFF\">
		<CENTER>
		<table width=\"100%\" height=\"100%\"><tr><td align=\"center\">
		<table class=\"loginform\" width=\"300\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><td align=\"center\" valign=\"top\">
		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
		<tr class=\"loginformheader\"><td align=\"center\"><img src=\"images/adminlogo.gif\" border=\"0\" alt=\"$ashopurl\"></a></td></tr>
		<tr><td align=\"center\"><br><br><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#000000\">".INVALIDRESETCODE."<br><br><span class=\"formtitlewh\"><a href=\"javascript:history.back()\">".TRYAGAIN."</a></span><br><br><br><br><br></font></td></tr></table>
		</td></tr></table>
		</td></tr></table></body></html>";
		exit;
	}
	if (empty($newpassword)) {
		// Show password reset page in browser...
		echo "<HTML><HEAD>".CHARSET."<title>$ashopname - ".ADMINPANELLOGIN."</title><meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\"><link rel=\"stylesheet\" href=\"admin.css\" type=\"text/css\"></HEAD>
		<BODY bgcolor=\"#FFFFFF\" text=\"#FFFFFF\">
		<CENTER>
		<table width=\"100%\" height=\"100%\"><tr><td align=\"center\">
		<form action=\"sendpass.php?r=$r&u=$u\" method=\"post\">
		<table class=\"loginform\" width=\"300\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><td align=\"center\" valign=\"top\">
		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
		<tr class=\"loginformheader\"><td align=\"center\"><img src=\"images/adminlogo.gif\" border=\"0\" alt=\"$ashopurl\"></a></td></tr>
		<tr><td style=\"padding-left: 10px; padding-top: 10px;\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#000000\">".ENTERNEWPASS."<br><br></font></td></tr></table>
		<table width=\"220\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
		<tr><td align=\"right\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#000000\"><span class=\"formlabel\">".NEWPASSWORD.": </span></font></td><td align=\"left\">
		<input type=\"password\" name=\"newpassword\" class=\"loginforminputsm\"><script language=\"JavaScript\">document.forms[0].newpassword.focus();</script></td></tr>
		<tr><td align=\"right\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#000000\"><span class=\"formlabel\">".CONFIRM.": </span></font></td><td align=\"left\">
		<input type=\"password\" name=\"confirm\" class=\"loginforminputsm\"></td></tr>
		<tr><td>&nbsp;</td><td align=\"right\"><br><input type=\"submit\" value=\"".SUBMIT."\"><br><br></td></tr></table></td></tr></table>
		</form></td></tr></table></body></html>";
		exit;
	} else {
		if ($newpassword != $confirm) {
			echo "<HTML><HEAD>".CHARSET."<title>$ashopname - ".ADMINPANELLOGIN."</title><meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\"><link rel=\"stylesheet\" href=\"admin.css\" type=\"text/css\"></HEAD>
			<BODY bgcolor=\"#FFFFFF\" text=\"#FFFFFF\">
			<CENTER>
			<table width=\"100%\" height=\"100%\"><tr><td align=\"center\">
			<form action=\"sendpass.php\" method=\"post\">
			<table class=\"loginform\" width=\"300\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><td align=\"center\" valign=\"top\">
			<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
			<tr class=\"loginformheader\"><td align=\"center\"><img src=\"images/adminlogo.gif\" border=\"0\" alt=\"$ashopurl\"></a></td></tr>
			<tr><td align=\"center\"><br><br><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#000000\">".PASSWORDSDIDNOTMATCH."<br><br><span class=\"formtitlewh\"><a href=\"javascript:history.back()\">".TRYAGAIN."</a></span><br><br><br><br><br></font></td></tr></table>
			</td></tr></table>
			</form></td></tr></table></body></html>";
			exit;
		} else {
			$passwordhash = $passhasher->HashPassword($newpassword);
			@mysql_query("UPDATE user SET password='$passwordhash', passwordreset='' WHERE userid='$u'",$db);
			echo "<HTML><HEAD>".CHARSET."<title>$ashopname - ".ADMINPANELLOGIN."</title><meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\"><link rel=\"stylesheet\" href=\"admin.css\" type=\"text/css\"></HEAD>
			<BODY bgcolor=\"#FFFFFF\" text=\"#FFFFFF\">
			<CENTER>
			<table width=\"100%\" height=\"100%\"><tr><td align=\"center\">
			<table class=\"loginform\" width=\"300\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><td align=\"center\" valign=\"top\">
			<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
			<tr class=\"loginformheader\"><td align=\"center\"><img src=\"images/adminlogo.gif\" border=\"0\" alt=\"$ashopurl\"></a></td></tr>
			<tr><td align=\"center\"><br><br><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#000000\">".YOURPASSWORDHASBEENCHANGED."<br><br><span class=\"formtitlewh\"><a href=\"login.php\">".ADMINPANELLOGIN."</a></span><br><br><br><br><br></font></td></tr></table>
			</td></tr></table>
			</td></tr></table></body></html>";
			exit;
		}
	}
}

if (isset($user) && empty($user)) $user = "ashopadmin";

if ($user != "") {

	// Function for generating unique passwords...
	function makePassword() {
		$alphaNum = array(2, 3, 4, 5, 6, 7, 8, 9, a, b, c, d, e, f, g, h, i, j, k, m, n, p, q, r, s, t, u, v, w, x, y, z);
		srand ((double) microtime() * 1000000);
		$pwLength = "7"; // this sets the limit on how long the password is.
		for($j = 1; $j <=$pwLength; $j++) {
			$newPass .= $alphaNum[(rand(0,31))];
		}
		return ($newPass);
	}

	// Generate a reset code...
	$resetcode = makePassword();
	$resethash = $passhasher->HashPassword($resetcode);

	// Get email from database...
	$sql="SELECT email,userid FROM user WHERE username='$user'";
	$result = @mysql_query("$sql",$db);
	if (@mysql_num_rows($result) != 0) {
		$email = @mysql_result($result, 0, "email");
		$uid = @mysql_result($result, 0, "userid");
		if (empty($email)) $email = $ashopemail;
	} else {
		unset($email);
	}

  if ($user && $email && $uid) {

	  // Store the reset hash..
	  @mysql_query("UPDATE user SET passwordreset='$resethash' WHERE username='$user'",$db);

	  // Send message with password reset link...
	  if ($email == $ashopemail) $subject="$ashopname - ".ADMINPANELPASSWORD;
	  else $subject="$ashopname - ".SHOPPINGMALLPASSWORD;
	  $message="<html><head><title>".YOURSHOPPINGMALLPASSWORD."</title></head><body><font face=\"$font\"><p>".YOURPASSWORDFOR." $ashopname ".CANBEGHANGED.": <a href=\"$ashopurl/admin/sendpass.php?r=$resetcode&u=$uid\">$ashopurl/admin/sendpass.php?r=$resetcode&u=$uid</a></p></font></body></html>";
	  $headers = "From: $ashopname<$ashopemail>\nX-Sender: <$ashopemail>\nX-Mailer: PHP\nX-Priority: 3\nReturn-Path: <$ashopemail>\nMIME-Version: 1.0\nContent-Type: text/html; charset=iso-8859-1\n";
	  @ashop_mail("$email","$subject","$message","$headers");
  }

  // Close database...
  @mysql_close($db);

  // Tell shopping mall user that the password reset code has been sent...
  if ($resetcode) {
	  echo "<HTML><HEAD>".CHARSET."<title>$ashopname - ".ADMINPANELLOGIN."</title><meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\"><link rel=\"stylesheet\" href=\"admin.css\" type=\"text/css\"></HEAD>
	    <BODY bgcolor=\"#FFFFFF\" text=\"#FFFFFF\">
		<CENTER>
		<table width=\"100%\" height=\"100%\"><tr><td align=\"center\">
		<form action=\"sendpass.php\" method=\"post\">
		<table class=\"loginform\" width=\"300\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><td align=\"center\" valign=\"top\">
		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
		<tr class=\"loginformheader\"><td align=\"center\"><img src=\"images/adminlogo.gif\" border=\"0\" alt=\"$ashopurl\"></a></td></tr>
		<tr><td align=\"center\"><br><br><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#000000\">".YOURPASSWORDHASBEENSENT."<br><br><span class=\"formtitlewh\"><a href=\"login.php\">".ADMINPANELLOGIN."</a></span><br><br><br><br><br></font></td></tr></table>
		</td></tr></table>
		</form></td></tr></table></body></html>";
	exit;
  } else {
	  echo "<HTML><HEAD>".CHARSET."<title>$ashopname - ".ADMINPANELLOGIN."</title><meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\"><link rel=\"stylesheet\" href=\"admin.css\" type=\"text/css\"></HEAD>
	    <BODY bgcolor=\"#FFFFFF\" text=\"#FFFFFF\">
		<CENTER>
		<table width=\"100%\" height=\"100%\"><tr><td align=\"center\">
		<form action=\"sendpass.php\" method=\"post\">
		<table class=\"loginform\" width=\"300\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><td align=\"center\" valign=\"top\">
		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
		<tr class=\"loginformheader\"><td align=\"center\"><img src=\"images/adminlogo.gif\" border=\"0\" alt=\"$ashopurl\"></a></td></tr>
		<tr><td align=\"center\"><br><br><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#000000\">".NOTREGISTERED."<br><br><span class=\"formtitlewh\"><a href=\"javascript:history.back()\">".TRYAGAIN."</a></span><br><br><br><br><br></font></td></tr></table>
		</td></tr></table>
		</form></td></tr></table></body></html>";
	exit;
  }
}

// Show send password page in browser...
echo "<HTML><HEAD>".CHARSET."<title>$ashopname - ".ADMINPANELLOGIN."</title><meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\"><link rel=\"stylesheet\" href=\"admin.css\" type=\"text/css\"></HEAD>
	    <BODY bgcolor=\"#FFFFFF\" text=\"#FFFFFF\">
		<CENTER>
		<table width=\"100%\" height=\"100%\"><tr><td align=\"center\">
		<form action=\"sendpass.php\" method=\"post\">
		<table class=\"loginform\" width=\"300\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><td align=\"center\" valign=\"top\">
		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
		<tr class=\"loginformheader\"><td align=\"center\"><img src=\"images/adminlogo.gif\" border=\"0\" alt=\"$ashopurl\"></a></td></tr>
		<tr><td style=\"padding-left: 10px; padding-top: 10px;\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#000000\">".ENTERUSERNAMEANDWEWILLSENDPASS."<br><br></font></td></tr></table>
    <table width=\"200\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
	<tr><td align=\"right\"><font face=\"Arial, Helvetica, sans-serif\" size=\"2\" color=\"#000000\"><span class=\"formlabel\">".USERNAME.": </span></font></td><td align=\"left\">
		<input type=\"text\" name=\"user\"><script language=\"JavaScript\">document.forms[0].user.focus();</script></td></tr>
		<tr><td>&nbsp;</td><td align=\"right\"><br><input type=\"submit\" value=\"".SUBMIT."\"><br><br></td></tr></table></td></tr></table>
		</form></td></tr></table></body></html>";
?>