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

// Validate variable name...

function ashop_validatename($name) {
	if (!preg_match("/[a-zA-Z_\x7f-\xff]/", substr($name, 0, 1))) return FALSE;
	for ($i = 1; $i <= strlen($name)-1; $i++) {
		if (!preg_match("/[a-zA-Z0-9_\x7f-\xff]/", substr($name, $i, 1))) return FALSE;
	}
	return TRUE;
}

// Copy a row in a database table...
function ashop_copyrow($table, $idfield, $copyid) {
	global $db;
	if ($table AND $idfield AND $copyid > 0) {
		$sql = "SELECT * FROM $table WHERE $idfield = '$copyid'";
		$result = @mysql_query($sql,$db);
		if ($result) {
			$sql = "INSERT INTO $table SET ";
			$row = @mysql_fetch_array($result);
			$rowkeys = array_keys($row);
			$rowvalues = array_values($row);
			$fieldnumber = 1;
			if (is_integer($rowkeys[0])) $startfield = 1;
			else $startfield = 0;
			for ($i=$startfield;$i<count($rowkeys);$i+=2) {
				if ($rowkeys[$i] != $idfield) {
					if ($fieldnumber!=1) $sql .= ", ";
					$sql .= $rowkeys[$i] . " = '" . $rowvalues[$i] . "'";
					$fieldnumber++;
				}
			}
			$result = @mysql_query($sql,$db);
		}
	}
}

// Input validation functions...
function ashop_is_zip($zip) {
	if (preg_match("/^[\-\: A-Za-z0-9]*$/", $zip)) return TRUE;
	else return FALSE;
}

function ashop_is_md5($hash) {
	if (preg_match("/^[0-9a-f]{32}$/", $hash)) return TRUE;
	else return FALSE;
}

function ashop_is_name($name) {
	if (preg_match("/^[ \-\.\'a-zA-ZÀ-ÿ]*$/", $name)) return TRUE;
	else return FALSE;
}

function ashop_is_address($address) {
	if (preg_match("/^[\-\.\(\)\#\'\\/, A-Za-zÀ-ÿ0-9]*$/", $address)) return TRUE;
	else return FALSE;
}

function ashop_is_phonenumber($phonenumber) {
	if (preg_match("/^[\-\.\(\)\+ 0-9]*$/", $phonenumber)) return TRUE;
	else return FALSE;
}

function ashop_is_email($email) {
	if (preg_match("/^[[:alnum:]][a-z0-9_\.\-]*@[a-z0-9\.\-]+\.[a-z]{2,4}$/", $email)) return TRUE;
	else return FALSE;
}

function ashop_is_country($country) {
	if (preg_match("/^[\,\-\.\(\) A-Za-zÀ-ÿ]*$/", $country)) return TRUE;
	else return FALSE;
}

function ashop_is_state($state) {
	if (preg_match("/^[\,\-\.\(\) A-Za-zÀ-ÿ]*$/", $state)) return TRUE;
	else return FALSE;
}

function ashop_is_vatnumber($vatnumber) {
	if (preg_match("/^[\*\- A-Za-z0-9]*$/", $vatnumber)) return TRUE;
	else return FALSE;
}

function ashop_is_businessname($businessname) {
	if (preg_match("/^[\-\.\(\)\#\'\, A-Za-zÀ-ÿ0-9]*$/", $businessname)) return TRUE;
	else return FALSE;
}

function ashop_is_url($url) {
	if (preg_match("/^(http(s)?\:\/\/)?[\=\.\#\?\-\&\/A-Za-z0-9]*$/", $url)) return TRUE;
	else return FALSE;
}

function ashop_is_captchacode($captchacode) {
	if (preg_match("/^[0-9]{6}$/", $captchacode)) return TRUE;
	else return FALSE;
}

function ashop_simulatepost($posttoscript,$querystring) {
	unset($_POST);
	unset($_GET);
	unset($_COOKIE);

	$queryarray = explode("&",$querystring);
	if (!empty($queryarray) && is_array($queryarray)) foreach($queryarray as $querypart) {
		$querypartarray = explode("=",$querypart);
		$queryname = $querypartarray[0];
		$queryvalue = $querypartarray[1];
		$_POST["$queryname"] = $queryvalue;
	}

	if (file_exists($posttoscript)) {
		include "$posttoscript";
		return "SUCCESS";
	} else return "FAILED";
}

// Detect mobile devices and override layout with an adapted version...
function ashop_mobile() {
	global $device, $ashoppath, $itemsperrow, $displayitems, $templatepath, $buttonpath, $devices;

	if (!$devices) include $ashoppath."/admin/ashopconstants.inc.php";

	if (!$device) {
		$useragent = $_SERVER['HTTP_USER_AGENT'];
		$device = "";
		foreach ($devices as $thisuseragent=>$thisdevice) if (stripos($useragent,$thisuseragent)) $device = $thisuseragent;
		if ($device == "Android" && !stripos($useragent,"mobile")) $device = "AndroidTablet";
	}

	if (!empty($device) && (array_key_exists($device,$devices) || $device == "mobile")) {
		if ($devices[$useragent]["itemsperrow"] < $itemsperrow) $itemsperrow = $devices[$device]["itemsperrow"];
		if ($devices[$useragent]["displayitems"] < $displayitems) $displayitems = $devices[$device]["displayitems"];
		$newtemplatepath = $templatepath."/".$devices[$device]["name"];
		if (is_dir($ashoppath.$newtemplatepath)) $templatepath = $newtemplatepath;
		$newbuttonpath = $buttonpath.$devices[$device]["name"];
		if (is_dir($ashoppath."/".$newbuttonpath."/images")) $buttonpath = $newbuttonpath;
		return $devices[$device]["name"];
	} else return FALSE;
}

