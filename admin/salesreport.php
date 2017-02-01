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

@set_time_limit(0);
include "config.inc.php";
include "checklogin.inc.php";
include "template.inc.php";
// Get language module...
include "language/$adminlang/customers.inc.php";
include "ashopfunc.inc.php";
include "ashopconstants.inc.php";

// Convert translated buttons...
if ($generate == "Redigera") $generate = "Edit";
if ($generate == "Visa") $generate = "View";
if ($generate == "Ladda ner") $generate = "Download";

// Convert double quote enclosure for CSV...
if ($defaultenclosure == "&quot;") $defaultenclosure = "\"";

// Get context help for this page...
		$contexthelppage = "salesreport";
		include "help.inc.php";

// Open database...
$db = @mysql_connect("$databaseserver", "$databaseuser", "$databasepasswd");
@mysql_select_db("$databasename",$db);

// Show report options...
if (!$generate) {
	
	// Get the oldest and newest order...
	$maxdate = date("Y-m-d H:i:s", time()+$timezoneoffset);
	$sql = "SELECT date FROM orders WHERE date != '' ORDER BY date LIMIT 1";
	$result = @mysql_query("$sql",$db);
	$mindate = @mysql_result($result, 0, "date");
	$sql = "SELECT date FROM orders WHERE date != '' AND wholesale='1' ORDER BY date LIMIT 1";
	$result = @mysql_query("$sql",$db);
	$wsmindate = @mysql_result($result, 0, "date");
	if($wsmindate && $wsmindate < $mindate) $mindate = $wsmindate;
	$oldestarray = explode("-", $mindate);
	$oldest = $oldestarray[0];
	$newestarray = explode("-", $maxdate);
	$newest = $newestarray[0];
	$fromyears = "";
	$toyears = "";
	for ($thisyear = $oldest; $thisyear<=$newest; $thisyear++) {
		$toyears .= "<option value=\"$thisyear\"";
		if ($thisyear==$newest) $toyears.= " selected";
		$toyears .= ">$thisyear</option>";
		$fromyears .= "<option value=\"$thisyear\">$thisyear</option>";
	}

	// Get the current month and day...
	$currentmonth = date("m", time()+$timezoneoffset);
	$currentday = date("d", time()+$timezoneoffset);

	echo "$header
	";
	if ($msg == "deleted") echo "<p align=\"center\" class=\"confirm\">".ORDERDELETED."</p>";
	echo "<div class=\"heading\">".SALESREPORT;
	echo " <a href=\"$help1\" target=\"_blank\"><img src=\"images/icon_helpsm.gif\" width=\"15\" height=\"15\" border=\"0\"></a>
	</div><center><form action=\"salesreport.php\" method=\"post\" name=\"salesreportform\"><table width=\"600\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr class=\"formtitle\"><td align=\"center\"><p>".TRANSACTIONTYPE.": <select name=\"transtype\">
	<option value=\"retail\">".RETAIL."</option><option value=\"wholesale\">".WHOLESALE."</option><option value=\"all\">".ALL."</option>
	</select> ".PAYMENTSTATUS.": <select name=\"reporttype\">
	<option value=\"paid\" selected>".PAID."</option><option value=\"unpaid\">".UNPAID."</option><option value=\"chargebacks\">".CHARGEBACKS."</option><option value=\"all\">".ALL."</option>
	</select></p><p>".FROM.":   
	
	<select name=\"startyear\">$fromyears</select>
	
	<select name=\"startmonth\"><option value=\"01\" selected>".JAN."</option><option value=\"02\">".FEB."</option><option value=\"03\">".MAR."</option><option value=\"04\">".APR."</option><option value=\"05\">".MAY."</option><option value=\"06\">".JUN."</option><option value=\"07\">".JUL."</option><option value=\"08\">".AUG."</option><option value=\"09\">".SEP."</option><option value=\"10\">".OCT."</option><option value=\"11\">".NOV."</option><option value=\"12\">".DEC."</option></select>

	<select name=\"startday\"><option value=\"01\" selected>1</option>";
	
	for ($i = 2; $i < 32; $i++) {
		echo "<option value=\"";
		if ($i < 10) echo "0";
		echo "$i\">$i</option>";
	}
    echo "</select>

	&nbsp;To:   
	
	<select name=\"toyear\">$toyears</select>
	
	<select name=\"tomonth\">";

	echo "<option value=\"01\""; if ($currentmonth == 1) echo "selected"; echo">".JAN."</option>";
	echo "<option value=\"02\""; if ($currentmonth == 2) echo "selected"; echo">".FEB."</option>";
	echo "<option value=\"03\""; if ($currentmonth == 3) echo "selected"; echo">".MAR."</option>";
	echo "<option value=\"04\""; if ($currentmonth == 4) echo "selected"; echo">".APR."</option>";
	echo "<option value=\"05\""; if ($currentmonth == 5) echo "selected"; echo">".MAY."</option>";
	echo "<option value=\"06\""; if ($currentmonth == 6) echo "selected"; echo">".JUN."</option>";
	echo "<option value=\"07\""; if ($currentmonth == 7) echo "selected"; echo">".JUL."</option>";
	echo "<option value=\"08\""; if ($currentmonth == 8) echo "selected"; echo">".AUG."</option>";
	echo "<option value=\"09\""; if ($currentmonth == 9) echo "selected"; echo">".SEP."</option>";
	echo "<option value=\"10\""; if ($currentmonth == 10) echo "selected"; echo">".OCT."</option>";
	echo "<option value=\"11\""; if ($currentmonth == 11) echo "selected"; echo">".NOV."</option>";
	echo "<option value=\"12\""; if ($currentmonth == 12) echo "selected"; echo">".DEC."</option>";

    echo "</select><select name=\"today\">";
	
	for ($i = 1; $i < 32; $i++) {
		echo "<option value=\"";
		if ($i < 10) echo "0";
		echo "$i\"";
		if ($i == $currentday) echo " selected";
		echo ">$i</option>";
	}
    echo "</select>	
	
    </p><p>".ORDERBY.": <select name=\"orderby\"><option value=\"date\" selected>".THEWORDDATE."</option><option value=\"price\">".AMOUNT."</option><option value=\"customerid\">".CUSTOMERID."</option><option value=\"orderid\">".ORDERID."</option><option value=\"affiliate\">".AFFILIATE."</option>";
	echo "</select> <select name=\"ascdesc\"><option value=\"asc\" selected>".ASCENDING."</option><option value=\"desc\">".DESCENDING."</option></select>
	</p><p><input type=\"submit\" name=\"generate\" value=\"".VIEW."\"> <input type=\"submit\" name=\"generate\" value=\"".DOWNLOAD."\">
	</p></td></tr></table></form>
	</center>$footer";

// Generate downloadable reports...
} else if ($generate == "Download") {
	$ordertable = "orders";
	$customertable = "customer";
	$customeridfield = "customerid";
	$paidcheck = " AND paid != ''";
	if ($transtype == "wholesale") $ordertypesql = " AND wholesale = '1'";
	else if ($transtype == "auction") $ordertypesql = " AND source = 'Auction'";
	else if ($transtype == "retail") $ordertypesql = " AND (wholesale IS NULL OR wholesale != '1')";
	//$usercheck = "LIKE '%|$user|%'";
	$startdate = "$startyear-$startmonth-$startday 00:00:00";
	$todate = "$toyear-$tomonth-$today 23:59:59";
	if ($reporttype == "chargebacks") {
		$filename = "chargebacks";
		$orderafftable = "orderaffiliate";
	} else if ($reporttype == "unpaid") {
		$filename = "unpaid";
		$orderafftable = "pendingorderaff";
	} else if ($reporttype == "paid") {
		$filename = "sales";
		$orderafftable = "orderaffiliate";
	} else $filename = "transactions";
	header ("Content-Type: application/octet-stream");
	header ("Content-Disposition: attachment; filename=$filename.csv");
	echo ORDERID2."{$defaultdelimiter}".TRANSID."{$defaultdelimiter}".THEWORDDATE."{$defaultdelimiter}".THEWORDTIME."{$defaultdelimiter}".PRODUCTS."{$defaultdelimiter}".SUBTOTAL."{$defaultdelimiter}".CUSTOMERID."{$defaultdelimiter}".FIRSTNAME."{$defaultdelimiter}".LASTNAME."{$defaultdelimiter}".EMAIL."{$defaultdelimiter}".IP."{$defaultdelimiter}";
	if ($reporttype == "chargebacks") echo COMMENT."\r\n";
	else echo REFERRED."\r\n";

	// Downloadable report...
	if ($reporttype == "paid") $paidstring = "paid != ''";
	else if ($reporttype == "unpaid") $paidstring = "paid = ''";
	$sql="SELECT * FROM $ordertable WHERE";
	if ($transtype != "wholesale" && $user > 1) $sql .= " userid $usercheck AND";
	$sql .= " date >= '$startdate' AND date <= '$todate'";
	if ($ordertypesql) $sql .= $ordertypesql;
	if ($paidcheck && $paidstring) $sql .= " AND $paidstring";
	if ($reporttype == "chargebacks") $sql .= " AND reference != '' AND reference IS NOT NULL AND price < 0";
	$sql .= " ORDER BY $orderby $ascdesc";
	$result = @mysql_query("$sql",$db);
	for ($i = 0; $i < @mysql_num_rows($result); $i++) {
		$orderid = @mysql_result($result, $i, "orderid");
		$transactionid = @mysql_result($result, $i, "invoice");
		$price = @mysql_result($result, $i, "price");
		$wholesale = @mysql_result($result, $i, "wholesale");
		$paid = @mysql_result($result, $i, "paid");
		$reference = @mysql_result($result, $i, "reference");
		$subtotal += $price;
		$timedate = explode(" ", @mysql_result($result, $i, "date"));
		$date = $timedate[0];
		$time = explode(":",$timedate[1]);
		$descriptionstring = (@mysql_result($result, $i, "product"));
		$descriptionstring = un_html($descriptionstring);
		if ($defaultenclosure == "\"" && strstr($descriptionstring,"\"")) $descriptionstring = str_replace("\"","\"\"",$descriptionstring);
		if ($reference && $price < 0) $descriptionstring = CHARGEBACKFOR." $reference, $descriptionstring";
		$comment = @mysql_result($result, $i, "comment");
		$thiscustomerid = @mysql_result($result, $i, "$customeridfield");
		$sql = "SELECT * FROM $customertable WHERE $customeridfield=$thiscustomerid";
		$result3 = @mysql_query("$sql", $db);
		$customerfirstname = trim(@mysql_result($result3, 0, "firstname"));
		$customerlastname = trim(@mysql_result($result3, 0, "lastname"));
		$customeremail = trim(@mysql_result($result3, 0, "email"));
		$ipnumber = @mysql_result($result3, 0, "ip");
		$thiscustomerid = @mysql_result($result3, 0, "remotecustomerid");
		$sql = "SELECT affiliate.affiliateid, affiliate.firstname, affiliate.lastname, affiliate.email FROM $orderafftable, affiliate WHERE $orderafftable.orderid=$orderid AND $orderafftable.affiliateid=affiliate.affiliateid AND ($orderafftable.secondtier=0 OR $orderafftable.secondtier IS NULL)";
		$result4 = @mysql_query("$sql", $db);
		$affiliatename = @mysql_result($result4, 0, "firstname")." ".@mysql_result($result4, 0, "lastname");
		$affiliatename = trim($affiliatename);
		$affiliateemail = trim(@mysql_result($result4, 0, "email"));
		$affiliateid = @mysql_result($result4, 0, "affiliateid");
		if ($wholesale) $ws = " W";
		else if (!$paid && $reporttype != "unpaid") $ws = " U";
		else $ws = "";
		echo "$orderid$ws{$defaultdelimiter}{$defaultenclosure}$transactionid{$defaultenclosure}{$defaultdelimiter}{$defaultenclosure}$date{$defaultenclosure}{$defaultdelimiter}{$defaultenclosure}$time[0]:$time[1]{$defaultenclosure}{$defaultdelimiter}{$defaultenclosure}$descriptionstring{$defaultenclosure}{$defaultdelimiter}{$defaultenclosure}".number_format($price,2,$decimalchar,$thousandchar)."{$defaultenclosure}{$defaultdelimiter}{$defaultenclosure}$thiscustomerid{$defaultenclosure}{$defaultdelimiter}{$defaultenclosure}$customerfirstname{$defaultenclosure}{$defaultdelimiter}{$defaultenclosure}$customerlastname{$defaultenclosure}{$defaultdelimiter}{$defaultenclosure}$customeremail{$defaultenclosure}{$defaultdelimiter}{$defaultenclosure}$ipnumber{$defaultenclosure}{$defaultdelimiter}{$defaultenclosure}";
		if ($reporttype != "chargebacks" && $affiliateid) echo "$affiliateid: $affiliatename";
		else echo "$comment";
		echo "{$defaultenclosure}\r\n";
	}

// Show "Please wait" page while completing the search...
} else if (!$showresult) {
	echo "$header<div class=\"heading\">".GENERATINGREPORT."</div>";
	foreach ($_POST as $field => $value) $getquerystring .= "&$field=$value";
	foreach ($_GET as $field => $value) $getquerystring .= "&$field=$value";
	echo "<meta http-equiv=\"Refresh\" content=\"0; URL=salesreport.php?showresult=true$getquerystring\"></table></center>$footer";
	exit;
}


