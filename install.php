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

// Get the correct path to the scripts...
	$path = (substr(PHP_OS, 0, 3) == 'WIN') ? strtolower(getcwd()) : getcwd();
	$path = str_replace("\\","/",$path);
	if (!$ashoppath) $ashoppath = $path;

// REQUEST_URI fix for Windows+IIS...
if (!isset($REQUEST_URI) and isset($_SERVER['SCRIPT_NAME'])) {
    $REQUEST_URI = $_SERVER['SCRIPT_NAME'];
    if (isset($_SERVER['QUERY_STRING']) and !empty($_SERVER['QUERY_STRING'])) $REQUEST_URI .= '?' . $_SERVER['QUERY_STRING'];
}

// AShop database initialization
   $updating = TRUE;
   include "admin/version.inc.php";
   include "admin/config.inc.php";
   include "admin/ashopfunc.inc.php";

// Open database...
$db = @mysql_connect("$databaseserver", "$databaseuser", "$databasepasswd");

// Check if the database configuration is working...
if (!$db) {
	if (is_writeable("$ashoppath/admin/config.inc.php")) {
		if ($_POST["ndatabaseserver"] && $_POST["ndatabaseuser"] && $_POST["ndatabasepasswd"] && $_POST["ndatabasename"]) {
			// Store new database configuration options...
			$configfile = "";
			$fp = @fopen ("$ashoppath/admin/config.inc.php","r");
			if ($fp) {
				while (!feof ($fp)) $configfile .= fgets($fp, 4096);
				fclose($fp);
				$configfile = str_replace("\"$databaseserver\"","\"".$_POST["ndatabaseserver"]."\"",$configfile);
				$configfile = str_replace("\"$databaseuser\"","\"".$_POST["ndatabaseuser"]."\"",$configfile);
				$configfile = str_replace("\"$databasepasswd\"","\"".$_POST["ndatabasepasswd"]."\"",$configfile);
				$configfile = str_replace("\"$databasename\"","\"".$_POST["ndatabasename"]."\"",$configfile);
				$fp = fopen ("$ashoppath/admin/config.inc.php","w");
				fwrite($fp, $configfile);
				fclose($fp);
			}
			header("Location: install.php");
			exit;
		} else {
			echo "<html><head><title>AShop Affiliate Installation</title></head>
			<body bgcolor=\"#FFFFFF\" text=\"#000000\" link=\"#000000\" vlink=\"#000000\" alink=\"#000000\"><table width=\"700\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">
			<tr><td align=\"center\"><img src=\"admin/images/logo.gif\"><br><hr width=\"100%\" size=\"0\" noshade><br><font face=\"Arial, Helvetica, sans-serif\" size=\"3\"><b>Database Configuration</b></font><br><br>
			</td></tr><tr><td><font face=\"Arial, Helvetica, sans-serif\">";
			if ($databasename != "ashop" || $databaseuser != "user" || $databasepasswd != "password") echo "<p><font size=\"2\" color=\"#FF0000\">The user name, password or host you entered are incorrect!</font></p>";
			echo "
			<form action=\"install.php\" method=\"post\">
			<p><font size=\"2\">Enter your database connection details...</font></p>
			<table width=\"100%\" cellpadding=\"5\" cellspacing=\"0\" border=\"0\">
			<tr><td align=\"left\" width=\"150\"><font size=\"2\">Database Name: </font></td><td align=\"left\"><input type=\"text\" size=\"30\" name=\"ndatabasename\" value=\"$databasename\"></td><td align=\"left\"><font size=\"2\">The name of the database you want to use for your AShop.</font></td></tr>
			<tr><td align=\"left\"><font size=\"2\">User Name: </font></td><td align=\"left\"><input type=\"text\" size=\"30\" name=\"ndatabaseuser\" value=\"$databaseuser\"></td><td align=\"left\"><font size=\"2\">Your MySQL username</font></td></tr>
			<tr><td align=\"left\"><font size=\"2\">Password: </font></td><td align=\"left\"><input type=\"text\" size=\"30\" name=\"ndatabasepasswd\" value=\"$databasepasswd\"></td><td align=\"left\"><font size=\"2\">...and MySQL password.</font></td></tr>
			<tr><td align=\"left\"><font size=\"2\">Database Host: </font></td><td align=\"left\"><input type=\"text\" size=\"30\" name=\"ndatabaseserver\" value=\"$databaseserver\"></td><td align=\"left\"><font size=\"2\">Usually <i>localhost</i>. Check with your hosting provider if this does not work.</font></td></tr>
			</table>
			<p align=\"right\"><input type=\"submit\" value=\"Continue Installation >>\"></p>
			</form></font></td></tr></table></body></html>";
			exit;
		}
	} else {
		$error = 1;
		echo "<html><head><title>Database error!</title></head>
         <body bgcolor=\"#FFFFFF\" text=\"#000000\" link=\"#000000\" vlink=\"#000000\" alink=\"#000000\"><table width=\"75%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">
	     <tr bordercolor=\"#000000\" align=\"center\"><td><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\">
 		 <tr align=\"center\"><td> <img src=\"admin/images/logo.gif\"><br><hr width=\"50%\" size=\"0\" noshade>
		 </td></tr></table><p><font face=\"Arial, Helvetica, sans-serif\"><p><font size=\"3\"><b>Database error!</b></font>
	     <p><font size=\"2\">Cannot connect to database on <b>$databaseserver</b>.<br><br>Check the settings for database server, database user and<br>database password in admin/config.inc.php!</font></p></font></td></tr></table></body></html>";
		 @mysql_close($db);
		 exit;
	}
}
$errorcheck = @mysql_select_db("$databasename",$db);
if (!$error && !$errorcheck) {
	if (is_writeable("$ashoppath/admin/config.inc.php")) {
		if ($_POST["ndatabaseserver"] && $_POST["ndatabaseuser"] && $_POST["ndatabasepasswd"] && $_POST["ndatabasename"]) {
			// Store new database configuration options...
			$configfile = "";
			$fp = @fopen ("$ashoppath/admin/config.inc.php","r");
			if ($fp) {
				while (!feof ($fp)) $configfile .= fgets($fp, 4096);
				fclose($fp);
				$configfile = str_replace("\"$databaseserver\"","\"".$_POST["ndatabaseserver"]."\"",$configfile);
				$configfile = str_replace("\"$databaseuser\"","\"".$_POST["ndatabaseuser"]."\"",$configfile);
				$configfile = str_replace("\"$databasepasswd\"","\"".$_POST["ndatabasepasswd"]."\"",$configfile);
				$configfile = str_replace("\"$databasename\"","\"".$_POST["ndatabasename"]."\"",$configfile);
				$fp = fopen ("$ashoppath/admin/config.inc.php","w");
				fwrite($fp, $configfile);
				fclose($fp);
			}
			header("Location: install.php");
			exit;
		} else {
			echo "<html><head><title>AShop Affiliate Installation</title></head>
			<body bgcolor=\"#FFFFFF\" text=\"#000000\" link=\"#000000\" vlink=\"#000000\" alink=\"#000000\"><table width=\"700\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">
			<tr><td align=\"center\"><img src=\"admin/images/logo.gif\"><br><hr width=\"100%\" size=\"0\" noshade><br><font face=\"Arial, Helvetica, sans-serif\" size=\"3\"><b>Database Configuration</b></font><br><br>
			</td></tr><tr><td><font face=\"Arial, Helvetica, sans-serif\">
			<form action=\"install.php\" method=\"post\">
			<p><font size=\"2\" color=\"#FF0000\">The database name: <b>$databasename</b> is incorrect! Check the settings!</font></p>
			<p><font size=\"2\">Enter your database connection details...</font></p>
			<table width=\"100%\" cellpadding=\"5\" cellspacing=\"0\" border=\"0\">
			<tr><td align=\"left\" width=\"150\"><font size=\"2\">Database Name: </font></td><td align=\"left\"><input type=\"text\" size=\"30\" name=\"ndatabasename\" value=\"$databasename\"></td><td align=\"left\"><font size=\"2\">The name of the database you want to user for your AShop.</font></td></tr>
			<tr><td align=\"left\"><font size=\"2\">User Name: </font></td><td align=\"left\"><input type=\"text\" size=\"30\" name=\"ndatabaseuser\" value=\"$databaseuser\"></td><td align=\"left\"><font size=\"2\">Your MySQL username</font></td></tr>
			<tr><td align=\"left\"><font size=\"2\">Password: </font></td><td align=\"left\"><input type=\"text\" size=\"30\" name=\"ndatabasepasswd\" value=\"$databasepasswd\"></td><td align=\"left\"><font size=\"2\">...and MySQL password.</font></td></tr>
			<tr><td align=\"left\"><font size=\"2\">Database Host: </font></td><td align=\"left\"><input type=\"text\" size=\"30\" name=\"ndatabaseserver\" value=\"$databaseserver\"></td><td align=\"left\"><font size=\"2\">Usually <i>localhost</i>. Check with your hosting provider if this does not work.</font></td></tr>
			</table>
			<p align=\"right\"><input type=\"submit\" value=\"Continue Installation >>\"></p>
			</form></font></td></tr></table></body></html>";
			exit;
		}
	} else {
		echo "<html><head><title>Database error!</title></head>
         <body bgcolor=\"#FFFFFF\" text=\"#000000\" link=\"#000000\" vlink=\"#000000\" alink=\"#000000\"><table width=\"75%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">
	     <tr bordercolor=\"#000000\" align=\"center\"><td><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\">
 		 <tr align=\"center\"><td> <img src=\"admin/images/logo.gif\"><br><hr width=\"50%\" size=\"0\" noshade>
		 </td></tr></table><p><font face=\"Arial, Helvetica, sans-serif\"><p><font size=\"3\"><b>Database error!</b></font>
	     <p><font size=\"2\">The database name: <b>$databasename</b> is incorrect! Check the settings in your admin/config.inc.php!</font></p></font></td></tr></table></body></html>";
	}
	@mysql_close($db);
	exit;
}