// Check for spam injection attempts...
function ashop_mailsafe($field) {
	$nonewlines = preg_match("/(%0A|%0D|\\n+|\\r+)/i", $field) == 0;
	$nomailheaders = preg_match("/(content-type:|to:|cc:|bcc:)/i", $field) == 0;
	if ($nonewlines && $nomailheaders) return $field;
	else return FALSE;
}

// Open database connection...
function ashop_opendatabase() {
	global $db, $databaseserver, $databaseuser, $databasepasswd, $databasename;
	$error = 0;
	if (!@mysql_get_server_info()) {
		$db = @mysql_connect("$databaseserver", "$databaseuser", "$databasepasswd");
		if (!$db) $error = 1;
		$errorcheck = @mysql_select_db("$databasename",$db);
		if (!$error) if (!$errorcheck) $error = 2;
	}
	return $error;
}

// Function for generating a random string...
function ashop_randomstring ($min, $max, $useupper, $usespecial, $usenumbers) {
	$characters = "abcdefghijklmnopqrstuvwxyz";
	if ($useupper) $characters .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	if ($usenumbers) $characters .= "0123456789";
	if ($usespecial) $characters .= "~@#$%^*()_+-={}|][";
	if ($min > $max) $length = mt_rand ($max, $min);
	else $length = mt_rand ($min, $max);
	$randomstring = "";
	for ($i=0; $i<$length; $i++) $randomstring .= $characters[(mt_rand(0,(strlen($characters)-1)))];
	return $randomstring;
}

// Function for encryption/decryption of data...
function ashop_endecrypt($pwd, $data, $case='') {
	if ($case == 'de') {
		$data = urldecode($data);
	}
	$key[] = "";
	$box[] = "";
	$temp_swap = "";
	$pwd_length = 0;
	$pwd_length = strlen($pwd);
	for ($i = 0; $i <= 255; $i++) {
		$key[$i] = ord(substr($pwd, ($i % $pwd_length), 1));
		$box[$i] = $i;
	}
	$x = 0;
	for ($i = 0; $i <= 255; $i++) {
		$x = ($x + $box[$i] + $key[$i]) % 256;
		$temp_swap = $box[$i];
		$box[$i] = $box[$x];
		$box[$x] = $temp_swap;
	}
	$temp = "";
	$k = "";
	$cipherby = "";
	$cipher = "";
	$a = 0;
	$j = 0;
	for ($i = 0; $i < strlen($data); $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$temp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $temp;
		$k = $box[(($box[$a] + $box[$j]) % 256)];
		$cipherby = ord(substr($data, $i, 1)) ^ $k;
		$cipher .= chr($cipherby);
	}
	if ($case == 'de') {
		$cipher = urldecode(urlencode($cipher));
	} else {
		$cipher = urlencode($cipher);
	}
	return $cipher;
}

// Generate code for form security check...
function generatecode($random) {
	global $databaseuser;
    $datekey = date("F j");
    $rcode = hexdec(md5($_SERVER[HTTP_USER_AGENT] . $databaseuser . $random . $datekey));
    $code = substr($rcode, 2, 6);
	return $code;
}

// Send mail by regular php mail(), SMTP and directly to eMerchant inbox if needed...
if(!function_exists('ashop_mail')) {
	function ashop_mail($recipient, $subject, $message, $headers) {
		global $db, $timezoneoffset, $ashoppath, $ashopname, $ashopemail, $databaseserver, $databaseuser, $databasepasswd, $databasename, $mailertype, $mailerserver, $mailerport, $maileruser, $mailerpass;
		if (!$db) {
			// Open database connection if missing...
			$db = @mysql_connect("$databaseserver", "$databaseuser", "$databasepasswd");
			@mysql_select_db("$databasename",$db);
			$openeddb = TRUE;
		} else $openeddb = FALSE;
		$result = @mysql_query("SELECT confvalue FROM emerchant_configuration WHERE confname='customeremail'",$db);
		$emmail = @mysql_result($result, 0, "confvalue");
		if ($recipient == $emmail && is_dir("$ashoppath/emerchant/mail")) {
			$timestamp = time()+$timezoneoffset;
			@mysql_query("INSERT INTO emerchant_inbox (received, name, email, subject) VALUES ('$timestamp', '$ashopname', '$recipient', '$subject')",$db);
			$mailid = @mysql_insert_id();
			if ($message) {
				$fp = @fopen ("$ashoppath/emerchant/mail/in1-$mailid", "w");
				if ($fp) {
					fwrite($fp, $headers."\n\n");
					fwrite($fp, $message);
					fclose($fp);
				}
			}
		} else if ($mailertype == "smtp") {
			require_once "$ashoppath/includes/class.phpmailer.php";
			$mail = new PHPMailer();
			if (strstr($headers,"text/html")) $mail->MsgHTML($message);
			else $mail->Body = $message;
			if ($mailerserver && $maileruser && $mailerpass) {
				$headersarray = explode("\n",$headers);
				if (!empty($headersarray) && is_array($headersarray)) {
					foreach ($headersarray as $header) {
						$thisheaderarray = explode(": ",$header);
						if (isset($thisheaderarray[0]) && $thisheaderarray[0] == "From") {
							$fullsender = $thisheaderarray[1];
							$senderarray = explode ("<",$fullsender);
							if (isset($senderarray[1])) {
								$sendername = $senderarray[0];
								$senderemail = str_replace(">","",$senderarray[1]);
							} else $senderemail = $fullsender;
						}
					}
				}
				if (!$senderemail) $senderemail = $ashopemail;
				if (!$sendername) $sendername = $ashopname;
				if (!$mailerport) $mailerport = "25";
				$mail->Port = $mailerport;
				$mail->IsSMTP();
				//$mail->SMTPDebug = 2;
				$mail->Host = $mailerserver;
				$mail->SMTPAuth = true;
				$mail->Username = $maileruser;
				$mail->Password = $mailerpass;
				$mail->From = $senderemail;
				$mail->FromName = $sendername;
				$mail->Subject = $subject;
				$mail->AddAddress($recipient, $recipientname);
				$result = $mail->Send();
				if (!$result) $result = $mail->ErrorInfo;
			} else $result = "SMTP Configuration Error";
		} else $result = @mail($recipient, $subject, $message, $headers);
		if ($openeddb) @mysql_close($db);
	}
	if (empty($result)) $result = FALSE;
	return $result;
}

