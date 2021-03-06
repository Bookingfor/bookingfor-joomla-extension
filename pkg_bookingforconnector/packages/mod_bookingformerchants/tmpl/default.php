<?php 
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$document 	= JFactory::getDocument();
$language 	= JFactory::getLanguage()->getTag();

$cols = $params->get('itemspage', 4);
$tags = $params->get('tags');
$maxitems = $params->get('maxitems', 10);
$descmaxchars = $params->get('desc_maxchars', 300);

$carouselid = uniqid();

$config = JComponentHelper::getParams('com_bookingforconnector');

//
//$db   = JFactory::getDBO();
//$lang = JFactory::getLanguage()->getTag();
//$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
//$currModID = uniqid('merchantdetails');
//
//$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
//$itemIdMerchant = ($db->getErrorNum())? 0 : intval($db->loadResult());
//if ($itemIdMerchant<>0)
//	$uriMerchant.='&Itemid='.$itemIdMerchant;

$uriMerchant = COM_BOOKINGFORCONNECTOR_URIMERCHANTDETAILS;

$merchantImageUrl = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";

$merchants = BFCHelper::getMerchantsExt($tags, 0, $maxitems);
if(count($merchants) > 0){

$listName = 'Merchant Highlight list';

		$analyticsEnabled =  BFCHelper::checkAnalytics($listName) && COM_BOOKINGFORCONNECTOR_EECENABLED == 1;
		if($analyticsEnabled) {
			$allobjects = array();
			$initobjects = array();
			foreach ($merchants as $key => $value) {
				$obj = new stdClass;
				$obj->id = "" . $value->MerchantId + " - Merchant";
//				if(isset($value->GroupId) && !empty($value->GroupId)) {
//					$obj->groupid = $value->GroupId;
//				}
				$obj->name = $value->Name;
				$obj->category = $value->MainCategoryName;
				$obj->brand = $value->Name;
				$obj->position = $key;
				$initobjects[] = $obj;
			}
			$document->addScriptDeclaration('var currentMerchants = ' .json_encode($allobjects) . ';
			var initMerchant = ' .json_encode($initobjects) . ';
			' . 'callAnalyticsEEc("addImpression", initMerchant, "list","Merchant Highlight");');
		}

?>


<div id="<?php echo $carouselid; ?>" class="bookingfor_carousel" >
		<?php foreach($merchants as $mrcKey => $merchant){ ?>
		<?php 
		
			$hasSuperior = !empty($merchant->RatingSubValue);
			$rating = (int)$merchant->Rating;
			if ($rating>9 )
			{
				$rating = $rating/10;
				$hasSuperior = ($MerchantDetail->Rating%10)>0;
			} 

			$currUriMerchant = $uriMerchant. '&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name);
			$merchantDescription = BFCHelper::shorten_string(BFCHelper::getLanguage($merchant->Description, $language, null, array('bbcode'=>'bbcode', 'striptags'=>'striptags')), $descmaxchars);

			$routeMerchant = JRoute::_($currUriMerchant);
			$currMerchantImageUrl = $merchantImageUrl;
			if(!empty($merchant->DefaultImg)){
				$currMerchantImageUrl = BFCHelper::getImageUrlResized('merchant',$merchant->DefaultImg, 'small');
			}
			if(!empty($merchant->ImageData)) {
				$images = explode(",", $merchant->ImageData);
				$currMerchantImageUrl = BFCHelper::getImageUrlResized('merchant',$images[0], 'small');
			}
			$merchantNameTrack =  BFCHelper::string_sanitize($merchant->Name);
			$merchantCategoryNameTrack = BFCHelper::string_sanitize($merchant->MainCategoryName);
		?>
			<div class="bfi-bookingforconnector-merchants" >
				<div class="bfi-row" style="height:100%">
					<div class="bfi-row" >
						<div class="bfi-col-md-12"><a href="<?php echo $routeMerchant?>" class="eectrack" data-type="Merchant" data-id="<?php echo $merchant->MerchantId?>" data-index="<?php echo $mrcKey?>" data-itemname="<?php echo $merchantNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>"><img src="<?php echo $currMerchantImageUrl; ?>" class="bfi-img-responsive center-block" /></a>
						</div>
					</div>
					<div class="bfi-row" >
						<div class="bfi-col-md-12 bfi-item-title" style="padding: 10px!important;">
						<a class="eectrack" href="<?php echo $routeMerchant ?>" id="nameAnchor<?php echo $merchant->MerchantId?>" data-type="Merchant" data-id="<?php echo $merchant->MerchantId?>" data-index="<?php echo $mrcKey?>" data-itemname="<?php echo $merchantNameTrack; ?>" data-brand="<?php echo $merchantNameTrack?>" data-category="<?php echo $merchantCategoryNameTrack; ?>"><?php echo  $merchant->Name ?></a> 
						<?php if($rating > 0){ ?>
								<span class="bfi-item-rating">
									<?php for($i = 0; $i < $rating; $i++) { ?>
										<i class="fa fa-star"></i>
									<?php } ?>	             
								</span>
						<?php } ?>
						<?php if ($hasSuperior) { ?>
							&nbsp;S
						<?php } ?>
						</div>
					</div>
					<div class="bfi-row bfi-hide" >
						<div class="bfi_merchant-description bfi-col-md-12" style="padding-left: 10px!important;padding-right: 10px!important;">
							<a href="<?php echo $routeMerchant ?>" id="nameAnchor<?php echo $merchant->MerchantId?>" class="eectrack" data-type="Merchant" data-id="<?php echo $merchant->MerchantId?>" data-index="<?php echo $mrcKey?>" data-itemname="<?php echo $merchantNameTrack; ?>" data-brand="<?php echo $merchantNameTrack?>" data-category="<?php echo $merchantCategoryNameTrack; ?>"><?php echo  $merchant->Name ?></a> 
						</div>
					</div>
					<div class="bfi-row" >
						<div class="bfi_merchant-description bfi-col-md-12" style="padding: 10px!important;" id="descr<?php echo $merchant->MerchantId?>"><?php echo $merchantDescription ;?></div>
					</div>
					<div class="bfi-row secondarysection">
						<div class="bfi-col-md-1  secondarysectionitem">
							&nbsp;
						</div>
						<div class="bfi-col-md-11 secondarysectionitem" style="padding: 10px!important;">
								<a href="<?php echo $routeMerchant?>" class="bfi-btn bfi-pull-right eectrack" data-type="Merchant" data-id="<?php echo $merchant->MerchantId?>" data-index="<?php echo $mrcKey?>" data-itemname="<?php echo $merchantNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"  data-category="<?php echo $merchantCategoryNameTrack; ?>"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_OFFER_DETAILS') ?></a>
						</div>
					</div>
				</div>
			</div>		
		<?php } ?>
</div>
<script type="text/javascript">
<!--
jQuery(document).ready(function() {
	var ncolslick = <?php echo $cols ?>;
	if(jQuery('#<?php echo $carouselid; ?>').width()<400){
		ncolslick = 2;
	}
	if(jQuery('#<?php echo $carouselid; ?>').width()<200){
		ncolslick = 1;
	}

	jQuery('#<?php echo $carouselid; ?>').slick({
		dots: false,
		draggable: false,
		arrows: true,
		infinite: true,
		slidesToShow: ncolslick,
		slidesToScroll: 1,
	});
	jQuery('#<?php echo $carouselid; ?>').on('afterChange', function(event, slick, currentSlide){
		var maxHeight = 0;
		jQuery('.bfi-bookingforconnector-merchants', jQuery(slick.$slider ))
		.each(function() { maxHeight = Math.max(maxHeight, jQuery(this).height()); })
		.height(maxHeight);
	});
});
//-->
</script>
<?php } ?>
