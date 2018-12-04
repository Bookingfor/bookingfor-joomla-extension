<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

if(!empty( COM_BOOKINGFORCONNECTOR_CRAWLER )){
	$listCrawler = json_decode(COM_BOOKINGFORCONNECTOR_CRAWLER , true);
	foreach( $listCrawler as $key=>$crawler){
	if (preg_match('/'.$crawler['pattern'].'/', $_SERVER['HTTP_USER_AGENT'])) return;
	}
	
}

$pathbase = JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;

//$db   = JFactory::getDBO();
//$language = $language;
//$uri  = 'index.php?option=com_bookingforconnector&view=search';
//
//$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri .'%' ) .' AND language='. $db->Quote($language) .' AND published = 1 LIMIT 1' );
//
//$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
//
//if ($itemId<>0)
//	$formAction = JRoute::_('index.php?Itemid='.$itemId.'' );
//else
//	$formAction = JRoute::_($uri);

$formAction = (isset($_SERVER['HTTPS']) ? "https" : "http") . ':' ."//$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

$currencyclass = bfi_get_currentCurrency();

$paymodes_text = array('freecancellation' => JTEXT::_('MOD_BOOKINGFORSEARCHFILTER_FREECANCELLATION'),
						'freepayment' => JTEXT::_('MOD_BOOKINGFORSEARCHFILTER_NOPREPAYMENT'),
						'freecc' => JTEXT::_('MOD_BOOKINGFORSEARCHFILTER_NOCC')                             
					);
$meals_text = array('ai' => JTEXT::_('COM_BOOKINGFORCONNECTOR_MEAL_ALLINCLUSIVE'),
						'fb' => JTEXT::_('COM_BOOKINGFORCONNECTOR_MEAL_FULLBOARD'),
						'hb' => JTEXT::_('COM_BOOKINGFORCONNECTOR_MEAL_HALFBOARD'),   
						'bb' => JTEXT::_('COM_BOOKINGFORCONNECTOR_MEAL_BREAKFAST')                                 
					);
$rating_text = array('null' => JTEXT::_('MOD_BOOKINGFORSEARCHFILTER_UNRATED'),
						'0' => JTEXT::_('MOD_BOOKINGFORSEARCHFILTER_UNRATED'),
						'1' => JTEXT::_('MOD_BOOKINGFORSEARCHFILTER_STARS1'),   
						'2' => JTEXT::_('MOD_BOOKINGFORSEARCHFILTER_STARS2'),   
						'3' => JTEXT::_('MOD_BOOKINGFORSEARCHFILTER_STARS3'),   
						'4' => JTEXT::_('MOD_BOOKINGFORSEARCHFILTER_STARS4'),   
						'5' => JTEXT::_('MOD_BOOKINGFORSEARCHFILTER_STARS5'),   
						'6' => JTEXT::_('MOD_BOOKINGFORSEARCHFILTER_STARS6'),   
						'7' => JTEXT::_('MOD_BOOKINGFORSEARCHFILTER_STARS7'),   
						'8' => JTEXT::_('MOD_BOOKINGFORSEARCHFILTER_STARS8'),   
						'9' => JTEXT::_('MOD_BOOKINGFORSEARCHFILTER_STARS9'),   
						'10' => JTEXT::_('MOD_BOOKINGFORSEARCHFILTER_STARS10'),                          
					);

$avg_text = array('-1' => JTEXT::_('MOD_BOOKINGFORSEARCHFILTER_UNRATED'),
						'0' => JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION_0'),
						'1' => JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION_1'),   
						'2' => JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION_2'),
						'3' => JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION_3'),
						'4' => JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION_4'),
						'5' => JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION_5'),  
						'6' => JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION_6'),
						'7' => JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION_7'),  
						'8' =>JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION_8'), 
						'9' => JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION_9'),  
						'10' => JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION_10'),                                
					);

$pars = BFCHelper::getSearchMerchantParamsSession();

$merchantCategoryId = isset($pars['merchantCategoryId']) ? $pars['merchantCategoryId'] : '';

$searchid = isset($_GET['searchid']) ? $_GET['searchid'] : '';

$filtersMerchantsServices = array();
$filtersMerchantsTags = array();
$filtersMerchantsZones = array();
$filtersMerchantsRating = array();
$filtersMerchantsAvg = array();
$filtersMerchantsCategories = array();