// Windows and Mac compatible memory usage function...
if(!function_exists('memory_get_usage')) { 
	function memory_get_usage() {
		if ( substr(PHP_OS,0,3) == 'WIN') {
			if ( substr( PHP_OS, 0, 3 ) == 'WIN' ) {
				$output = array();
				exec( 'tasklist /FI "PID eq ' . getmypid() . '" /FO LIST', $output );
				return preg_replace( '/[\D]/', '', $output[5] ) * 1024;
			}
		} else {
			$pid = getmypid();
			exec("ps -eo%mem,rss,pid | grep $pid", $output);
			$output = explode("  ", $output[0]);
			return $output[1] * 1024;
		}
	} 
}

// Chunked readfile to preserve memory...
function readfile_chunked($filename,$retbytes=true) { 
   $chunksize = 1*(1024*1024);
   $buffer = ''; 
   $cnt =0; 
   $handle = fopen($filename, 'rb'); 
   if ($handle === false) { 
       return false; 
   } 
   while (!feof($handle)) { 
       $buffer = fread($handle, $chunksize); 
       echo $buffer; 
       ob_flush(); 
       flush(); 
       if ($retbytes) { 
           $cnt += strlen($buffer); 
       } 
   } 
       $status = fclose($handle); 
   if ($retbytes && $status) { 
       return $cnt;
   } 
   return $status; 
}

function getresponsevalue($responsecode, $responsestring) {
	$startpos = strpos($responsestring, "\"$responsecode,\"");
	if ($startpos === false) return "";
	else {
		$endresponsestring = substr($responsestring, $startpos+strlen("\"$responsecode,\""));
		return substr($endresponsestring, 0, strpos($endresponsestring, "\""));
	}
}

function ashop_bitlyshorten($url) {
	$ch = curl_init();
	$bitlyurl = "http://api.bit.ly/shorten?version=2.0.1&longUrl=$url&login=ashopsoftware&apiKey=R_232dbc0723b0129d920e44605aafed5f";
	curl_setopt ($ch, CURLOPT_URL,$bitlyurl);
	curl_setopt ($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_HTTPGET, 1);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	$result = curl_exec ($ch);
	curl_close ($ch);
	if (strpos($result,"shortUrl")) {
		$resultarray = explode("\"shortUrl\": \"",$result);
		$resultarray = explode("\"}",$resultarray[1]);
		$shorturl = $resultarray[0];
	}
	if(strpos($shorturl,"t.ly")) return $shorturl;
	else return FALSE;
}

// Reverse conversion of special characters to html entities...
function un_html($textstring,$keepdotsandcommas=0) {
	if (!$keepdotsandcommas) {
		$textstring = str_replace(",","",$textstring);
		$textstring = str_replace(".","",$textstring);
	}
	$trans = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
	$trans = array_flip($trans);
	$original = strtr($textstring, $trans);
	return $original;
}

// Remove content-type meta header...
function demetafy($textstring) {
	$checklowercase = FALSE;
	$metastop = 0;
	while (!$checklowercase) {
		$metastart = strpos($textstring, "<meta", $metastop);
		if ($metastart === FALSE) $checklowercase = TRUE;
		else {
			$metastop = strpos($textstring, ">", $metastart)+1;
			$metatag = substr($textstring, $metastart, $metastop-$metastart);
			if (stristr($metatag, "charset=")) $textstring = substr_replace($textstring, "",  $metastart, $metastop-$metastart);
		}
	}
	$checkuppercase = FALSE;
	$metastop = 0;
	while (!$checkuppercase) {
		$metastart = strpos($textstring, "<META", $metastop);
		if ($metastart === FALSE) $checkuppercase = TRUE;
		else {
			$metastop = strpos($textstring, ">", $metastart)+1;
			$metatag = substr($textstring, $metastart, $metastop-$metastart);
			if (stristr($metatag, "charset=")) $textstring = substr_replace($textstring, "",  $metastart, $metastop-$metastart);
		}
	}
	return $textstring;
}

