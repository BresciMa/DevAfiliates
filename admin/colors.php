<?php
// Fix incompatible php settings...
if (ini_get("register_globals") != 1 || !get_magic_quotes_gpc()) include "vars.inc.php";

echo "<html><head><title>Pick a color...</title></head>
<script language=\"JavaScript\">
function setcolor(formname,fieldname,color) 
{
	eval('opener.document.'+formname+'.'+fieldname+'.value = \"#'+color+'\"');
	this.close();
}
</script>
<table width=\"360\" cellpadding=\"0\" cellspacing=\"0\" border=\"1\" bordercolor=\"#000000\">";
echo "<tr>";
$webcolors[] = "000000";
echo "<td bgcolor=\"#000000\"><a href=\"javascript:setcolor('$form','$field','000000')\"><img src=\"images/spacer.gif\" width=\"8\" height=\"8\"></a></td>";
for ($c1 = 0; $c1 <= 5; $c1++) {
	$hexcolor1 = dechex($c1*51);
	if (!$hexcolor1) $hexcolor1 = "00";
	for ($c2 = 0; $c2 <= 5; $c2++) {
		$hexcolor2 = dechex($c2*51);
		if (!$hexcolor2) $hexcolor2 = "00";
		for ($c3 = 0; $c3 <= 5; $c3++) {
			$hexcolor3 = dechex($c3*51);
			if (!$hexcolor3) $hexcolor3 = "00";
			$colorstring = strtoupper("$hexcolor1$hexcolor2$hexcolor3");
			if (!in_array($colorstring,$webcolors)) {
				$webcolors[] = $colorstring;
				echo "<td bgcolor=\"#$colorstring\"><a href=\"javascript:setcolor('$form','$field','$colorstring')\"><img src=\"images/spacer.gif\" width=\"10\" height=\"10\" border=\"0\"></a></td>";
			}
		}
	}
	echo "</tr>";
}
echo "</table></html>";
?>