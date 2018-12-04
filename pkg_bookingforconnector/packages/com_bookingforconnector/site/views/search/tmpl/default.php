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

$language = $this->language;
$showmap = true;
$total = $this->pagination->total;
$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;
$showdata = COM_BOOKINGFORCONNECTOR_SHOWDATA;

$showmap = true;
if($total<1){
	$showmap = false;
}

$currParam = BFCHelper::getSearchParamsSession();
$merchantResults = false;
$condominiumsResults = false;
$variationPlanIds = '';
$currencyclass = bfi_get_currentCurrency();
$checkin = BFCHelper::getStayParam('checkin', new DateTime('UTC'));
$checkout = BFCHelper::getStayParam('checkout', new DateTime('UTC'));
$duration = $checkin->diff($checkout)->format('%a');
$paxes = 2;
$paxages = array();
$points =  '';
$merchantCategoryId = '';
$masterTypeId = '';
$availabilitytype = 1;
$itemtypes = '0';
$merchantTagIds = '';
$condominiumId = '';
$merchantIds = '';
$stateIds = '';
$regionIds = '';
$cityIds = '';
$newsearch = 0;
if (!empty($currParam)){
	$merchantResults = isset($currParam['merchantResults']) ? $currParam['merchantResults']: $merchantResults ;
	$condominiumsResults = isset($currParam['condominiumsResults']) ? $currParam['condominiumsResults']: $condominiumsResults ;
	$variationPlanIds = !empty($currParam['variationPlanIds']) ? $currParam['variationPlanIds'] : $variationPlanIds;
//	if (!empty($currParam['paxes'])) {
//		$paxes = $currParam['paxes'];
//	}
//	if (!empty($currParam['paxages'])) {
//		$paxages = $currParam['paxages'];
//	}
	$paxes = !empty($currParam['paxes']) ? $currParam['paxes'] : $paxes ;
	$paxages = !empty($currParam['paxages']) ? $currParam['paxages'] : $paxages ;
	$points = !empty($currParam['points']) ? $currParam['points'] : $points ;
	$merchantCategoryId = !empty($currParam['merchantCategoryId']) ? $currParam['merchantCategoryId'] : $merchantCategoryId ;
	$masterTypeId = !empty($currParam['masterTypeId']) ? $currParam['masterTypeId'] : $masterTypeId ;
	$availabilitytype = isset($currParam['availabilitytype']) ? $currParam['availabilitytype'] : $availabilitytype ;
	$itemtypes = !empty($currParam['itemtypes']) ? $currParam['itemtypes'] : $itemtypes ;
	$merchantTagIds = !empty($currParam['merchantTagIds']) ? $currParam['merchantTagIds'] : $merchantTagIds ;
	$merchantIds = !empty($currParam['merchantIds']) ? $currParam['merchantIds'] : $merchantIds ;
	$stateIds = !empty($currParam['stateIds']) ? $currParam['stateIds'] : $stateIds ;
	$regionIds = !empty($currParam['regionIds']) ? $currParam['regionIds'] : $regionIds ;
	$cityIds = !empty($currParam['cityIds']) ? $currParam['cityIds'] : $cityIds ;
	$newsearch = !empty($currParam['newsearch']) ? $currParam['newsearch'] : $newsearch ;
}

if (empty($paxages)){
	$paxes = 2;
	$paxages = array(BFCHelper::$defaultAdultsAge, BFCHelper::$defaultAdultsAge);
}
if(empty( $condominiumId )){
	$condominiumId = BFCHelper::getVar('condominiumId','');
}

