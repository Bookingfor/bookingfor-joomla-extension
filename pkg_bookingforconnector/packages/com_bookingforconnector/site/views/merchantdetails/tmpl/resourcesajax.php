<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$counterResources = 1;
$merchant = $this->item;

?>
<div id="com_bookingforconnector-items-container-wrapper">
	<?php if ($this->items != null): ?>
	<div class="com_bookingforconnector-items-container">
	    <div class="com_bookingforconnector-offerlist com_bookingforconnector-items <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> com_bookingforconnector-list">
		<?php foreach($this->items as $resource): ?>
		<?php
		// assign the current resource to a property so it will be available inside template 'resource'
		if ($counterResources<COM_BOOKINGFORCONNECTOR_MAXRESOURCESAJAXMERCHANT){
			$counterResources +=1;
			$this->item->currentResource = $resource; 
			echo  $this->loadTemplate('resource'); 
		}else{
			echo '<div class="text-center">' . JHTML::link(JRoute::_('index.php?option=com_bookingforconnector&layout=resources&view=merchantdetails&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name). "&limitstart=0"), JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_ALL')) . '</div>';
			break;
		}
		?>
		<?php endforeach?>
	</div>	
	</div>
	<?php endif?>
</div>
<script type="text/javascript">
<!--
	var shortenOption = {
		moreText: "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_VIEW_READMORE')?>",
		lessText: "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_VIEW_READLESS')?>",
		showChars: '250'
};
jQuery(document).ready(function() {
	jQuery(".com_bookingforconnector_merchantdetails-resource-desc").shorten(shortenOption);
});

//-->
</script>
