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

$isFromSearch = $this->isFromSearch;

$app = JFactory::getApplication();
$results = $this->items;
$language = $this->language;
$searchid = "";
if(!empty($this->params['searchid'])){
	$searchid =  $this->params['searchid'];
}
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$onlystay =  true;
if(!empty($this->params['onlystay'])){
	$onlystay =  $this->params['onlystay'] === 'false'? false: true;
}

$activePrice = '';
$activeRating = '';
$activeOffer = '';

if($listOrder == 'stay') {
$activePrice = ' active';
}
if($listOrder == 'rating') {
$activeRating = ' active';
}
if($listOrder == 'offer') {
$activeOffer = ' active';
}

$counterdiv=1;
$ordselect = array(
	JHTML::_('select.option', '', JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_LABEL') ),
//	JHTML::_('select.option', 'stay|asc', JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_STAYASC') ),
//	JHTML::_('select.option', 'stay|desc',JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_STAYDESC')),
	JHTML::_('select.option', 'rooms|asc',JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_ROOMSASC')),
	JHTML::_('select.option', 'rooms|desc',JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_ROOMSDESC')),
	JHTML::_('select.option', 'distancefromsea|asc',JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_SEADISTANCEASC')),
	JHTML::_('select.option', 'distancefromsea|desc',JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_SEADISTANCEDESC'))
);

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


$uriFav = 'index.php?option=com_bookingforconnector&view=resources&layout=favorites';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriFav ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
//$itemIdFav = ($db->getErrorNum())? 0 : intval($db->loadResult());
$itemIdFav = intval($db->loadResult());
//-------------------pagina per i l redirect di tutte le risorsein vendita

if ($itemIdFav<>0)
	$routeFav = JRoute::_($uriFav.'&Itemid='.$itemIdFav );
else
	$routeFav = JRoute::_($uriFav);


$onchange = 'onchange="setOrdering(this);"';

$resourceImageUrl = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";
$merchantLogoUrl = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s1.jpeg";

$resourceLogoPath = BFCHelper::getImageUrlResized('resources',"[img]", 'medium');
$resourceLogoPathError = BFCHelper::getImageUrl('resources',"[img]", 'medium');

$merchantImageUrl = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";
$merchantLogoUrl = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";

$merchantLogoPath = BFCHelper::getImageUrlResized('merchant',"[img]", 'logomedium');
$merchantLogoPathError = BFCHelper::getImageUrl('merchant',"[img]", 'logomedium');

?>
<?php
$url=JFactory::getURI()->toString();
//$purl=parse_url($url);
//$oldq=$purl['query'];
//$arrTmp=array();
//parse_str($oldq,$arrTmp); //To array
//
//$arrTmp['onlystay'] = 1;
//$newq = http_build_query($arrTmp);//build again
//$action=str_replace($oldq,$newq,$url);
$action=$url;

$hidesort = "";
if(!empty($this->hidesort)) {
	$hidesort = ' style="display:none;"';
}

?>
<div class="com_bookingforconnector-search-menu">
	<form action="<?php echo $action; ?>" method="post" name="bookingforsearchForm" id="bookingforsearchFilterForm">
	<fieldset class="filters">
		<input type="hidden" class="filterOrder" name="filter_order" value="<?php echo $listOrder ?>" />
		<input type="hidden" class="filterOrderDirection" name="filter_order_Dir" value="<?php echo $listDirn ?>" />
		<input type="hidden" name="searchid" value="<?php echo $searchid?>" />
		<input type="hidden" name="limitstart" value="0" />
	</fieldset>
	</form>
	<div class="com_bookingforconnector-results-sort" style="display:none;">
		<span class="com_bookingforconnector-sort-help"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_LABEL')?>:</span>
		<span class="com_bookingforconnector-sort-item<?php echo $activePrice; ?>" rel="stay|asc" <?php echo $hidesort ?>><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_PRICE'); ?></span>
		<span class="com_bookingforconnector-sort-item<?php echo $activeRating; ?>" rel="rating|desc" <?php echo $hidesort ?>><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_GUEST_RATING'); ?></span>
		<span class="com_bookingforconnector-sort-item<?php echo $activeOffer; ?>" rel="offer|desc" <?php echo $hidesort ?>><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_OFFERS'); ?></span>
	</div>
	<div class="com_bookingforconnector-view-changer">
		<div id="list-view" class="com_bookingforconnector-view-changer-list active"><i class="fa fa-list"></i> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_TAB_LIST') ?></div>
		<div id="grid-view" class="com_bookingforconnector-view-changer-grid"><i class="fa fa-th-large"></i> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_TAB_GRID') ?></div>
	</div>
</div>
<div class="clearfix"></div>
<div class="com_bookingforconnector-search-resources com_bookingforconnector-items <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> com_bookingforconnector-list">
<?php foreach ($results as $key => $result):?>
	<?php 
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
		$result->ResName = $result->Name;
		$result->MinPaxes = $result->MinCapacityPaxes;
		$result->MaxPaxes = $result->MaxCapacityPaxes;
		$result->MrcCategoryName = $result->MerchantCategoryName;
		$result->Price = 0;
		$result->TotalPrice = 0;
		$onlystay=false;
	}
	
	
	if ($result->ResRating>9)
	{
		$result->ResRating = $result->ResRating/10;
	}
	
	$showResourceMap = (($resourceLat != null) && ($resourceLon !=null));
	
	$currUriresource = $uri.'&resourceId=' . $result->ResourceId . ':' . BFCHelper::getSlug($resourceName);

	if ($itemId<>0)
		$currUriresource.='&Itemid='.$itemId;
	
	$route = JRoute::_($currUriresource);
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

	if($isFromSearch){
		$availability = $result->Availability;
		$bookingType = $result->BookingType;
		if (($result->Price < $result->TotalPrice) || $result->IsOffer){
			$ribbonofferdisplay = "";
			$classofferdisplay = "com_bookingforconnector_highlight";
			
		}
		if (!empty($result->RateplanId)){
			 $route .= "?pricetype=" . $result->RateplanId;
		}
	}
	
	$images = array($resourceImageUrl);
?>
  <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 com_bookingforconnector-item-col" style="padding: 10px !important;" >
    <div class="com_bookingforconnector-search-resource com_bookingforconnector-item <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
        <div class="com_bookingforconnector-search-merchant-carousel com_bookingforconnector-item-carousel <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4">
        <?php if (count ($images)>0): ?>					
	     <div id="com_bookingforconnector-search-resource-carousel<?php echo $result->ResourceId; ?>" class="carousel" data-ride="carousel" data-interval="false">
          <div class="carousel-inner" role="listbox">
            <?php foreach($images as $id => $image) : ?>
            <?php
              $active_class = '';
              if($id == 0) { $active_class = ' active'; }
            ?>
            <div class="item<?php echo $active_class; ?>"><img src="<?php echo $image; ?>"></div>
            <?php endforeach;  ?>
          </div>
		<?php if($isportal): ?>
			<a class="com_bookingforconnector-search-resource-logo com_bookingforconnector-logo-grid eectrack" href="<?php echo $routeMerchant?>" data-type="Merchant" id="merchantname<?php echo $result->MerchantId?>" data-id="<?php echo $result->MerchantId?>" data-index="<?php echo $key?>" data-category="<?php echo $result->MrcCategoryName; ?>" data-category="<?php echo $result->MrcCategoryName; ?>" data-itemname="<?php echo $result->MrcName; ?>" data-brand="<?php echo $result->MrcName; ?>"><div class="containerlogo"><img class="com_bookingforconnector-logo" id="com_bookingforconnector-logo-grid-<?php echo $result->ResourceId?>" src="<?php echo $merchantLogoUrl; ?>" /></div></a>
		<?php endif; ?>
          <a class="left carousel-control" href="#com_bookingforconnector-search-resource-carousel<?php echo $result->ResourceId; ?>" role="button" data-slide="prev">
            <i class="fa fa-chevron-left"></i>
          </a>
          <a class="right carousel-control" href="#com_bookingforconnector-search-resource-carousel<?php echo $result->ResourceId; ?>" role="button" data-slide="next">
            <i class="fa fa-chevron-right"></i>
          </a>
        </div> 					
        <?php endif; ?>
      </div>
      <div class="com_bookingforconnector-search-merchant-details com_bookingforconnector-item-primary <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
		  <?php if($result->ResRating != 0) { ?>
		  <div class="com_bookingforconnector-search-merchant-reviews-ratings com_bookingforconnector-item-reviews-ratings">
			  <span class="com_bookingforconnector-search-merchant-rating com_bookingforconnector-item-rating">
				  <?php for($i = 0; $i < $result->ResRating; $i++) { ?>
					<i class="fa fa-star"></i>
				  <?php } ?>	             
			  </span>
		  </div>
		  <?php } ?>
		  <div class="com_bookingforconnector-search-merchant-name com_bookingforconnector-item-primary-name">
			  <a class="com_bookingforconnector-search-merchant-name-anchor com_bookingforconnector-item-primary-name-anchor eectrack" href="<?php echo $route ?>" id="nameAnchor<?php echo $result->ResourceId?>" data-id="<?php echo $result->ResourceId?>" data-index="<?php echo $key?>" data-category="<?php echo $result->MrcCategoryName; ?>" data-type="Resource" data-itemname="<?php echo $result->ResName; ?>" data-brand="<?php echo $result->MrcName; ?>"><?php echo  $resourceName; ?></a>
		  </div>
		  <div class="com_bookingforconnector-search-merchant-address com_bookingforconnector-item-primary-address">
			  <?php if ($showResourceMap):?>
				<span class="address<?php echo $result->ResourceId?>"></span>
			  <?php endif; ?>
		  </div>
		  <div class="com_bookingforconnector-search-merchant-address com_bookingforconnector-item-primary-phone-inforequest"> 
			  <span class="com_bookingforconnector_phone"><a  href="javascript:void(0);" onclick="getData(urlCheck,'merchantid=<?php echo $result->MerchantId ?>&task=GetPhoneByMerchantId&language=' + cultureCode,this,'<?php echo  addslashes($result->MrcName) ?>','PhoneView' )"  class="phone<?php echo $result->ResourceId?>"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_SHOWPHONE') ?></a></span>
			  <a class="boxedpopup com_bookingforconnector_email" href="<?php echo $routeInfoRequest?>" rel="{handler:'iframe'}" ><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_EMAIL') ?></a>
		  </div>
		</div><!--  COL 6-->
		<?php if($isportal): ?>
		<div class="com_bookingforconnector-item-secondary-logo <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2">
			<a class="com_bookingforconnector-search-merchant-logo com_bookingforconnector-logo-list" href="<?php echo $routeMerchant ?>" data-type="Merchant" data-id="<?php echo $result->MerchantId?>" data-index="<?php echo $key?>" data-itemname="<?php echo $result->MrcName; ?>" data-category="<?php echo $result->MrcCategoryName; ?>" data-brand="<?php echo $result->MrcName; ?>"><img class="com_bookingforconnector-logo" src="<?php echo $merchantLogoUrl; ?>" id="com_bookingforconnector-logo-list-<?php echo $result->ResourceId?>" /></a>
		</div> <!--  COL 2-->
		<?php endif; ?>
   <div class="com_bookingforconnector-search-resource com_bookingforconnector-item-secondary <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>"  style="padding-top: 10px !important;padding-bottom: 10px !important;">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4">				
			<div class="com_bookingforconnector-search-resource-paxes com_bookingforconnector-item-secondary-paxes">
                <i class="fa fa-user"></i>
                <?php if ($result->MinPaxes == $result->MaxPaxes):?>
                               <?php echo  $result->MaxPaxes ?>
                <?php else: ?>
                               <?php echo  $result->MinPaxes ?>-<?php echo  $result->MaxPaxes ?>
                <?php endif; ?>
			</div>
			<?php if ($onlystay): ?>
			<div class="com_bookingforconnector-search-resource-availability com_bookingforconnector-item-secondary-availability">
				<?php if ($result->Availability < 4): ?>
					<?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_LESSAVAIL'),$result->Availability) ?>
					<?php else: ?>
					<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_AVAILABLE')?>
					<?php endif; ?>
			</div>
			<?php endif; ?>
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>5">
			<?php if ($result->Price > 0): ?>
				<div class="com_bookingforconnector-search-resource-details-price com_bookingforconnector-item-secondary-price">
					<span class="com_bookingforconnector-gray-highlight"><?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_TOTALDAYS'),$result->Days) ?></span>
					<div class="com_bookingforconnector-search-resource-details-stay-price com_bookingforconnector-item-secondary-stay-price">
						<span class="com_bookingforconnector-search-resource-details-stay-total com_bookingforconnector-item-secondary-stay-total"><?php echo number_format($result->Price,2, ',', '.') ?></span>
						<?php if ($result->Price < $result->TotalPrice): ?>
							<span class="com_bookingforconnector_strikethrough"><span class="com_bookingforconnector-search-resource-details-stay-discount com_bookingforconnector-item-secondary-stay-discount">&euro; <?php echo number_format($result->TotalPrice,2, ',', '.')  ?></span></span>
						<?php endif; ?>
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
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3"> 
				<?php if (isset($result) && $result->Price > 0 && isset($bookingType) && $bookingType>0):?>
					<a href="<?php echo $route ?>" class=" com_bookingforconnector-item-secondary-more eectrack" data-id="<?php echo $result->ResourceId?>" data-type="Resource" data-index="<?php echo $key?>" data-itemname="<?php echo $result->ResName; ?>" data-category="<?php echo $result->MrcCategoryName; ?>" data-brand="<?php echo $result->MrcName; ?>"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_BUTTON_HTTPS')?></a>
				<?php else: ?>
					<a href="<?php echo $route ?>" class=" com_bookingforconnector-item-secondary-more eectrack" data-id="<?php echo $result->ResourceId?>" data-type="Resource" data-index="<?php echo $key?>" data-itemname="<?php echo $result->ResName; ?>" data-category="<?php echo $result->MrcCategoryName; ?>" data-brand="<?php echo $result->MrcName; ?>"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_BUTTON')?></a>
				<?php endif; ?>
		</div>
    </div>
    
	 <?php 
	   if ($result->Price < $result->TotalPrice) {
	     $current_discount = ($result->TotalPrice - $result->Price) * 100 / $result->TotalPrice;
	 ?>
	 <div class="discount-box">
	   <?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_DISCOUNT_BOX_TEXT'), number_format($current_discount, 1)); ?>
	 </div>
	 <?php } ?>
    </div>
  </div>
	<?php 
	$listsId[]= $result->ResourceId;
	?>
<?php endforeach; ?>
</div>
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
                jQuery('#com_bookingforconnector-search-resource-carousel'+val.Resource.ResourceId).carousel('pause');
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
							addressData = strAddressSimple + addressData;
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
	jQuery(".offerslabel").click(
		function(){
				var discountId = jQuery(this).attr('rel'); 
				var hasRateplans = jQuery(this).attr('rel1'); 
				if (jQuery.inArray(discountId,offersLoaded)===-1)
				{
					getDiscountAjaxInformations(discountId,hasRateplans);
					offersLoaded.push(discountId);
				}
				jQuery(".divoffers"+discountId).slideToggle( "slow" );
			}
		);
});


//-->
</script>
