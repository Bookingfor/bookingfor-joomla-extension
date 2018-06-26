<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;
$showdata = COM_BOOKINGFORCONNECTOR_SHOWDATA;
$currencyclass = bfi_get_currentCurrency();

$language = $this->language;
$total = $this->pagination->total;
$searchid =  $this->params['searchid'];
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

$results = $this->items;
$listNameAnalytics = $this->listNameAnalytics;
$fromsearchparam = "&lna=".$listNameAnalytics;

$listsId = array();

$currSorting=$listOrder . "|" . $listDirn;

$totalResult = $total;

$resourceImageUrl = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";

$url=JFactory::getURI()->toString();
$formAction=$url;

//-------------------pagina per i l redirect di tutte le risorse in vendita

$db   = JFactory::getDBO();
$uri  = 'index.php?option=com_bookingforconnector&view=onsellunit';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').')  AND published = 1 LIMIT 1' );
$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
//------------------- pagina per i l redirect di tutte le risorse in vendita


//-------------------pagina per il redirect di tutti i merchant

$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1  LIMIT 1' );
$itemIdMerchant = ($db->getErrorNum())? 0 : intval($db->loadResult());
//-------------------pagina per il redirect di tutti i merchant


?>
<div class="bfi-content">
	<div class="bfi-row">
		<div class="bfi-col-xs-9 ">
			<div class="bfi-search-title">
				<?php echo sprintf( JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_SEARCHRESULTS_TITLE_DEFAULT'),$totalResult ) ?>
			</div>
		</div>	
	<?php if(!empty(COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLEMAPSKEY)){ ?>
		<div class="bfi-col-xs-3 ">
			<div class="bfi-search-view-maps ">
			<span><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_MAPVIEW') ?></span>
			</div>	
		</div>	
	<?php } ?>
	</div>	

	<div class="bfi-search-menu">
		<form action="<?php echo $formAction; ?>" method="post" name="bookingforsearchForm" id="bookingforsearchFilterForm">
				<input type="hidden" class="filterOrder" name="filter_order" value="<?php echo $listOrder ?>" />
				<input type="hidden" class="filterOrderDirection" name="filter_order_Dir" value="<?php echo $listDirn ?>" />
				<input type="hidden" name="searchid" value="<?php //echo   $searchid ?>" />
				<input type="hidden" name="limitstart" value="0" />
		</form>
		<div class="bfi-results-sort">
			<span class="bfi-sort-item"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_LABEL')?>:</span>
			<span class="bfi-sort-item <?php echo $currSorting=="price|asc" ? "bfi-sort-item-active": "" ; ?>" rel="price|asc" ><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_STAYASC'); ?></span>
			<span class="bfi-sort-item <?php echo $currSorting=="created|desc" ? "bfi-sort-item-active": "" ; ?>" rel="AddedOn|desc" ><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_ORDERBY_CREATEDASC'); ?></span>
		</div>
		<div class="bfi-view-changer">
			<div class="bfi-view-changer-selected"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_TAB_LIST') ?></div>
			<div class="bfi-view-changer-content">
				<div id="list-view"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_TAB_LIST') ?></div>
				<div id="grid-view" class="bfi-view-changer-grid"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_TAB_GRID') ?></div>
			</div>
		</div>
	</div>

	<div class="bfi-clearfix"></div>




<div id="bfi-list" class="bfi-row bfi-list">
	<?php foreach ($results as $currKey => $result){?>
		<?php 
		$resourceImageUrl = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";

		$resource = $result;
		$resourceName = BFCHelper::getLanguage($result->Name, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
		$merchantName = $resource->MerchantName;
		
		if (!empty($result->OnSellUnitId)){
			$resource->ResourceId = $result->OnSellUnitId;
		}
		$resource->Price = $result->MinPrice;	

		$typeName =  BFCHelper::getLanguage($resource->CategoryName, $language);
		$contractType = ($resource->ContractType) ? JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_CONTRACTTYPE1')  : JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_CONTRACTTYPE');
		$location = $resource->LocationName;
		$resourceName = BFCHelper::getLanguage($resource->Name, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 

		$addressData = "";
		$resourceLat ="";
		$resourceLon = "";
		if(!empty($resource->XPos)){
			$resourceLat = $resource->XPos;
		}
		if(!empty($resource->YPos)){
			$resourceLon = $resource->YPos;
		}
		$isMapVisible = $resource->IsMapVisible;
		$isMapMarkerVisible = $resource->IsMapMarkerVisible;
		$showResourceMap = (($resourceLat != null) && ($resourceLon !=null) && $isMapVisible && $isMapMarkerVisible);
		
		$currUriresource = $uri.'&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName);
		if ($itemId<>0)
			$currUriresource.='&Itemid='.$itemId;
		
		$resourceRoute = $route = JRoute::_($currUriresource.$fromsearchparam);		
		
		$routeMerchant = "";
		if($isportal){
			$currUriMerchant = $uriMerchant. '&merchantId=' . $resource->MerchantId . ':' . BFCHelper::getSlug($merchantName);
			if ($itemIdMerchant<>0)
				$currUriMerchant.= '&Itemid='.$itemIdMerchant;
			$routeMerchant = JRoute::_($currUriMerchant.$fromsearchparam);
		}
		

		if(!empty($result->ImageUrl)){
			$resourceImageUrl = BFCHelper::getImageUrlResized('onsellunits',$result->ImageUrl, 'medium');
		}
		$resource->RatingsContext = 0;	//set 0 so not show 
		
		$rating = 0;	//set 0 so not show 
		$ratingMrc= 0;	//set 0 so not show 
//		$rating = $resource->Rating;
//		if ($rating>9 )
//		{
//			$rating = $rating/10;
//		}
//		$ratingMrc = $resource->MrcRating;
//		if ($ratingMrc>9 )
//		{
//			$ratingMrc = $ratingMrc/10;
//		}
		$resourceNameTrack =  BFCHelper::string_sanitize($resourceName);
		$merchantNameTrack =  BFCHelper::string_sanitize($merchantName);
		$merchantCategoryNameTrack =  BFCHelper::string_sanitize($resource->MerchantCategoryName);
	?>
	<div class="bfi-col-sm-6 bfi-item">
		<div class="bfi-row bfi-sameheight" >
			<div class="bfi-col-sm-3 bfi-img-container">
				<a href="<?php echo $resourceRoute ?>" style='background: url("<?php echo $resourceImageUrl; ?>") center 25% / cover;' target="_blank" class="eectrack" data-type="Sales Resource" data-id="<?php echo $resource->ResourceId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><img src="<?php echo $resourceImageUrl; ?>" class="bfi-img-responsive" /></a> 
			</div>
			<div class="bfi-col-sm-9 bfi-details-container">
				<!-- merchant details -->
				<div class="bfi-row" >
					<div class="bfi-col-sm-12">
						<div class="bfi-item-title">
							<a href="<?php echo $resourceRoute ?>" id="nameAnchor<?php echo $resource->ResourceId?>" target="_blank" class="eectrack" data-type="Sales Resource" data-id="<?php echo $resource->ResourceId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><?php echo  $resourceName?></a> 
							<span class="bfi-item-rating">
								<?php for($i = 0; $i < $rating; $i++) { ?>
									<i class="fa fa-star"></i>
								<?php } ?>	             
							</span>
							<?php if($isportal) { ?>
								- <a href="<?php echo $routeMerchant?>" class="bfi-subitem-title eectrack" target="_blank" data-type="Merchant" data-id="<?php echo $resource->MerchantId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $merchantNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><?php echo $merchantName; ?></a>
								<span class="bfi-item-rating">
									<?php for($i = 0; $i < $ratingMrc; $i++) { ?>
										<i class="fa fa-star"></i>
									<?php } ?>	             
								</span>
							<?php } ?>
							
						</div>
						<div class="bfi-item-address">
							<?php if ($showResourceMap):?>
							<a href="javascript:void(0);" onclick="showMarker(<?php echo $resource->ResourceId?>)"><span id="address<?php echo $resource->ResourceId?>"></span></a>
							<?php endif; ?>
						</div>
						<div class="bfi-mrcgroup" id="bfitags<?php echo $resource->ResourceId; ?>"></div>
						<span class="bfi-label-alternative2 bfi-hide" id="showcaseresource<?php echo $resource->ResourceId?>">
							<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_SHOWCASERESOURCE') ?>
							<i class="fa fa-angle-double-up"></i>
						</span>
						<span class="bfi-label-alternative bfi-hide" id="topresource<?php echo $resource->ResourceId?>">
							<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_TOPRESOURCE') ?>
							<i class="fa fa-angle-up"></i>
						</span>
						<span class="bfi-label bfi-hide" id="newbuildingresource<?php echo $resource->ResourceId?>">
							<?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_NEWBUILDINGRESOURCE') ?>
							<i class="fa fa-home"></i>
						</span>

					</div>
				</div>
				<div class="bfi-clearfix bfi-hr-separ"></div>
				<!-- resource details -->
				<div class="bfi-row" >
					<div class="bfi-col-sm-5">
						<?php if (isset($resource->Rooms) && $resource->Rooms>0):?>
						<div class="bfi-icon-rooms">
							<?php echo $resource->Rooms ?> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_ROOMS') ?>
						</div>
						<?php endif; ?>
						<?php if (isset($resource->Rooms) && $resource->Rooms>0 && isset($resource->Area) && $resource->Area>0 ){?>
						- 
						<?php } ?>
						
						<?php if (isset($resource->Area) && $resource->Area>0):?>
						<div class="bfi-icon-area  ">
							<?php echo  $resource->Area ?> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_AREAMQ') ?>
						</div>
						<?php endif; ?>
					</div>
					<div class="bfi-col-sm-4 bfi-pad0-10 bfi-text-right">
						<?php if ($resource->Price != null && $resource->Price > 0 && isset($resource->IsReservedPrice) && $resource->IsReservedPrice!=1 ) {?>
							<span class="bfi-price bfi-price-total bfi_<?php echo $currencyclass ?>"> <?php echo BFCHelper::priceFormat($resource->Price,0, ',', '.')?></span>
						<?php }else{ ?>
							<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_ISRESERVEDPRICE')?>
						<?php } ?>
					
					</div>
					<div class="bfi-col-sm-3 bfi-text-right">
							<a href="<?php echo $resourceRoute ?>" class="bfi-btn eectrack" target="_blank" data-type="Sales Resource" data-id="<?php echo $resource->ResourceId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_OFFER_DETAILS')?></a>
					</div>
				</div>
				<div class="bfi-clearfix"></div>
				<!-- end resource details -->
		</div>
				<div  class="bfi-ribbonnew bfi-hide" id="ribbonnew<?php echo $resource->ResourceId?>"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_RIBBONNEW') ?></div>
	</div>
	</div>
		<?php 
		$listsId[]= $result->ResourceId;
		?>
	<?php } ?>
</div>
</div>


<script type="text/javascript">
<!--
var listToCheck = "<?php echo implode(",", $listsId) ?>";
var strAddressSimple = " ";
var strAddress = "[indirizzo] - [cap] - [comune] ([provincia])";

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

var loaded=false;
function getAjaxInformations(){
	if (!loaded)
	{
		loaded=true;		
		var query = "resourcesId=" + listToCheck + "&language=<?php echo $language ?>&task=GetResourcesOnSellByIds";

	jQuery.post(bfi_variable.bfi_urlCheck, query, function(data) {
			jQuery.each(data || [], function(key, val) {

				$html = '';

				var $indirizzo = "";
				var $cap = "";
				var $comune = "";
				var $provincia = "";
				
				if (val.IsAddressVisible)
				{
					$indirizzo = val.Address;
				}	
				$cap = val.ZipCode;
				$comune = val.CityName;
				$provincia = val.RegionName;

				addressData = strAddress.replace("[indirizzo]",$indirizzo);
				addressData = addressData.replace("[cap]",$cap);
				addressData = addressData.replace("[comune]",$comune);
				addressData = addressData.replace("[provincia]",$provincia);
				jQuery("#address"+val.ResourceId).html(addressData);

				if(val.AddedOn!= null){
					var parsedDate = new Date(parseInt(val.AddedOn.substr(6)));
					var jsDate = new Date(parsedDate); //Date object				
					var isNew = jsDate > newFromDate;
					if (isNew)
						{
							jQuery("#ribbonnew"+val.ResourceId).removeClass("bfi-hide");
						}
				}

				/* highlite seller*/
				if(val.IsHighlight){
							jQuery("#container"+val.ResourceId).addClass("com_bookingforconnector_highlight");
						}

				/*Top seller*/
				if (val.IsForeground)
					{
						jQuery("#topresource"+val.ResourceId).removeClass("bfi-hide");
//						jQuery("#borderimg"+val.ResourceId).addClass("bfi-hide");
					}

				/*Showcase seller*/
				if (val.IsShowcase)
					{
						jQuery("#topresource"+val.ResourceId).addClass("bfi-hide");
						jQuery("#showcaseresource"+val.ResourceId).removeClass("bfi-hide");
						jQuery("#lensimg"+val.ResourceId).removeClass("bfi-hide");
//						jQuery("#borderimg"+val.ResourceId).addClass("bfi-hide");
					}
				
				/*Top seller*/
				if(val.IsNewBuilding){
					jQuery("#newbuildingresource"+val.ResourceId).removeClass("bfi-hide");
				}


					jQuery(".container"+val.ResourceId).click(function(e) {
						var $target = jQuery(e.target);
						if ( $target.is("div")|| $target.is("p")) {
							document.location = jQuery( ".nameAnchor"+val.ResourceId ).attr("href");
						}
					});
			});	
		},'json');
	}
}

	function createMarkers(data, oms, bounds, currentMap) {
		jQuery.each(data, function(key, val) {
			if (val.X == '' || val.Y == '' || val.X == null || val.Y == null)
				return true;
//			console.log(val);
			var url = "<?php echo JRoute::_('index.php?option=com_bookingforconnector&view=onsellunit') ?>";
			url += '?format=raw&layout=map&resourceId=' + val.Id;
			var marker = new google.maps.Marker({
				position: new google.maps.LatLng(val.X, val.Y),
				map: currentMap
			});
			marker.url = url;
			marker.extId = val.Id;
			/*
			google.maps.event.addListener(marker, 'click', (function(marker, key) {
				return function() {
				  showMarkerInfo(marker);
//				  infowindow.setContent(marker.position.toString());
//				  infowindow.open(map, marker);
				}
			  })(marker, key));
			*/
			oms.addMarker(marker,true);
					
			bounds.extend(marker.position);
		});
	}


<?php if(count($results)>0){ ?>
	
jQuery(document).ready(function() {
	getAjaxInformations();
	jQuery('.bfi-maps-static,.bfi-search-view-maps').click(function() {
		jQuery( "#bfi-maps-popup" ).dialog({
			open: function( event, ui ) {
				openGoogleMapSearch();
			},
			height: 500,
			width: 800,
			dialogClass: 'bfi-dialog bfi-dialog-map'
		});
	});


	jQuery('.bfi-sort-item').click(function() {
		var rel = jQuery(this).attr('rel');
		var vals = rel.split("|"); 
		jQuery('#bookingforsearchFilterForm .filterOrder').val(vals[0]);
		jQuery('#bookingforsearchFilterForm .filterOrderDirection').val(vals[1]);
		jQuery('#bookingforsearchFilterForm').submit();
	})

});
<?php } ?>


//-->
</script>