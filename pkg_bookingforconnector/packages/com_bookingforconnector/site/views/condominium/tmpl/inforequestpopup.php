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
$resource->Price = $resource->MinPrice;

//$resourceName = BFCHelper::getLanguage($resource->Name, $this->language);
$resourceName = BFCHelper::getLanguage($resource->Name, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 

//$addressData = $resource->AddressData;
//$merchantAddress = false;
//if ($addressData == '' || $addressData == null || $merchant->MerchantTypeId != 2) {
//	$addressData = $merchant->AddressData;
//	$merchantAddress = true;
//}

$typeName =  BFCHelper::getLanguage($resource->CategoryName, $this->language);
$zone = $resource->LocationZone;
$location = $resource->LocationName;
$contractType = ($resource->ContractType) ? JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_CONTRACTTYPE1')  : JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_CONTRACTTYPE');

$route = JRoute::_('index.php?option=com_bookingforconnector&view=onsellunit&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName));
$routeMerchant = JRoute::_('index.php?option=com_bookingforconnector&view=merchantdetails&merchantId=' . $resource->MerchantId . ':' . BFCHelper::getSlug($merchant->Name));

$addressData ="";
$arrData = array();
if ($resource->IsAddressVisible)
{
	if(!empty($resource->AddressData)){
		$arrData[] = ($resource->AddressData);
	}
}
if(!empty($zone)){
	$arrData[] = ($zone);
}
if(!empty($location)){
	$arrData[] = ($location);
}
$addressData = implode(" - ",$arrData);

////add counter
//$model      = $this->getModel();
//$retCounter = $model->setCounterByResourceId($resource->ResourceId,"contact",$this->language);


?>
<div class="com_bookingforconnector_resource com_bookingforconnector_resource-mt<?php echo  $merchant->MerchantTypeId?> com_bookingforconnector_resource-t<?php echo  $resource->MasterTypologyId?>">
	<h2 class="com_bookingforconnector_resource-name"><?php echo  $resourceName?> 
		<span class="com_bookingforconnector_resource-rating com_bookingforconnector_resource-rating<?php echo  $merchant->Rating ?>">
			<!-- <span class="com_bookingforconnector_resource-ratingText">Rating <?php echo  $merchant->Rating ?></span> -->
		</span>
	</h2>
	<div class="com_bookingforconnector_resource-address">
		<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTLIST_ADDRESS') ?>: <?php echo  $addressData?>
	</div>	
	<div class="clear"></div>
	{rsform 14}
</div>