$filterscount = BFCHelper::getEnabledFilterSearchMerchantParamsSession();
$firstFilters = BFCHelper::getFirstFilterSearchMerchantParamsSession();

if (!empty($firstFilters) ) {
	foreach ($firstFilters as $filter){
		switch ($filter->Name) {
			case 'mrcservices':
				if(!empty( $filter->Items )){
					foreach ($filter->Items as $item ) {
					   $filtersMerchantsServices[$item->Id] = $item;
					}
				}
				break; 
			case 'mrctags':
				if(!empty( $filter->Items )){
					foreach ($filter->Items as $item ) {
					   $filtersMerchantsTags[$item->Id] = $item;
					}
				}
				break; 
			case 'mrccategory':
				if(!empty( $filter->Items )){
					foreach ($filter->Items as $item ) {
					   $filtersMerchantsCategories[$item->Id] = $item;
					}
				}
				break; 
			case 'mrczones':
				if(!empty( $filter->Items )){
					foreach ($filter->Items as $item ) {
					   $filtersMerchantsZones[$item->Id] = $item;
					}
				}
				break; 
			case 'mrcrating':
				if(!empty( $filter->Items )){
					$allItems = $filter->Items;
					usort($allItems, function($a, $b)
					{
						return strcmp($b->Id,$a->Id);
					});
					foreach ($allItems as $item ) {
						$rating = intval($item->Name);
						if (!isset($item->Sup)) {
							$item->Sup = "";
						}
						if ($rating>9 )
						{
							if(($rating%10)>0){
								$item->Sup = "S";
							}
							$rating = $rating/10;
							$item->Name = intval($rating);
						}
					   $filtersMerchantsRating[$item->Id] = $item;
					}
				}
				break; 
			case 'mrcavg':
				if(isset( $filter->Items )){
					$allItems = $filter->Items;
					usort($allItems, function($a, $b)
					{
						return ((int)$b->Id > (int)$a->Id);
					});
					foreach ($allItems as $item ) {
					   $filtersMerchantsAvg[$item->Id] = $item;
					}
				}
				break; 
		} 
	}
}

//order filtersMerchantsRating



$filtersMerchantsServicesCount = array();
$filtersMerchantsTagsCount = array();
$filtersMerchantsZonesCount = array();
$filtersMerchantsRatingCount = array();
$filtersMerchantsAvgCount = array();
$filtersMerchantsCategoriesCount = array();

if(!empty( $filterscount )){
	foreach ($filterscount as $filter){
		switch ($filter->Name) {
			case 'mrcservices':
				if(!empty( $filter->Items )){
					foreach ($filter->Items as $item ) {
					   $filtersMerchantsServicesCount[$item->Id] = $item->Count;
					}
				}
				break; 
			case 'mrctags':
				if(!empty( $filter->Items )){
					foreach ($filter->Items as $item ) {
					   $filtersMerchantsTagsCount[$item->Id] = $item->Count;
					}
				}
				break; 
			case 'mrccategory':
				if(!empty( $filter->Items )){
					foreach ($filter->Items as $item ) {
					   $filtersMerchantsCategoriesCount[$item->Id] = $item->Count;
					}
				}
				break; 				
			case 'mrczones':
				if(!empty( $filter->Items )){
					foreach ($filter->Items as $item ) {
					   $filtersMerchantsZonesCount[$item->Id] = $item->Count;
					}
				}
				break; 
			case 'mrcrating':
				if(!empty( $filter->Items )){
					foreach ($filter->Items as $item ) {
					   $filtersMerchantsRatingCount[$item->Id] = $item->Count;
					}
				}
				break; 
			case 'mrcavg':
				if(isset( $filter->Items )){
					foreach ($filter->Items as $item ) {
					   $filtersMerchantsAvgCount[$item->Id] = $item->Count;
					}
				}
				break; 
		} 
	}	
}



$filtersSelected = BFCHelper::getFilterSearchMerchantParamsSession();

$filtersRatingValue = "";
$filtersAvgValue = "";
$filtersMerchantsServicesValue = "";
$filtersZonesValue = "";
$filtersTagsValue = "";
$filtersCategoriesValue = "";

