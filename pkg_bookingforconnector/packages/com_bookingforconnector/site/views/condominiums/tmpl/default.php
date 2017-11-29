<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$activeMenu = JFactory::getApplication()->getMenu()->getActive();
$config = $this->config;
$isportal = $config->get('isportal', 1);
$showdata = $config->get('showdata', 1);
$posx = COM_BOOKINGFORCONNECTOR_GOOGLE_POSX;
$posy = COM_BOOKINGFORCONNECTOR_GOOGLE_POSY;
$startzoom = COM_BOOKINGFORCONNECTOR_GOOGLE_STARTZOOM;
$googlemapsapykey = COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLEMAPSKEY;
$searchid = -1;
$onlystay=false;

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$language = $this->language;

$listNameAnalytics = $this->listNameAnalytics;
$fromsearchparam = "&lna=".$listNameAnalytics;

$currSorting=$listOrder . "|" . $listDirn;

//-------------------pagina per il redirect di tutte le risorse 

$db   = JFactory::getDBO();
$uri  = 'index.php?option=com_bookingforconnector&view=condominium';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri ) .' AND (language='. $db->Quote($this->language) .' OR language='.$db->Quote('*').') AND published = 1  LIMIT 1' );
//$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
$itemId = intval($db->loadResult());
//------------------- pagina per i l redirect di tutte le risorse 

$merchantImageUrl = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";

$merchantImagePath = BFCHelper::getImageUrlResized('condominium', "[img]",'medium');
$merchantImagePathError = BFCHelper::getImageUrl('condominium', "[img]",'medium');

$merchants = $this->items;
$total = $this->pagination->total;

$itemIdMerchant=0;
$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
if($isportal){
  $db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
  $itemIdMerchant = intval($db->loadResult());
}
$counter = 0;
if($itemId == 0){
	$itemId = $itemIdMerchant;
}
$url=JFactory::getURI()->toString();
$formAction=$url;

