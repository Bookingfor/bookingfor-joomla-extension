<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$counterResources = 1;
$condominium = $this->item;
$merchant = $condominium->Merchant;
$condominiumName = BFCHelper::getLanguage($condominium->Name, $this->language);

?>
<div class="com_bookingforconnector_merchantdetails com_bookingforconnector_merchantdetails-t">
	<?php if ($this->items != null): ?>
	<div class="com_bookingforconnector_merchantdetails-resources">
		<?php foreach($this->items as $resource): ?>
		<?php
		// assign the current resource to a property so it will be available inside template 'resource'
		if ($counterResources<COM_BOOKINGFORCONNECTOR_MAXRESOURCESAJAXMERCHANT){
			$counterResources +=1;
			$this->item->currentResource = $resource; 
			echo  $this->loadTemplate('resource'); 
		}else{
			echo JHTML::link(JRoute::_('index.php?option=com_bookingforconnector&layout=resources&view=condominium&resourceId=' . $condominium->CondominiumId . ':' . BFCHelper::getSlug($condominiumName)), JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_ALL'));
			break;
		}
		?>
		<?php endforeach?>
	</div>	
	<?php endif?>
</div>
