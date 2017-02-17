<?php 
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$document 	= JFactory::getDocument();
$language 	= $document->getLanguage();

$cols = $params->get('itemspage', 4);
$carouselid = uniqid();
		$config = JComponentHelper::getParams('com_bookingforconnector');


$db   = JFactory::getDBO();
$lang = JFactory::getLanguage()->getTag();
$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
$currModID = uniqid('merchantdetails');

$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
$itemIdMerchant = ($db->getErrorNum())? 0 : intval($db->loadResult());

$merchantImageUrl = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";
$merchantImagePath = BFCHelper::getImageUrlResized('merchant', "[img]",'small');
$merchantImagePathError = BFCHelper::getImageUrl('merchant', "[img]",'small');
if ($itemIdMerchant<>0)
	$formAction = JRoute::_('index.php?Itemid='.$itemIdMerchant );
else
	$formAction = JRoute::_($uriMerchant);


$tags = $params->get('tags');


$maxitems = $params->get('maxitems', 10);

$merchants = BFCHelper::getMerchantsExt($tags, 0, $maxitems);
if(count($merchants) > 0):

$listName = 'Merchant Highlight list';

		$analyticsEnabled =  BFCHelper::checkAnalytics($listName) && $config->get('eecenabled', 0) == 1;
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
			' . 'callAnalyticsEEc("addImpression", initMerchant, "list","MerchantHighlight");');
		}

?>


<div id="<?php echo $carouselid; ?>" class="bookingfor_carousel" >
		<?php foreach($merchants as $mrcKey => $merchant): ?>
		<?php 
		
			$rating = $merchant->Rating;

			$currUriMerchant = $uriMerchant. '&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name);

			if ($itemIdMerchant<>0)
				$currUriMerchant.='&Itemid='.$itemIdMerchant;
			
			$routeMerchant = JRoute::_($currUriMerchant);
			$currMerchantImageUrl = $merchantImageUrl;
			if(!empty($merchant->DefaultImg)){
				$currMerchantImageUrl = BFCHelper::getImageUrlResized('merchant',$merchant->DefaultImg, 'small');
			}
			$urls = array();
			if(!empty($merchant->ImageData)) {
				$images = explode(",", $merchant->ImageData);
				$currMerchantImageUrl = BFCHelper::getImageUrlResized('merchant',$images[0], 'small');
				foreach($images as $i => $url) {
					if(!empty($url)) {
						$imgLogo = str_replace("[img]", $url, $merchantImagePath);
						$urls[] = '<div class="item ' . ($i == 0 ? 'active' : '') .'><img src="$imgLogo"></div>';
					}
				}
			}
		?>
			<div class="com_bookingforconnector-item-col" >
				<div class="com_bookingforconnector-search-merchant com_bookingforconnector-item  <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>" style="height:100%">
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>" >
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12"><a href="<?php echo $routeMerchant?>" class="eectrack"  data-type="Merchant"  data-id="<?php echo $merchant->MerchantId?>" data-index="<?php echo $mrcKey?>" data-itemname="<?php echo $merchant->Name; ?>" data-brand="<?php echo $merchant->Name; ?>" data-category="<?php echo $merchant->MainCategoryName; ?>"><img src="<?php echo $currMerchantImageUrl; ?>" class="img-responsive center-block" /></a>
						</div>
					</div>
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>" >
						<div class="com_bookingforconnector-search-merchant-details <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?><?php //echo $rating > 0 ? "4" : "12" ?>12" style="padding: 10px!important;">
						<a class="com_bookingforconnector-search-merchant-name-anchor eectrack" style="font-weight:bold;color:black; font-size: 16px; color:#08c;" href="<?php echo $routeMerchant ?>" id="nameAnchor<?php echo $merchant->MerchantId?>"  data-type="Merchant"  data-id="<?php echo $merchant->MerchantId?>" data-index="<?php echo $mrcKey?>" data-itemname="<?php echo $merchant->Name; ?>" data-brand="<?php echo $merchant->Name; ?>" data-category="<?php echo $merchant->MainCategoryName; ?>"><?php echo  $merchant->Name ?></a> 
							<!-- <label class="com_bookingforconnector-search-merchant-name-anchor" style="font-weight:bold; color:#08c; font-size:14px;text-transform: uppercase;width: 100%;"><?php echo $merchant->MainCategoryName?> -->
						<?php if($rating > 0): ?>
								<span class="com_bookingforconnector-search-merchant-rating com_bookingforconnector-item-rating">
									<?php for($i = 0; $i < $rating; $i++) { ?>
										<i class="fa fa-star"></i>
									<?php } ?>	             
								</span>
						<?php endif; ?></label>
						</div>
					</div>
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> hide" >
						<div class="com_bookingforconnector-merchant-description <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12" style="padding-left: 10px!important;padding-right: 10px!important;">
							<a class="com_bookingforconnector-search-merchant-name-anchor" style="font-weight:bold;color:black; font-size: 16px;" href="<?php echo $routeMerchant ?>" id="nameAnchor<?php echo $merchant->MerchantId?>"><?php echo  $merchant->Name ?></a> 
						</div>
					</div>
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>" >
						<div class="com_bookingforconnector-merchant-description <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12" style="padding: 10px!important;" id="descr<?php echo $merchant->MerchantId?>"><?php echo BFCHelper::shorten_string($merchant->Description, $params->get('desc_maxchars', 300));?></div>
					</div>
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> secondarysection">
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>1  secondarysectionitem">
							&nbsp;
						</div>
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>11 secondarysectionitem" style="padding: 10px!important;">
								<a href="<?php echo $routeMerchant?>" class="pull-right eectrack"  data-type="Merchant"  data-id="<?php echo $merchant->MerchantId?>" data-index="<?php echo $mrcKey?>" data-itemname="<?php echo $merchant->Name; ?>" data-brand="<?php echo $merchant->Name; ?>"  data-category="<?php echo $merchant->MainCategoryName; ?>"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_OFFER_DETAILS') ?></a>
						</div>
					</div>
				</div>
			</div>		
		<?php 
		endforeach; ?>
</div>
<script type="text/javascript">
<!--
	jQuery(document).ready(function() {
		jQuery('#<?php echo $carouselid; ?>').slick({
			dots: false,
			draggable: false,
			arrows: true,
			infinite: true,
			slidesToShow: <?php echo $cols ?>,
			slidesToScroll: 1,
		});
//		jQuery('#<?php echo $carouselid; ?>').on('afterChange', function(event, slick, currentSlide){
//			var maxHeight = 0;
//			jQuery('.com_bookingforconnector-item-col', jQuery(slick.$slider ))
//			.each(function() { maxHeight = Math.max(maxHeight, jQuery(this).height()); })
//			.height(maxHeight);
//		});
});
//-->
</script>
<?php endif; ?>
