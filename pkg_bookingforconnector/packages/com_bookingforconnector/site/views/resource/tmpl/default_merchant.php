<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$resource = $this->item;
$merchant = $resource->Merchant;
$addressData = $merchant->AddressData;
$contacts = $merchant->ContactData;
?>
<div class="com_bookingforconnector_resource-merchant">
	<h2 class="com_bookingforconnector_resource-merchantName"><?php echo $merchant->Name?></h2>
	<p class="com_bookingforconnector_resource-merchantAddress">
		<strong><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_ADDRESS') ?>:</strong> <?php echo  BFCHelper::getItem($addressData, 'indirizzo') ?> - <?php echo  BFCHelper::getItem($addressData, 'cap') ?> - <?php echo  BFCHelper::getItem($addressData, 'comune') ?> (<?php echo  BFCHelper::getItem($addressData, 'provincia') ?>)<br/>
		<strong><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_PHONE') ?>:</strong> <?php echo BFCHelper::getItem($contacts, 'telefono1') ?> <?php if (BFCHelper::getItem($contacts, 'telefono2') != ''): ?> - <?php echo BFCHelper::getItem($contacts, 'telefono2') ?><?php endif?><br/>
		<strong><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_FAX') ?>:</strong> <?php echo BFCHelper::getItem($contacts, 'fax') ?><br/>
		<strong><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_INFO') ?>:</strong> <a class="com_bookingforconnector_resource-merchantLink" href="<?php echo JRoute::_('index.php?option=com_bookingforconnector&view=merchantdetails&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name)) ?>"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_MERCHANTSHEET')?></a>
	</p>
</div>