// Parse start and end tags...
function ashop_parsetags($text,$tag,$endtag,$replacement) {
	$timeout = 0;
	while (substr_count($text,$tag) && substr_count($text,$endtag)) {
		if ($timeout > 50) break;
		$start = strpos($text,$tag);
		$end = strpos($text,$endtag)+strlen($endtag);
		$length = $end-$start;
		$text = substr_replace($text,$replacement,$start,$length);
		$timeout++;
	}
	return $text;
}

// Parse affiliate tags for page replication...
function ashop_parseaffiliatetags($text) {
	global $db, $affiliate, $ashopname, $ashopurl, $bgcolor, $pageheader, $shop;
	
	// Parse basic template tags first...
	$text = str_replace("<!-- AShopname -->", $ashopname, $text);
	$text = str_replace("<!-- AShoplogo -->", "<img src=\"$ashopurl/images/logo.gif\" alt=\"$ashopname\" border=\"0\">", $text);
	$text = str_replace("<!-- AShopbgcolor -->", $bgcolor, $text);
	$text = str_replace("<!-- AShopmemberheader -->", $pageheader, $text);
	if ($shop > 1) $$text = str_replace("<!-- AShopcss -->", "includes/ashopcss.inc.php?shop=$shop", $text);
	else if ($shop < 0) $text = str_replace("<!-- AShopcss -->", "../includes/ashopcss.inc.php?shop=$shop", $text);
	else $text = str_replace("<!-- AShopcss -->", "includes/ashopcss.inc.php", $text);
	if ((empty($affiliate) || !is_numeric($affiliate)) && !empty($_COOKIE["affiliate"]) && is_numeric($_COOKIE["affiliate"])){ 
		$affiliate = $_COOKIE["affiliate"];
		echo "bloco 450";
	}
	if (!empty($affiliate) && is_numeric($affiliate) && !empty($db) && is_resource($db)) {
		$result = @mysql_query("SELECT * FROM affiliate WHERE affiliateid='$affiliate'",$db);
		if (@mysql_num_rows($result)) {
			$row = @mysql_fetch_array($result);
			$firstname = $row["firstname"];
			$lastname = $row["lastname"];
			$user = $row["user"];
			$business = $row["business"];
			$email = $row["email"];
			$paypalid = $row["paypalid"];
			$address = $row["address"];
			$state = $row["state"];
			$zip = $row["zip"];
			$city = $row["city"];
			$country = $row["country"];
			$signedup = $row["signedup"];
			$url = $row["url"];
			$phone = $row["phone"];
			$referralcode = $row["referralcode"];
			$extrainfo = $row["extrainfo"];
			$text = ashop_parsetags($text,"<!-- AShop_affiliate_firstname -->","<!-- /AShop_affiliate_firstname -->",$firstname);
			$text = ashop_parsetags($text,"<!-- AShop_affiliate_lastname -->","<!-- /AShop_affiliate_lastname -->",$lastname);
			$text = ashop_parsetags($text,"<!-- AShop_affiliate_user -->","<!-- /AShop_affiliate_user -->",$user);
			$text = ashop_parsetags($text,"<!-- AShop_affiliate_business -->","<!-- /AShop_affiliate_business -->",$business);
			$text = ashop_parsetags($text,"<!-- AShop_affiliate_email -->","<!-- /AShop_affiliate_email -->",$email);
			$text = ashop_parsetags($text,"<!-- AShop_affiliate_paypalid -->","<!-- /AShop_affiliate_paypalid -->",$paypalid);
			$text = ashop_parsetags($text,"<!-- AShop_affiliate_address -->","<!-- /AShop_affiliate_address -->",$address);
			$text = ashop_parsetags($text,"<!-- AShop_affiliate_state -->","<!-- /AShop_affiliate_state -->",$state);
			$text = ashop_parsetags($text,"<!-- AShop_affiliate_zip -->","<!-- /AShop_affiliate_zip -->",$zip);
			$text = ashop_parsetags($text,"<!-- AShop_affiliate_city -->","<!-- /AShop_affiliate_city -->",$city);
			$text = ashop_parsetags($text,"<!-- AShop_affiliate_country -->","<!-- /AShop_affiliate_country -->",$country);
			$text = ashop_parsetags($text,"<!-- AShop_affiliate_signedup -->","<!-- /AShop_affiliate_signedup -->",$signedup);
			$text = ashop_parsetags($text,"<!-- AShop_affiliate_url -->","<!-- /AShop_affiliate_url -->",$url);
			$text = ashop_parsetags($text,"<!-- AShop_affiliate_phone -->","<!-- /AShop_affiliate_phone -->",$phone);
			$text = ashop_parsetags($text,"<!-- AShop_affiliate_referralcode -->","<!-- /AShop_affiliate_referralcode -->",$referralcode);
			$text = ashop_parsetags($text,"<!-- AShop_affiliate_extrainfo -->","<!-- /AShop_affiliate_extrainfo -->",$extrainfo);

			// Parse custom tags...
			$tagresult = @mysql_query("SELECT * FROM affiliatetags",$db);
			if (@mysql_num_rows($tagresult)) while ($tagrow = @mysql_fetch_array($tagresult)) {
				$tagid = $tagrow["affiliatetagid"];
				$starttag = "<!-- AShop_affiliate_".$tagrow["tagname"]." -->";
				$endtag = str_replace("AShop_","/AShop_",$starttag);
				$affinforesult = @mysql_query("SELECT * FROM affiliatetaginfo WHERE affiliateid='$affiliate' AND affiliatetagid='$tagid'",$db);
				if (@mysql_num_rows($affinforesult)) {
					$affinfo = @mysql_result($affinforesult,0,"value");
					$text = ashop_parsetags($text,"$starttag","$endtag",$affinfo);
				}
			}
		}
	} else {
		$text = str_replace("<!-- AShop_affiliate_firstname -->","",$text);
		$text = str_replace("<!-- /AShop_affiliate_firstname -->","",$text);
		$text = str_replace("<!-- AShop_affiliate_lastname -->","",$text);
		$text = str_replace("<!-- /AShop_affiliate_lastname -->","",$text);
		$text = str_replace("<<!-- AShop_affiliate_user -->","",$text);
		$text = str_replace("<!-- /AShop_affiliate_user -->","",$text);
		$text = str_replace("<!-- AShop_affiliate_business -->","",$text);
		$text = str_replace("<!-- /AShop_affiliate_business -->","",$text);
		$text = str_replace("<!-- AShop_affiliate_email -->","",$text);
		$text = str_replace("<!-- /AShop_affiliate_email -->","",$text);
		$text = str_replace("<!-- AShop_affiliate_paypalid -->","",$text);
		$text = str_replace("<!-- /AShop_affiliate_paypalid -->","",$text);
		$text = str_replace("<!-- AShop_affiliate_address -->","",$text);
		$text = str_replace("<!-- /AShop_affiliate_address -->","",$text);
		$text = str_replace("<!-- AShop_affiliate_state -->","",$text);
		$text = str_replace("<!-- /AShop_affiliate_state -->","",$text);
		$text = str_replace("<!-- AShop_affiliate_zip -->","",$text);
		$text = str_replace("<!-- /AShop_affiliate_zip -->","",$text);
		$text = str_replace("<!-- AShop_affiliate_city -->","",$text);
		$text = str_replace("<!-- /AShop_affiliate_city -->","",$text);
		$text = str_replace("<!-- AShop_affiliate_country -->","",$text);
		$text = str_replace("<!-- /AShop_affiliate_country -->","",$text);
		$text = str_replace("<!-- AShop_affiliate_signedup -->","",$text);
		$text = str_replace("<!-- /AShop_affiliate_signedup -->","",$text);
		$text = str_replace("<!-- AShop_affiliate_url -->","",$text);
		$text = str_replace("<!-- /AShop_affiliate_url -->","",$text);
		$text = str_replace("<!-- AShop_affiliate_phone -->","",$text);
		$text = str_replace("<!-- /AShop_affiliate_phone -->","",$text);
		$text = str_replace("<!-- AShop_affiliate_referralcode -->","",$text);
		$text = str_replace("<!-- /AShop_affiliate_referralcode -->","",$text);
		$text = str_replace("<!-- AShop_affiliate_extrainfo -->","",$text);
		$text = str_replace("<!-- /AShop_affiliate_extrainfo -->","",$text);
		$text = str_replace("","",$text);
		$text = str_replace("","",$text);

		// Remove custom tags...
		if (!empty($db) && is_resource($db)) {
			$tagresult = @mysql_query("SELECT * FROM affiliatetags",$db);
			if (@mysql_num_rows($tagresult)) while ($tagrow = @mysql_fetch_array($tagresult)) {
				$tagid = $tagrow["affiliatetagid"];
				$starttag = "<!-- AShop_affiliate_".$tagrow["tagname"]." -->";
				$endtag = str_replace("AShop_","/AShop_",$starttag);
				$text = str_replace($starttag,"",$text);
				$text = str_replace($endtag,"",$text);
			}
		}
	}
	return($text);
}
	
