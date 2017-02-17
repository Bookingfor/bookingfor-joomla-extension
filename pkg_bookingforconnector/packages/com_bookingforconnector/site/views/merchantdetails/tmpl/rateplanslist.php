<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$this->document->setTitle($this->item->Name);
$this->document->setDescription( BFCHelper::getLanguage($this->item->Description, $this->language));
?>
<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$this->document->setTitle($this->item->Name);
$this->document->setDescription( BFCHelper::getLanguage($this->item->Description, $this->language));
?>
<div class="com_bookingforconnector_merchantdetails com_bookingforconnector_merchantdetails-t<?php echo  $this->item->MerchantTypeId?>">
	<?php echo  $this->loadTemplate('head'); ?>
</div>
<SCRIPT LANGUAGE="JavaScript">
<!--
	function toggleDetails(anchor) {
		var c = 'com_bookingforconnector_merchantdetails-resource-open';
		var a = jQuery(anchor);
		var p = a.parents('div.com_bookingforconnector_merchantdetails-resource').first();
		if (p.hasClass(c))
			p.removeClass(c);
		else
			p.addClass(c);
	}
//-->
</SCRIPT>
<script type="text/javascript">
jQuery(function($) {
	$('.moduletable-insearch').show();
});
</script>