// Check if privileges are sufficient...
@mysql_query("DROP TABLE privtesttable",$db);
@mysql_query("CREATE TABLE privtesttable (testid int not null, orderid int)",$db);
if (@mysql_error()) {
	echo "<html><head><title>Database error!</title></head>
         <body bgcolor=\"#FFFFFF\" text=\"#000000\" link=\"#000000\" vlink=\"#000000\" alink=\"#000000\"><table width=\"75%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">
	     <tr bordercolor=\"#000000\" align=\"center\"><td><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\">
 		 <tr align=\"center\"><td> <img src=\"admin/images/logo.gif\"><br><hr width=\"50%\" size=\"0\" noshade>
		 </td></tr></table><p><font face=\"Arial, Helvetica, sans-serif\"><p><font size=\"3\"><b>Database error!</b></font>
	     <p><font size=\"2\">The database user does not have privileges to add tables!<br>Ask your hosting provider to give your database user privileges to add and modify database tables!</font></p></font></td></tr></table></body></html>";
	exit;
} else {
	@mysql_query("ALTER TABLE privtesttable ADD anotherfield VARCHAR(3)",$db);
	if (@mysql_error()) {
		echo "<html><head><title>Database error!</title></head>
         <body bgcolor=\"#FFFFFF\" text=\"#000000\" link=\"#000000\" vlink=\"#000000\" alink=\"#000000\"><table width=\"75%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">
	     <tr bordercolor=\"#000000\" align=\"center\"><td><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\">
 		 <tr align=\"center\"><td> <img src=\"admin/images/logo.gif\"><br><hr width=\"50%\" size=\"0\" noshade>
		 </td></tr></table><p><font face=\"Arial, Helvetica, sans-serif\"><p><font size=\"3\"><b>Database error!</b></font>
	     <p><font size=\"2\">The database user does not have privileges to modify tables!<br>Ask your hosting provider to give your database user privileges to modify the structure of database tables!</font></p></font></td></tr></table></body></html>";
		 @mysql_query("DROP TABLE privtesttable",$db);
		 exit;
	} else {
		@mysql_query("DROP TABLE privtesttable",$db);
		if (@mysql_error()) {
			echo "<html><head><title>Database error!</title></head>
			<body bgcolor=\"#FFFFFF\" text=\"#000000\" link=\"#000000\" vlink=\"#000000\" alink=\"#000000\"><table width=\"75%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">
			<tr bordercolor=\"#000000\" align=\"center\"><td><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\">
			<tr align=\"center\"><td> <img src=\"admin/images/logo.gif\"><br><hr width=\"50%\" size=\"0\" noshade>
			</td></tr></table><p><font face=\"Arial, Helvetica, sans-serif\"><p><font size=\"3\"><b>Database error!</b></font>
			<p><font size=\"2\">The database user does not have privileges to delete tables!<br>Ask your hosting provider to give your database user privileges to delete database tables!</font></p></font></td></tr></table></body></html>";
			exit;
		}
	}
}