/*---------- track analytics   ---*/
if(!empty( $newsearch )){

$nad = 0;
$nch = 0;
$nse = 0;
$countPaxes = 0;
$maxchildrenAge = (int)BFCHelper::$defaultAdultsAge-1;
$nchs = array(null,null,null,null,null,null);
if(is_array($paxages)){
	$countPaxes = array_count_values($paxages);
	$nchs = array_values(array_filter($paxages, function($age) {
		if ($age < (int)BFCHelper::$defaultAdultsAge)
			return true;
		return false;
	}));
}
array_push($nchs, null,null,null,null,null,null);
if($countPaxes>0){
	foreach ($countPaxes as $key => $count) {
		if ($key >= BFCHelper::$defaultAdultsAge) {
			if ($key >= BFCHelper::$defaultSenioresAge) {
				$nse += $count;
			} else {
				$nad += $count;
			}
		} else {
			$nch += $count;
		}
	}
}
$nchs = array_slice($nchs,0,$nch);

$track = array($merchantCategoryId,$masterTypeId,$checkin->format('d/m/Y'),$checkout->format('d/m/Y'),$nad,$nse,$nch,implode(',',$nchs),$itemtypes,$total,$merchantIds,$stateIds,$regionIds,$cityIds);
$trackstr = implode('|',$track);
if(strlen($trackstr) > 500){
	$trackstr = substr($trackstr, 0, 500);
}
?>
<script type="text/javascript">
<!--
if (typeof(ga) !== 'undefined') {
	ga('send', 'event', 'Bookingfor - Search', 'Search', '<?php echo $trackstr ?>');
}
	
//-->
</script>
<?php } ?>
<div id="bfi-merchantlist">
	<div>
	<?php 
	if (!empty($variationPlanIds )) {
	    
	$offers = json_decode(BFCHelper::getDiscountDetails($variationPlanIds,$language));
	
	foreach ($offers as $offer ) {
		if (!empty($offer)){ ?>
		<div class="bfi-content">
			<div class="bfi-title-name"><h1><?php echo  $offer->Name?></h1> </div>
			<?php if (!empty($offer->Description)) {?>
			<div class="bfi-description">
					<?php echo BFCHelper::getLanguage($offer->Description, $language, null, array( 'striptags'=>'striptags', 'bbcode'=>'bbcode','ln2br'=>'ln2br')); ?>
			</div>
			<?php } ?>
		</div>
		<div class="bfi-clearfix "></div>
		<?php 
			} 
		}
	}
	?>

		<?php if ($total > 0){ ?>
			<div>
				<?php 
					if($merchantResults) {
						echo  $this->loadTemplate('merchants');
					}
					elseif ($condominiumsResults) {
						echo  $this->loadTemplate('condominiums');
					}
					else {
						echo  $this->loadTemplate('resources');
					}
					?>
					<?php if ($this->pagination->get('pages.total') > 1) { ?>
<?php 
				if($merchantResults) {
								$this->pagination->setAdditionalUrlParam("merchantResults", 1);
		}
		?>
						<div class="text-center">
						<div class="pagination bfi-pagination">
							<?php echo $this->pagination->getPagesLinks(); ?>
						</div>
						</div>
					<?php } ?>
			</div>
		<?php }else{ ?>
		<div class="bfi-content">
				<div class="bfi-noresults">

				<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_SEARCHRESULTS_NORESULTS') ?>
						<?php 
						if($isportal ){
							
		$listMerchants = JModelLegacy::getInstance('Merchants', 'BookingForConnectorModel');
		$state		= $listMerchants->getState();
		$params = $state->params;

		$listMerchantsparams = BFCHelper::getSearchMerchantParamsSession();
		$locationzone = BFCHelper::getVar('locationzone',"");
		$categoryId = BFCHelper::getVar('merchantCategoryId');
		
		if (!empty($locationzone)) {
			$params['zoneIds'] = $locationzone ;
		}
		if (!empty($categoryId)) {
			$params['categoryId'] = explode(',',$categoryId );
		}
		
		$listMerchants->setParam($params);
		BFCHelper::setSearchMerchantParamsSession($listMerchantsparams);
		$items = $listMerchants->getItems();
		$pagination	= $listMerchants->getPagination();
//		$pagination->setAdditionalUrlParam("filter_order", $sortColumn);
//		$pagination->setAdditionalUrlParam("filter_order_Dir", $sortDirection);
//		$pagination->setAdditionalUrlParam("startswith", $startswith);
//		$pagination->setAdditionalUrlParam("searchseed", $searchseed);
		$pagination->setAdditionalUrlParam("newsearch", 0);
		$pagination->setAdditionalUrlParam("altsearch", 1);
		$this->addTemplatePath( JPATH_COMPONENT . '/views/merchants/tmpl/' );
		$this->items = $items;
		$this->pagination = $pagination;
		$this->altsearch = 1;
		if (count((array)$items)>0) {
		?>
							<div class="bfi-content">
								<div class="bfi-check-more" data-type="merchant" data-id="<?php echo $items[0]->MerchantId?>" >
									<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_SEARCHRESULTS_NORESULTS_MOREAVAILABILITY') ?>
									<div class="bfi-check-more-slider">
									</div>
								</div>
							</div>

		<?php 
			echo  $this->loadTemplate();
		    
		}

							
							//echo  $this->loadTemplate('merchantscategoryid');
						}else{
							echo  $this->loadTemplate('contacts');
						}
						?>
				
				</div>
		</div>
		<?php } ?>
		<div class="bfi-clearboth"></div>		
	</div>