if (isset($filtersSelected)) {
	$filtersRatingValue = isset( $filtersSelected[ 'rating' ] ) ? $filtersSelected[ 'rating' ] : "";
	$filtersAvgValue =  isset( $filtersSelected[ 'avg' ] ) ? $filtersSelected[ 'avg' ] : "";
	$filtersMerchantsServicesValue = ! empty( $filtersSelected[ 'merchantsservices' ] ) ? $filtersSelected[ 'merchantsservices' ] : "";
	$filtersZonesValue = ! empty( $filtersSelected[ 'zones' ] ) ? $filtersSelected[ 'zones' ] : "";
	$filtersTagsValue = ! empty( $filtersSelected[ 'tags' ] ) ? $filtersSelected[ 'tags' ] : "";
	$filtersCategoriesValue = ! empty( $filtersSelected[ 'merchantscategories' ] ) ? $filtersSelected[ 'merchantscategories' ] : "";
}

$filtersRating = array();
$filtersAvg = array();
$filtersZones = array();
$filtersTags = array();
$filtersCategories = array();
$filtersRatingCount = array();
$filtersAvgCount = array();
$filtersZonesCount = array();
$filtersTagsCount = array();
$filtersCategoriesCount = array();


	$filtersRating = $filtersMerchantsRating;
	$filtersAvg = $filtersMerchantsAvg;
	$filtersZones = $filtersMerchantsZones;
	$filtersTags = $filtersMerchantsTags;
	$filtersCategories = $filtersMerchantsCategories;
	$filtersRatingCount = $filtersMerchantsRatingCount;
	$filtersAvgCount = $filtersMerchantsAvgCount;
	$filtersZonesCount = $filtersMerchantsZonesCount;
	$filtersTagsCount = $filtersMerchantsTagsCount;
	$filtersCategoriesCount = $filtersMerchantsCategoriesCount;
$minvaluetoshow=1;

?>
<div class="bfi-searchfilter">
<h3><?php echo JText::_('MOD_BOOKINGFORSEARCHFILTER_TITLE') ?></h3>

<form action="<?php echo $formAction; ?>" method="post" id="searchMerchantformfilter" name="searchMerchantformfilter" >
	<input type="hidden" value="<?php echo $searchid ?>" name="searchid">
	<input type="hidden" value="0" name="limitstart">
	<input type="hidden" value="0" name="newsearch">
	<input type="hidden" name="filter_order" class="filterOrder" id="filter_order_filter" value="stay">
	<input type="hidden"  name="filter_order_Dir" class= "filterOrderDirection"id="filter_order_Dir_filter" value="asc">