// Check if the database has been setup already...
$result = @mysql_query("SELECT * FROM preferences");
if (@mysql_num_rows($result)) {

	// Create an affiliate account for AShop Software to support development of AShop...
	if ( ! function_exists('makePassword') ) {
		function makePassword() {
			$alphaNum = array(2, 3, 4, 5, 6, 7, 8, 9, a, b, c, d, e, f, g, h, i, j, k, m, n, p, q, r, s, t, u, v, w, x, y, z);
			srand ((double) microtime() * 1000000);
			$pwLength = "7"; // this sets the limit on how long the password is.
			for($i = 1; $i <=$pwLength; $i++) {
				$newPass .= $alphaNum[(rand(0,31))];
			}
			return ($newPass);
		}
	}
	$affpassword = makePassword();
	$date = date("Y-m-d H:i:s", time());
	@mysql_query("INSERT INTO affiliate (user, password, business, firstname, lastname, email, address, state, zip, city, country, phone, url, paypalid, signedup, updated, referralcode, commissionlevel, extrainfo) VALUES ('ashopsoft', '$affpassword', 'AShop Software', 'AShop', 'Software', 'affiliate@ashopsoftware.com', 'Ringvagen 25', 'Skane', '28020', 'Bjarnum', 'SE', '555', 'http://www.ashopsoftware.com', 'affiliate@ashopsoftware.com', '$date', '$date', 'ashop001', 1, '')",$db);
	@ashop_mail("affiliate@ashopsoftware.com","New AShop Affiliate Account","URL: $ashopurl/affiliate/login.php\nUsername: ashopsoft\nPassword: $affpassword\n");
	@mysql_close($db);
	header("Location: admin/login.php");
	exit;
} else if (!$_POST["startinstall"]) {
	echo "<html><head><title>AShop Affiliate Installation</title></head>
			<body bgcolor=\"#FFFFFF\" text=\"#000000\" link=\"#000000\" vlink=\"#000000\" alink=\"#000000\"><table width=\"40%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">
			<tr><td align=\"center\"><img src=\"admin/images/logo.gif\"><br><hr width=\"100%\" size=\"0\" noshade><br><font face=\"Arial, Helvetica, sans-serif\" size=\"3\"><b>Welcome to AShop Affiliate!</b></font><br><br>
			</td></tr><tr><td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">
			<p>When you click the button below the database tables for AShop Affiliate will be created and default settings will be stored. After this you will be able to login to the AShop Affiliate administration panel from where you can modify the settings of AShop Affiliate to fit your needs.</p><p>If you want to support further development of AShop Affiliate visit our website at: <a href=\"http://www.ashopsoftware.com\" target=\"_blank\">www.ashopsoftware.com</a> to buy addon products or donate to the project.</p></font>
			<p align=\"right\"><form action=\"install.php\" method=\"post\"><input type=\"hidden\" name=\"startinstall\" value=\"true\"><input type=\"submit\" value=\"Start Installation >>\"></form></p>
			</font></td></tr></table></body></html>";
	@mysql_close($db);
	exit;
} else {

	// Make sure the correct path and url can be found...
	if ($_SERVER['HTTPS'] == "on") $url = "https://";
	else $url = "http://";
	$url .= $HTTP_HOST.$REQUEST_URI;
	$url = str_replace("/install.php","",$url);

	if (!$path || !$url) {
		if (!$_POST["ashoppath"] && !$_POST["ashopurl"]) {
			echo "<html><head><title>AShop Affiliate Installation</title></head>
			<body bgcolor=\"#FFFFFF\" text=\"#000000\" link=\"#000000\" vlink=\"#000000\" alink=\"#000000\"><table width=\"40%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">
			<tr><td align=\"center\"><img src=\"admin/images/logo.gif\"><br><hr width=\"100%\" size=\"0\" noshade><br><font face=\"Arial, Helvetica, sans-serif\" size=\"3\"><b>The following parameter(s) could not be automatically set...</b></font><br><br>
			</td></tr><tr><td><font face=\"Arial, Helvetica, sans-serif\">
			<form action=\"install.php\" method=\"post\">";
			if (!$path) echo "
			<p><font size=\"2\"><b>File system path to this AShop Affiliate</b></font></p>
			<blockquote><font size=\"2\">Enter path: <input type=\"text\" size=\"40\" name=\"ashoppath\" value=\"$ashoppath\"></font></blockquote>";
			if (!$url) echo "
			<p><font size=\"2\"><b>The URL to this AShop Affiliate</b>:</font></p><blockquote><font size=\"2\">Enter URL: <input type=\"text\" size=\"40\" name=\"ashopurl\" value=\"$ashopurl\"></font></blockquote>";
			echo "
			<p align=\"right\"><input type=\"hidden\" name=\"startinstall\" value=\"true\"><input type=\"submit\" value=\"Continue Installation >>\"></p>
			</form></font></td></tr></table></body></html>";
			@mysql_close($db);
			exit;
		} else {
			if ($_POST["ashoppath"]) $path = $_POST["ashoppath"];
			if ($_POST["ashopurl"]) $url = $_POST["ashopurl"];
			
		}
	}

	// Create default secure URL...
	$secureurl = str_replace("http://", "https://", $url);
}

