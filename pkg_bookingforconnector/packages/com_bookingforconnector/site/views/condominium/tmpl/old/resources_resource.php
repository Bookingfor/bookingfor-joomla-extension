<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$merchant = $this->item->Merchant;
$resource = $this->item->currentResource;
$resource->ResourceId = $resource->UnitId;
$unit = $resource;
$resourceName = BFCHelper::getLanguage($resource->Name, $this->language);
$addressData = $resource->AddressData;
$merchantAddress = false;
if ($addressData == '' || $addressData == null || $merchant->MerchantTypeId != 2) {
	$addressData = $merchant->AddressData;
	$merchantAddress = true;
}
$db   = JFactory::getDBO();
$uri  = 'index.php?option=com_bookingforconnector&view=resource';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') LIMIT 1' );
//$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
$itemId = intval($db->loadResult());

	if ($itemId<>0)
		$route = JRoute::_($uri.'&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName).'&Itemid='.$itemId );
	else
		$route = JRoute::_($uri.'&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName));

//$route = JRoute::_('index.php?option=com_bookingforconnector&view=resource&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName));
	$resourceImageUrl =  JURI::root()."images/default.png";
	if ($resource->ImageUrl != '') {
		$resourceImageUrl = BFCHelper::getImageUrl('resources',$resource->ImageUrl, 'resource_list_default');		
	}elseif ($merchant->LogoUrl != ''){
		$resourceImageUrl = BFCHelper::getImageUrl('merchant',$merchant->LogoUrl, 'resource_list_default_logo');		
	}


?>
	<div class="com_bookingforconnector_search-resource">
		<div class="com_bookingforconnector_merchantdetails-resource com_bookingforconnector_merchantdetails-resource-t<?php echo $resource->MasterTypologyId?>">
			<div class="com_bookingforconnector_merchantdetails-resource-features">
				<a class="com_bookingforconnector_resource-imgAnchor" href="<?php echo $route ?>"><img class="com_bookingforconnector_resource-img" src="<?php echo $resourceImageUrl?>" /></a>
				<h3 class="com_bookingforconnector_merchantdetails-resource-name"><a class="com_bookingforconnector_resource-resource-nameAnchor" href="<?php echo $route ?>"><?php echo  $resourceName ?></a></h3>
				<div class="com_bookingforconnector_merchantdetails-resource-address">
					<?php if ($merchantAddress):?>
					<strong><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_ADDRESS') ?>:</strong> <?php echo  BFCHelper::getItem($addressData, 'indirizzo') ?> - <?php echo  BFCHelper::getItem($addressData, 'cap') ?> - <?php echo  BFCHelper::getItem($addressData, 'comune') ?> (<?php echo  BFCHelper::getItem($addressData, 'provincia') ?>)		
					<?php  else:?>
					<strong><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_ADDRESS') ?>:</strong> <?php echo  $addressData ?>
					<?php endif;?>
				</div>
				<p class="com_bookingforconnector_merchantdetails-resource-desc">
					<?php echo  BFCHelper::getLanguage($resource->Description, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')) ?>
				</p>
				<a class="com_bookingforconnector_merchantdetails-resource-moreinfo" href="javascript:void(0);" onclick="toggleDetails(this);"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_LINK')?></a>
			</div>
			<div class="clearboth"></div>
			<div class="row-fluid com_bookingforconnector_search-merchant-resource nominheight noborder">
					<div class="row-fluid ">
						<div class="span3 com_bookingforconnector_merchantdetails-resource-paxes minheight34 borderright">
							<?php if ($resource->MinCapacityPaxes == $resource->MaxCapacityPaxes):?>
								<?php echo  $resource->MaxCapacityPaxes ?> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_PAXES')?>
							<?php else: ?>
								<?php echo  $resource->MinCapacityPaxes ?>-<?php echo  $resource->MaxCapacityPaxes ?> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_PAXES')?>
							<?php endif; ?>
						</div>
						<div class="span3 com_bookingforconnector_merchantdetails-resource-rooms minheight34 ">
							<?php if ($unit != null):?>
								<?php echo  $resource->Rooms ?> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_ROOMS')?>
							<?php endif; ?>
						</div>
						<div class="span6">
							<a class="btn btn-info pull-right" href="<?php echo $route ?>"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_BUTTON')?></a>
						</div>
					</div>
				</div>
		</div>
	</div>
