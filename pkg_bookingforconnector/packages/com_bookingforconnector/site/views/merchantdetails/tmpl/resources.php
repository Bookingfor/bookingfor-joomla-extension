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
$showdata = $config->get('showdata', 1);

$merchant = $this->item;
$sitename = $this->sitename;
$language = $this->language;
//$this->document->setTitle($this->item->Name);
//$this->document->setDescription( BFCHelper::getLanguage($this->item->Description, $this->language));
$this->document->setTitle(sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_MERCHANTDETAILS_RESOURCES_TITLE'),$merchant->Name,$sitename));

$total = $this->pagination->total;


$db   = JFactory::getDBO();
$uri  = 'index.php?option=com_bookingforconnector&view=resource';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
//$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
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

$resourceImageUrl = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";

$resourceLogoPath = BFCHelper::getImageUrlResized('resources',"[img]", 'medium');
$resourceLogoPathError = BFCHelper::getImageUrl('resources',"[img]", 'medium');

$merchantLogoPath = BFCHelper::getImageUrlResized('merchant',"[img]", 'logomedium');
$merchantLogoPathError = BFCHelper::getImageUrl('merchant',"[img]", 'logomedium');

$merchantImageUrl = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";
$merchantLogoUrl = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";

$action = htmlspecialchars(JFactory::getURI()->toString());
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
//$searchid =  $this->params['searchid'];
$searchid ="";
$activePrice="";
$activeOffer="";
$hidesort = "";
//if(!empty($this->hidesort)) {
//	$hidesort = ' style="display:none;"';
//}
$isFromSearch =false;
?>
<script type="text/javascript">
<!--
var cultureCode = '<?php echo $language ?>';
//-->
</script>
<div id="com_bookingforconnector-items-container-wrapper">
	<div class="com_bookingforconnector-items-container">
	<?php if ($this->items != null): ?>
		<h1 class="com_bookingforconnector_search-title"><?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEW_MERCHANTDETAILS_RESOURCES_TITLE_TOTAL'), $total) ?></h1>
	<div class="com_bookingforconnector-search-menu">
		<!--
		<form action="<?php echo $action; ?>" method="post" name="bookingforsearchForm" id="bookingforsearchFilterForm">
			<fieldset class="filters">
				<input type="hidden" class="filterOrder" name="filter_order" value="<?php echo $listOrder ?>" />
				<input type="hidden" class="filterOrderDirection" name="filter_order_Dir" value="<?php echo $listDirn ?>" />
				<input type="hidden" name="searchid" value="<?php echo $searchid?>" />
				<input type="hidden" name="limitstart" value="0" />
			</fieldset>
		</form>
		<div class="com_bookingforconnector-results-sort">
			<span class="com_bookingforconnector-sort-help"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_LABEL')?>:</span>
			<span class="com_bookingforconnector-sort-item<?php echo $activePrice; ?>" rel="stay|asc" <?php echo $hidesort ?>><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_PRICE'); ?></span>
			<span class="com_bookingforconnector-sort-item<?php echo $activeOffer; ?>" rel="offer|asc" <?php echo $hidesort ?>><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_OFFERS'); ?></span>
		</div>
		-->
		<div class="com_bookingforconnector-view-changer">
			<div id="list-view" class="com_bookingforconnector-view-changer-list active"><i class="fa fa-list"></i> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_TAB_LIST') ?></div>
			<div id="grid-view" class="com_bookingforconnector-view-changer-grid"><i class="fa fa-th-large"></i> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_TAB_GRID') ?></div>
		</div>
	</div>

	<div class="clearfix"></div>
	
	<div class="com_bookingforconnector-search-resources com_bookingforconnector-items <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> com_bookingforconnector-list">
		<?php
		// assign the current resource to a property so it will be available inside template 'resource'
		$results = $this->items;
		?>

<?php foreach ($results as $key => $result):?>
	<?php 
		$resourceImageUrl = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";
		$merchantLogoUrl = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s1.jpeg";
	$resourceName = "";
	$resourceLat = null;
	$resourceLon = null;
	if($isFromSearch){
		$resourceName = BFCHelper::getLanguage($result->ResName, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
		$resourceLat = $result->ResLat;
		$resourceLon = $result->ResLng;
	}else{
		$resourceName = BFCHelper::getLanguage($result->Name, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
		$resourceLat = $result->XPos;
		$resourceLon = $result->YPos;
		$result->MrcName = $result->MerchantName;
		$result->ResRating = $result->Rating;
		$result->MinPaxes = $result->MinCapacityPaxes;
		$result->MaxPaxes= $result->MaxCapacityPaxes;
		$result->Price = 0;
		$result->TotalPrice = 0;
		$onlystay=false;
	}
	
	$showResourceMap = (($resourceLat != null) && ($resourceLon !=null));
	
	$currUriresource = $uri.'&resourceId=' . $result->ResourceId . ':' . BFCHelper::getSlug($resourceName);

	if ($itemId<>0)
		$currUriresource.='&Itemid='.$itemId;
	
	$resourceRoute = JRoute::_($currUriresource);
	$routeRating = JRoute::_($currUriresource.'&layout=rating');
	$routeInfoRequest = JRoute::_($currUriresource.'&layout=inforequestpopup&tmpl=component');
	$routeRapidView = JRoute::_($currUriresource.'&layout=rapidview&tmpl=component');

	$routeMerchant = "";
	if($isportal){
		$currUriMerchant = $uriMerchant. '&merchantId=' . $result->MerchantId . ':' . BFCHelper::getSlug($result->MrcName);
		if ($itemIdMerchant<>0)
			$currUriMerchant.= '&Itemid='.$itemIdMerchant;
		$routeMerchant = JRoute::_($currUriMerchant);
	}

	$bookingType = 0;
	$availability = 0;
	$ribbonofferdisplay = "hidden";
	$classofferdisplay = "";
	$stay = new StdClass;
		$discount = 0;
		$current_discount = 0;

	if($isFromSearch){
		$availability = $result->Availability;
		$bookingType = $result->BookingType;
		if (($result->Price < $result->TotalPrice) || $result->IsOffer){
			$ribbonofferdisplay = "";
			$classofferdisplay = "com_bookingforconnector_highlight";
			
		}
		if (!empty($result->RateplanId)){
			 $resourceRoute .= "?pricetype=" . $result->RateplanId;
		}
	}
	
		$rating = $result->ResRating;
		if ($rating>10 )
		{
			$rating = $rating/10;
		}

	if(!empty($result->ImageUrl)){
		$resourceImageUrl = BFCHelper::getImageUrlResized('resources',$result->ImageUrl, 'medium');
	}

	$merchantLogoUrl = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s1.jpeg";
	if(!empty($result->LogoUrl)){
		$merchantLogoUrl = BFCHelper::getImageUrlResized('merchant',$result->LogoUrl, 'logomedium');
	}
?>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 com_bookingforconnector-item-col" >
			<div class="com_bookingforconnector-search-resource com_bookingforconnector-item <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
				<div class="mrcgroup" id="bfcmerchantgroup<?php echo $result->ResourceId; ?>"><span class="bfcmerchantgroup"></span></div>
				<div class="com_bookingforconnector-item-details  <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>" >
						<div class="com_bookingforconnector-search-merchant-carousel com_bookingforconnector-item-carousel <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4">
							<div id="com_bookingforconnector-search-resource-carousel<?php echo $result->ResourceId; ?>" class="carousel" data-ride="carousel" data-interval="false">
								<div class="carousel-inner" role="listbox">
										<div class="item active"><img src="<?php echo $resourceImageUrl; ?>"></div>
								</div>
								<?php if($isportal): ?>
									<a class="com_bookingforconnector-search-resource-logo com_bookingforconnector-logo-grid eectrack" href="<?php echo $routeMerchant?>" id="merchantname<?php echo $result->ResourceId?>" data-id="<?php echo $result->MerchantId?>" data-index="<?php echo $key?>" data-type="Merchant" data-itemname="<?php echo $merchant->Name; ?>" data-category="<?php echo $merchant->MainCategoryName; ?>" data-brand="<?php echo $merchant->Name; ?>"><div class="containerlogo"><img class="com_bookingforconnector-logo" id="com_bookingforconnector-logo-grid-<?php echo $result->ResourceId?>" src="<?php echo $merchantLogoUrl; ?>" /></div></a>
								<?php endif; ?>
								<a class="left carousel-control" href="#com_bookingforconnector-search-resource-carousel<?php echo $result->ResourceId; ?>" role="button" data-slide="prev">
									<i class="fa fa-chevron-circle-left"></i>
								</a>
								<a class="right carousel-control" href="#com_bookingforconnector-search-resource-carousel<?php echo $result->ResourceId; ?>" role="button" data-slide="next">
									<i class="fa fa-chevron-circle-right"></i>
								</a>
							</div>
						</div>
						<div class="com_bookingforconnector-search-merchant-details com_bookingforconnector-item-primary <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
							<?php if($rating != 0) { ?>
							<div class="com_bookingforconnector-search-merchant-reviews-ratings com_bookingforconnector-item-reviews-ratings">
								<span class="com_bookingforconnector-search-merchant-rating com_bookingforconnector-item-rating">
									<?php for($i = 0; $i < $rating; $i++) { ?>
									<i class="fa fa-star"></i>
									<?php } ?>
								</span>
							</div>
							<?php } //if $result->ResRating?>
							<div class="com_bookingforconnector-search-merchant-name com_bookingforconnector-item-primary-name">
								<a class="com_bookingforconnector-search-merchant-name-anchor com_bookingforconnector-item-primary-name-anchor eectrack" href="<?php echo $resourceRoute ?>" data-type="Resource" id="nameAnchor<?php echo $result->ResourceId; ?>" data-id="<?php echo $result->ResourceId?>" data-index="<?php echo $key?>" data-category="<?php echo $merchant->MainCategoryName; ?>" data-itemname="<?php echo  $resourceName; ?>" data-brand="<?php echo $result->MrcName; ?>"><?php echo  $resourceName; ?></a>
							</div>
							<div class="com_bookingforconnector-search-merchant-address com_bookingforconnector-item-primary-address">
								<?php if ($showResourceMap):?>
									<a href="javascript:void(0);" onclick="showMarker(<?php echo $result->ResourceId?>)"><span class="address<?php echo $result->ResourceId?>"></span></a>
								<?php endif; ?>
							</div>
							<div class="com_bookingforconnector-search-merchant-address com_bookingforconnector-item-primary-phone-inforequest"> 
								<span class="com_bookingforconnector_phone"><a  href="javascript:void(0);" onclick="getData(urlCheck,'merchantid=<?php echo $result->MerchantId ?>&task=GetPhoneByMerchantId&language=' + cultureCode,this,'<?php echo  addslashes($result->MrcName) ?>','PhoneView' )"  class="phone<?php echo $result->ResourceId?>"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_SHOWPHONE') ?></a></span>
								<a class="boxedpopup com_bookingforconnector_email" href="<?php echo $routeInfoRequest?>" rel="{handler:'iframe'}" ><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_EMAIL') ?></a>
							</div>
						</div><!--  COL 6-->
						<?php if($isportal): ?>
							<div class="com_bookingforconnector-item-secondary-logo <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2">
								<a class="com_bookingforconnector-search-merchant-logo com_bookingforconnector-logo-list eectrack" href="<?php echo $routeMerchant ?>" data-type="Merchant" id="imganchor<?php echo $result->ResourceId; ?>" data-id="<?php echo $result->MerchantId?>" data-index="<?php echo $key?>" data-itemname="<?php echo $merchant->Name; ?>" data-category="<?php echo $merchant->MainCategoryName; ?>" data-brand="<?php echo $merchant->Name; ?>"><img class="com_bookingforconnector-logo" src="<?php echo $merchantLogoUrl; ?>" id="com_bookingforconnector-logo-list-<?php echo $result->ResourceId?>" /></a>
							</div> <!--  COL 2-->
						<?php endif; ?>
					</div>
						<div class="clearfix"></div>

						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> secondarysection"  style="padding-top: 10px !important;padding-bottom: 10px !important;">
							<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>5 com_bookingforconnector-item-secondary-section-1 secondarysectionitem">	 
								<div class="com_bookingforconnector-search-resource-paxes com_bookingforconnector-item-secondary-paxes">
									<i class="fa fa-user"></i>
									<?php if ($result->MinPaxes == $result->MaxPaxes):?>
										<?php echo  $result->MaxPaxes ?>
									<?php else: ?>
										<?php echo  $result->MinPaxes ?>-<?php echo  $result->MaxPaxes ?>
									<?php endif; ?>
								</div>
								<?php if ($onlystay): ?>
									<div class="com_bookingforconnector-search-resource-details-availability com_bookingforconnector-item-secondary-availability">
									<?php if ($result->Availability < 4): ?>
									  <?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_LESSAVAIL'),$result->Availability) ?>
									<?php else: ?>
									  <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_AVAILABLE')?>
									<?php endif; ?>
									</div>

									<?php if (!$result->IsBase): ?>
										<div class="com_bookingforconnector_res_rateplanname">
											<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_TREATMENT')?>:<br />
											<span><?php echo $result->RateplanName ?></span>
										</div>
									<?php endif; ?>

								<?php endif; ?>
							</div>
								<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4 com_bookingforconnector-item-secondary-section-2 secondarysectionitem">
									<?php if ($result->Price > 0): ?>
										<div class="com_bookingforconnector-search-grouped-resource-details-price com_bookingforconnector-item-secondary-price">
											<span class="com_bookingforconnector-gray-highlight"><?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_TOTALDAYS'),$result->Days) ?></span>
											 <div class="com_bookingforconnector-search-resource-details-stay-price com_bookingforconnector-item-secondary-stay-price">
												<?php if ($result->Price < $result->TotalPrice): ?>
												<?php 
												  $current_discount = $result->PercentVariation;
												  $discount = $current_discount < $discount ? $current_discount : $discount;
												?>
												<span class="com_bookingforconnector_strikethrough"><span class="com_bookingforconnector-search-resource-details-stay-discount com_bookingforconnector-item-secondary-stay-discount">&euro; <?php echo number_format($result->TotalPrice,2, ',', '.')  ?></span></span>
												<?php endif; ?>
												<span class="com_bookingforconnector-search-resource-details-stay-total com_bookingforconnector-item-secondary-stay-total"><?php echo number_format($result->Price,2, ',', '.') ?></span>
											</div>
										</div>
									<?php else: ?>
										<div class="com_bookingforconnector-search-resource-details-price com_bookingforconnector-item-secondary-price">
											<span class="com_bookingforconnector-gray-highlight" id="totaldays<?php echo $result->ResourceId?>"></span>
											<div class="com_bookingforconnector-search-resource-details-stay-price com_bookingforconnector-item-secondary-stay-price">
												<span id="resourcestaytotal<?php echo $result->ResourceId?>" class="com_bookingforconnector-search-resource-details-stay-total com_bookingforconnector-item-secondary-stay-total"><i class="fa fa-spinner fa-spin"></i></span>
												<span class="com_bookingforconnector_strikethrough"><span id="resourcestaydiscount<?php echo $result->ResourceId?>"  class="com_bookingforconnector-search-resource-details-stay-discount com_bookingforconnector-item-secondary-stay-discount"></span></span>
											</div>
										</div>
									<?php endif; ?>
								</div>
								<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3 com_bookingforconnector-item-secondary-section-3 secondarysectionitem">
									<?php if ($result->Price > 0): ?>
											<?php if (isset($bookingType) && $bookingType>0):?>
												<a href="<?php echo $resourceRoute ?>" id="viewbutton<?php echo $result->ResourceId; ?>" data-id="<?php echo $result->ResourceId?>" data-type="Resource" data-index="<?php echo $key?>" data-itemname="<?php echo  $resourceName; ?>" data-category="<?php echo $merchant->MainCategoryName; ?>" data-brand="<?php echo $result->MrcName; ?>" class=" com_bookingforconnector-item-secondary-more eectrack"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_BUTTON_HTTPS')?></a>
											<?php else: ?>
												<a href="<?php echo $resourceRoute ?>" id="viewbutton<?php echo $result->ResourceId; ?>" data-id="<?php echo $result->ResourceId?>" data-type="Resource" data-index="<?php echo $key?>" data-itemname="<?php echo  $resourceName; ?>" data-category="<?php echo $merchant->MainCategoryName; ?>" data-brand="<?php echo $result->MrcName; ?>" class=" com_bookingforconnector-item-secondary-more eectrack"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_BUTTON')?></a>
											<?php endif; ?>
									<?php else: ?>
											<a href="<?php echo $resourceRoute ?>" id="viewbutton<?php echo $result->ResourceId; ?>" data-id="<?php echo $result->ResourceId?>" data-type="Resource" data-index="<?php echo $key?>" data-itemname="<?php echo  $resourceName; ?>" data-category="<?php echo $merchant->MainCategoryName; ?>" data-brand="<?php echo $result->MrcName; ?>" class=" com_bookingforconnector-item-secondary-more eectrack"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_OFFER_DETAILS')?></a>
									<?php endif; ?>
								</div>
							</div>
		
		 <div class="discount-box" style="display:<?php if($discount < 0) { ?>block<?php }else{ ?>none<?php } ?>;">
		   <?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_DISCOUNT_BOX_TEXT'), number_format($current_discount, 1)); ?>
		 </div>
		</div>
				<div class="clearfix"><br /></div>
	  </div>
		<?php 
		$listsId[]= $result->ResourceId;
		?>
	<?php endforeach; ?>
	</div>

		<?php if (!$isFromSearch && $this->pagination->get('pages.total') > 1) : ?>
			<div class="pagination">
				<?php echo $this->pagination->getPagesLinks(); ?>
			</div>
		<?php endif; ?>

<script type="text/javascript">
<!--
jQuery('#list-view').click(function() {
	jQuery('.com_bookingforconnector-view-changer div').removeClass('active');
	jQuery(this).addClass('active');
	jQuery('.com_bookingforconnector-items').removeClass('com_bookingforconnector-grid');
	jQuery('.com_bookingforconnector-items').addClass('com_bookingforconnector-list');
	jQuery('.com_bookingforconnector-items > div').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12');
	jQuery('.com_bookingforconnector-item-carousel').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4');
	jQuery('.com_bookingforconnector-item-primary').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6');
//	jQuery('.com_bookingforconnector-item-secondary-section-1').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>5').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>5');
//	jQuery('.com_bookingforconnector-item-secondary-section-2').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4');
//	jQuery('.com_bookingforconnector-item-secondary-section-3').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3');
	localStorage.setItem('display', 'list');
})

jQuery('#grid-view').click(function() {
	jQuery('.com_bookingforconnector-view-changer div').removeClass('active');
	jQuery(this).addClass('active');
	jQuery('.com_bookingforconnector-items').removeClass('com_bookingforconnector-list');
	jQuery('.com_bookingforconnector-items').addClass('com_bookingforconnector-grid');
	jQuery('.com_bookingforconnector-items > div').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6');
	jQuery('.com_bookingforconnector-item-carousel').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4');
	jQuery('.com_bookingforconnector-item-primary').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6');
//	jQuery('.com_bookingforconnector-item-secondary-section-1').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>5').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>5');
//	jQuery('.com_bookingforconnector-item-secondary-section-2').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4');
//	jQuery('.com_bookingforconnector-item-secondary-section-3').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3');
	localStorage.setItem('display', 'grid');
})

if (localStorage.getItem('display')) {
	if (localStorage.getItem('display') == 'list') {
		jQuery('#list-view').trigger('click');
	} else {
		jQuery('#grid-view').trigger('click');
	}
} else {
	 if(typeof bfc_display === 'undefined') {
		jQuery('#list-view').trigger('click');
	 } else {
		if (bfc_display == '1') {
			jQuery('#grid-view').trigger('click');
		} else { 
			jQuery('#list-view').trigger('click');
		}
	}
}


var urlCheck = "<?php echo JRoute::_('index.php?option=com_bookingforconnector') ?>";
var listToCheck = "<?php echo implode(",", $listsId) ?>";
var strAddressSimple = " ";
var strAddress = "[indirizzo] - [cap] - [comune] ([provincia])";

var strRatingNoResult = "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_RATING_NO_RESULT')?>";
var strRatingBased = "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_RATING_BASED')?>";
var strRatingValuation = "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_RATING_VALUATION')?>";
var defaultcultureCode = '<?php echo BFCHelper::$defaultFallbackCode ?>';


var loaded=false;
function getAjaxInformations(){
	if (!loaded)
	{
		loaded=true;
		if (cultureCode.length>1)
		{
			cultureCode = cultureCode.substring(0, 2).toLowerCase();
		}
		if (defaultcultureCode.length>1)
		{
			defaultcultureCode = defaultcultureCode.substring(0, 2).toLowerCase();
		}
		var query = "resourcesId=" + listToCheck + "&language=<?php echo $language ?>";
<?php
$limitstart = BFCHelper::getInt('limitstart', 0);
$newsearch = BFCHelper::getInt('newsearch', 0);
if(($limitstart ==0 && $newsearch !=0) || $onlystay){
?>
		if(typeof callfilterloading === 'function'){
			query +="&task=GetResourcesCalculateByIds";
		}else{
			query +="&task=GetResourcesByIds";
		}
<?php
}else{
?>
			query +="&task=GetResourcesByIds";
<?php
}
?>
		var imgPathmerchant = "<?php echo $merchantLogoPath ?>";
		var imgPathmerchantError = "<?php echo $merchantLogoPathError ?>";

		var imgPath = "<?php echo $resourceLogoPath ?>";
		var imgPathError = "<?php echo $resourceLogoPathError ?>";

		var imgPathmerchantresized =  imgPathmerchant.substring(0,imgPathmerchant.lastIndexOf("/")).match(/([^\/]*)\/*$/)[1] + "/";
		imgPathmerchant = imgPathmerchant.replace(imgPathmerchantresized , "" );

//		jQuery.getJSON(urlCheck + "?" + query, function(data) {
		jQuery.post(urlCheck, query, function(data) {
				if(typeof callfilterloading === 'function'){
					callfilterloading();
					callfilterloading = null;
				}
				jQuery.each(data || [], function(key, val) {

					imgLogo="<?php echo $resourceImageUrl ?>";
					imgLogoError="<?php echo $resourceImageUrl ?>";
					
					imgMerchantLogo="<?php echo $merchantImageUrl ?>";
					imgMerchantLogoError="<?php echo $merchantImageUrl ?>";

					imgPath = "<?php echo $resourceLogoPath ?>";
					imgPathError = "<?php echo $resourceLogoPathError ?>";

				if (val.Resource.ImageData!= null && val.Resource.ImageData!= '') {
					var imgSliderData = '';
					var ImageData = val.Resource.ImageData.split(',');
					var start = 0;
                jQuery.each(ImageData,function(index){
                  // new system with preresized images
					  imgLogo = imgPath.replace("[img]", ImageData[index]);

					  // old system with resized images on the fly
					  imgLogoError = imgPathError.replace("[img]", ImageData[index]);
					  if(start == 0) {
					    imgSliderData = imgSliderData + '<div class="item active"><img src="'+imgLogo+'"></div>';					  
					  }
					  else {
					    imgSliderData = imgSliderData + '<div class="item"><img src="'+imgLogo+'"></div>';
				     }
				     start++;
                });
                jQuery('#com_bookingforconnector-search-resource-carousel'+val.Resource.ResourceId).carousel("pause").removeData();
                jQuery('#com_bookingforconnector-search-resource-carousel'+val.Resource.ResourceId+' .carousel-inner').html(imgSliderData);
                jQuery('#com_bookingforconnector-search-resource-carousel'+val.Resource.ResourceId).carousel("pause");
				}

					if (val.Merchant.LogoUrl != null && val.Merchant.LogoUrl  != '') {
						// new system with preresized images
						var ImageUrl = val.Merchant.LogoUrl.substr(val.Merchant.LogoUrl.lastIndexOf('/') + 1);
						imgMerchantLogo = imgPathmerchant.replace("[img]", val.Merchant.LogoUrl.replace(ImageUrl, imgPathmerchantresized + ImageUrl ) );
						
						// old system with resized images on the fly
						imgMerchantLogoError = imgPathmerchantError.replace("[img]", val.Merchant.LogoUrl);
	//					imgMerchantLogo = imgPathmerchant.replace("[img]", val.Merchant.LogoUrl );		
					}

					jQuery("#com_bookingforconnector-logo-grid-"+val.Resource.ResourceId).attr('src',imgMerchantLogo);
					jQuery("#com_bookingforconnector-logo-grid-"+val.Resource.ResourceId).attr('onerror',"this.onerror=null;this.src='" + imgMerchantLogoError + "';");

					jQuery("#com_bookingforconnector-logo-list-"+val.Resource.ResourceId).attr('src',imgMerchantLogo);
					jQuery("#com_bookingforconnector-logo-list-"+val.Resource.ResourceId).attr('onerror',"this.onerror=null;this.src='" + imgMerchantLogoError + "';");
					
					addressData = val.Resource.AddressData;
					if ((val.Resource.AddressData == '' || val.Resource.AddressData == null) &&  val.Merchant.AddressData != '') {
						
						var merchAddress = "";
						var $indirizzo = "";
						var $cap = "";
						var $comune = "";
						var $provincia = "";
						
						xmlDoc = jQuery.parseXML(val.Merchant.AddressData);
						if(xmlDoc!=null){
							$xml = jQuery(xmlDoc);
							$indirizzo = $xml.find("indirizzo:first").text();
							$cap = $xml.find("cap:first").text();
							$comune = $xml.find("comune:first").text();
							$provincia = $xml.find("provincia:first").text();
						}else{
							$indirizzo = val.Merchant.AddressData.Address;
							$cap = val.Merchant.AddressData.ZipCode;
							$comune = val.Merchant.AddressData.CityName;
							$provincia = val.Merchant.AddressData.RegionName;
						}
						addressData = strAddress.replace("[indirizzo]",$indirizzo);
						addressData = addressData.replace("[cap]",$cap);
						addressData = addressData.replace("[comune]",$comune);
						addressData = addressData.replace("[provincia]",$provincia);
//							xmlDoc = jQuery.parseXML(val.Merchant.AddressData);
//							$xml = jQuery(xmlDoc);
//							//$addressdata = $xml.find("addressdata")
//							$indirizzo = $xml.find("indirizzo:first");
//							addressData = strAddress.replace("[indirizzo]",$indirizzo.text())
//							$cap = $xml.find("cap:first");
//							addressData = addressData.replace("[cap]",$cap.text())
//							$comune = $xml.find("comune:first");
//							addressData = addressData.replace("[comune]",$comune.text())
//							$provincia = $xml.find("provincia:first");
//							addressData = addressData.replace("[provincia]",$provincia.text())
					}else{
						addressData = strAddress.replace("[indirizzo]",val.Resource.AddressData);
						addressData = addressData.replace("[cap]",val.Resource.ZipCode);
						addressData = addressData.replace("[comune]",val.Resource.CityName);
						addressData = addressData.replace("[provincia]",val.Resource.RegionName);
					}

					jQuery(".address"+val.Resource.ResourceId).html(addressData);
					jQuery(".logo"+val.Resource.ResourceId).attr('src',imgLogo);

	//AVG Avg
//					console.log(val.RatingsContext);
					if (val.Merchant.RatingsContext!= null && (val.Merchant.RatingsContext == '2' || val.Merchant.RatingsContext == '3')){
						$htmlAvg = '';
						if (val.Resource.Avg != null && val.Resource.Avg != '' ) {
							$htmlAvg += strRatingValuation;
							$htmlAvg += '<div class="bfcvaluation average">' + number_format(val.Resource.Avg.Average, 1, '.', '') + '</div>';
							$htmlAvg += '<div class="bfcvaluationcount votes">' + strRatingBased.replace("%s", val.Resource.Avg.Count) + '</div>';
						}else{
							$htmlAvg += strRatingNoResult;
						}
						jQuery(".ratingAnchor"+val.Resource.ResourceId).html($htmlAvg);
					}else{
						jQuery(".ratingAnchor"+val.Resource.ResourceId).parent().hide();					
					}
					
					//price
					
					jQuery("#resourcestaytotal"+val.Resource.ResourceId).html("&nbsp; ");
					if (val.Resource.RatePlanStay != null && val.Resource.RatePlanStay != '' && val.Resource.RatePlanStay.SuggestedStay!= null) {
						var st = val.Resource.RatePlanStay.SuggestedStay;
						var TotalPrice = parseFloat(st.TotalPrice);
						var DiscountedPrice = parseFloat(st.DiscountedPrice);
						if(TotalPrice>0){
							var currnt = "<?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_TOTALDAYS'),'') ?>"
							jQuery("#totaldays"+val.Resource.ResourceId).html(currnt.replace("0", st.Days));
							if(DiscountedPrice < TotalPrice){
								jQuery("#resourcestaydiscount"+val.Resource.ResourceId).html("&euro; " +number_format(TotalPrice, 2, '.', ''));
							}
							jQuery("#resourcestaytotal"+val.Resource.ResourceId).html("&euro; " + number_format(DiscountedPrice, 2, '.', ''));
							if(st.IsOffer){
								jQuery(".container"+val.Resource.ResourceId).addClass("com_bookingforconnector_highlight");
							}

//							if(st.DiscountId>0){
//							}
						}
					}

					jQuery(".container"+val.Resource.ResourceId).click(function(e) {
						var $target = jQuery(e.target);
						if ( $target.is("div")|| $target.is("p")) {
							document.location = jQuery( ".nameAnchor"+val.Resource.ResourceId ).attr("href");
						}
					});
			});	
		},'json');
	}
}

function getDiscountAjaxInformations(discountId,hasRateplans){
//	if (jQuery("#divoffers"+discountId).is(":visible") )
//	{
//		jQuery("#divoffers"+discountId).hide();
//	}else{
//	
	if (cultureCode.length>1)
	{
		cultureCode = cultureCode.substring(0, 2).toLowerCase();
	}
	if (defaultcultureCode.length>1)
	{
		defaultcultureCode = defaultcultureCode.substring(0, 2).toLowerCase();
	}

	var query = "discountId=" + discountId + "&hasRateplans=" + hasRateplans + "&language=<?php echo $language ?>&task=getDiscountDetails";
//	jQuery.getJSON(urlCheck + "?" + query, function(data) {
	jQuery.post(urlCheck, query, function(data) {

				var name = getXmlLanguage(data.Name,cultureCode,defaultcultureCode);;
				name = nl2br(jQuery("<p>" + name + "</p>").text());
				jQuery(".divoffersTitle"+discountId).html(name);

				var descr = getXmlLanguage(data.Description,cultureCode,defaultcultureCode);;
				descr = nl2br(jQuery("<p>" + descr + "</p>").text());
				jQuery(".divoffersDescr"+discountId).html(descr);
				jQuery(".divoffersDescr"+discountId).removeClass("com_bookingforconnector_loading");
//console.log(JSON.stringify(data));
	},'json');

}

var offersLoaded = []
	
jQuery(document).ready(function() {
	getAjaxInformations();
	jQuery('.mod_bookingformaps-static').click(function() {
     jQuery( "#mod_bookingformaps-popup" ).dialog({
       open: function( event, ui ) {
       openGoogleMapSearch();
    },
    height: 500,
    width: 800,
    });
  });
	jQuery('.com_bookingforconnector-sort-item').click(function() {
	  var rel = jQuery(this).attr('rel');
	  var vals = rel.split("|"); 
	  jQuery('#bookingforsearchFilterForm .filterOrder').val(vals[0]);
	  jQuery('#bookingforsearchFilterForm .filterOrderDirection').val(vals[1]);
//	  jQuery('#bookingforsearchFilterForm').submit();
	  jQuery('#searchformfilter').submit();
	});
	jQuery(".offerslabel").click(function(){
				var discountId = jQuery(this).attr('rel'); 
				var hasRateplans = jQuery(this).attr('rel1'); 
				if (jQuery.inArray(discountId,offersLoaded)===-1)
				{
					getDiscountAjaxInformations(discountId,hasRateplans);
					offersLoaded.push(discountId);
				}
				jQuery(".divoffers"+discountId).slideToggle( "slow" );
			});

		jQuery('a.boxedpopup').on('click', function (e) {
			var width = jQuery(window).width()*0.9;
			var height = jQuery(window).height()*0.9;
				if(width>800){width=870;}
				if(height>600){height=600;}

			e.preventDefault();
			var page = jQuery(this).attr("href")
			var pagetitle = jQuery(this).attr("title")
			var $dialog = jQuery('<div id="boxedpopupopen"></div>')
				.html('<iframe style="border: 0px; " src="' + page + '" width="100%" height="100%"></iframe>')
				.dialog({
					autoOpen: false,
					modal: true,
					height:height,
					width: width,
					fluid: true, //new option
					title: pagetitle
				});
			$dialog.dialog('open');
		});

		jQuery(window).resize(function() {
			var bpOpen = jQuery("#boxedpopupopen");
				var wWidth = jQuery(window).width();
				var dWidth = wWidth * 0.9;
				var wHeight = jQuery(window).height();
				var dHeight = wHeight * 0.9;
				if(dWidth>800){dWidth=870;}
				if(dHeight>600){dHeight=600;}
					bpOpen.dialog("option", "width", dWidth);
					bpOpen.dialog("option", "height", dHeight);
					bpOpen.dialog("option", "position", "center");
		});
});


//-->
</script>

	<?php else:?>
	<div class="com_bookingforconnector_merchantdetails-noresources">
		<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_NORESULT')?>
	</div>
	<?php endif?>
</div>
</div>