// Show the portion of a template that comes before <!-- AShopstart -->...
function ashop_showtemplateheader($templatepath) {
	global $font, $fontsize1, $fontsize2, $fontsize3, $tablesize1, $tablesize2, $ashopname, $ashoptitle, $ashopmetakeywords, $ashopmetadescription, $shop, $ashopurl, $ashoppath, $bgcolor, $textcolor, $linkcolor, $ashopuser, $pageheader, $returntotoplink, $cmsurl, $basket, $ashopcurrency, $db, $databaseserver, $databaseuser, $lang, $defaultlanguage, $ashopimage, $device;
	if (!$lang) $lang = $defaultlanguage;
	$template = "";
	if ($ashoptitle) $ashoptitle = strip_tags($ashoptitle);
	if ($ashopmetakeywords) $ashopmetakeywords = strip_tags($ashopmetakeywords);
	if ($ashopmetadescription) $ashopmetadescription = strip_tags($ashopmetadescription);
	if ($cmsurl) $template = ashop_helicmstemplate();
	if (file_exists("$templatepath") || $template) {
		if (!$template) {
			$fp = fopen ("$templatepath","r");
			while (!feof ($fp)) $template .= fgets($fp, 4096);
			fclose($fp);
		}
		$templateheader = explode("<!-- AShopstart -->", $template);
		if (count($templateheader) < 2) {
			if (defined("CHARSET")) echo "<html><head><title>$ashopname</title>\n".CHARSET."</head>";
			else echo "<html><head><title>$ashopname</title><link rel=\"stylesheet\" href=\"$ashopurl/includes/ashopcss.inc.php\" type=\"text/css\"></head>";
			echo "<body bgcolor=\"$bgcolor\" text=\"$textcolor\" link=\"$linkcolor\" alink=\"$linkcolor\" vlink=\"$linkcolor\"><center><p><img src=\"$ashopurl/images/logo.gif\"></p><p><font face=\"$font\" size=\"2\" color=\"#900000\"><b>Error! Incorrectly formatted template file!</b></font></p>";
		} else {
			if (strpos($templateheader[0],"<!-- AShopcart -->")) {
				ob_start();
				// Get subtotal...
				$layout = 4;
				$customerlogin = "off";
				$fromtemplate = "true";
				include "includes/topform.inc.php";
				echo "<br><br><div align=\"center\">";
				// Get shopping cart buttons...
				$layout = 5;
				include "includes/topform.inc.php";
				echo "</div>";
				$carthtml = ob_get_contents();
				ob_end_clean();
				$templateheader[0] = str_replace("<!-- AShopcart -->", $carthtml, $templateheader[0]);
			}
			if (strpos($templateheader[0],"<!-- AShopcategories -->")) {
				ob_start();
				$tempdir = getcwd();
				// Get categories...
				$catalog = "index.php";
				$fromtemplate = "true";
				chdir($ashoppath);
				include "includes/categories.inc.php";
				chdir($tempdir);
				$categorieshtml = ob_get_contents();
				ob_end_clean();
				$templateheader[0] = str_replace("<!-- AShopcategories -->", $categorieshtml, $templateheader[0]);
			}
			if (strpos($templateheader[0],"<!-- AShopcustomerlinks -->")) {
				$layout = 6;
				ob_start();
				$tempdir = getcwd();
				chdir($ashoppath);
				include "includes/topform.inc.php";
				chdir($tempdir);
				$resulthtml = ob_get_contents();
				ob_end_clean();
				$templateheader[0] = str_replace("<!-- AShopcustomerlinks -->", $resulthtml, $templateheader[0]);
			}
			if (strpos($templateheader[0],"name=\"cart\"")) $returntotoplink = "cart2";
			else $returntotoplink = "cart";
			if (defined("CHARSET") && !$cmsurl) {
				$templateheader[0] = demetafy($templateheader[0]);
				$templateheader[0] = str_replace("</title>", "</title>\n".CHARSET, $templateheader[0]);
			}
			if ($ashopuser && $ashopuser != "ashopadmin" && file_exists("$ashoppath/members/files/$ashopuser/logo.gif")) $templateheader[0] = str_replace("<!-- AShoplogo -->", "<img src=\"$ashopurl/members/files/$ashopuser/logo.gif\" alt=\"$ashopname\" border=\"0\">", $templateheader[0]);
			else $templateheader[0] = str_replace("<!-- AShoplogo -->", "<img src=\"$ashopurl/images/logo.gif\" alt=\"$ashopname\" border=\"0\">", $templateheader[0]);
			$templateheader[0] = str_replace("<!-- AShopimage -->", $ashopimage, $templateheader[0]);
			$templateheader[0] = str_replace("<!-- AShopbgcolor -->", $bgcolor, $templateheader[0]);
			$templateheader[0] = str_replace("<!-- AShopmemberheader -->", $pageheader, $templateheader[0]);
			$templateheader[0] = str_replace("<!-- AShopname -->", $ashopname, $templateheader[0]);
			$templateheader[0] = str_replace("<!-- AShoptitle -->", $ashoptitle, $templateheader[0]);
			$templateheader[0] = str_replace("<!-- AShopURL -->", $ashopurl, $templateheader[0]);
			$templateheader[0] = str_replace("<!-- AShopmetakeywords -->",$ashopmetakeywords,$templateheader[0]);
			$templateheader[0] = str_replace("<!-- AShopmetadescription -->",$ashopmetadescription,$templateheader[0]);
			if ($shop > 1) {
				$templateheader[0] = str_replace("<!-- AShopcss -->", "includes/ashopcss.inc.php?shop=$shop", $templateheader[0]);
				$templateheader[0] = str_replace("<!-- AShopmember -->", "$shop", $templateheader[0]);
			} else if ($shop < 0) $templateheader[0] = str_replace("<!-- AShopcss -->", "../includes/ashopcss.inc.php?shop=$shop", $templateheader[0]);
			else {
				$templateheader[0] = str_replace("<!-- AShopcss -->", "includes/ashopcss.inc.php", $templateheader[0]);
				$templateheader[0] = str_replace("?shop=<!-- AShopmember -->", "", $templateheader[0]);
				$templateheader[0] = str_replace("&shop=<!-- AShopmember -->", "", $templateheader[0]);
				$templateheader[0] = str_replace("shop=<!-- AShopmember -->", "", $templateheader[0]);
				$templateheader[0] = str_replace("<!-- AShopmember -->", "1", $templateheader[0]);
			}
			$templateheader[0] = ashop_parseaffiliatetags($templateheader[0]);
			echo $templateheader[0];
		}
	} else {
		if (defined("CHARSET")) echo "<html><head><title>$ashopname</title>\n".CHARSET."</head>";
		else echo "<html><head><title>$ashopname</title></head>";
		echo "<body bgcolor=\"$bgcolor\" text=\"$textcolor\" link=\"$linkcolor\" alink=\"$linkcolor\" vlink=\"$linkcolor\"><center><p><img src=\"$ashopurl/images/logo.gif\"></p>";
	}
}