?>
<?php if (count($merchants)>0) { ?>
<div class="bfi-content">
<?php if(!empty(COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLEMAPSKEY)){ ?>
	<div class="bfi-text-right ">
		<div class="bfi-search-view-maps ">
		<span><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_MAPVIEW') ?></span>
		</div>	
	</div>
<?php } ?>
<div class="bfi-search-menu">
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
<?php 
$listsId = array(); 
$isCondominium = true;
?>  
	<?php foreach ($merchants as  $currKey => $merchant){ ?>
		<?php 
			$currName = BFCHelper::getLanguage($merchant->Name, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
			$merchantName = $merchant->MerchantName;
			
			$rating = $merchant->Rating;
			if ($rating>9 )
			{
				$rating = $rating/10;
			} 
			
			$currUriMerchant = $uriMerchant. '&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchantName);
			$currUri = $uri. '&resourceId=' . $merchant->CondominiumId. ':' . BFCHelper::getSlug($merchant->Name);
			$merchantDescription = ""; // BFCHelper::getLanguage($resource->Description, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
			
			if ($itemId<>0)
				$currUri.='&Itemid='.$itemId;

			$currUri .= $fromsearchparam;
			$routeCondominium = JRoute::_($currUri);

			if ($itemIdMerchant<>0)
				$currUriMerchant.='&Itemid='.$itemIdMerchant;
			
			$routeMerchant = JRoute::_($currUriMerchant);
			$routeRating = JRoute::_($currUriMerchant.'&layout=ratings');				
			$routeInfoRequest = JRoute::_($currUriMerchant.'&layout=contactspopup&tmpl=component');			
			
			$counter = 0;
			$merchantLat = $merchant->XGooglePos;
			$merchantLon = $merchant->XGooglePos;
			$showMerchantMap = (($merchantLat != null) && ($merchantLon !=null));
//			$showMerchantMap = false;
			$merchantImageUrl = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";
			
			if(!empty($merchant->DefaultImg)){
				$merchantImageUrl = BFCHelper::getImageUrlResized('condominium',$merchant->DefaultImg, 'medium');
			}
			
			$merchantDescription = BFCHelper::getLanguage($merchant->Description, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 

			$merchantNameTrack =  BFCHelper::string_sanitize($merchantName);
			$merchantCategoryNameTrack = ""; // BFCHelper::string_sanitize($merchant->MainCategoryName);

			if(isset($merchant->XGooglePos) && !empty($merchant->XGooglePos)  ){
				$val= new StdClass;
				$val->Id = $merchant->CondominiumId ;
				$val->X = $merchant->XGooglePos;
				$val->Y = $merchant->YGooglePos;
				$listResourceMaps[] = $val;
			}
			$resourceDataTypeTrack =  ($isCondominium)?"Resource Group":"Resource";
			$resourceNameTrack =  BFCHelper::string_sanitize($currName);
			$merchantNameTrack =  BFCHelper::string_sanitize($merchantName);
			$merchantCategoryNameTrack =  BFCHelper::string_sanitize($merchant->MrcCategoryName);

		?>


	<div class="bfi-col-sm-6 bfi-item">
		<div class="bfi-row bfi-sameheight" >
			<div class="bfi-col-sm-3 bfi-img-container">
				<a href="<?php echo $routeCondominium ?>" style='background: url("<?php echo $merchantImageUrl; ?>") center 25% / cover;' target="_blank" class="eectrack" data-type="<?php echo $resourceDataTypeTrack ?>" data-id="<?php echo $merchant->CondominiumId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><img src="<?php echo $merchantImageUrl; ?>" class="bfi-img-responsive" /></a> 
			</div>
			<div class="bfi-col-sm-9 bfi-details-container">
				<!-- merchant details -->
				<div class="bfi-row" >
					<div class="bfi-col-sm-12">
						<div class="bfi-item-title">
							<a href="<?php echo $routeCondominium ?>" id="nameAnchor<?php echo $merchant->CondominiumId?>" target="_blank" class="eectrack" data-type="<?php echo $resourceDataTypeTrack ?>" data-id="<?php echo $merchant->CondominiumId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><?php echo  $currName ?></a> 
							<span class="bfi-item-rating">
								<?php for($i = 0; $i < $rating; $i++) { ?>
									<i class="fa fa-star"></i>
								<?php } ?>	             
							</span>
						</div>
						<div class="bfi-item-address">
							<?php if ($showMerchantMap){?>
							<a href="javascript:void(0);" onclick="showMarker(<?php echo $merchant->CondominiumId?>)"><?php }?><span id="address<?php echo $merchant->CondominiumId?>"></span><?php if ($showMerchantMap){?></a>
							<div class="bfi-hide" id="markerInfo<?php echo $merchant->CondominiumId?>">
									<div class="bfi-item-title">
										<a href="<?php echo $routeCondominium ?>" target="_blank" class="eectrack" data-type="<?php echo $resourceDataTypeTrack ?>" data-id="<?php echo $merchant->CondominiumId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><?php echo  $currName ?></a> 
										<span class="bfi-item-rating">
											<?php for($i = 0; $i < $rating; $i++) { ?>
												<i class="fa fa-star"></i>
											<?php } ?>	             
										</span>
									</div>
									<span id="mapaddress<?php echo $merchant->CondominiumId?>"></span>
							</div>
							<?php } ?>
						</div>
						<div class="bfi-mrcgroup" id="bfitags<?php echo $merchant->CondominiumId; ?>"></div>
						<div class="bfi-description" id="bfidescription<?php echo $merchant->CondominiumId; ?>"><?php echo $merchantDescription ?></div>
					</div>
				</div>
				<div class="bfi-clearfix bfi-hr-separ"></div>
				<!-- end merchant details -->
					<div class=" bfi-text-right">
							<a href="<?php echo $routeCondominium ?>" class="bfi-btn eectrack" target="_blank" data-type="<?php echo $resourceDataTypeTrack ?>" data-id="<?php echo $merchant->CondominiumId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_BUTTON') ?></a>
					</div>
				<div class="bfi-clearfix"></div>
			</div>
		</div>
	</div>
		<?php $listsId[]= $merchant->CondominiumId; ?>
	<?php } ?>
</div>
</div>
<?php if ($this->pagination->get('pages.total') > 1) : ?>
	<div class="text-center">
		<div class="pagination">
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
	</div>
<?php endif; ?>

<script type="text/javascript">
<!--

if(typeof jQuery.fn.button.noConflict !== 'undefined'){
	var btn = jQuery.fn.button.noConflict(); // reverts $.fn.button to jqueryui btn
	jQuery.fn.btn = btn; // assigns bootstrap button functionality to $.fn.btn
}

jQuery('#list-view').click(function() {
	jQuery('.bfi-view-changer-selected').html(jQuery(this).html());
	jQuery('#bfi-list').removeClass('bfi-grid-group')
	jQuery('#bfi-list .bfi-item').addClass('bfi-list-group-item')
	jQuery('#bfi-list .bfi-img-container').addClass('bfi-col-sm-3')
	jQuery('#bfi-list .bfi-details-container').addClass('bfi-col-sm-9')

	localStorage.setItem('display', 'list');
});

jQuery('#grid-view').click(function() {
	jQuery('.bfi-view-changer-selected').html(jQuery(this).html());
	jQuery('#bfi-list').addClass('bfi-grid-group')
	jQuery('#bfi-list .bfi-item').removeClass('bfi-list-group-item')
	jQuery('#bfi-list .bfi-img-container').removeClass('bfi-col-sm-3')
	jQuery('#bfi-list .bfi-details-container').removeClass('bfi-col-sm-9')
	localStorage.setItem('display', 'grid');
});
	jQuery('#bfi-list .bfi-item').addClass('bfi-grid-group-item')

if (localStorage.getItem('display')) {
	if (localStorage.getItem('display') == 'list') {
		jQuery('#list-view').trigger('click');
	} else {
		jQuery('#grid-view').trigger('click');
	}
} else {
	 if(typeof bfi_variable === 'undefined' || bfi_variable.bfi_defaultdisplay === 'undefined') {
		jQuery('#list-view').trigger('click');
	 } else {
		if (bfi_variable.bfi_defaultdisplay == '1') {
			jQuery('#grid-view').trigger('click');
		} else { 
			jQuery('#list-view').trigger('click');
		}
	}
}

var listToCheck = "<?php echo implode(",", $listsId) ?>";
var strAddress = "[indirizzo] - [cap] - [comune] ([provincia])";
var imgPathMG = "<?php echo BFCHelper::getImageUrlResized('tag','[img]', 'merchant_merchantgroup') ?>";
var imgPathMGError = "<?php echo BFCHelper::getImageUrl('tag','[img]', 'merchant_merchantgroup') ?>";

var shortenOption = {
		moreText: "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_VIEW_READMORE')?>",
		lessText: "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_VIEW_READLESS')?>",
		showChars: '250'
};


var mg = [];

var loaded=false;
function getAjaxInformations(){
	if (!loaded)
	{
		loaded=true;
		var queryMG = "task=getResourceGroups";
		jQuery.post(bfi_variable.bfi_urlCheck, queryMG, function(data) {
				if(data!=null){
					jQuery.each(JSON.parse(data) || [], function(key, val) {
						if (val.ImageUrl!= null && val.ImageUrl!= '') {
							var $imageurl = imgPathMG.replace("[img]", val.ImageUrl );		
							var $imageurlError = imgPathMGError.replace("[img]", val.ImageUrl );		
							/*--------getName----*/
							var $name = bookingfor.getXmlLanguage(val.Name,bfi_variable.bfi_cultureCode, bfi_variable.bfi_defaultcultureCode);
							/*--------getName----*/
							mg[val.TagId] = '<img src="' + $imageurl + '" onerror="this.onerror=null;this.src=\'' + $imageurlError + '\';" alt="' + $name + '" data-toggle="tooltip" title="' + $name + '" />';
						} else {
							if (val.IconSrc != null && val.IconSrc != '') {
								mg[val.TagId] = '<i class="fa ' + val.IconSrc + '" data-toggle="tooltip" title="' + val.Name + '"> </i> ';
							}
						}
					});	
				}
				getlist();
		},'json');
	}
}

function getlist(){
	var query = "ids=" + listToCheck + "&language=<?php echo $language ?>&task=GetCondominiumsByIds";
	if(listToCheck!='')
	

	jQuery.post(bfi_variable.bfi_urlCheck, query, function(data) {
			var eecitems = [];

				if(typeof callfilterloading === 'function'){
					callfilterloading();
					callfilterloading = null;
				}
			jQuery.each(data || [], function(key, val) {
				$html = '';
				eecitems.push({
					id: "" + val.CondominiumId+ " - Resource Group",
					name: val.Name,
					category: val.MrcCategoryName,
					brand: val.MerchantName,
					position: key
				});
	
				var $indirizzo = "";
				var $cap = "";
				var $comune = "";
				var $provincia = "";
				
				$indirizzo = val.Address;
				$cap = val.ZipCode;
				$comune = val.CityName;
				$provincia = val.RegionName;

				addressData = strAddress.replace("[indirizzo]",$indirizzo);
				addressData = addressData.replace("[cap]",$cap);
				addressData = addressData.replace("[comune]",$comune);
				addressData = addressData.replace("[provincia]",$provincia);
				jQuery("#address"+val.CondominiumId).html(addressData);
				jQuery("#mapaddress"+val.CondominiumId).append(addressData);

<?php if($showdata): ?>
				if (val.Description!= null && val.Description != ''){
					$html += bookingfor.nl2br(jQuery("<p>" + val.Description + "</p>").text());
				}
				jQuery("#descr"+val.CondominiumId).data('jquery.shorten', false);
				jQuery("#descr"+val.CondominiumId).html($html);
				
				jQuery("#descr"+val.CondominiumId).removeClass("com_bookingforconnector_loading");
				jQuery("#descr"+val.CondominiumId).shorten(shortenOption);
<?php endif; ?>

				if (val.TagsIdList!= null && val.TagsIdList != '')
				{
					var mglist = val.TagsIdList.split(',');
					$htmlmg = '';
					jQuery.each(mglist, function(key, mgid) {
						if(typeof mg[mgid] !== 'undefined' ){
							$htmlmg += mg[mgid];
						}
					});
					jQuery("#bfitags"+val.CondominiumId).html($htmlmg);
				}			

				jQuery(".container"+val.CondominiumId).click(function(e) {
					var $target = jQuery(e.target);
					if ( $target.is("div")|| $target.is("p")) {
						document.location = jQuery( ".nameAnchor"+val.CondominiumId).attr("href");
					}
				});
		});	
		jQuery('[data-toggle="tooltip"]').tooltip({
			position : { my: 'center bottom', at: 'center top-10' },
			tooltipClass: 'bfi-tooltip bfi-tooltip-top '
		}); 
		<?php if($this->analyticsEnabled): ?>
		callAnalyticsEEc("addImpression", eecitems, "list");
		<?php endif; ?>
		},'json');
}

		var mapSearch;
		var myLatlngsearch;
		var oms;
		var markersLoading = false;
		var infowindow = null;
		var markersLoaded = false;

		// make map
		function handleApiReadySearch() {
			myLatlngsearch = new google.maps.LatLng(<?php echo $posy ?>, <?php echo $posx ?>);
			var myOptions = {
					zoom: <?php echo $startzoom ?>,
					center: myLatlngsearch,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				}
			mapSearch = new google.maps.Map(document.getElementById("bfi-maps-popup"), myOptions);
			loadMarkers();
		}
		
		function openGoogleMapSearch() {

			if (typeof google !== 'object' || typeof google.maps !== 'object'){
				var script = document.createElement("script");
				script.type = "text/javascript";
				script.src = "https://maps.google.com/maps/api/js?key=<?php echo $googlemapsapykey ?>&libraries=drawing,places&callback=handleApiReadySearch";
				document.body.appendChild(script);
			}else{
				if (typeof mapSearch !== 'object' ){
					handleApiReadySearch();
				}
			}
		}

	var bfiCurrMarkerId = 0;

	function loadMarkers() {
		var isVisible = jQuery('#bfi-maps-popup').is(":visible");
		 bookingfor.waitSimpleBlock(jQuery('#bfi-maps-popup'));
		if (mapSearch != null && !markersLoaded && isVisible) {
			if (typeof oms !== 'object'){
				jQuery.getScript("<?php echo JURI::root()?>components/com_bookingforconnector/assets/js/oms.js", function(data, textStatus, jqxhr) {
					var bounds = new google.maps.LatLngBounds();
					oms = new OverlappingMarkerSpiderfier(mapSearch, {
							keepSpiderfied : true,
							nearbyDistance : 1,
							markersWontHide : true,
							markersWontMove : true 
						});

					oms.addListener('click', function(marker) {
						showMarkerInfo(marker);
					});
					if (!markersLoading) {
						var data = <?php echo json_encode($listResourceMaps) ?>; 
						createMarkers(data, oms, bounds, mapSearch);
						if (oms.getMarkers().length > 0) {
							mapSearch.fitBounds(bounds);
						}
						markersLoaded = true;
						jQuery(jQuery('#bfi-maps-popup')).unblock();
						if(bfiCurrMarkerId>0){
							setTimeout(function() {
								showMarker(bfiCurrMarkerId);
								bfiCurrMarkerId = 0;
								},10);
						}						
						
					}
					markersLoading = true;

				});
			}
		}
	}

	function createMarkers(data, oms, bounds, currentMap) {
		jQuery.each(data, function(key, val) {
			if (val.X == '' || val.Y == '' || val.X == null || val.Y == null)
				return true;
			var marker = new google.maps.Marker({
				position: new google.maps.LatLng(val.X, val.Y),
				map: currentMap
			});
			marker.extId = val.Id;
			oms.addMarker(marker,true);
			bounds.extend(marker.position);
		});
	}

	function showMarker(extId) {
		if(jQuery( "#bfi-maps-popup").length ){
			if(jQuery( "#bfi-maps-popup").hasClass("ui-dialog-content") && jQuery( "#bfi-maps-popup" ).dialog("isOpen" )){
						jQuery(oms.getMarkers()).each(function() {
							if (this.extId != extId) return true; 
							showMarkerInfo(this);
							return false;
						});		
			
			}else{
				jQuery( "#bfi-maps-popup" ).dialog({
					open: function( event, ui ) {
						if(!markersLoaded) {
							bfiCurrMarkerId = extId;
						}
						openGoogleMapSearch();
						if(!markersLoaded) {
							return;
						}
						jQuery(oms.getMarkers()).each(function() {
							if (this.extId != extId) return true; 
							showMarkerInfo(this);
							return false;
						});		
					},
					height: 500,
					width: 800,
					dialogClass: 'bfi-dialog bfi-dialog-map'
				});
			}
		}
	}

	function showMarkerInfo(marker) {
		if (infowindow) infowindow.close();
			var data = jQuery("#markerInfo"+marker.extId).html();
//			mapSearch.setZoom(17);
			mapSearch.setCenter(marker.position);
			infowindow = new google.maps.InfoWindow({ content: data });
			infowindow.open(mapSearch, marker);
	}

	
jQuery(document).ready(function() {
	getAjaxInformations();
	if(jQuery( "#bfi-maps-popup").length == 0) {
		jQuery("body").append("<div id='bfi-maps-popup'></div>");
	}
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

	jQuery(".bfi-description").shorten(shortenOption);

});


//-->
</script>



<?php } 
