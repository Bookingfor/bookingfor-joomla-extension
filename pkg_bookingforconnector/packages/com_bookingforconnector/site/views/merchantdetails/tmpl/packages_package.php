<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$config = $this->config;
$isportal = $config->get('isportal', 1);
$merchant = $this->item;
$offer = $this->item->currentOffer;
$key = $this->item->currentIndex;
$offerName = $offer->Name;
$language = $this->language;


//-------------------pagina per i l redirect di tutte le risorse 

$db   = JFactory::getDBO();
$uri  = 'index.php?option=com_bookingforconnector&view=resource';

$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );

$itemId = intval($db->loadResult());


$itemIdMerchant=0;
$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
if($isportal){
	//-------------------pagina per il redirect di tutti i merchant

	$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
	//$itemIdMerchant = ($db->getErrorNum())? 0 : intval($db->loadResult());
	$itemIdMerchant = intval($db->loadResult());
	//-------------------pagina per il redirect di tutti i merchant

	//-------------------pagina per il redirect di tutte le risorse in vendita favorite
}
if($itemId == 0){
	$itemId = $itemIdMerchant;
}



$offer->OfferId =  $offer->PackageId;
//$offer->Price = $offer->Value;
$offer->Price = 0;

$route = JRoute::_('index.php?option=com_bookingforconnector&view=merchantdetails&layout=package&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name) . '&packageId=' . $offer->OfferId . ':' . BFCHelper::getSlug($offerName));

$img = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";
$imgError = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";
$images = $img;

		$routeMerchant = "";
		if($isportal){
			$currUriMerchant = $uriMerchant. '&merchantId=' . $this->item->MerchantId . ':' . BFCHelper::getSlug($this->item->Name);
			if ($this->item->MerchantId<>0)
				$currUriMerchant.= '&Itemid='.$this->item->MerchantId;
			$routeMerchant = JRoute::_($currUriMerchant);
		}

$offerImageUrl = "";
			if(!empty($offer->DefaultImg)){
				$offerImageUrl = BFCHelper::getImageUrlResized('offers',$offer->DefaultImg, 'medium');
			}

if ($offer->DefaultImg != ''){
  $images = BFCHelper::getImageUrlResized('offers',$offer->DefaultImg , 'medium');
}elseif ($merchant->LogoUrl != ''){
  $images = BFCHelper::getImageUrlResized('merchant',$merchant->LogoUrl, 'medium');
}

$merchantLogoUrl = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s1.jpeg";

if(!empty($merchant->LogoUrl)){
	$merchantLogoUrl = BFCHelper::getImageUrlResized('merchant',$merchant->LogoUrl, 'logomedium');
}

?>

		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 com_bookingforconnector-item-col" >
			<div class="com_bookingforconnector-search-resource com_bookingforconnector-item <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
				<div class="mrcgroup" id="bfcmerchantgroup<?php echo $offer->OfferId; ?>"><span class="bfcmerchantgroup"></span></div>
				<div class="com_bookingforconnector-item-details  <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>" >
					<div class="com_bookingforconnector-search-merchant-carousel com_bookingforconnector-item-carousel <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4">
						<div id="com_bookingforconnector-search-resource-carousel<?php echo $offer->OfferId; ?>" class="carousel" data-ride="carousel" data-interval="false">
							<div class="carousel-inner" role="listbox">
									<div class="item active"><img src="<?php echo $images; ?>"></div>
							</div>
								<?php if($isportal): ?>
									<a class="com_bookingforconnector-search-resource-logo com_bookingforconnector-logo-grid eectrack" href="<?php echo $routeMerchant?>" data-type="Merchant" id="merchantname<?php echo $offer->OfferId?>" data-id="<?php echo $offer->MerchantId?>" data-category="<?php echo $merchant->MainCategoryName; ?>" data-index="<?php echo $key?>" data-itemname="<?php echo $merchant->Name; ?>" data-brand="<?php echo $merchant->Name; ?>"><div class="containerlogo"><img class="com_bookingforconnector-logo" id="com_bookingforconnector-logo-grid-<?php echo $offer->OfferId?>" src="<?php echo $merchantLogoUrl; ?>" /></div></a>
								<?php endif; ?>
							<a class="left carousel-control" href="#com_bookingforconnector-search-resource-carousel<?php echo $offer->OfferId; ?>" role="button" data-slide="prev">
								<i class="fa fa-chevron-circle-left"></i>
							</a>
							<a class="right carousel-control" href="#com_bookingforconnector-search-resource-carousel<?php echo $offer->OfferId; ?>" role="button" data-slide="next">
								<i class="fa fa-chevron-circle-right"></i>
							</a>
						</div>
					</div>
					
					<div class="com_bookingforconnector-search-merchant-details com_bookingforconnector-item-primary <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
						<div class="com_bookingforconnector-search-merchant-name com_bookingforconnector-item-primary-name">
							<a class="com_bookingforconnector-search-merchant-name-anchor com_bookingforconnector-item-primary-name-anchor eectrack" data-type="Package" href="<?php echo $route ?>" id="nameAnchor<?php echo $offer->OfferId?>" data-id="<?php echo $offer->OfferId?>" data-index="<?php echo $key?>" data-category="<?php echo $merchant->MainCategoryName; ?>" data-itemname="<?php echo $offerName; ?>" data-brand="<?php echo $merchant->Name; ?>"><?php echo $offerName; ?></a>
						</div>
					</div><!--  COL 6-->
					<?php if($isportal): ?>
						<div class="com_bookingforconnector-item-secondary-logo <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2">
							<a class="com_bookingforconnector-search-merchant-logo com_bookingforconnector-logo-list eectrack" href="<?php echo $routeMerchant ?>" data-type="Merchant" data-id="<?php echo $offer->MerchantId?>" data-index="<?php echo $key?>" data-itemname="<?php echo $merchant->Name; ?>" data-category="<?php echo $merchant->MainCategoryName; ?>" data-brand="<?php echo $merchant->Name; ?>"><img class="com_bookingforconnector-logo" src="<?php echo $merchantLogoUrl; ?>" id="com_bookingforconnector-logo-list-<?php echo $offer->OfferId?>" /></a>
						</div> <!--  COL 2-->
					<?php endif; ?>
				</div>
				<div class="clearfix"></div>
				
				
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> secondarysection"  style="padding-top: 10px !important;padding-bottom: 10px !important;">
							<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>10 com_bookingforconnector-item-secondary-section-1 secondarysectionitem" style="padding-left:20px!important;">
								<?php if ($offer->Value > 0): ?>
										<div class="com_bookingforconnector-search-grouped-resource-details-price com_bookingforconnector-item-secondary-price">
											 <div class="com_bookingforconnector-search-resource-details-stay-price com_bookingforconnector-item-secondary-stay-price">
												<span class="com_bookingforconnector-search-resource-details-stay-total com_bookingforconnector-item-secondary-stay-total">&euro; <?php echo number_format($offer->Value ,2, ',', '.') ?></span>
											</div>
										</div>
									<?php endif; ?>
							</div>
							<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2 com_bookingforconnector-item-secondary-section-3 secondarysectionitem">
									<a  href="<?php echo $route?>" style="color: #fff;" class=" com_bookingforconnector-item-secondary-more eectrack" data-type="Package" id="viewbutton<?php echo $offer->OfferId?>" data-id="<?php echo $offer->OfferId?>" data-index="<?php echo $key?>" data-itemname="<?php echo $offerName; ?>" data-category="<?php echo $merchant->MainCategoryName; ?>" data-brand="<?php echo $merchant->Name; ?>"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_MERCHANTDETAILS_VIEWOFFER') ?></a>
							</div>
						</div>
		</div>
		<div class="clearfix"><br /></div>
	  </div>
