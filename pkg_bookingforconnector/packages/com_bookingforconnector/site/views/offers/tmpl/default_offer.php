<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');


$merchant = $this->item->Merchant;
$offer = $this->item;
$offerName = BFCHelper::getLanguage($offer->Name, $this->language);

$offer->OfferId =  $offer->PackageId;
$offer->Price = $offer->Value;
$uriMerchant = $this->uriMerchant . '&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name);
//$itemIdMerchant = $this->itemIdMerchant;
//
//	if ($itemIdMerchant<>0)
//		$uriMerchant.='&Itemid='.$itemIdMerchant;

	$routeMerchant = JRoute::_($uriMerchant);
	$route = JRoute::_($uriMerchant. '&layout=offer&offerId=' . $offer->OfferId . ':' . BFCHelper::getSlug($offerName));

	$img = JURI::root() . "media/com_bookingfor/images/default.png";
	$imgError = JURI::root() . "media/com_bookingfor/images/default.png";

	if ($offer->DefaultImg != ''){
		$img = BFCHelper::getImageUrlResized('offers',$offer->DefaultImg , 'offer_list_default');
		$imgError = BFCHelper::getImageUrl('offers',$offer->DefaultImg , 'offer_list_default');
	}elseif ($merchant->LogoUrl != ''){
		$img = BFCHelper::getImageUrlResized('merchant',$merchant->LogoUrl, 'resource_list_default_logo');
		$imgError = BFCHelper::getImageUrl('merchant',$merchant->LogoUrl, 'resource_list_default_logo');

	}



?>
	<div class="com_bookingforconnector_search-resource">
		<div class="com_bookingforconnector_merchantdetails-resource com_bookingforconnector_merchantdetails-resource-t">
			<div class="com_bookingforconnector_merchantdetails-resource-features">
				<a class="com_bookingforconnector_resource-imgAnchor" href="<?php echo $route ?>"><img class="com_bookingforconnector_resource-img" src="<?php echo $img?>" onerror="this.onerror=null;this.src='<?php echo $imgError?>'" /></a>
				<h4 class="com_bookingforconnector_merchantdetails-resource-name"><a class="com_bookingforconnector_resource-resource-nameAnchor" href="<?php echo $route ?>"><?php echo  $offerName ?></a></h4>

				<p class="com_bookingforconnector_merchantdetails-resource-desc">
					<?php echo  BFCHelper::getLanguage($offer->Description, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')) ?>
				</p>
			</div>
			<div class="clearboth"></div>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> com_bookingforconnector_search-merchant-resource nominheight noborder">
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> ">
				<?php if ($offer->Price != null && $offer->Price > 0):?>
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 com_bookingforconnector-merchantlist-merchant-price minheight34 borderright">
							<div class="com_bookingforconnector_merchantdetails-resource-stay-price">
								<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_OFFER_PRICE')?> &euro; <?php echo number_format((float)$offer->Price,2, ',', '.')?>
								<?php if ($offer->PriceViewType == 1): ?>
									<span class="com_bookingforconnector_merchantdetails-resource-stay-discount">&euro; <?php echo number_format((float)$offer->OldValue,2, ',', '.')?></span>
								<?php endif; ?>
								<?php if ($offer->PriceViewType == 2): ?>
									- <?php echo number_format((float)(100- ($offer->Price *100 / $offer->OldValue)),2, ',', '.')?> %
								<?php endif; ?>
								<?php if ($offer->PriceViewType == 3): ?>
									(- &euro; <?php echo number_format((float)($offer->OldValue - $offer->Price),2, ',', '.')?>)
								<?php endif; ?>
							</div>
						</div>
				<?php else: ?>
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 minheight34 ">&nbsp;</div>
				<?php endif; ?>
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
							<a class="btn btn-info pull-right" href="<?php echo $route ?>"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_OFFER_DETAILS')?></a>
						</div>
					</div>
			</div>
		</div>
	</div>