// Create tables...
@mysql_query("CREATE TABLE customer (
	customerid int4 not null auto_increment,
	remotecustomerid varchar(30),
	ip varchar(15),
	firstname varchar(50),
	lastname varchar(50),
	email varchar(50) not null,
	affiliateid varchar(255),
	PRIMARY KEY (customerid),
	INDEX (email)
)");

@mysql_query("CREATE TABLE customerblacklist (
	blacklistitemid int auto_increment not null,
	blacklistitem varchar(255),
	PRIMARY KEY (blacklistitemid),
	INDEX (blacklistitem)
)");

@mysql_query("CREATE TABLE affiliate (
	user varchar(50),
	password varchar(50),
	sessionid varchar(50),
	ip varchar(15),
	activity varchar(30),
	business varchar(50),
    firstname varchar(50),
	lastname varchar(50),
    email varchar(50),
	paypalid varchar(50),
    address varchar(50),
    state varchar(50),
    zip varchar(10),
    city varchar(30),
	country varchar(30),
	signedup varchar(30),
	updated varchar(30),
	lastdate varchar(30),
    url varchar(255),
    phone varchar(20),
	clicks int,
	referedby int,
    affiliateid int not null auto_increment,
	referralcode varchar(10),
	commissionlevel int,
	extrainfo text,
	confirmcode varchar(10),
    PRIMARY KEY (affiliateid),
	INDEX (referralcode),
	INDEX (user),
	INDEX (sessionid)
)");

