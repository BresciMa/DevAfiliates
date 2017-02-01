<?php header('Content-type: text/css'); ?>

@charset "utf-8";
/* CSS Document */
<?php
if (!$databaseserver || !$databaseuser) include "../admin/config.inc.php";

// Apply selected theme...
if ($ashoptheme && $ashoptheme != "none" && file_exists("$ashoppath/themes/$ashoptheme/theme.cfg.php")) include "../themes/$ashoptheme/theme.cfg.php";

echo "
.ashopcategoriestable { width: 100%; border-style: none; border-bottom: 1px solid $catalogheader; text-align: left; }

.ashopselectedcategory { background-color: $selectedcategory; font-family: $font; font-size: {$fontsize2}px; color: $selectedcategorytext; font-weight: bold; }

.ashopcategory { background-color: $categorycolor; font-family: $font; font-size: {$fontsize2}px; color: $categorytextcolor; }

td.ashopcategory, td.ashopselectedcategory {
	border: 1px solid $catalogheader;
	border-top: none;
	border-bottom: none;
}

.ashopmessagetable { padding: 0px; width: 75%; border-style: none; }

.ashopmessageheader { font-family: $font; font-size: {$fontsize3}px; color: $textcolor; font-weight: bold; }

.ashopmessage { font-family: $font; font-size: {$fontsize3}px; color: $textcolor; }

.ashopaffiliatebutton { width: 100px; }

.ashopaffiliatebuttonlarge { width: 150px; }

.ashopaffiliatebuttonsmall { width: 82px; }

.ashopaffiliateloginframe { padding: 2px; width: 430px; border: none; }

.ashopaffiliatesignupframe { padding: 2px; width: {$tablesize2}px; border-style: none; }

.ashopaffiliatesignupbox { background-color: $formsbgcolor; padding: 2px; width: {$tablesize2}px; border-style: solid; border-width: 1px; border-color: $formsbordercolor; }

.ashopaffiliatecodeframe { padding: 2px; width: 730px; border-style: none; }

.ashopaffiliatecategoriesbox { width: 200px; border-style: none; vertical-align: top; text-align: left; }

.ashopaffiliatecategoriestable { width: 100%; border: 1px solid $catalogheader; }

.ashopaffiliatecategoriesheader { background-color: $catalogheader; padding: 5px; font-family: $font; font-size: {$fontsize2}px; color: $catalogheadertext; font-weight: bold; }

.ashopaffiliateselectedcategory { background-color: $selectedcategory; font-family: $font; font-size: {$fontsize2}px; color: $selectedcategorytext; font-weight: bold; }

.ashopaffiliatecategory { background-color: $categorycolor; font-family: $font; font-size: {$fontsize2}px; color: $categorytextcolor; }

.ashopaffiliatecodebox { background-color: #D0D0D0; padding: 5px; width: 530px; border-style: none; }

.ashopaffiliateheader { font-family: $font; font-size: {$fontsize3}px; color: $textcolor; font-weight: bold; }

.ashopaffiliatetext1 { font-family: $font; font-size: {$fontsize2}px; color: $textcolor; font-weight: bold; }

.ashopaffiliatetext2 { font-family: $font; font-size: {$fontsize2}px; color: $textcolor; }

.ashopaffiliatetext3 { font-family: $font; font-size: {$fontsize2}px; color: $formstextcolor; }

.ashopaffiliatefield { text-align: left; }

.ashopaffiliatenotice { font-family: $font; font-size: {$fontsize1}px; color: $formstextcolor; }

.ashopaffiliatemessagesbox { background-color: #D0D0D0; padding: 2px; width: 600px; border-style: solid; border-width: 1px; border-color: $formsbordercolor; }

.ashopaffiliatemessagesrow { background-color: #808080; }

.ashopaffiliatemessagestext1 { font-family: $font; font-size: {$fontsize2}px; color: $catalogheadertext; font-weight: bold; }

.ashopaffiliatemessagestext2 { font-family: $font; font-size: {$fontsize2}px; color: $formstextcolor; }

.ashopaffiliatemessagestext3 { font-family: $font; font-size: {$fontsize2}px; color: $formstextcolor; font-weight: bold; }

.ashopaffiliatemessagebox { background-color: $bgcolor; padding: 2px; width: 500px; border-style: solid; border-width: 1px; border-color: $formsbordercolor; }

.ashopaffiliatemessagetext1 { font-family: $font; font-size: {$fontsize2}px; color: $catalogheadertext; font-weight: bold; }

.ashopaffiliatemessagetext2 { font-family: $font; font-size: {$fontsize2}px; color: $alertcolor; }

.ashopaffiliatehistorybox { background-color: #D0D0D0; padding: 2px; width: 450px; border-style: solid; border-width: 1px; border-color: $formsbordercolor; }

.ashopaffiliatehistoryrow { background-color: #808080; }

.ashopaffiliatehistorytext1 { font-family: $font; font-size: {$fontsize2}px; color: $catalogheadertext; font-weight: bold; }

.ashopaffiliatehistorytext2 { font-family: $font; font-size: {$fontsize2}px; color: $alertcolor; }

.ashopaffiliateleadsbox { background-color: #D0D0D0; padding: 2px; width: 600px; border-style: solid; border-width: 1px; border-color: $formsbordercolor; }

.ashopaffiliateleadsrow { background-color: #808080; }

.ashopaffiliateleadstext1 { font-family: $font; font-size: {$fontsize2}px; color: $catalogheadertext; font-weight: bold; }

.ashopbutton { background-color: #a3a3a3; }

.ashopbutton:hover { background-color: #ddd; }
";
?>