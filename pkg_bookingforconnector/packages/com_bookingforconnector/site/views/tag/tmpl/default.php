<?php 
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$language = $this->language;
$showmap = true;
$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;
$showdata = COM_BOOKINGFORCONNECTOR_SHOWDATA;
$tagsearch = "";

$tag = $this->item;
$category = $this->category;
$showGrouped = $this->showGrouped;
$showmap = true;
$total = 0;
if(isset($this->pagination)){
	$total = $this->pagination->total;
}
if($total<1){
	$showmap = false;
}

?>   
<!-- {emailcloak=off} -->
<div class="bfi-content">
<?php if (!empty($tag)){ ?>
		<div class="bfi-title-name"><?php echo  $tag->Name?> </div>
		<div class="bfi-clearfix "></div>
		<?php if (!empty($tag->Description)){?>
		<div class="bfi-description-data bfi-row">
			<div class="bfi-description-data bfi-col-md-12">
				<?php echo $tag->Description ?>		
			</div>
		</div>
		<?php } ?>
		<div class="bfi-clearfix "></div>
	<!-- MERCHANTS -->
	<?php if ($category == 1 && $total > 0){ 
		$tagsearch = "&groupresulttype=1&merchantTagIds=".$tag->TagId;
		?>
		<?php  include(JPATH_COMPONENT.'/views/merchants/tmpl/default.php');  ?>
	<?php } ?>
	<!-- MERCHANTS -->

	<!-- RESOURCES -->
	<?php if ($category == 4 && $total > 0){ 
		?>
			<div class="com_bookingforconnector-items-container">
				<?php 
					$this->addTemplatePath( JPATH_COMPONENT . '/views/search/tmpl/' );
					$this->setLayout('tags');
					if  ($showGrouped == true) {
						$tagsearch = "&groupresulttype=1&productTagIds=".$tag->TagId;
						echo  $this->loadTemplate('merchants');
					}
					else {
						$tagsearch = "&groupresulttype=0&productTagIds=".$tag->TagId;
						echo  $this->loadTemplate('resources');
					}
					?>
					<?php if ($this->pagination->get('pages.total') > 1) { ?>
						<div class="text-center">
						<div class="pagination bfi-pagination">
							<?php echo $this->pagination->getPagesLinks(); ?>
						</div>
						</div>
					<?php } ?>
			</div>
			<div class="clearboth"></div>
			<script type="text/javascript">
			<!--
				if(typeof jQuery.fn.button.noConflict !== 'undefined'){
					var btn = jQuery.fn.button.noConflict(); // reverts $.fn.button to jqueryui btn
					jQuery.fn.btn = btn; // assigns bootstrap button functionality to $.fn.btn
				}
				jQuery(document).ready(function() {
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

			//-->
			</script>
	<?php } ?>
	<!-- RESOURCES -->
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
						var query = "task=searchjson&newsearch=0<?php echo $tagsearch ?>";
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

	function toggleMap() {
		jQuery('#mapcontainer').toggle();
		if (jQuery('#mapcontainer').is(":visible")) {
			openGoogleMapSearch();
		}
	}

	function showMap() {
//		jQuery('#mapcontainer').hide();
//		toggleMap();
		jQuery('#maptab').click();
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
<?php }else{ // showmap ?>
<script type="text/javascript">
<!--
		function openGoogleMapSearch() {}
//-->
</script>

<?php } // showmap ?>

<?php } ?>
</div>