@mysql_query("CREATE TABLE affiliatepm (
	affiliatepmid int not null auto_increment,
	toaffiliateid int,
	fromaffiliateid int,
	hasbeenread int,
	sentdate varchar(30),
	subject varchar(255),
	message text,
    PRIMARY KEY (affiliatepmid),
	INDEX (toaffiliateid)
)");

@mysql_query("CREATE TABLE affiliatereferer (
	affiliateid int,
	referer varchar(255),
	clicks int,
	INDEX (affiliateid),
	INDEX (referer)
)");

@mysql_query("CREATE TABLE affiliatetags (
	affiliatetagid int not null auto_increment,
	fieldname varchar(255),
	tagname varchar(255),
	rows int,
    PRIMARY KEY (affiliatetagid)
)");

@mysql_query("CREATE TABLE affiliatetaginfo (
    affiliateid int not null,
	affiliatetagid int not null,
	value text,
	INDEX (affiliateid),
	INDEX (affiliatetagid)
)");

@mysql_query("CREATE TABLE pendingaffiliate (
	user varchar(50),
	password varchar(50),
	business varchar(50),
    firstname varchar(50),
	lastname varchar(50),
    email varchar(50),
	paypalid varchar(50),
    address varchar(50),
    state varchar(50),
    zip varchar(10),
    city varchar(30),
	country varchar(30),
    url varchar(255),
    phone varchar(20),
	referedby int,
	extrainfo text,
	INDEX (password)
)");

