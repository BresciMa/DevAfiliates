<?php
switch ($contexthelppage) {
	case "shopparameters":
		
// configure.php tooltip text and help links

	$tip1 = "To reset the AShop Affiliate Administration Panel password, enter the current password, then enter the new one twice, and then submit this page.";

	$tip2 = "The <b>Contact Information</b> appears within the AShop Affiliate pages and emailed notices.";

	$tip3a = "The <b>Currency</b> setting changes the currency symbol that appears in the AShop Affiliate pages and emailed notices.";

	$tip3f = "<b>Do you like this program?</b><br><br> Profit by letting other merchants know about us when they visit your website.<br><br>Join the AShop Software affiliate program and enter your ID here. A small &quot;Powered by AShop&quot; image with an affiliate tracking link will appear below the category listings in the affiliate area. When the link is clicked, a new browser will open so that your site is still open also. Commissions are paid out approximately 60 days following each order.<br><br>If nothing is entered here, the image and link does not appear.";
	$help3f = "http://www.ashopsoftware.com/ashop-affiliate.htm";
	
	$tip7 = "<b>Advanced Options</b> are for setting server configuration for the AShop program and for integration to third party software. Click this link to open the Advanced Options page.";

	$tip14 = "Set the meta tags of your pages. Product pages can have their own meta tags.";
	
	break;

	case "advancedoptions":

		$tip1 = "The <b>Site URL</b> is automatically set by the install.php script in most cases.  This setting is used by the program to construct links.<br><br>If it is not correct, payment responses will not be received and orders may not be completed.<br><br><b>Warning!</b> If this URL is on a secure SSL connection (https instead of http) some features may not work properly! To avoid trouble make sure the main shopping cart scripts are installed on a regular http connection.";

		$tip2 = "The <b>File System Path</b> is automatically set by the install.php script in most cases. It should be the complete server path to the directory where AShop is installed with a leading / and no trailing slash. Here are some examples.<br>/home/httpd/vhosts/yourdomain.com/httpdocs/ashop<br> /home/user/public_html/ashop.";

		$tip3 = "<b>Time Zone Offset</b><br><br>Many times, the server or hosting service is located in a different time zone than where the merchant is located. Also, many servers are set to Greenwich Mean Time (GMT). Unless something is done to compensate for the difference, the time stamp for orders and stats may show a different time than your local time.<br><br>Enter the difference of time between your time zone and the server time in seconds. For instance: If server time is 3 hours later than your time zone, enter &quot;-10800&quot; (minus 10800, 3 hours x 60 minutes x 60 seconds). If the server time is 2 hours earlier than your time zone, enter &quot;7200&quot; (2 hours x 60 minutes x 60 seconds).";

	break;

	case "affiliateconfiguration":

		$tip1 = "<b>Affiliate Text</b> content appears on the affiliate sign up page. It is commonly used to disclose the terms of the affiliate program and to invite new affiliates to join. HTML for images and hyperlinks can be included in this area.<br><br>Click on the text link to open the help topic for this page.";
                $help1 = "http://www.ashopsoftware.com/help/ashopv/affiliate_program_1.htm";

                $tip2 = "<b>Affiliate eMail</b> is the addresss where administrative notices are sent when commissions have been earned by affiliates.";

                $tip3 = "The <b>Default Affiliate Commission</b> is used to initially set the commission rate by percentage for each new product that is created. The commission rate can then be changed for each product individually by percentage or amount.";

                $tip4 = "<b>Multi Tier</b><br><br>Check the box to activate referral commissions for affiliates.<br><br>Click the text link to open the related help topic.";
                $help4 = "http://www.ashopsoftware.com/help/ashopv/secondtieraffiliatecommissi.htm";

                $tip5 = "<b>Require PayPal ID</b><br><br>The affiliate signup form has a field to enter a PayPal ID (email address), which can be used to pay commissions through the Manage Affiliates menu. If this box is checked, the PayPal ID field is required for affiliates to submit the form.";

                $tip6 = "<b>Redirect URL</b><br><br>Affiliate links go to the script named affiliate.php, which records the click, sets a tracking cookie, and immediately redirects to a page of your choosing. If no other redirect is specified, this is the page that customers land on.<br><br>Note: Alternative redirects can be specified in each affiliate link. This is handled automatically by the affiliate link code generator under Manage Affiliates > Link Codes.";

				$tip7 = "<b>Email Confirmation</b><br><br>New affiliates will be sent an email with an activation link which must be clicked to activate the affiliate account.";

				$tip8 = "<b>Manually Verify Orders</b><br><br>Orders will be registered as pending until you activate them and affiliate commission will not be assigned until they are activated.";

	break;

        case "layout":

                $tip1 = "The <b>Default Logo Image</b> is displayed at the top of any page that does not use an HTML page template or theme template or when HTML page templates are not installed. It is also displayed at the top of some error message pages.<br><br>Browse to a new .gif file on your local PC to upload a new logo image. Leave this box blank to keep the existing logo image.";
                $tip2 = "Each <b>Theme</b> is a set of page templates and a configuration file. The configuration file overrides all of the following settings except for the thumbnail image size.";
                $tip3 = "The <b>Page Body Colors</b> settings are for page body tag parameters. Background Color, Text Color, and Link Color settings here are overridden by the presence of AShop HTML page templates or themes. Pages such as pop-up notices and error messages that do not use templates always use these settings.";
                $tip4 = "<b>Forms Colors</b> control the background and text color in forms that are used to collect shipping and billing information.";
                $tip5 = "<b>Product Layout & Colors</b> settings control the table border width, background colors, and font colors within the area where products are displayed in the catalogue.<br><br><b>Condensed Layout</b> reduces the amount of vertical space that product listings take up in the catalogue pages.";
                $tip6 = "<b>Category Colors</b> set the background and text colors for the areas in the catalogue where the categories are listed. The category color is also used in the shopping cart bar above where the products are listed.";
                $tip7 = "This <b>Font</b> setting controls fonts in areas of the pages that are not controlled by a page template or included style sheet.";
                $tip8 = "The <b>Thumbnail Image Size</b> sets the size that thumbnail images appear within the catalogue pages. *If GD or ImageMagick is supported on the server, it also sets the size that images are automatically resized to when uploaded through this program.<br><br>It is best to set this initially and not change it. Otherwise, previously resized images may appear distorted.<br><br>Note that the images always appear in 100 x 100 pixel size within the Edit Products menu.<br><br>*The requirements for automatically editing images from php using the image resizing is GD 2.0 or higher and PHP 4.3 or higher. GD is an additional PHP library that might not be installed on all servers. AShop will check if these features are available and use them if they are. Since not all versions of the GD library have support for saving gif images, the script checks if this is available and if it is not, then all images will be converted to jpeg.";
                $tip9 = "If the thumbnail image is automatically resized with GD or ImageMagick this setting will, when activated, keep the original larger image file and create a link to it from the thumbnail. The text <b>Click to enlarge</b> will be visible above the thumbnail in your product catalog and when the customer clicks the thumbnail a popup window will display the larger image.";
                $tip10 = "Shows the thumbnail in the table on the View Cart page, when activated.";

        break;

        case "shipping":

        	$tip1 = "<b>Sales Tax Type</b><br><br><b>US Sales Tax</b> charges the <i>Sales Tax/VAT/GST Percentage</i> only if the ship to state matches the <i>State/Province To Charge Sales Tax To</i>.<br><br><b>Canada CST/PST</b> charges the <i>Sales Tax/VAT/GST Percentage</i> to all orders shipping to Canada. If shipping to the province that is set in <i>State/Province To Charge Sales Tax To</i>, the <i>PST Percentage</i> is also added.<br><br><b>European/VAT</b> charges <i>Sales Tax/VAT/GST Percentage</i> to all European Union countries. If <i>Request VAT Number</i> is checked, customers outside of the country that is set as <i>EU VAT Origin Country</i> may be exempted from paying tax by submitting a VAT registration number.";
        
		$tip2 = "<b>Shipping Options</b><br><br>Add one or more <b>Local Countries</b>. The <b>Local Handling Charge</b> will be applied once per order when shipped to a <i>Local Country</i>. The <b>International Handling Charge</b> will be applied instead if the shipped to a non-local country.<br><br>If the <b>Ship Locally Only</b> box is checked, only the countries that are listed as local will be available to customers in the shipping destination form.";

		$tip3 = "<b>Zip Tables</b> are used to set shipping per product based upon zip code lookup for seven zones within the U.S. Click this text link to Manage Zip Tables.";

		$tip4 = "Customer selectable <b>Shipping Options</b> for special delivery charges can be defined here based upon local and/or international destinations.";

		$tip5 = "<b>Shipping Discounts</b> can be defined here based upon the total quantity of shippable products and local or international destination. The shipping discounts can be applied selectively to individual <i>Shipping Options</i> or to all shipping options.";

		$tip6 = "<b>Storewide Shipping</b> lets you use the same shipping calculation options for multiple products.";

		$tip7 = "<b>UPS Shipping Options</b><br>Configure UPS shipping calculation.";

		$tip8 = "<b>FedEx Shipping Options</b><br>Configure FedEx shipping calculation.";

		$tip9 = "<b>Local Rates</b> are used to set local sales tax rates at the county or city level, which will be added to the base sales tax. Click this text link to manage Local Tax Rates.";

		$tip10 = "<b>USPS Shipping Options</b><br>Configure USPS shipping calculation.";

		$help1 = "http://www.ashopsoftware.com/help/ashopv/editlocaltaxrates.htm";

	break;

        case "editzones":

		$tip1 = "A <b>Zip Zone Table</b> is used to lookup one of 7 U.S. shipping zones based upon the first three digits of the destination zip code. Only one zipzone table is required for each shipping origin point.<br><br>For each product with zipzone shipping, the destination zone is looked up from the assigned zipzone table. The shipping rate for each zone is set for each product.<br><br>Click the Help icon for more information about zip zone tables and zone rates for each product.";
		$help1 = "http://www.ashopsoftware.com/help/ashopv/zipzonetableshippingcharge.htm";

		$tip2 = "<b>Add New Zip</b><br><br>The left hand column is the first three digits of the zip code. The right hand column is the zone that will be looked up for that zip code. It is not necessary to put every zip code in. Only make an entry where there is a change in the zone. For instance, if the first zip is 000 for zone 8 and the second zip is 350 for zone 7, all zip codes with the first 3 digits equal to or larger than 000 and less than 350 will be charged the zone 8 shipping charge for the product.<br><br>To edit an existing zip zone row, click on the zip number in the table below.";

	break;

        case "editshipoptions":

		$tip1 = "<b>Shipping Options</b> appear within the shipping information form for customers to select. Shipping options are typically used to add charges for express or overnight delivery.<br><br><b>Create New Shipping Option</b><ol><li>Enter the Description that customer\'s will see.</li><li>Enter the Fee that will be added to the order when this shipping option is selected.</li><li>Select from the Shipped drop-down box to set if the shipping option will apply to local, international, or both (all) destination countries. If locally is selected, this shipping option will only appear in the shipping information form that customers see when they select a destination country that is local. See the Shipping and Taxes help topic for information about setting local destination countries.</li><li>Click Add or Update.</li></ol>";
		$help1 = "http://www.ashopsoftware.com/help/ashopv/manageshippingoptions.htm";

	break;

        case "editshipdiscounts":

		$tip1 = "<b>Shipping Discounts</b> are based upon the quantity of shippable products in the shopping cart basket and can be set to apply only to products selectively based upon whether the destination country is local or international and can also be applied to specific shipping options or to all shippable products in the basket.<br><br>There is no limit to the number of shipping discounts that can be created, but you should be careful not to create shipping discounts that conflict with each other as the results may be unpredictable.";
		$help1 = "http://www.ashopsoftware.com/help/ashopv/manageshippingdiscounts.htm";

	break;

        case "payoptions":

		$tip1 = "Select a <b>Payment Gateway</b> and click Add. A new payment option table will be created.<br><br>The <b>Option Name</b> and <b>Description</b> appear in the customer payment option selection page, which only appears if there is more than one payment option.<br><br>A <b>Payment Fee</b> can optionally be added for each payment option.<br><br>A custom <b>Thank You Message</b> can be set for each payment option.<br><br>Click the text link to open the related help topic.";
		$help1 ="http://www.ashopsoftware.com/help/ashopv/payment_options.htm";

	break;

        case "fulfiloptions":

		$tip1 = "<b>Fulfilment Options</b> can be created to email plain text order notices or to send order information as an attachment in a fulfilment company format. Each product can be assigned a fulfilment option so that a fulfilment notice is emailed when the product is ordered.<br><br>Click the text link to open the related help topic.";
		$help1 ="http://www.ashopsoftware.com/help/ashopv/fulfilment.htm";

	break;

        case "addcategory":

		$tip1 = "<b>Add New Top Category</b><br><br>Before adding products to AShop, at least one category must be added. The category <i>Name</i> field appears in the shopping cart category selector. The category <i>Description</i> appears above the product listings in the shopping cart. When viewing the shopping cart catalogue, if only one category exists, the category selector does NOT appear. If more than one category exists, then the category selector appears to the left of the product listings.<br><br>Category levels are limited to three; top-category, sub-category, and subsub-category. There is no limit to the number of categories within each of the three possible levels.";

		$tip2 = "<b>Add New Subcategory</b><br><br>Top categories are listed below. Click on a top category link to add a subcategory to it."; 

	break;

        case "editdiscount":

		$help1 = "http://www.ashopsoftware.com/help/ashopv/discountcoupons.htm";

	break;

        case "editcatalogue":

		$help1 = "http://www.ashopsoftware.com/help/ashopv/edit_catalogue.htm";
		$alt1 = "Click to open the help topic for this area.";

	break;

        case "editqtypricing":

		$tip0 = "This will be displayed on the shopping cart pages instead of the product's price. This can for example be a text that describes the quantity pricing levels.";
		$tip1 = "The quantity that is used to calculate the pricing levels can either be the total quantity of this product or the total quantity of all products in the customers shopping cart.<br><b>Note</b> This does not apply the quantity pricing levels to all products in the catalog.<br><b>Note</b> If the option <i>All products</i> is selected the pricing levels will be a global set that is used for all products that use this setting.";
		$tip2 = "When set to <i>Quantity Discount</i> only the highest applicable level will be applied making it work like a simple quantity based discount. If the option <i>Calculate Levels Separately</i> is selected then all applicable price levels will be used which will result in different prices on the same item.";

	break;

        case "editbilling":

		$help1 = "http://www.ashopsoftware.com/help/ashopv/recurringbilltemplates.htm";

	break;

        case "editproduct":

		$tip0 = "The product number is used to identify the product in the catalog and to set up buy buttons or buy links.";
		$tip1 = "Select from a ListMessenger mailing group. When this product is ordered, the customer will be added to the selected group.";
		$tip2 = "Select from a ListMail Pro mailing group. When this product is ordered, the customer will be added to the selected group.";
		$tip3 = "Select from a phpBB discussion group. When this product is ordered, the customer will be added to the selected group.";
		$tip3a = "Select from an AutoResponse follow-up program. When this product is ordered, the customer will receive the corresponding series of messages.";
		$tip3b = "Select from an Infinity Responder follow-up program. When this product is ordered, the customer will receive the corresponding series of messages and/or be removed from the subscription you selected in the Remove option.";
		$tip3c = "Select from your list of autoresponders. When this product is ordered, the customer will receive the corresponding series of messages and/or be removed from the subscription you selected in the Remove option.";
		$tip4 = "The product name appears in the catalogue, on receipts, and in reports.<br><br>It is recommended that you do not use any of the following characters in this field; single or double quote, apostrophe, dollar sign $, or pound symbol #. The product name is included in a string of characters that is sent through the payment gateway. Some payment gateways will not send a response post if there are reserved or special characters in the order string.";
		$tip5 = "The catalogue status setting affects whether the product appears in the dynamically generated catalogue pages or not. A retail product that is inactive in the retail catalogue can still be ordered by using OrderForm or OrderLink methods.";
		$tip6 = "When products are listed in an eBay store and have only the PayPal payment option set for the eBay listing, payment for the eBay item can be linked to AShop. Click on the eBay Item ID link to open a help topic about this.";
		$help6 = "http://www.ashopsoftware.com/help/ashopv/ebayitems.htm";
		$tip7 = "This is the regular price (before discount) that is displayed and charged in the retail catalogue and for OrderForm and OrderLink methods.";
		$tip8 = "This is the price that displays and is charged in the wholesale catalogue.";
		$tip9 = "The product description appears in the retail and wholesale catalogue. HTML can be used in this field to include page formatting, images, and links to other pages and images.";
		$tip10 = "When a license agreement is set for a product, customers are required to check a box agreeing to the terms of sale when adding a product to the cart. At that point, there is a link for the customer to view the license agreement.<br><br>The license agreement field may contain HTML for page formatting, images, links, and forms.";
		$help10 = "http://www.ashopsoftware.com/help/ashopv/productlicenseagreements.htm";
		$tip11 = "The thumbnail image is the image that appears to the left of the product description in the catalogue. The dimensions of this image are set in Store Configuration > Layout. It is best to set the thumbnail image size before uploading the images. Most servers support a feature that resizes the images as they are uploaded. If the images are uploaded in one size, then the thumbnail image size is changed, the images may appear distorted.<br><br>Note: The thumbnail image size only affects the catalogue. The images size when viewed through this administration panel is always 100 pixels square.";
		$help11 = "http://www.ashopsoftware.com/help/ashopv/layout.htm";
		$tip11a = "The subscription directory must be located in the same directory as AShop. Enter the name of the directory (without leading or trailing slashes).";
		$help11a = "http://www.ashopsoftware.com/help/ashopv/subscription_directory_folder_.htm";
$tip11b = "The subscription directory URL is used to add a link to the protected directory in the customer receipt.";
		$help11b = "http://www.ashopsoftware.com/help/ashopv/passwordadministratorintegr.htm";
		$tip12 = "There are two ways to upload a product file.<br><br>1. Browse to an image file on your local computer and then submit this form. Note that many hosting services limit the size of http file uploads to 2 MB. If the product file exceeds the http upload limit, it will not be uploaded this way.<br><br>2. Upload the file by FTP to the products directory. When there are new files present in the products directory, a drop-down box appears on this page in order to select the product file. After selecting the product file, submit this form to rename the file and save its original file name in the database.";
		$help12 = "http://www.ashopsoftware.com/help/ashopv/downloadableproducts.htm";
		$tip12a = "The number of days until passwords expire is set here, but the actual removal of expired passwords must be performed by running a script named checksubscr.php. To run checksubscr.php automatically, a cron job is the simplest method. The checksubscr.php script can also be run by simply calling it from a browser. (Place it in your favorites and click on it once a month.)<br><br>For more information about expiring subscription passwords, click on the link to the right.";
		$help12a = "http://www.ashopsoftware.com/help/ashopv/subscriptionpasswordexpirati.htm";
		$tip13 = "To upload a text file containing key codes and save them to the database, browse to the text file that contains the key codes and then submit this form.  Each key code (string of characters) must be separated by a line break. Do not upload binary files.";
		$help13 = "http://www.ashopsoftware.com/help/ashopv/keycodeproducts.htm";
		$tip14 = "The tier-1 affiliate commission can be set as an amount or percentage of the price for each product. A default setting may be entered in Store Configuration > Affiliate Program so that the initial commission value is automatically set each time that a new product is added.<br><br>The link to the right of this tip will open a help topic about the affiliate program.";
		$help14 = "http://www.ashopsoftware.com/help/ashopv/affiliate_program_1.htm";
		$tip15 = "Check the box to make the product taxable. If there is at least one taxable product in the cart, the shipping information window will open before checkout to collect the destination country and state in order to calculate sales tax.";
		$tip16 = "Flags can be used to indicate a certain state of a product, such as Available, Parental Advisory. They can be added/edited on the Store Configuration page.";
		$tip17 = "When a preview/demo file is uploaded for the product ,a link to view or download (depending on what type of file it is) the file appears in the catalogue product listing for customers.";
		$tip18 = "You can set this to the URL to a page describing this product in more detail. The product name will be linked to this URL in the catalog.";

	break;

        case "editinventory":

		$tip0 = "The product number is used to identify the product in the catalog and to set up buy buttons or buy links.";
		$tip4 = "The SKU will be included in purchase notices sent to the shop owner.";
		$tip5 = "You can deactivate inventory management here if the product is digital goods, a service or other type of product for which an inventory is not applicable.";
		$tip18 = "Enter the number of items you currently have in stock. Each sale will be subtracted from this number. Make sure to keep this up to date with deliveries from your supplier.";
		$tip19 = "When the stock level falls below the number you enter here a Low Stock warning will be shown for the product to tell your customers that the item may soon be out of stock.";
		$tip20 = "Select your supplier here to have purchase orders generated automatically by the system.";
		$tip21 = "This is what you pay your supplier per item.";

	break;

        case "editcontent":

		$tip5 = "The catalogue status setting affects whether the content appears in the dynamically generated catalogue pages or not.";
		$tip9 = "Enter text or other content that will appear in the retail and/or wholesale catalogue.";


	break;

        case "editshipping":

		$help1 = "http://www.ashopsoftware.com/help/ashopv/shippingoptions.htm";
		$tip2 = "Zip Zone shipping uses the first three digits of the destination zip code to look up one of seven US shipping zones, which are relative to the shipping origin zip code. The rate for each zone is set for each product.<br><br>To ship products from more than one origin, set up a zip/zone table for each of the origin points, then set the zip/zone table and zone rates for each product.";
		$help2 = "http://www.ashopsoftware.com/help/ashopv/zipzonetableshippingcharge.htm";
		$tip3 = "UPS and FedEx shipping calculations lookup a zone based upon the origin zip code, then they look up the rate based upon weight. This shipping lookup method is only supported for US and Canada ground shipments.";
		$help4 = "http://www.ashopsoftware.com/help/ashopv/quantitybasedshipping.htm";

	break;

        case "editparameters":

		$help1 = "http://www.ashopsoftware.com/help/ashopv/attributes.htm";
		$tip2 = "To create an input field where customers can add a comment when ordering this product, enter 0 (zero) for the number of alternatives. The comment will then be recorded with the product name and will be displayed in the receipt, order notices, fulfilment notices, and reports.";

	break;

        case "editflags":

		$help1 = "http://www.ashopsoftware.com/help/ashopv/flags.htm";

	break;

        case "editfulfilment":

		$help1 = "http://www.ashopsoftware.com/help/ashopv/fulfilment.htm";

	break;

        case "affiliateadmin":

		$help1 = "http://www.ashopsoftware.com/help/ashopv/manage_affiliates.htm";

	break;

        case "affiliatestats":

		$help1 = "http://www.ashopsoftware.com/help/ashopv/statistics_and_payment.htm";

	break;

        case "affiliatecodes":

		$help1 = "http://www.ashopsoftware.com/help/ashopv/affiliate_link_code_generator.htm";
		$help2 = "http://www.ashopsoftware.com/help/ashopv/affiliate_link_code_categories.htm";
		$help3 = "http://www.ashopsoftware.com/help/ashopv/affiliate_custom_tags.htm";

	break;

        case "salesadmin":

		$help1 = "http://www.ashopsoftware.com/help/ashopv/customers_and_messaging.htm";
	
	break;

        case "salesreport":

		$help1 = "http://www.ashopsoftware.com/help/ashopv/sales_reports.htm";

	break;

        case "emconfigure":

		$tip1 = "Set the <b>Customer Mail Address</b> to the address of the POP3/IMAP mailbox where customer email is received. For instance service@yourdomain.com.<br><br>The <b>Mail Server</b> setting is typically something like mail.yourdomain.com.<br><br>The <b>account username</b> is typically the first part of the mail address, for instance service, and the password is the one that you have set for that mail account through your hosting service.<br><br>The <b>Mail Port</b> is typically 110 for POP3 and 143 for IMAP.<br><br>A <b>mailform.php contact form</b> script is included with AShop. Multiple contact forms may be used in separate directories under the same directory as AShop and each contact form may include extra fields. In order to avoid spam mail, it is advisable to remove email addresses links and text in web pages. mailform.php posts directly to the eMerchant customer inquiry inbox and it has a random graphic code text box to block automated submissions.";

		$tip2 = "Set the <b>Vendor Mail</b> address to the address of the POP3/IMAP mailbox where vendor email is received. For instance vendor@yourdomain.<br><br>The <b>Mail Server</b> setting is typically something like mail.yourdomain.com.<br><br>The <b>Account Username</b> is typically the first part of the mail address, for instance vendor, and the password is the one that you have set for that mail account through your hosting service.<br><br>The <b>Mail Port</b> is typically 110. If it is set, the vendor mail address will be checked automatically upon logging into the Sales Office. If it is not set, the vendor mail check does not occur.";

		$tip3 = "Activate <b>Spam protection</b> to block any email coming from a sender that is not already a registered customer. All messages through the contact form will still be delivered and the sender will be registered to allow email in the future.";
		
		$tip4 = "Set the <b>Server Type</b> to <i>POP3</i> to download email from your mail account and save them in your Sales Office, similar to how an email client software like Outlook or Thunderbird works.<br><br>Set it to <i>IMAP</i> to read the email directly on your mail server without downloading anything. This will improve performance if your mail server is on the same machine or network as your web site but should not be used if you intend to read your email with a POP3 client software too since the POP3 client will download the messages and make them unavailable to your eMerchant.";		

	break;

        case "emuseradmin":

		$tip1 = "Any number of <b>Sales Office Users</b> may be added here.<br><br>The <b>admin user</b> cannot be deleted. The admin password can be the same or different than the administration panel login password. When admin is logged into eMerchant, a link to the administration panel appears on the left under Links. The same link is not available for other users.";

	break;

        case "makehtml":

		$help1 = "http://www.ashopsoftware.com/help/ashopv/makehtml.htm";
		$tip1 = "You can set this to the URL to an existing page describing this product in more detail. The product name will be linked to this URL in the catalog.";
		$tip2 = "The filename of the generated html file. Two different placeholder codes can be used to set the filename dynamically:<br><b>%productid%</b> = the ID number of this product<br><b>%productname%</b> = the name of this product";
		$tip3 = "The name of the subdirectory where the html file should be saved on your server. This must be a subdirectory of your AShop installation and it must be writable.<br><br><b>Be careful not to generate the page somewhere where you already have another file with the same name that you wish to keep!</b>";
		$tip4 = "The URL to the buy button image file. The default is the standard button that is included with AShop but you can replace this with a button of your own choice by uploading your image file to your site and changing this URL.";
		$tip5 = "The text that is used to create a link to buy your product.";
		$tip6 = "This box can be used to add extended, more detailed information about your product. The extended information will be inserted into your page template where you have put the placeholder code <b>%productlongdescription%</b> and makes it possible to create detailed product pages with more information about the product than what is included in your product catalog.";
		$tip7 = "All available templates in your <b>templates</b> directory are included in this drop down list. You can use your layout templates or create new ones and add them to the <b>templates</b> directory.";
		$tip8 = "The filename of the generated html files. Two different placeholder codes can be used to set the filenames dynamically:<br><b>%productid%</b> = the ID number of the product<br><b>%productname%</b> = the name of the product<br><br><b>Please note</b> that if you do not use these placeholder codes the page generator will just overwrite the same file and you will only get one single html page.";
		$tip9 = "The name of the subdirectory where the html file should be saved on your server. This must be a subdirectory of your AShop installation and it must be writable.<br><br><b>Be careful not to generate the page somewhere where you already have another file with the same name that you wish to keep!</b>";
		$tip10 = "Limits the pages generated to include only products for one specific Shopping Mall member. If this is set to <b>0</b> all products in the category will be included.</b>";
		$tip11 = "The META keywords content of the generated page for this product. Use &lt; !-- AShopmetakeywords --&gt; in your template to include this.";
		$tip12 = "The META description content of the generated page for this product. Use &lt; !-- AShopmetadescription --&gt; in your template to include this.";
		$tip13 = "Use the default thumbnail image for the product or enter the name of another image that you have uploaded to <i>prodimg</i>. Add a leading &quot;b&quot; to the name to get the original size image if automatic thumbnail resizing is activated, for example: b54.jpg.";

	break;

        case "upsshipping":

		$tip1 = "Select the UPS service and options you wish to use for local and international delivery. These settings will be applied to all UPS shipping rate calculations. <b>Please note</b> that available options might change after clicking Submit depending on the service or origin country you have selected.";

	break;

        case "fedexshipping":

		$tip1 = "Select the FedEx service and options you wish to use for local and international delivery. These settings will be applied to all FedEx shipping rate calculations.";

	break;
}
?>