// Show the portion of a template that comes before <!-- AShopstart -->...
function ashop_showtemplateheaderssl($templatepath,$logourl) {
	global $font, $fontsize1, $fontsize2, $fontsize3, $tablesize1, $tablesize2, $ashopname, $ashoptitle, $shop, $ashopurl, $ashopsurl, $ashoppath, $bgcolor, $textcolor, $linkcolor, $ashopuser, $pageheader, $cmsurl;
	$templateheader = "";
	if ($ashoptitle) $ashoptitle = strip_tags($ashoptitle);
	if ($ashopmetakeywords) $ashopmetakeywords = strip_tags($ashopmetakeywords);
	if ($ashopmetadescription) $ashopmetadescription = strip_tags($ashopmetadescription);
	if ($cmsurl) $template = ashop_helicmstemplate();
	if (file_exists("$templatepath") || $template) {
		if (!$template) {
			$fp = fopen ("$templatepath","r");
			while (!feof ($fp)) $template .= fgets($fp, 4096);
			fclose($fp);
		}
		$templateheader = explode("<!-- AShopstart -->", $template);
		if ($logourl) $templateheader[0] = str_replace("\"images/logo.gif", "\"$logourl", $templateheader[0]);
		$templateheader[0] = str_replace("\"$ashopurl/images/logo.gif", "\"$logourl", $templateheader[0]);
		if (count($templateheader) < 2) {
			if (defined("CHARSET")) echo "<html><head><title>$ashopname</title>\n".CHARSET."<link rel=\"stylesheet\" href=\"includes/ashopcss.inc.php\" type=\"text/css\"></head>";
			else echo "<html><head><title>$ashopname</title></head>";
			echo "<body bgcolor=\"$bgcolor\" text=\"$textcolor\" link=\"$linkcolor\" alink=\"$linkcolor\" vlink=\"$linkcolor\"><center>
			<p><img src=\"$logourl\"></p><p><font face=\"$font\" size=\"2\" color=\"#900000\"><b>Error! Incorrectly formatted template file!</b></font></p>";
		} else {
			if (defined("CHARSET") && !$cmsurl) {
				$templateheader[0] = demetafy($templateheader[0]);
				$templateheader[0] = str_replace("</title>", "</title>\n".CHARSET, $templateheader[0]);
			} else str_replace("CHARSET", "", $templateheader[0]);
			if ($ashopuser && $ashopuser != "ashopadmin" && file_exists("$ashoppath/members/files/$ashopuser/logo.gif")) $templateheader[0] = str_replace("<!-- AShoplogo -->", "<img src=\"$ashopsurl/members/files/$ashopuser/logo.gif\" border=\"0\">", $templateheader[0]);
			else {
				if ($logourl) $templateheader[0] = str_replace("<!-- AShoplogo -->", "<img src=\"$logourl\" border=\"0\">", $templateheader[0]);
				else $templateheader[0] = str_replace("<!-- AShoplogo -->", "<img src=\"$ashopsurl/images/logo.gif\" border=\"0\">", $templateheader[0]);
			}
			$templateheader[0] = str_replace("<!-- AShopbgcolor -->", $bgcolor, $templateheader[0]);
			$templateheader[0] = str_replace("<!-- AShopmemberheader -->", $pageheader, $templateheader[0]);
			$templateheader[0] = str_replace("<!-- AShopname -->", $ashopname, $templateheader[0]);
			$templateheader[0] = str_replace("<!-- AShoptitle -->", $ashoptitle, $templateheader[0]);
			$templateheader[0] = str_replace("<!-- AShopURL -->", $ashopsurl, $templateheader[0]);
			$templateheader[0] = str_replace("<!-- AShopmetakeywords -->",$ashopmetakeywords,$templateheader[0]);
			$templateheader[0] = str_replace("<!-- AShopmetadescription -->",$ashopmetadescription,$templateheader[0]);
			if ($shop > 1) {
				$templateheader[0] = str_replace("<!-- AShopcss -->", "includes/ashopcss.inc.php?shop=$shop", $templateheader[0]);
				$templateheader[0] = str_replace("<!-- AShopmember -->", "$shop", $templateheader[0]);
			} else if ($shop < 0) $templateheader[0] = str_replace("<!-- AShopcss -->", "../includes/ashopcss.inc.php?shop=$shop", $templateheader[0]);
			else {
				$templateheader[0] = str_replace("<!-- AShopcss -->", "includes/ashopcss.inc.php", $templateheader[0]);
				$templateheader[0] = str_replace("?shop=<!-- AShopmember -->", "", $templateheader[0]);
				$templateheader[0] = str_replace("&shop=<!-- AShopmember -->", "", $templateheader[0]);
				$templateheader[0] = str_replace("shop=<!-- AShopmember -->", "", $templateheader[0]);
				$templateheader[0] = str_replace("<!-- AShopmember -->", "1", $templateheader[0]);
			}
			echo $templateheader[0];
		}
	} else {
		if (defined("CHARSET")) echo "<html><head><title>$ashopname</title>\n".CHARSET."</head>";
		else echo "<html><head><title>$ashopname</title></head>";
		echo "<body bgcolor=\"$bgcolor\" text=\"$textcolor\" link=\"$linkcolor\" alink=\"$linkcolor\" vlink=\"$linkcolor\"><center><p><img src=\"$logourl\"></p>";
	}
}