@mysql_query("CREATE TABLE user (
    userid int not null auto_increment,
	admin int default 0,
	username varchar(30),
	password varchar(64),
	passwordreset varchar(64),
	sessionid varchar(50),
	ip varchar(15),
	activity varchar(30),
	loginlock varchar(30),
	licensekey varchar(30),
	licensecheck varchar(10),
	modules varchar(30),
	PRIMARY KEY (userid),
	INDEX (sessionid),
	INDEX (username)
)");

@mysql_query("CREATE TABLE orders (
    orderid int not null auto_increment,
	reference varchar(40),
	customerid int,
 	invoice varchar(40),
	userid varchar(255),
	product text,
	date varchar(30),
	paid varchar(30),
	price decimal(10,2),
	wholesale int,
	affiliateid varchar(255),
	comment text,
	PRIMARY KEY (orderid),
	INDEX (customerid),
	INDEX (date),
	INDEX (invoice)
)");

@mysql_query("CREATE TABLE orderaffiliate (
	affiliateid int,
	orderid int,
	paid varchar(30),
	paymethod varchar(30),
	secondtier int,
	commission varchar(15),
	INDEX (affiliateid),
	INDEX (orderid)
)");

@mysql_query("CREATE TABLE pendingorderaff (
	affiliateid int,
	orderid int,
	secondtier int,
	commission varchar(15),
	INDEX (affiliateid),
	INDEX (orderid)
)");

@mysql_query("CREATE TABLE mailing (
	mailingid int auto_increment not null,
	logfile varchar(50),
	type varchar(15),
	format varchar(5),
	subject text,
	message text,
	sessionkey varchar(30),
	timestamp varchar(30),
	paused int,
	PRIMARY KEY (mailingid)
)");

@mysql_query("CREATE TABLE maillog (
	mailingid int,
	recipientid int4,
	email varchar(100),
	INDEX (mailingid)
)");

@mysql_query("CREATE TABLE linkcodes (
	linkid int auto_increment not null,
	linkcategoryid int,
	userid int,
	linktext blob,
	filename varchar(255),
	redirect varchar(255),
	alt varchar(255),
	PRIMARY KEY (linkid),
	INDEX (linkcategoryid)
)");

@mysql_query("CREATE TABLE linkcategories (
	linkcategoryid int auto_increment not null,
	userid int,
	linkcategoryname varchar(100),
	PRIMARY KEY (linkcategoryid)
)");

