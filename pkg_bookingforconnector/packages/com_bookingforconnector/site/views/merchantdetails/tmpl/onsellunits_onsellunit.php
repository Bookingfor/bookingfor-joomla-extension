<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$merchant = $this->item;
$resource = $this->item->currentOnSellUnit;
$resourceName = BFCHelper::getLanguage($resource->Name, $this->language);

$addressData = $resource->Address;

$route = JRoute::_('index.php?option=com_bookingforconnector&view=merchantdetails&layout=onsellunit&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name) . '&onsellunitid=' . $resource->OnSellUnitId . ':' . BFCHelper::getSlug($resourceName));
	$resourceImageUrl = "/media/com_bookingfor/images/default.png";
	if ($resource->ImageUrl != '') {
		$resourceImageUrl = BFCHelper::getImageUrl('onsellunits',$resource->ImageUrl, 'onsellunit_list_default');		
	}elseif ($merchant->LogoUrl != ''){
		$resourceImageUrl = BFCHelper::getImageUrl('merchant',$merchant->LogoUrl, 'resource_list_default_logo');		
	}
?>

	<div class="com_bookingforconnector_search-resource">
		<div class="com_bookingforconnector_merchantdetails-resource">
			<div class="com_bookingforconnector_merchantdetails-resource-features">
				<a class="com_bookingforconnector_resource-imgAnchor" href="<?php echo $route ?>"><img class="com_bookingforconnector_resource-img" src="<?php echo $resourceImageUrl?>" /></a>
				<h3 class="com_bookingforconnector_merchantdetails-resource-name"><a class="com_bookingforconnector_resource-resource-nameAnchor" href="<?php echo $route ?>"><?php echo  $resourceName ?></a></h3>
				<div class="com_bookingforconnector_merchantdetails-resource-address">
					<strong><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_ADDRESS') ?>:</strong> <?php echo  $addressData ?>
				</div>
				<p class="com_bookingforconnector_merchantdetails-resource-desc">
					<?php echo  BFCHelper::getLanguage($resource->Description, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')) ?>
				</p>
				<a class="com_bookingforconnector_merchantdetails-resource-moreinfo" href="javascript:void(0);" onclick="toggleDetails(this);"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_LINK')?></a>
			</div>
			<div class="clearboth"></div>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> com_bookingforconnector_search-merchant-resource nominheight noborder">
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> ">
				<?php if ($resource->Price != null && $resource->Price > 0):?>
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3 com_bookingforconnector-merchantlist-merchant-price minheight34 borderright">
							<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_OFFER_PRICE')?>: &euro; <?php echo number_format($resource->Price,2, ',', '.')?>
						</div>
				<?php else: ?>
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3 minheight34 ">&nbsp;</div>
				<?php endif; ?>
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>9">
							<a class="btn btn-success pull-right" href="<?php echo $route ?>"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_OFFER_DETAILS')?></a>
						</div>
					</div>
			</div>
		</div>
	</div>
