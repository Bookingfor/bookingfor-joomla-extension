
<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

 JHTML::_('behavior.modal', 'a.boxed'); 
 
$activeMenu = JFactory::getApplication()->getMenu()->getActive();
$config = $this->config;
$isportal = $config->get('isportal', 1);
$showdata = $config->get('showdata', 1);

$language = $this->language;

$results = $this->items;
//$searchid =  $this->params['searchid'];
$searchid =  uniqid('', true);

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

$counterdiv=1;
$ordselect = array(
	JHTML::_('select.option', '', JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_LABEL') ),
	JHTML::_('select.option', 'PriceMin|asc', JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_STAYASC') ),
	JHTML::_('select.option', 'PriceMin|desc',JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_STAYDESC')),
	JHTML::_('select.option', 'Created|asc', JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_CREATEDASC') ),
	JHTML::_('select.option', 'Created|desc',JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_CREATEDESC')),
//	JHTML::_('select.option', 'rooms|asc',JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_ROOMSASC')),
//	JHTML::_('select.option', 'rooms|desc',JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_ROOMSDESC')),
//	JHTML::_('select.option', 'distancefromsea|asc',JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_SEADISTANCEASC')),
//	JHTML::_('select.option', 'distancefromsea|desc',JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_SEADISTANCEDESC'))
);

$onchange = 'onchange="setOrdering(this);"';

if(!$this->params['show_latest']){

}else{
	$ordselect = array(
		JHTML::_('select.option', '', JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_LABEL') ),
		JHTML::_('select.option', 'MinPrice|asc', JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_STAYASC') ),
		JHTML::_('select.option', 'MinPrice|desc',JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_STAYDESC')),
		JHTML::_('select.option', 'Created|asc', JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_CREATEDASC') ),
		JHTML::_('select.option', 'Created|desc',JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_CREATEDESC')),
	);
}

$listsId = array();

$resourceImageUrl = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";
$merchantLogoUrl = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s1.jpeg";

$resourceLogoPath = BFCHelper::getImageUrlResized('onsellunits',"[img]", 'medium');
$resourceLogoPathError = BFCHelper::getImageUrl('onsellunits',"[img]", 'medium');

$merchantImageUrl = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";
$merchantLogoUrl = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";

$merchantLogoPath = BFCHelper::getImageUrlResized('merchant',"[img]", 'logomedium');
$merchantLogoPathError = BFCHelper::getImageUrl('merchant',"[img]", 'logomedium');

//-------------------pagina per i l redirect di tutte le risorsein vendita

$db   = JFactory::getDBO();
$uri  = 'index.php?option=com_bookingforconnector&view=onsellunit';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
//-------------------pagina per i l redirect di tutte le risorsein vendita

//-------------------pagina per il redirect di tutti i merchant

$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1  LIMIT 1' );
$itemIdMerchant = ($db->getErrorNum())? 0 : intval($db->loadResult());
//-------------------pagina per il redirect di tutti i merchant

//-------------------pagina per il redirect di tutte le risorse in vendita favorite

$uriFav = 'index.php?option=com_bookingforconnector&view=onsellunits&layout=favorites';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriFav ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1  LIMIT 1' );
$itemIdFav = ($db->getErrorNum())? 0 : intval($db->loadResult());
//-------------------pagina per i l redirect di tutte le risorsein vendita

if ($itemIdFav<>0)
	$routeFav = JRoute::_($uriFav.'&Itemid='.$itemIdFav );
else
	$routeFav = JRoute::_($uriFav);

?>
<script type="text/javascript">
<!--
var urlCheck = "<?php echo JRoute::_('index.php?option=com_bookingforconnector') ?>";	
var cultureCode = '<?php echo $language ?>';
//-->
</script>
<div class="clearboth"></div>
<h1><?php echo $activeMenu->title?></h1>

<div class="com_bookingforconnector-items-container">

<?php if (!empty($results)):?>

<div class="com_bookingforconnector-search-menu">
	<form  action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="filterForm" id="filterForm">
		<fieldset class="filters">
			<input type="hidden" class="filterOrder" name="filter_order" value="<?php echo $listOrder ?>" />
			<input type="hidden" class="filterOrderDirection" name="filter_order_Dir" value="<?php echo $listDirn ?>" />
			<input type="hidden" name="limitstart" value="0" />
		</fieldset>
	</form>
	<div class="com_bookingforconnector-results-sort">
		<span class="com_bookingforconnector-sort-item"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_LABEL')?>:</span>
		<span class="com_bookingforconnector-sort-item " rel="MinPrice|asc" ><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_STAYASC'); ?></span>
		<span class="com_bookingforconnector-sort-item " rel="Created|desc" ><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_CREATEDESC'); ?></span>
	</div>
	<div class="com_bookingforconnector-view-changer">
		<div id="list-view" class="com_bookingforconnector-view-changer-list active"><i class="fa fa-list"></i> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_TAB_LIST') ?></div>
		<div id="grid-view" class="com_bookingforconnector-view-changer-grid"><i class="fa fa-th-large"></i> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_TAB_GRID') ?></div>
	</div>
</div>
<div class="clearfix"></div>
<div class="com_bookingforconnector-search-merchants com_bookingforconnector-items <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> com_bookingforconnector-list">
<?php foreach ($results as $mrcKey => $result):?>
	<?php 
	$resource = $result;
	$resource->Price = $result->MinPrice;
//	$resourceName = BFCHelper::getLanguage($resource->Name, $this->language);
	$resourceName = BFCHelper::getLanguage($resource->Name, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
	$resourceLat = '';
	$resourceLon = '';
	if(!empty($resource->XGooglePos)){
		$resourceLat = $resource->XGooglePos;
	}
	if(!empty($resource->YGooglePos)){
		$resourceLon = $resource->YGooglePos;
	}
	if(!empty($resource->XPos)){
		$resourceLat = $resource->XPos;
	}
	if(!empty($resource->YPos)){
		$resourceLon = $resource->YPos;
	}
	$isMapVisible = $resource->IsMapVisible;
	$isMapMarkerVisible = $resource->IsMapMarkerVisible;
	$showResourceMap = (($resourceLat != null) && ($resourceLon !=null) && $isMapVisible && $isMapMarkerVisible);

	if ($itemId<>0){
		$route = JRoute::_($uri.'&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName).'&Itemid='.$itemId );
	} else {
		$route = JRoute::_($uri.'&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName));
	}
	
//	if ($itemIdMerchant<>0)
//		$uriMerchant.='&merchantId=' . $resource->MerchantId . ':' . BFCHelper::getSlug($merchant->Name).'&Itemid='.$itemIdMerchant;
//	else
//		$uriMerchant.='&merchantId=' . $resource->MerchantId . ':' . BFCHelper::getSlug($merchant->Name);
	if ($itemIdMerchant<>0)
		$uriMerchant.='&merchantId=' . $resource->MerchantId . ':' . BFCHelper::getSlug($resource->MerchantName)  . '&Itemid='.$itemIdMerchant;
	else
		$uriMerchant.='&merchantId=' . $resource->MerchantId . ':' . BFCHelper::getSlug($resource->MerchantName) ;

	$routeMerchant = JRoute::_($uriMerchant);

	$routeInfoRequest = JRoute::_('index.php?option=com_bookingforconnector&view=onsellunit&layout=inforequestpopup&tmpl=component&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName));
	$routeRapidView = JRoute::_('index.php?option=com_bookingforconnector&view=onsellunit&layout=rapidview&tmpl=component&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName));
		
	if(!empty($resource->ImageUrl)){
		$resourceImageUrl = BFCHelper::getImageUrlResized('onsellunits',$result->ImageUrl, 'medium');
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
									<a class="com_bookingforconnector-search-resource-logo com_bookingforconnector-logo-grid eectrack" href="<?php echo $routeMerchant?>" id="merchantname<?php echo $result->ResourceId?>" data-id="<?php echo $resource->MerchantId?>" data-type="Merchant" data-index="<?php echo $mrcKey?>" data-itemname="<?php echo $resource->MerchantName; ?>" data-category="<?php echo $resource->MerchantCategoryName; ?>" data-brand="<?php echo $resource->MerchantName; ?>"><div class="containerlogo"><img class="com_bookingforconnector-logo" id="com_bookingforconnector-logo-grid-<?php echo $result->ResourceId?>" src="<?php echo $merchantLogoUrl; ?>" /></div></a>
								<?php endif; ?>
							<a class="left carousel-control" href="#com_bookingforconnector-search-resource-carousel<?php echo $result->ResourceId; ?>" role="button" data-slide="prev">
								<i class="fa fa-chevron-left"></i>
							</a>
							<a class="right carousel-control" href="#com_bookingforconnector-search-resource-carousel<?php echo $result->ResourceId; ?>" role="button" data-slide="next">
								<i class="fa fa-chevron-right"></i>
							</a>
						</div>
					</div>

				<div class="com_bookingforconnector-offers-item-primary com_bookingforconnector-item-primary <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
					<div class="com_bookingforconnector-offers-item-primary-name com_bookingforconnector-item-primary-name">
						<div class="com_bookingforconnector-offers-item-primary-nameAnchor"><a class="com_bookingforconnector-item-primary-nameAnchor eectrack" href="<?php echo $route ?>" data-id="<?php echo $resource->ResourceId?>" data-index="<?php echo $mrcKey?>" data-category="<?php echo $resource->MerchantCategoryName; ?>" data-type="Sales Resource" data-itemname="<?php echo $resource->Name; ?>" data-brand="<?php echo $resource->MerchantName; ?>"><?php echo  $resourceName ?></a></div>
					</div>
					<div class="com_bookingforconnector-onsell-address com_bookingforconnector_merchantdetails-resource-address com_bookingforconnector-item-primary-address">         
						<?php if ($showResourceMap):?>
							<a href="javascript:void(0);" onclick="showMarker(<?php echo $resource->ResourceId?>)"><span class="" id="address<?php echo $resource->ResourceId?>"></span></a>
						<?php endif; ?>
					</div>
					<span class="showcaseresource hidden" id="showcaseresource<?php echo $resource->ResourceId?>">
						<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_SHOWCASERESOURCE') ?> 
						<i class="fa fa-angle-double-up"></i>
					</span>
					<span class="topresource hidden" id="topresource<?php echo $resource->ResourceId?>">
						<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_TOPRESOURCE') ?>
						<i class="fa fa-angle-up"></i>
					</span>
					<span class="newbuildingresource hidden" id="newbuildingresource<?php echo $resource->ResourceId?>">
						<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_NEWBUILDINGRESOURCE') ?> 
						<i class="fa fa-home"></i>
					</span>
					<div class="com_bookingforconnector-onsell-address com_bookingforconnector-search-merchant-address com_bookingforconnector-item-primary-phone-inforequest" style="margin-top:5px;"> 
						<span class="com_bookingforconnector_phone"><a  href="javascript:void(0);" onclick="getData(urlCheck,'merchantid=<?php echo $result->MerchantId ?>&task=GetPhoneByMerchantId&language=' + cultureCode,this,'<?php echo  addslashes($result->MerchantName) ?>','PhoneView' )"  class="phone<?php echo $result->ResourceId?>"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_SHOWPHONE') ?></a></span>
						- 
						<a class="boxedpopup com_bookingforconnector_email" href="<?php echo $routeInfoRequest?>" rel="{handler:'iframe'}" ><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_EMAIL') ?></a>
					</div>
				</div>

				<?php if($isportal): ?>
					<div class="com_bookingforconnector-item-secondary-logo <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2">
						<a class="com_bookingforconnector-search-merchant-logo com_bookingforconnector-logo-list eectrack" href="<?php echo $routeMerchant ?>" data-id="<?php echo $resource->MerchantId?>" data-index="<?php echo $mrcKey?>" data-type="Merchant" data-itemname="<?php echo $resource->MerchantName; ?>" data-category="<?php echo $resource->MerchantCategoryName; ?>" data-brand="<?php echo $resource->MerchantName; ?>"><img class="com_bookingforconnector-logo" src="<?php echo $merchantLogoUrl; ?>" id="com_bookingforconnector-logo-list-<?php echo $result->ResourceId?>" /></a>
					</div> <!--  COL 2-->
				<?php endif; ?>
		</div>

		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> secondarysection"  style="padding-top: 10px !important;padding-bottom: 10px !important;">
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>5 com_bookingforconnector-item-secondary-section-1 secondarysectionitem">				
					<?php if (isset($resource->Rooms) && $resource->Rooms>0):?>
					<div class="com_bookingforconnector-search-resource-paxes com_bookingforconnector-item-secondary-paxes rooms<?php echo $result->ResourceId?>">
						<?php echo $resource->Rooms?> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_ROOMS') ?>
					</div>
					<?php endif; ?>
					<?php if (isset($resource->Area) && $resource->Area>0):?>
					<div class="com_bookingforconnector_merchantdetails-resource-area  ">
						<?php echo  $resource->Area ?> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_AREAMQ') ?>
					</div>
					<?php endif; ?>
				</div>
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>5 com_bookingforconnector-item-secondary-section-2 secondarysectionitem">
						<div class="com_bookingforconnector-search-resource-details-price com_bookingforconnector-item-secondary-price">
							<span class="com_bookingforconnector-gray-highlight">&nbsp;</span>
							<div class="com_bookingforconnector-search-resource-details-stay-price com_bookingforconnector-item-secondary-stay-price">
								<span class="com_bookingforconnector-search-resource-details-stay-total com_bookingforconnector-item-secondary-stay-total" style="margin-top: 12px;">
									<?php if ($resource->Price != null && $resource->Price > 0 && isset($resource->IsReservedPrice) && $resource->IsReservedPrice!=1 ) :?>
										&euro; <?php echo number_format($resource->Price,0, ',', '.')?>
									<?php else: ?>
										<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_ISRESERVEDPRICE')?>
									<?php endif; ?>
								</span>
							</div>
						</div>
				</div>
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2 com_bookingforconnector-item-secondary-section-3 secondarysectionitem">
						<a href="<?php echo $route ?>" class=" com_bookingforconnector-item-secondary-more eectrack" data-id="<?php echo $resource->ResourceId?>" data-index="<?php echo $mrcKey?>" data-category="<?php echo $resource->MerchantCategoryName; ?>" data-itemname="<?php echo $resource->Name; ?>" data-brand="<?php echo $resource->MerchantName; ?>"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_OFFER_DETAILS')?></a>
				</div>
			</div>
			<div  class="ribbonnew hidden" id="ribbonnew<?php echo $resource->ResourceId?>"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_RIBBONNEW') ?></div>
		</div>
		
				<div class="clearfix"><br /></div>		

	</div>
	<?php 
	$listsId[]= $resource->ResourceId;
	?>
<?php endforeach; ?>
</div>

<!-- PAGINATION -->
<div class="clearboth"></div>
<?php if ($this->pagination->get('pages.total') > 1) : ?>
	<div class="pagination">
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
<?php endif; ?>
</div>
<?php echo  $this->loadTemplate('googlemap'); ?>
					

<script type="text/javascript">
<!--
jQuery(document).ready(function() {
	jQuery('.com_bookingforconnector-sort-item').click(function() {
	  var rel = jQuery(this).attr('rel');
	  var vals = rel.split("|"); 
	  jQuery('#filterForm .filterOrder').val(vals[0]);
	  jQuery('#filterForm .filterOrderDirection').val(vals[1]);
	  jQuery('#filterForm').submit();
	});
});
jQuery('#list-view').click(function() {
	jQuery('.com_bookingforconnector-view-changer div').removeClass('active');
	jQuery(this).addClass('active');
	jQuery('.com_bookingforconnector-items').removeClass('com_bookingforconnector-grid');
	jQuery('.com_bookingforconnector-items').addClass('com_bookingforconnector-list');
	jQuery('.com_bookingforconnector-items > div').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12');
	jQuery('.com_bookingforconnector-item-carousel').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4');
	jQuery('.com_bookingforconnector-item-primary').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6');
	jQuery('.com_bookingforconnector-item-secondary-section-1').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>5');
	jQuery('.com_bookingforconnector-item-secondary-section-2').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>5');
	jQuery('.com_bookingforconnector-item-secondary-section-3').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2');
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
	jQuery('.com_bookingforconnector-item-secondary-section-1').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>5').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4');
	jQuery('.com_bookingforconnector-item-secondary-section-2').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>5').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4');
	jQuery('.com_bookingforconnector-item-secondary-section-3').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4');
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

	function setOrdering(option) {
		var form = option.form;
		var order_dir = option.value;
		var vals = order_dir.split("|"); 
		form.filter_order.value = vals[0];
		form.filter_order_Dir.value = vals[1];
		form.submit();
	}
	function setOrderingAlphabet(startswith) {
		jQuery("#filterForm #startswith").val(startswith)
		jQuery("#filterForm").submit();
	}

//	function showMarker(extId) {
//	}
	
//-->
</script>

<script type="text/javascript">
<!--
var listToCheck = "<?php echo implode(",", $listsId) ?>";
var imgPathmerchant = "<?php echo $merchantLogoPath ?>";
var imgPathmerchantError = "<?php echo $merchantLogoPathError ?>";

var imgPath = "<?php echo $resourceLogoPath ?>";
var imgPathError = "<?php echo $resourceLogoPathError ?>";

var strAddressSimple = "";
var strAddress = "[indirizzo] - [cap] - [comune] ([provincia])";
var defaultcultureCode = '<?php echo BFCHelper::$defaultFallbackCode ?>';
var onsellunitDaysToBeNew = '<?php echo BFCHelper::$onsellunitDaysToBeNew ?>';
var nowDate =  new Date();
var newFromDate =  new Date();
newFromDate.setDate(newFromDate.getDate() - onsellunitDaysToBeNew); 
var listAnonymous = ",<?php echo COM_BOOKINGFORCONNECTOR_ANONYMOUS_TYPE ?>,";

var shortenOption = {
		moreText: "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_VIEW_READMORE')?>",
		lessText: "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_VIEW_READLESS')?>",
		showChars: '250'
};

function getAjaxInformations(){
	if (cultureCode.length>1)
	{
		cultureCode = cultureCode.substring(0, 2).toLowerCase();
	}
	if (defaultcultureCode.length>1)
	{
		defaultcultureCode = defaultcultureCode.substring(0, 2).toLowerCase();
	}
	
	var query = "resourcesId=" + listToCheck + "&language=<?php echo $language ?>&task=GetResourcesOnSellByIds";

//	var imgPathresized =  imgPath.substring(0,imgPath.lastIndexOf("/")).match(/([^\/]*)\/*$/)[1] + "/";
//	imgPath = imgPath.replace(imgPathresized , "" );

	var imgPathmerchantresized =  imgPathmerchant.substring(0,imgPathmerchant.lastIndexOf("/")).match(/([^\/]*)\/*$/)[1] + "/";
	imgPathmerchant = imgPathmerchant.replace(imgPathmerchantresized , "" );

	jQuery.post(urlCheck, query, function(data) {
			jQuery.each(data || [], function(key, val) {

				imgLogo="<?php echo $resourceImageUrl ?>";
				imgLogoError="<?php echo $resourceImageUrl ?>";
				
				imgMerchantLogo="<?php echo $merchantImageUrl ?>";
				imgMerchantLogoError="<?php echo $merchantImageUrl ?>";

//				if (val.ImageUrl!= null && val.ImageUrl!= '') {
//					// new system with preresized images
//					var ImageUrl = val.ImageUrl.substr(val.ImageUrl.lastIndexOf('/') + 1);
//					imgLogo = imgPath.replace("[img]", val.ImageUrl.replace(ImageUrl, imgPathresized + ImageUrl ) );
//
//					// old system with resized images on the fly
//					imgLogoError = imgPathError.replace("[img]", val.ImageUrl );
////					imgLogo = imgPath.replace("[img]", val.ImageUrl );		
//				}
				if (val.ImageData!= null && val.ImageData!= '') {
					var imgSliderData = '';
					var ImageData = val.ImageData.split(',');
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
					jQuery('#com_bookingforconnector-search-resource-carousel'+val.ResourceId).carousel("pause").removeData();
					jQuery('#com_bookingforconnector-search-resource-carousel'+val.ResourceId+' .carousel-inner').html(imgSliderData);
					jQuery('#com_bookingforconnector-search-resource-carousel'+val.ResourceId).carousel('pause');
				}
					if (val.LogoUrl != null && val.LogoUrl  != '') {
						// new system with preresized images
						var ImageUrl = val.LogoUrl.substr(val.LogoUrl.lastIndexOf('/') + 1);
						imgMerchantLogo = imgPathmerchant.replace("[img]", val.LogoUrl.replace(ImageUrl, imgPathmerchantresized + ImageUrl ) );
						
						// old system with resized images on the fly
						imgMerchantLogoError = imgPathmerchantError.replace("[img]", val.LogoUrl);
	//					imgMerchantLogo = imgPathmerchant.replace("[img]", val.Merchant.LogoUrl );		
					}
					jQuery("#com_bookingforconnector-logo-grid-"+val.ResourceId).attr('src',imgMerchantLogo);
					jQuery("#com_bookingforconnector-logo-grid-"+val.ResourceId).attr('onerror',"this.onerror=null;this.src='" + imgMerchantLogoError + "';");

					jQuery("#com_bookingforconnector-logo-list-"+val.ResourceId).attr('src',imgMerchantLogo);
					jQuery("#com_bookingforconnector-logo-list-"+val.ResourceId).attr('onerror',"this.onerror=null;this.src='" + imgMerchantLogoError + "';");


				var addressData ="";
				var arrData = new Array();
				if (val.IsAddressVisible)
				{
					if(val.Address!= null && val.Address!=''){
						arrData.push(val.Address);
					}
				}
				if(val.LocationZone!= null && val.LocationZone!=''){
					arrData.push(val.LocationZone);
				}
				if(val.LocationName!= null && val.LocationName!=''){
					arrData.push(val.LocationName);
				}
				addressData = arrData.join(" - ");
				addressData = strAddressSimple + addressData;
				jQuery("#address"+val.ResourceId).append(addressData);
				
				if(listAnonymous.indexOf(","+val.MainMerchantCategoryId+",")<0){
					var tmpHref = jQuery("#merchantname"+val.ResourceId).attr("href");
					if (tmpHref && !tmpHref.endsWith("-"))
					{
						tmpHref += "-";
					}
					jQuery("#merchantname"+val.ResourceId).attr("href", tmpHref + make_slug(val.MerchantName));
					jQuery("#logomerchant"+val.ResourceId).attr('src',imgMerchantLogo);
					jQuery("#logomerchant"+val.ResourceId).attr('onerror',"this.onerror=null;this.src='" + imgMerchantLogoError + "';");

				}else{
					jQuery("#merchantname"+val.ResourceId).hide();
				}

//				var descr = getXmlLanguage(val.Description,cultureCode,defaultcultureCode);;
//				var descr = nl2br(jQuery("<p>" + nomore1br(val.Description) + "</p>").text());
//
//				jQuery("#descr"+val.ResourceId).append(descr);
//
				if(val.AddedOn!= null){
					var parsedDate = new Date(parseInt(val.AddedOn.substr(6)));
					var jsDate = new Date(parsedDate); //Date object				
					var isNew = jsDate > newFromDate;
					if (isNew)
						{
							jQuery("#ribbonnew"+val.ResourceId).removeClass("hidden");
						}
				}

				/* highlite seller*/
				if(val.IsHighlight){
							jQuery("#container"+val.ResourceId).addClass("com_bookingforconnector_highlight");
						}

				/*Top seller*/
				if (val.IsForeground)
					{
						jQuery("#topresource"+val.ResourceId).removeClass("hidden");
//						jQuery("#borderimg"+val.ResourceId).addClass("hidden");
					}

				/*Showcase seller*/
				if (val.IsShowcase)
					{
						jQuery("#topresource"+val.ResourceId).addClass("hidden");
						jQuery("#showcaseresource"+val.ResourceId).removeClass("hidden");
						jQuery("#lensimg"+val.ResourceId).removeClass("hidden");
//						jQuery("#borderimg"+val.ResourceId).addClass("hidden");
					}
				
				/*Top seller*/
				if(val.IsNewBuilding){
					jQuery("#newbuildingresource"+val.ResourceId).removeClass("hidden");
				}
//jQuery("#topresource"+val.ResourceId).removeClass("hidden");
//jQuery("#showcaseresource"+val.ResourceId).removeClass("hidden");
//jQuery("#newbuildingresource"+val.ResourceId).removeClass("hidden");
				
				if(val.Rooms!=null && val.Rooms>0){
					var sp = jQuery("<div />", { "id": 'Span_' + val.ResourceId, html: val.Rooms + " <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_ROOMS') ?>" })
					sp.addClass("padding1020 minheight34 borderright font16 com_bookingforconnector_merchantdetails-resource-rooms");
					jQuery("#divfeature"+val.ResourceId).append(sp);
				}
				if(val.Area!=null && val.Area>0){
					var sp = jQuery("<div />", { "id": 'Span_' + val.ResourceId, html: val.Area + " <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_AREAMQ') ?>"})
					sp.addClass("padding1020 minheight34 borderright font16 com_bookingforconnector_merchantdetails-resource-area");
					jQuery("#divfeature"+val.ResourceId).append(sp);
				}
				jQuery("#descr"+val.ResourceId).removeClass("com_bookingforconnector_loading");
//				jQuery("#descr"+val.ResourceId).data('jquery.shorten', false);
//				jQuery("#descr"+val.ResourceId).shorten(shortenOption);

				jQuery("#container"+val.ResourceId).click(function(e) {
					var $target = jQuery(e.target);
					if ( $target.is("div")|| $target.is("p")) {
						document.location = jQuery( "#nameAnchor"+val.ResourceId ).attr("href");
					}
				});

		});	
	}, "json");
}

jQuery(document).ready(function() {
	if (listToCheck!=="")
	{

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
	}
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


//-->
</script>
<?php endif; ?>