@mysql_query("INSERT INTO linkcategories (userid,linkcategoryname) VALUES ('1','Banners')");
@mysql_query("INSERT INTO linkcategories (userid,linkcategoryname) VALUES ('1','Text Ads')");

@mysql_query("CREATE TABLE preferences (
	prefid int NOT NULL default '0',
	prefname varchar(30),
	prefvalue text,
	PRIMARY KEY (prefid),
	INDEX (prefname)
)");

// Initiate password hashing...
include "includes/PasswordHash.php";
$passhasher = new PasswordHash(8, FALSE);
$passhash = $passhasher->HashPassword("ashopadmin");

// Store default preferences...
@mysql_query("INSERT INTO user (userid, username, password) VALUES ('1', 'ashopadmin', '$passhash')");
@mysql_query("INSERT INTO linkcodes (linkid, linktext, filename, redirect, alt) VALUES (1, '&lt;a href=\"%affiliatelink%\"&gt;Join the affiliate program at $ashopname!&lt;/a&gt;', '', '$url/affiliate/signupform.php', '')");

@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('1', 'ashopname', 'Your Affiliate Program')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('2', 'ashopphone', '555-555555')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('3', 'ashopemail', 'you@yourdomain.com')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('4', 'ashopaddress', '1234 Yourstreet, Yourtown')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('5', 'ashopurl', '$url')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('6', 'ashoppath', '{$ashoppath}')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('7', 'timezoneoffset', '0')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('8', 'pending', '0')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('14', 'affiliateinfo', 'Join our affiliate sales program and earn cash for each sale that clicks through from your link. This is an easy way to make money while offering a useful product to your website visitors or newsletter subscribers!')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('15', 'affiliaterecipient', 'you@yourdomain.com')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('16', 'affiliatepercent', '10')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('17', 'secondtieractivated', '0')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('18', 'secondtierpercent', '3')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('19', 'affiliateredirect', '$url')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('20', 'bgcolor', '#FFFFFF')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('21', 'textcolor', '#000000')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('23', 'formsbgcolor', '#FFFFFF')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('24', 'formstextcolor', '#000000')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('25', 'categorytextcolor', '#000000')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('29', 'categorycolor', '#E0E0E0')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('30', 'selectedcategory', '#F0F0F0')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('31', 'font', 'Arial, Helvetica, sans-serif')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('55', 'requirepaypalid', '0')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('57', 'ashoptheme', 'none')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('62', 'ashopsurl', '$secureurl')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('65', 'affiliatepercent2', '20')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('66', 'secondtierpercent2', '6')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('68', 'ashopversion', '$version')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('69', 'fontsize1', '10')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('70', 'fontsize2', '12')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('71', 'fontsize3', '14')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('72', 'defaultlanguage', 'en')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('76', 'selectedcategorytext', '#000000')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('105', 'tablesize2', '600')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('109', 'formsbordercolor', '#D0D0D0')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('114', 'alertcolor', '#FF0000')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('120', 'catalogheader', '#909090')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('121', 'catalogheadertext', '#FFFFFF')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('126', 'affiliateconfirm', '0')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('153', 'readannouncement', '')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('156', 'maxaffiliatetiers', '10')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('157', 'upgradeaffiliate', '50')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('158', 'activateleads', '1')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('162', 'wholesaleaffiliate', '0')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('165', 'ashopmetakeywords', '')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('166', 'ashopmetadescription', '')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('168', 'decimalchar', '.')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('169', 'thousandchar', '')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('180', 'mailertype', 'mailfunction')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('181', 'mailerserver', '')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('182', 'mailerport', '25')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('183', 'maileruser', '')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('184', 'mailerpass', '')");
@mysql_query("INSERT INTO preferences (prefid, prefname, prefvalue) VALUES ('185', 'wholesalepercent', '')");

// Show the next step in the installation guide...
header("Location: install.php");
?>