<?php
// Fix incompatible php settings...
if (ini_get("register_globals") != 1 || !get_magic_quotes_gpc()) include "vars.inc.php";

echo "<html><head><title>Select a font...</title></head>
<script language=\"JavaScript\">
function setfont(formname,fieldname,font) 
{
	eval('opener.document.'+formname+'.'+fieldname+'.value = \"'+font+'\"');
	this.close();
}
</script>
<table width=\"340\" cellpadding=\"0\" cellspacing=\"0\" border=\"1\" bordercolor=\"#000000\">
<tr><td>
<table width=\"100%\" cellpadding=\"2\" cellspacing=\"0\" border=\"0\">
<tr><td><font face=\"Arial, Helvetica, sans-serif\" size=\"2\">Arial, Helvetica, sans-serif</font></td><td><input type=\"button\" value=\"Select\" onClick=\"setfont('$form','$field','Arial, Helvetica, sans-serif')\"></td></tr>
<tr><td><font face=\"Times New Roman, Times, serif\" size=\"2\">Times New Roman, Times, serif</font></td><td><input type=\"button\" value=\"Select\" onClick=\"setfont('$form','$field','Times New Roman, Times, serif')\"></td></tr>
<tr><td><font face=\"Courier New, Courier, mono\" size=\"2\">Courier New, Courier, mono</font></td><td><input type=\"button\" value=\"Select\" onClick=\"setfont('$form','$field','Courier New, Courier, mono')\"></td></tr>
<tr><td><font face=\"Georgia, Times New Roman, Times, serif\" size=\"2\">Georgia, Times New Roman, Times, serif</font></td><td><input type=\"button\" value=\"Select\" onClick=\"setfont('$form','$field','Georgia, Times New Roman, Times, serif')\"></td></tr>
<tr><td><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"2\">Verdana, Arial, Helvetica, sans-serif</font></td><td><input type=\"button\" value=\"Select\" onClick=\"setfont('$form','$field','Verdana, Arial, Helvetica, sans-serif')\"></td></tr>
<tr><td><font face=\"Geneva, Arial, Helvetica, sans-serif\" size=\"2\">Geneva, Arial, Helvetica, sans-serif</font></td><td><input type=\"button\" value=\"Select\" onClick=\"setfont('$form','$field','Geneva, Arial, Helvetica, sans-serif')\"></td></tr>
</table></td></tr></table></html>";
?>