</div>
<script type="text/javascript">
<!--
if(typeof jQuery.fn.button.noConflict !== 'undefined'){
	var btn = jQuery.fn.button.noConflict(); // reverts $.fn.button to jqueryui btn
	jQuery.fn.btn = btn; // assigns bootstrap button functionality to $.fn.btn
}
var shortenOption = {
		moreText: "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_VIEW_READMORE')?>",
		lessText: "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_VIEW_READLESS')?>",
		showChars: '250'
};

jQuery(document).ready(function() {
	jQuery('#list-view').click(function() {
		jQuery('.bfi-view-changer-selected').html(jQuery(this).html());
		jQuery('#bfi-list').removeClass('bfi-grid-group')
		jQuery('#bfi-list .bfi-item').addClass('bfi-list-group-item')
		jQuery('#bfi-list .bfi-img-container').addClass('bfi-col-sm-3')
		jQuery('#bfi-list .bfi-details-container').addClass('bfi-col-sm-9')
		jQuery('#bfi-list').trigger('cssClassChanged')
		localStorage.setItem('display', 'list');
	});

	jQuery('#grid-view').click(function() {
		jQuery('.bfi-view-changer-selected').html(jQuery(this).html());
		jQuery('#bfi-list').addClass('bfi-grid-group')
		jQuery('#bfi-list .bfi-item').removeClass('bfi-list-group-item')
		jQuery('#bfi-list .bfi-img-container').removeClass('bfi-col-sm-3')
		jQuery('#bfi-list .bfi-details-container').removeClass('bfi-col-sm-9')
		jQuery('#bfi-list').trigger('cssClassChanged')
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
	jQuery(".bfi-description").shorten(shortenOption);

	bfiCheckOtherAvailability();
});

	function showResponse(responseText, statusText, xhr, $form)  { 
		jQuery('#bfi-merchantlist').unblock();
		if(typeof getAjaxInformations === 'function' ) {
			getAjaxInformations();
		}
			// reset map
			mapSearch = undefined;
			oms =  undefined;

			// Attach modal behavior to document
			if (typeof(SqueezeBox) !== 'undefined'){
				SqueezeBox.initialize({});
				SqueezeBox.assign($$('#bfi-merchantlist  a.boxed'), { //change the divid (#contentarea) as to the div that you use for refreshing the content
					parse: 'rel'
				});
			}

			if (jQuery.prototype.masonry){
				jQuery('.main-siderbar, .main-siderbar1').masonry('reload');
			}
	}
	function showError(responseText, statusText, xhr, $form)  { 
		jQuery('#bfi-merchantlist').html('<?php echo JText::_('MOD_BOOKINGFORSEARCHFILTER_NORESULT') ?>')
		jQuery('#bfi-merchantlist').unblock();
	}

	function bfiCheckOtherAvailability() {
		jQuery(".bfi-check-more").each(function(){
			var currType = jQuery(this).attr("data-type") || "";
			var currId = jQuery(this).attr("data-id");
			var currdata = {
				checkin: '<?php echo $checkin->format('Ymd'); ?>',
				duration: <?php echo $duration ?>,
				paxes:  <?php echo $paxes ?>,
				paxages:  '<?php echo implode('|',$paxages) ?>',
//				merchantId:  currId,
				condominiumId:  '<?php echo $condominiumId ?>',
				//resourceId:  currResourceId,
				cultureCode:  bfi_variable.bfi_cultureCode,
				points:  '<?php echo $points ?>',
				//	userid:  bookingfor.getUrlParameter("userid"),
				//tagids:  currTag,
				//merchantsList:  '',
				availabilityTypes:  '<?php echo $availabilitytype ?>',
				itemTypeIds:  '<?php echo $itemtypes ?>',
				//	domainLabel:  bookingfor.getUrlParameter("availabilitytype"),
				merchantCategoryIds:  '<?php echo $merchantCategoryId ?>',
				masterTypeIds:  '<?php echo $masterTypeId ?>',
				merchantTagsIds:  '<?php echo $merchantTagIds ?>',
				task:'GetAlternativeDates'
				};
				if(currType=="merchant"){
					currdata.merchantId = currId;
				}
				if(currType=="resource"){
					currdata.resourceId = currId;
				}
//			var query = "merchantsId=" + currMerchant + "&language=<?php echo $language ?>&task=GetOtherAvailability&checkin=<?php echo $checkin->format('dmY'); ?>";
			var currThis = this;
			bookingfor.waitSimpleWhiteBlock(jQuery(currThis));
			jQuery.post(bfi_variable.bfi_urlCheck, currdata, function(data) {
				jQuery(currThis).unblock();
				var currSlider = jQuery(currThis).find(".bfi-check-more-slider").first();
				var initialSlide = 0;
				var totalAlt = 0;
				var tt = JSON.parse(data);
				jQuery.each(JSON.parse(data) || [], function(key, val) {
					totalAlt = key;
					var price = val.BestValue ;
					var minstay = val.Duration ;
					if (initialSlide ==0 && val.Duration  == <?php echo $duration ?>)
					{
						initialSlide = key;
					}
					var checkinDate =  bookingfor.parseODataDate(val.StartDate);
					var checkoutDate = bookingfor.parseODataDate(val.EndDate);
					
					month1 = bookingfor.pad((checkinDate.getMonth() + 1),2);
					month2 = bookingfor.pad((checkoutDate.getMonth() + 1),2);
					day1 = bookingfor.pad((checkinDate.getDate()),2);
					day2 = bookingfor.pad((checkoutDate.getDate()),2);
					day1week = bookingfor.pad((checkinDate.getDay()),2);
					day2week = bookingfor.pad((checkoutDate.getDay()),2);
					if (typeof Intl == 'object' && typeof Intl.NumberFormat == 'function') {
						month1 = checkinDate.toLocaleString(localeSetting, { month: "short" });              
						month2 = checkoutDate.toLocaleString(localeSetting, { month: "short" });            
						day1week = checkinDate.toLocaleString(localeSetting, { weekday: "short" });   
						day2week = checkoutDate.toLocaleString(localeSetting, { weekday: "short" }); 
					}

					var currSearchUrl = window.location.href; 
					currSearchUrl = bookingfor.updateQueryStringParameter(currSearchUrl,"checkin",bookingfor.convertDateToIta(checkinDate));
					currSearchUrl = bookingfor.updateQueryStringParameter(currSearchUrl,"checkout",bookingfor.convertDateToIta(checkoutDate));
					currSearchUrl = bookingfor.updateQueryStringParameter(currSearchUrl,"limitstart",0);
					currSearchUrl = bookingfor.updateQueryStringParameter(currSearchUrl,"start",0);
//					if(currType=="merchant"){
//						currSearchUrl = bookingfor.updateQueryStringParameter(currSearchUrl,"filter_merchantid","merchantid:" + currId);
//					}
//					console.log(currSearchUrl);
					var curravailabilitytype = bookingfor.getUrlParameter("availabilitytype");
					var strSummaryDays = '<span class="bfi-check-more-los">'+minstay+' <?php echo strtolower (JTEXT::_('MOD_BOOKINGFORSEARCH_NIGHT')) ?>, '+day1week+' – '+day2week+'</span>'; 
					if (curravailabilitytype == "0") {
						minstay += 1;
						strSummaryDays ='<span class="bfi-check-more-los">'+minstay+' <?php echo strtolower (JTEXT::_('MOD_BOOKINGFORSEARCH_DAYS')) ?>, '+day1week+' – '+day2week+'</span>'; 
					}
					if(days<1){strSummaryDays ="";}

					currSlider.append('<a href="'+currSearchUrl+'"><span class="bfi-check-more-dates">'+day1+' '+month1+' - '+day2+' '+month2+'</span>'+strSummaryDays+'<span class="bfi-check-more-price"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCH_VIEW_TOTALFOR_FROM') ?> <span class="bfi_<?php echo $currencyclass ?>">'+bookingfor.priceFormat(price, 2, ',', '.')+'</span></span></a>');
				});	
				var currSliderWidth = jQuery(currThis).width()-80;
				jQuery(currSlider).width(currSliderWidth);
				var ncolslick = Math.round(currSliderWidth/120);
				if (totalAlt>0)
				{
					jQuery(currSlider).slick({
						dots: false,
						draggable: false,
						arrows: true,
						initialSlide: initialSlide,
						infinite: false,
						slidesToShow: ncolslick,
						slidesToScroll: ncolslick,
					});
				}
				
			});

		});

	}

//-->
</script>
<?php if ($showmap) {  
$posx = COM_BOOKINGFORCONNECTOR_GOOGLE_POSX;
$posy = COM_BOOKINGFORCONNECTOR_GOOGLE_POSY;
$startzoom = COM_BOOKINGFORCONNECTOR_GOOGLE_STARTZOOM;
$googlemapsapykey = COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLEMAPSKEY;

?>
<div class="bfi-clearboth"></div>
<div id="bfi-maps-popup"></div>

<script type="text/javascript">
<!--
		var mapSearch;
		var myLatlngsearch;
		var oms;
		var markersLoading = false;
		var infowindow = null;
		var markersLoaded = false;

		// make map
		function handleApiReadySearch() {
			if (typeof MarkerWithLabel !== 'function' ){
				var script = document.createElement("script");
				script.type = "text/javascript";
				script.src = "<?php echo JURI::root()?>components/com_bookingforconnector/assets/js/markerwithlabel.js";
				document.body.appendChild(script);
			}

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
			if(jQuery( "#bfi-maps-popup").length == 0) {
				jQuery("body").append("<div id='bfi-maps-popup'></div>");
			}
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
//						jQuery.getJSON(urlCheck + '?' + 'task=searchjson&newsearch=0', function(data) {
						var query = "task=searchjson&newsearch=0";
						var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
						for(var i = 0; i < hashes.length; i++)
						{
							hash = hashes[i].split('=');
							if(hash[0]!="newsearch"){
								query += "&" + hashes[i];
							}
						}

						jQuery.post(bfi_variable.bfi_urlCheck, query, function(data) {

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

						},'json');
					}
					markersLoading = true;

				});
			}
		}
	}


	function showMarker(extId) {
		if(jQuery( "#bfi-maps-popup").length ){
			if(jQuery( "#bfi-maps-popup").hasClass("ui-dialog-content") && jQuery( "#bfi-maps-popup" ).dialog("isOpen" )){
						jQuery(oms.getMarkers()).each(function() {
							if (this.extId != extId) return true; 
	//						var offset = jQuery('#bfi-maps-popup').offset();
	//						jQuery('html, body').scrollTop(offset.top-20);
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
	//						setTimeout(function() {showMarker(extId)},500);
							return;
						}
						jQuery(oms.getMarkers()).each(function() {
							if (this.extId != extId) return true; 
	//						var offset = jQuery('#bfi-maps-popup').offset();
	//						jQuery('html, body').scrollTop(offset.top-20);
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
		jQuery.get(marker.url, function (data) {
			mapSearch.setZoom(17);
			mapSearch.setCenter(marker.position);
			infowindow = new google.maps.InfoWindow({ content: data });
			infowindow.open(mapSearch, marker);
		});		
	}


//-->
</script>
<?php }else{ // showmap 
	if ($total > 0){ 
?>
<script type="text/javascript">
<!--
		function openGoogleMapSearch() {}
//-->
</script>

<?php
	}
} // showmap ?>