<div id="bfi-filtertoggleMerchant">
	<?php if (isset($filtersCategories) &&  is_array($filtersCategories) && count($filtersCategories)>0) { 
	$filtersValueArr = explode ("|",$filtersCategoriesValue);
	?>
		<div>
			<div class="bfi-option-title bfi-option-active"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_TIPOLOGY') ?></div>
			<div class="bfi-filteroptions">
				<?php foreach ($filtersCategories as $itemId => $item){?>
					<a href="javascript:void(0);" rel="<?php echo $itemId ?>" rel1="merchantscategories" class="<?php echo (in_array(strval($itemId), $filtersValueArr, true))?"bfi-filter-active":""; ?>">
					<span class="bfi-filter-label"><?php echo $item->Name ?></span>
					<span class="bfi-filter-count"><?php echo BFCHelper::bfi_returnFilterCount($item->Count, $filtersCategoriesCount, $itemId) ?></span>
					</a>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
	<?php if (isset($filtersRating) &&  is_array($filtersRating) && count($filtersRating)>$minvaluetoshow) { 
	$filtersValueArr = explode ("|",$filtersRatingValue);
	?>
		<div>
			<div class="bfi-option-title bfi-option-active"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCHFILTER_STARRATING') ?></div>
			<div class="bfi-filteroptions">
				<?php foreach ($filtersRating as $itemId => $item){?>
					<a href="javascript:void(0);" rel="<?php echo $itemId ?>" rel1="rating" class="<?php echo (in_array(strval($itemId), $filtersValueArr, true))?"bfi-filter-active":""; ?>">
					<span class="bfi-filter-label"><?php echo $rating_text[$item->Name] ?> <?php echo $item->Sup ?></span>
					<span class="bfi-filter-count"><?php echo BFCHelper::bfi_returnFilterCount($item->Count, $filtersRatingCount, $itemId) ?></span>
					</a>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
	<?php if (isset($filtersAvg) &&  is_array($filtersAvg) && count($filtersAvg)>$minvaluetoshow) { 
	$filtersValueArr = explode ("|",$filtersAvgValue);
	?>
		<div>
			<div class="bfi-option-title bfi-option-active"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCHFILTER_GUESTRATING') ?> </div>
			<div class="bfi-filteroptions">
				<?php foreach ($filtersAvg as $itemId => $item){?>
					<a href="javascript:void(0);" rel="<?php echo $itemId ?>" rel1="avg" class="<?php echo (in_array(strval($itemId), $filtersValueArr, true))?"bfi-filter-active":""; ?>">
					<span class="bfi-filter-label"><?php echo $avg_text[$item->Name] ?></span>
					<span class="bfi-filter-count"><?php echo BFCHelper::bfi_returnFilterCount($item->Count, $filtersAvgCount, $itemId) ?></span>
					</a>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
	<?php if (isset($filtersMerchantsServices) &&  is_array($filtersMerchantsServices) && count($filtersMerchantsServices)>$minvaluetoshow) { 
	$filtersValueArr = explode ("|",$filtersMerchantsServicesValue);
	?>
		<div>
			<div class="bfi-option-title bfi-option-active"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCHFILTER_SERVICESMERCHANTS') ?></div>
			<div class="bfi-filteroptions">
				<?php foreach ($filtersMerchantsServices as $itemId => $item){?>
					<a href="javascript:void(0);" rel="<?php echo $itemId ?>" rel1="merchantsservices" class="<?php echo (in_array(strval($itemId), $filtersValueArr, true))?"bfi-filter-active":""; ?>">
					<span class="bfi-filter-label"><?php echo $item->Name ?></span>
					<span class="bfi-filter-count"><?php echo BFCHelper::bfi_returnFilterCount($item->Count, $filtersMerchantsServicesCount, $itemId)  ?></span>
					</a>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
	<?php if (isset($filtersZones) &&  is_array($filtersZones) && count($filtersZones)>$minvaluetoshow)  { 
	$filtersValueArr = explode ("|",$filtersZonesValue);
	?>
		<div>
			<div class="bfi-option-title bfi-option-active"><?php echo JText::_('MOD_BOOKINGFORSEARCHFILTER_LOCATION') ?></div>
			<div class="bfi-filteroptions">
				<?php foreach ($filtersZones as $itemId => $item){?>
					<a href="javascript:void(0);" rel="<?php echo $itemId ?>" rel1="zones" class="<?php echo (in_array(strval($itemId), $filtersValueArr, true))?"bfi-filter-active":""; ?>">
					<span class="bfi-filter-label"><?php echo $item->Name ?></span>
					<span class="bfi-filter-count"><?php echo BFCHelper::bfi_returnFilterCount($item->Count, $filtersZonesCount, $itemId) ?></span>
					</a>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
	<?php if (isset($filtersTags) &&  is_array($filtersTags) && count($filtersTags)>0) { 
	$filtersValueArr = explode ("|",$filtersTagsValue);
	?>
		<div>
			<div class="bfi-option-title bfi-option-active"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCHFILTER_MERCHANTGROUPS') ?></div>
			<div class="bfi-filteroptions">
				<?php foreach ($filtersTags as $itemId => $item){?>
					<a href="javascript:void(0);" rel="<?php echo $itemId ?>" rel1="tags" class="<?php echo (in_array(strval($itemId), $filtersValueArr, true))?"bfi-filter-active":""; ?>">
					<span class="bfi-filter-label"><?php echo $item->Name ?></span>
					<span class="bfi-filter-count"><?php echo BFCHelper::bfi_returnFilterCount($item->Count, $filtersTagsCount, $itemId) ?></span>
					</a>
				<?php } ?>
			</div>
		</div>
	<?php } ?>

</div>
<div class="bfi-clearboth"></div>
	<input type="hidden" name="filters[rating]" id="filtersRatingsHidden" value="<?php echo $filtersRatingValue ?>" />
	<input type="hidden" name="filters[avg]" id="filtersAvgHidden" value="<?php echo $filtersAvgValue ?>" />
	<input type="hidden" name="filters[merchantsservices]" id="filtersMerchantsServicesHidden" value="<?php echo $filtersMerchantsServicesValue ?>" />
	<input type="hidden" name="filters[zones]" id="filtersZonesHidden" value="<?php echo $filtersZonesValue ?>" />
	<input type="hidden" name="filters[tags]" id="filtersTagsHidden" value="<?php echo $filtersTagsValue ?>" />
	<input type="hidden" name="filters[merchantscategories]" id="filtersMerchantsCategoriesHidden" value="<?php echo $filtersCategoriesValue ?>" />
	
</form>
</div>

<script type="text/javascript">
function bfi_applyfilterMerchantdata(){ 		

	jQuery(".bfi-searchfilter h3").click(function(){
		jQuery(this).toggleClass("bfi-searchfilter-active");
		jQuery("#bfi-filtertoggleMerchant").slideToggle("normal",function() {
			if (jQuery.prototype.masonry){
				jQuery('.main-siderbar, .main-siderbar1').masonry('reload');
			}
		});
	});

	jQuery("#bfi-filtertoggleMerchant .bfi-option-title ").click(function(){
		jQuery(this).toggleClass("bfi-option-active");
		jQuery(this).next("div").stop('true','true').slideToggle("normal",function() {
			if (jQuery.prototype.masonry){
				jQuery('.main-siderbar, .main-siderbar1').masonry('reload');
			}
		});
	});

	jQuery('.bfi-filter-label').bind('mouseenter', function(){
		var $this = jQuery(this);
		var divWidthBefore = $this.width();
		$this.css('width','auto');
		$this.css('white-space','nowrap');
		var divWidth = $this.width();
		$this.width(divWidthBefore+1);
		$this.css('white-space','normal');
		if(divWidthBefore< divWidth && !$this.attr('title') ){
			$this.attr('title', $this.text());
			$this.tooltip({
			position : { my: 'center bottom', at: 'center top-10' },
			tooltipClass: 'bfi-tooltip bfi-tooltip-top '
			});
			$this.tooltip("open");
		}
	});
	
	jQuery('#searchMerchantformfilter').submit(function( event ) {
				try
				{
					jQuery("#filter_order_filter").val(jQuery("#bookingforsearchFilterForm input[name='filter_order']").val());					
					jQuery("#filter_order_Dir_filter").val(jQuery("#bookingforsearchFilterForm input[name='filter_order_Dir']").val());	
					
				}
				catch (e)
				{
				}
				jQuery("input[name^='filters\\[']").val("");

				jQuery("a.bfi-filter-active").each(function(){
					currValue = jQuery(this).attr("rel");
					currHiddenInput = jQuery("input[name='filters\\["+jQuery(this).attr("rel1")+"\\]']");
					currHiddenInput.val(currHiddenInput.val() + "|" + currValue);
				});
				jQuery("input[name^='filters\\[']").each(function(){
					jQuery(this).val(jQuery(this).val().substr(1));
				});
				jQuery('body').block({
					message:"",
						overlayCSS: {backgroundColor: '#ffffff', opacity: 0.7}  
				});
		});

			jQuery('.bfi-filteroptions a').on('click',function() {
<?php 
if(COM_BOOKINGFORCONNECTOR_GAENABLED == 1 && !empty(COM_BOOKINGFORCONNECTOR_GAACCOUNT) && COM_BOOKINGFORCONNECTOR_EECENABLED == 1) {
?>
//				currValue = jQuery(this).attr("rel");
				currValue = jQuery(this).find(".bfi-filter-label").first().text(); 
				listname = jQuery(this).attr("rel1");
				currAction = jQuery(this).hasClass("bfi-filter-active")? "Remove":"Add";
				callAnalyticsEEc("", "", listname + "|" + currValue, null, currAction, "Search Filters");
<?php 
}
?>
				jQuery(this).toggleClass("bfi-filter-active");
				jQuery(this).closest('form').submit();
			});
	
			if (jQuery.prototype.masonry){
				jQuery('.main-siderbar, .main-siderbar1').masonry('reload');
			}
}

jQuery(document).ready(function() {
	bfi_applyfilterMerchantdata();
});  
</script>
<div class="bfi-clearboth"></div>