// Show the portion of a template that comes after <!-- AShopend -->...
function ashop_showtemplatefooter($templatepath) {
	global $pagefooter, $ashopname, $ashoppath, $cmsurl, $basket, $ashopcurrency, $db;
	$template = "";
	if ($cmsurl) $template = ashop_helicmstemplate();
	if (file_exists("$templatepath") || $template) {
		if (!$template) {
			$fp = fopen ("$templatepath", "r");
			while (!feof ($fp)) $template .= fgets($fp, 4096);
			fclose($fp);
		}
		$templatefooter = explode("<!-- AShopend -->", $template);
		if (count($templatefooter) < 2) echo "<p><font face=\"$font\" size=\"2\" color=\"#900000\"><b>Error! Incorrectly formatted template file!</b></font></p>";
		else {
			if (strpos($templatefooter[1],"<!-- AShopcart -->")) {
				ob_start();
				// Get subtotal...
				$layout = 4;
				$customerlogin = "off";
				$fromtemplate = "true";
				include "includes/topform.inc.php";
				print "<br><br><div align=\"center\">";
				// Get shopping cart buttons...
				$layout = 5;
				include "includes/topform.inc.php";
				print "</div>";
				$carthtml = ob_get_contents();
				ob_end_clean();
				$templatefooter[1] = str_replace("<!-- AShopcart -->", $carthtml, $templatefooter[1]);
			}
			if (strpos($templatefooter[1],"<!-- AShopcategories -->")) {
				ob_start();
				// Get categories...
				$catalog = "index.php";
				$fromtemplate = "true";
				include "includes/categories.inc.php";
				$categorieshtml = ob_get_contents();
				ob_end_clean();
				$templatefooter[1] = str_replace("<!-- AShopcategories -->", $categorieshtml, $templatefooter[1]);
			}
			$templatefooter[1] = str_replace("<!-- AShopmemberfooter -->", $pagefooter, $templatefooter[1]);
			$templatefooter[1] = str_replace("<!-- AShopname -->", $ashopname, $templatefooter[1]);
			echo $templatefooter[1];
		}
	}
	else echo "</body></html>";
}