// Show report in browser...	
else {
	ob_start();
	if (!isset($reporttype)) $reporttype = "paid";
	if ($reporttype == "paid") $paidcheck = " AND paid != ''";
	else if ($reporttype == "unpaid") $paidcheck = " AND paid = ''";
	if ($transtype == "wholesale") $ordertypesql = " AND wholesale = '1'";
	else if ($transtype == "retail") $ordertypesql = " AND (wholesale IS NULL OR wholesale != '1')";
	if ($customerid) {
		$sql="SELECT * FROM customer WHERE customerid='$customerid'";
		$result = @mysql_query("$sql",$db);
		$customername = @mysql_result($result, 0, "firstname")." ".@mysql_result($result, 0, "lastname");
		$customeremail = @mysql_result($result, 0, "email");
		$customerstring = " ".THEWORDFOR." $customername, ".CUSTOMERID." $customerid ";
	}
	$usercheck = "LIKE '%|$user|%'";
	if ($reporttype == "paid" || $reporttype == "chargebacks") $orderafftable = "orderaffiliate";
	else $orderafftable = "pendingorderaff";
	echo "$header
	<div class=\"heading\">".SALESREPORT;
	if ($userid == "1") echo " <a href=\"$help1\" target=\"_blank\"><img src=\"images/icon_helpsm.gif\" width=\"15\" height=\"15\" border=\"0\"></a>";
	echo "</div><center>";
	if ($customerstring) {
		if ($reporttype == "wholesale") $editcustomer = "edituser";
		else $editcustomer = "editcustomer";
		echo "<span class=\"heading\"><font size=\"2\">$customerstring <a href=\"editcustomer.php?customerid=$customerid\"><img src=\"images/icon_profile.gif\" alt=\"".PROFILEFOR." $customerid\" title=\"".PROFILEFOR." $customerid\" border=\"0\"></a>";
		if ($userid == "1") echo "&nbsp;<a href=\"editcustomer.php?customerid=$customerid&remove=True\"><img src=\"images/icon_trash.gif\" alt=\"".DELETECUSTOMER." $customerid ".FROMDB."\" title=\"".DELETECUSTOMER." $customerid ".FROMDB."\" border=\"0\"></a>";
		echo "</font></span><br>";
	} else if ($memberstring) {
		echo "<span class=\"heading\"><font size=\"2\">$memberstring <a href=\"editmember.php?memberid=$user\"><img src=\"images/icon_profile.gif\" alt=\"".MEMBERPROFILEFOR." $user\" title=\"".MEMBERPROFILEFOR." $user\" border=\"0\"></a>";
		if ($userid == "1") echo "&nbsp;<a href=\"editmember.php?memberid=$user&remove=True\"><img src=\"images/icon_trash.gif\" alt=\"".DELETEMEMBER." $user ".FROMDB."\" title=\"".DELETEMEMBER." $user ".FROMDB."\" border=\"0\"></a>";
		echo "</font></span><br>";
	}
	if ($msg == "deleted") echo "<p align=\"center\" class=\"confirm\">".ORDERDELETED."</p>";
	else if ($msg == "activated") echo "<p align=\"center\" class=\"confirm\">".ORDERACTIVATIONSENT."</p>";
	if ($msg == "remindersent") echo "<p align=\"center\" class=\"confirm\">".REMINDERSENT."</p>";
	else echo "<br>";
	echo "<span class=\"formtitle\">";
	if ($transtype == "wholesale") $ordertoptext = WHOLESALE." ";
	else $ordertoptext = "";
	if ($transtype == "auction") $ordertoptext2 = AUCTIONS;
	else $ordertoptext2 = ORDERS;
	if ($reporttype == "paid") echo PAID." {$ordertoptext}".$ordertoptext2;
	else if ($reporttype == "unpaid") echo UNPAID." {$ordertoptext}".$ordertoptext2;
	else if ($reporttype == "chargebacks") echo "{$ordertoptext}".CHARGEBACKS;
	else echo "{$ordertoptext}".TRANSACTIONS;
	$startdate = "$startyear-$startmonth-$startday 00:00:00";
	$todate = "$toyear-$tomonth-$today 23:59:59";
	$subtotal = 0;
	if (!$customerid && !$orderid && $memberid <= "1") echo " - ".SMALLFROM." $startdate ".TO." $todate, ".ORDEREDBY,": ";
	if ($orderby == "date") echo THEWORDDATE;
	else if ($orderby == "price") echo AMOUNT;
	else if ($orderby == "customerid") echo CUSTOMERID;
	else if ($orderby == "orderid") echo ORDERID;
	echo "</span> 
	<table width=\"800\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\" align=\"center\" bgcolor=\"#C0C0C0\">
	<tr class=\"reporthead\"><td nowrap align=\"left\">".THEWORDDATE."</td>
	<td nowrap align=\"left\">".GATEWAYORDERID."</td>
	<td align=\"left\">Products</td><td align=\"center\">".AMT."</td>";
	if (!$customerid) echo "<td align=\"left\">".CUSTOMER."</td>";
	echo "<td align=\"center\" nowrap>".REFERRED."</td><td>&nbsp;</td></tr>";

	// Get order information from database...
	if ($customerid) {
		$sql="SELECT * FROM orders WHERE customerid='$customerid'";
		if ($transtype != "wholesale" && $user > 1) $sql .= " AND userid $usercheck";
		if ($ordertypesql) $sql .= $ordertypesql;
		if ($paidcheck) $sql .= $paidcheck;
		$sql .= " AND date != '' ORDER BY date";
	} else if ($orderid) {
		$sql="SELECT * FROM orders WHERE orderid='$orderid'";
		if ($paidcheck) $sql .= $paidcheck;
	}
	else if ($orderby == "affiliate") {
		if ($reporttype == "paid") $paidstring = "orders.paid != ''";
		else if ($reporttype == "unpaid") $paidstring = "orders.paid = ''";
		$sql="SELECT * FROM orders, $orderafftable, affiliate WHERE orders.orderid=$orderafftable.orderid AND $orderafftable.affiliateid=affiliate.affiliateid AND";
		if ($transtype != "wholesale" && $user > 1) $sql .= " orders.userid $usercheck AND";
		$sql .= " orders.date >= '$startdate' AND orders.date <= '$todate'";
		if ($paidcheck && $paidstring) $sql .= " AND $paidstring";
		if ($reporttype == "chargebacks") $sql .= " AND orders.reference != '' AND orders.reference IS NOT NULL AND orders.price < 0";
		if ($ordertypesql) $sql .= $ordertypesql;
		$sql .= " ORDER BY affiliate.lastname, orders.date, orders.orderid $ascdesc";
	}
	else {
		if ($reporttype == "paid") $paidstring = "paid != ''";
		else if ($reporttype == "unpaid") $paidstring = "paid = ''";
		$sql="SELECT * FROM orders WHERE";
		if ($transtype != "wholesale" && $user > 1) $sql .= " userid $usercheck AND";
		$sql .= " date >= '$startdate' AND date <= '$todate'";
		if ($paidcheck && $paidstring) $sql .= " AND $paidstring";
		if ($reporttype == "chargebacks") $sql .= " AND reference != '' AND reference IS NOT NULL AND price < 0";
		if ($ordertypesql && $userid == "1") $sql .= $ordertypesql;
		if ($userid > "1") {
			if ($transtype == "auction") $sql .= " AND auction='1'";
			else $sql .= " AND (auction='0' OR auction IS NULL)";
		}
		$sql .= " ORDER BY $orderby $ascdesc";
	}
	$result = @mysql_query("$sql",$db);
	$rowcolor = "#E0E0E0";
	$affiliatecommission = 0.00;
	$paidaffiliatecommission = 0.00;
	for ($i = 0; $i < @mysql_num_rows($result); $i++) {
		$orderid = @mysql_result($result, $i, "orderid");
		$invoice = @mysql_result($result, $i, "invoice");
		$paid = @mysql_result($result, $i, "paid");
		$reference = @mysql_result($result, $i, "reference");
		if ($paid && !$reference) $ordertype = "paid";
		else if (!$reference) $ordertype = "unpaid";
		else $ordertype = "chargebacks";
		$price = @mysql_result($result, $i, "price");
		$wholesale = @mysql_result($result, $i, "wholesale");
		$checkchargebackresult = @mysql_query("SELECT * FROM orders WHERE reference='$orderid' AND price < 0", $db);
		if (@mysql_num_rows($checkchargebackresult)) $checkchargeback = TRUE;
		else $checkchargeback = FALSE;
		$totalamt += $price;
		$subtotal += $price;
		$timedate = explode(" ", @mysql_result($result, $i, "date"));
		$date = $timedate[0];
		$time = explode(":",$timedate[1]);
		$descriptionstring = @mysql_result($result, $i, "product");
		if ($reference) {
			$comment = str_replace("\r\n", " ", @mysql_result($result, $i, "comment"));
			$comment = str_replace("\n", " ", $comment);
			$comment = str_replace("\r", " ", $comment);
			$comment = str_replace("'", "&#039;", $comment);
			if ($comment) $descriptionstring = "<a href=\"javascript: void(0)\" onMouseOver=\"window.status='$comment'; return true;\" onMouseOut=\"window.status=window.defaultStatus;\"><img src=\"images/icon_info.gif\" alt=\"$comment\" title=\"$comment\" border=\"0\"></a> <font color=\"#FF0000\">".CHARGEBACKFOR." $reference</font><br>$descriptionstring";
			else $descriptionstring = "<font color=\"#FF0000\">".CHARGEBACKFOR." $reference</font><br>$descriptionstring";
		}
		$displaydescr = $descriptionstring;
		$thiscustomerid = @mysql_result($result, $i, "customerid");
		$sql = "SELECT * FROM customer WHERE customerid='$thiscustomerid'";
		$result3 = @mysql_query("$sql", $db);
		$customername = @mysql_result($result3, 0, "firstname")." ".@mysql_result($result3, 0, "lastname");
		$customeremail = @mysql_result($result3, 0, "email");
		$sql = "SELECT affiliate.affiliateid, affiliate.firstname, affiliate.lastname, affiliate.email, $orderafftable.commission";
		if ($reporttype != "unpaid") $sql .= ", $orderafftable.paid";
		$sql .= " FROM $orderafftable, affiliate WHERE $orderafftable.orderid='$orderid' AND $orderafftable.affiliateid=affiliate.affiliateid AND ($orderafftable.secondtier=0 OR $orderafftable.secondtier IS NULL)";
		$result4 = @mysql_query("$sql", $db);
		$affiliatename = @mysql_result($result4, 0, "firstname")." ".@mysql_result($result4, 0, "lastname");
		$affiliateemail = @mysql_result($result4, 0, "email");
		$affiliateid = @mysql_result($result4, 0, "affiliateid");
		$affiliatecommission += @mysql_result($result4, 0, "commission");
		if (@mysql_result($result4, 0, "paid")) $paidaffiliatecommission += @mysql_result($result4, 0, "commission");
		echo "<tr class=\"reportlinesm\" valign=\"top\" bgcolor=\"$rowcolor\"><td nowrap align=\"left\">$date<br>{$time[0]}:{$time[1]}</td><td align=\"left\">";
		if ($wholesale) {
			$ws = "&nbsp;".WHOLESALELETTER;
			if ($purchaseorder) $ws .= "</a> <a href=\"javascript: void(0)\" onMouseOver=\"window.status='$purchaseorder'; return true;\" onMouseOut=\"window.status=window.defaultStatus;\"><img src=\"images/icon_info.gif\" alt=\"$purchaseorder\" title=\"$purchaseorder\" border=\"0\">";
		} else $ws = "";
		echo "$invoice$ws";
		echo "</td><td align=\"left\">$displaydescr</td><td align=\"right\">";
		if ($price < 0) echo "<font color=\"#FF0000\">";
		echo number_format($price,2,$decimalchar,$thousandchar);
		if ($price < 0) echo "</font>";
		echo "</td>";
		if (!$customerid) echo "<td nowrap align=\"left\">$thiscustomerid: <a href=\"editcustomer.php?customerid=$thiscustomerid\">$customername</a></td>";
		if ($affiliateid) echo "
		<td align=\"center\">$affiliateid: <a href=\"affiliatedetail.php?affiliateid=$affiliateid\">$affiliatename</td>";
		else echo "<td>&nbsp;</td>";
		echo "<td align=\"center\">";
		if ($ordertype == "paid" && !$reference) echo "<a href=\"editsales.php?orderid=$orderid&action=chargeback&salesreport=$reporttype|$startyear|$startmonth|$startday|$toyear|$tomonth|$today|$orderby|$ascdesc|$generate\"><img src=\"images/icon_chargeback.gif\" alt=\"".CHARGEBACKORDER."\" title=\"".CHARGEBACKORDER."\" border=\"0\"></a> <a href=\"editsales.php?action=delete&orderid=$orderid&salesreport=$reporttype|$startyear|$startmonth|$startday|$toyear|$tomonth|$today|$orderby|$ascdesc|$generate\"><img src=\"images/icon_trash.gif\" alt=\"".DELETEORDER."\" title=\"".DELETEORDER."\" border=\"0\"></a>";
		else if ($ordertype == "unpaid") {
			echo "<a href=\"activate.php?orderid=$orderid&salesreport=$reporttype|$startyear|$startmonth|$startday|$toyear|$tomonth|$today|$orderby|$ascdesc|$generate\"><img src=\"images/icon_activatem.gif\" alt=\"".RECORDPAYMENTANDACTIVATE."\" title=\"".RECORDPAYMENTANDACTIVATE."\" border=\"0\"></a> <a href=\"editsales.php?action=delete&orderid=$orderid&salesreport=$reporttype|$startyear|$startmonth|$startday|$toyear|$tomonth|$today|$orderby|$ascdesc|$generate\"><img src=\"images/icon_trash.gif\" alt=\"".DELETEORDER."\" title=\"".DELETEORDER."\" border=\"0\"></a>";
		} 
		echo "</td></tr>\n";
		if ($rowcolor == "#C0C0C0") $rowcolor = "#E0E0E0";
		else $rowcolor = "#C0C0C0";
	}

	echo "<tr class=\"reportheadsm\"><td colspan=\"3\" align=\"right\">".TOTALS.":</td>
	<td align=\"right\">&nbsp;".$currencysymbols[$ashopcurrency]["pre"].number_format($totalamt,2,$decimalchar,$thousandchar)." ".$currencysymbols[$ashopcurrency]["post"]."</td>";
	if (!$customerid) echo "<td>&nbsp;</td>";
	echo "<td>&nbsp;</td><td>&nbsp;</td></tr></table><br>
	<font face=\"Arial, Helvetica, sans-serif\" size=\"2\">".AFFILIATECOMMISSION.": <b>".$currencysymbols[$ashopcurrency]["pre"].number_format($affiliatecommission,2,$decimalchar,$thousandchar).$currencysymbols[$ashopcurrency]["post"]."</b>, ".PAIDSMALL.": <b>".$currencysymbols[$ashopcurrency]["pre"].number_format($paidaffiliatecommission,2,$decimalchar,$thousandchar).$currencysymbols[$ashopcurrency]["post"]."</b></font><br>
	<br></center>$footer";

}

ob_end_flush();
?>