// Show the portion of a template that comes between <!-- AShopstart --> and <!-- AShopend -->...
function ashop_showtemplatemiddle($templatepath) {
	global $font, $ashopname, $ashopurl;
	$template = "";
	if (file_exists("$templatepath")) {
		$fp = fopen ("$templatepath","r");
		while (!feof ($fp)) $template .= fgets($fp, 4096);
		fclose($fp);
		$templatemiddle = explode("<!-- AShopstart -->", $template);
		if (count($templatemiddle) < 2) $templateerror = 1;
		$templatemiddle = explode("<!-- AShopend -->", $templatemiddle[1]);
		if (count($templatemiddle) < 2) $templateerror = 1;
		$templatemiddle[0] = str_replace("<!-- AShoplogo -->", "<img src=\"$ashopurl/images/logo.gif\" border=\"0\">", $templatemiddle[0]);
		$templatemiddle[0] = str_replace("<!-- AShopname -->", $ashopname, $templatemiddle[0]);
		if ($templateerror) echo "<p><font face=\"$font\" size=\"2\" color=\"#900000\"><b>Error! Incorrectly formatted template file!</b></font></p>";
		else echo $templatemiddle[0];
	}
}

function ashop_ipncheck($gateway_input, $testmode=0) {
	global $ashoppath;
	if ($gateway_input['txn_type'] != "reversal") {
		foreach ($gateway_input as $key => $value) $paypalcheck .= $key . '=' . str_replace("%5C%27", "'", urlencode($value)) . '&';
		$paypalcheck .= "cmd=_notify-validate";
		$paypalcheck = str_replace("%5C%22", "\"", $paypalcheck);
		if (function_exists('curl_version')) {
			$curlversion = curl_version();
			if (strstr($curlversion, "SSL") || (is_array($curlversion) && (strstr($curlversion["ssl_version"], "SSL") || strstr($curlversion["ssl_version"], "NSS")))) {
				$ch = curl_init();
				if (file_exists("$ashoppath/admin/curl.inc.php")) include "$ashoppath/admin/curl.inc.php";
				if ($testmode) curl_setopt($ch, CURLOPT_URL,"https://www.sandbox.paypal.com/cgi-bin/webscr");
				else curl_setopt($ch, CURLOPT_URL,"https://www.paypal.com/cgi-bin/webscr");
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $paypalcheck);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
				$validate[0]=curl_exec ($ch);
				$curlerror = curl_error($ch);
				if ($validate[0] != "VERIFIED") {
					$validate[0] = "INVALID";
					if ($curlerror) {
						$validate[1] = "NOCURL";
						$validate[2] = $curlerror;
					} else $validate[1] = "SUSPECT";
				}
				curl_close ($ch);
			}
		} else {
			$header .= "POST /cgi-bin/webscr HTTP/1.0\r\nContent-Type: application/x-www-form-urlencoded\r\nContent-Length: ".strlen ($paypalcheck)."\r\n\r\n";
			$fp = fsockopen ("www.paypal.com", 80, $errno, $errstr, 30);
			fputs ($fp, $header . $paypalcheck);
			while (!feof($fp)) {
				$res = fgets ($fp, 1024);
				if (strcmp ($res, "VERIFIED") == 0) {
					$validate = TRUE;
				}
				else if (strcmp ($res, "INVALID") == 0) {
					$validate = FALSE;
				}
			}
			fclose ($fp);
		}
		if ($gateway_input['payment_status'] != "Completed") $validate = FALSE;
		return $validate;
	} else return FALSE;
}
?>