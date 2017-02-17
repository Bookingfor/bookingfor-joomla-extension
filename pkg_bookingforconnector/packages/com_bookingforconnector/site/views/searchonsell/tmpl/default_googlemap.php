<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$searchid =  $this->params['searchid'];
$config = $this->config;
$locationzones = "";
if(!empty($this->params['locationzones'])){
	$locationzones =  $this->params['locationzones'];
}
$XGooglePosDef = $config->get('posx', 0);
$YGooglePosDef = $config->get('posy', 0);

$startzoom = $config->get('startzoom',14);
$googlemapsapykey = $config->get('googlemapskey','');

////$XGooglePos = htmlspecialchars($this->item->params->get('XGooglePos')); //45.406947;
////$YGooglePos = htmlspecialchars($this->item->params->get('YGooglePos')); //11.892443;
//// Menu parameters
//$input = JFactory::getApplication()->input;
//$menuitemid = $input->getInt( 'Itemid' );  // this returns the menu id number so you can reference parameters
//$menu =   JSite::getMenu();
//if ($menuitemid) {
//   $menuparams = $menu->getParams( $menuitemid );
//   $XGooglePosDef = htmlspecialchars($menuparams->get('XGooglePos'));  // This shows how to get an individual parameter for use
//   $YGooglePosDef = htmlspecialchars($menuparams->get('YGooglePos'));  // This shows how to get an individual parameter for use
//}

//echo "<pre>$this<br />";
//echo print_r($paramsmenu);
//echo "</pre>";
?>

<div id="map_canvassearch" class="searchmap"></div>

<script type="text/javascript">
<!--
		var mapSearch;
		var myLatlngsearch;
		var mcOnSell;
		var oms; // old version 
		var markersLoading = false;
		var infowindow = null;
		var markersLoaded = false;
		var mapSearchminZoom = 7;
		var mapSearchmaxZoom = 17;
		var mapOverlay;

$XGooglePos = <?php echo $XGooglePosDef?>;
$YGooglePos = <?php echo $YGooglePosDef?>;

		// make map
		function handleApiReadySearch() {

			myLatlngsearch = new google.maps.LatLng($XGooglePos, $YGooglePos);
			var myOptions = {
					zoom: <?php echo $startzoom ?>,
					maxZoom: mapSearchmaxZoom,
					minZoom: mapSearchminZoom,
					center: myLatlngsearch,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				}
			mapSearch = new google.maps.Map(document.getElementById("mod_bookingformaps-popup"), myOptions);
			loadMarkers();
		}
		
		function openGoogleMapSearch() {

			if (typeof google !== 'object' || typeof google.maps !== 'object'){
				var script = document.createElement("script");
				script.type = "text/javascript";
				script.src = "https://maps.google.com/maps/api/js?key=<?php echo $googlemapsapykey ?>&libraries=drawing&callback=handleApiReadySearch";
				document.body.appendChild(script);
			}else{
				if (typeof mapSearch !== 'object' ){
					handleApiReadySearch();
				}
			}
		}


	function loadMarkers() {
		var isVisible = jQuery('#mod_bookingformaps-popup').is(":visible");
		if (mapSearch != null && !markersLoaded && isVisible) {
			if (typeof mcOnSell !== 'object'){
				jQuery.getScript("<?php echo JURI::root()?>components/com_bookingforconnector/assets/js/markerclusterer_packed.js", function(data, textStatus, jqxhr) {
					var bounds = new google.maps.LatLngBounds();
					
					mcOptions = {
						imagePath: "https://googlemaps.github.io/js-marker-clusterer/images/m"
					};

					mcOnSell = new MarkerClusterer(mapSearch,null,mcOptions );
					if (!markersLoading) {
						jQuery.getJSON("<?php echo JRoute::_('index.php?option=com_bookingforconnector&format=json&view=searchonsell&searchid=' . $searchid . '&locationzones=' . $locationzones ); ?>", function(data) {
								createMarkers(data, mcOnSell, bounds, mapSearch);
								if (mcOnSell.getMarkers().length > 0) {
									mapSearch.fitBounds(bounds);
								}
								markersLoaded = true;
								jQuery.getScript("<?php echo JURI::root()?>components/com_bookingforconnector/assets/js/oms.min.js", function(data, textStatus, jqxhr) {					
										oms = new OverlappingMarkerSpiderfier(mapSearch, {
												keepSpiderfied : true,
												nearbyDistance : 1,
												markersWontHide : true,
												markersWontMove : true 
											});
											
//										google.maps.event.addListener(mapSearch, 'center_changed', function() {
////											// 3 seconds after the center of the map has changed, pan back to the
////											// marker.
////											window.setTimeout(function() {
////											  map.panTo(marker.getPosition());
////											}, 3000);
//											alert("center_changed")
//											var currentOms = oms.getMarkers();
//											if (currentOms.length>0)
//											{
//												mcOnSell.addMarkers(currentOms,true);
//												oms.clearMarkers();
//											}
//
//										  });
										google.maps.event.addListener(mapSearch, 'zoom_changed', function() {
											if (mapSearch.getZoom() < mapSearchmaxZoom)
											{
												var currentOms = oms.getMarkers();
												if (currentOms.length>0)
												{
													mcOnSell.addMarkers(currentOms,true);
													oms.clearMarkers();
												}
											}
										});

										google.maps.event.addListener(mcOnSell, 'click', function(cluster){
											var currentOms = oms.getMarkers();
											if (currentOms.length>0)
											{
												mcOnSell.addMarkers(currentOms,true);
												oms.clearMarkers();
											}
											var clickedMarkers = cluster.getMarkers();
											if( clickedMarkers.length>1 &&  mapSearch.getZoom() == mapSearchmaxZoom){
												mapOverlay = new google.maps.OverlayView();
												mapOverlay.draw = function() {};
												mapOverlay.setMap(mapSearch);
												var markerDataToSpiderfy = [];
//												var firstMarker = null;
												jQuery(clickedMarkers).each(function() {
													mcOnSell.removeMarker(this);
													this.setMap(mapSearch);
//													if (firstMarker==null)
//													{
//														firstMarker = this
//													}
													oms.addMarker(this);  
													markerDataToSpiderfy.push({
													  marker: this,
													  markerPt:  mapOverlay.getProjection().fromLatLngToDivPixel(this.position)
													});
												});
												//google.maps.event.trigger(firstMarker, 'click');
												window.setTimeout(function() {
												  oms.spiderfy(markerDataToSpiderfy,[]);
												}, 300);												
												

												//showMarkerInfo(clickedMarkers[0]);
											}
										});
								});


						});
					}
					markersLoading = true;
				}); //end load javascript
			} // end check mcOnSell is object
			
//			if (typeof oms !== 'object'){
//				jQuery.getScript("<?php echo JURI::root()?>components/com_bookingforconnector/assets/js/oms.min.js", function(data, textStatus, jqxhr) {
//					var bounds = new google.maps.LatLngBounds();
//					oms = new OverlappingMarkerSpiderfier(mapSearch, {
//							keepSpiderfied : true,
//							nearbyDistance : 1,
//							markersWontHide : true,
//							markersWontMove : true 
//						});
//
//					oms.addListener('click', function(marker) {
//						showMarkerInfo(marker);
//					});
//					if (!markersLoading) {
//						jQuery.getJSON("<?php echo JRoute::_('index.php?option=com_bookingforconnector&format=json&view=searchonsell&searchid=' . $searchid); ?>", function(data) {
////		prompt("<?php echo JRoute::_('index.php?option=com_bookingforconnector&format=json&view=searchonsell&searchid=' . $searchid); ?>","<?php echo JRoute::_('index.php?option=com_bookingforconnector&format=json&view=searchonsell&searchid=' . $searchid); ?>");
//								createMarkers(data, oms, bounds, mapSearch);
//								alert(oms.getMarkers().length);
//								if (oms.getMarkers().length > 0) {
//									mapSearch.fitBounds(bounds);
//								}
//								markersLoaded = true;
//						});
//					}
//					markersLoading = true;
//				});
//			}
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

//	function showMarker(extId) {
//		showMap();
//		if(!markersLoaded) {
//			setTimeout(function() {showMarker(extId)},500);
//			return;
//		}
////		jQuery(oms.getMarkers()).each(function() {
////			if (this.extId != extId) return true; 
////			var offset = jQuery('#mapcontainer').offset();
////			jQuery('html, body').scrollTop(offset.top-20);
////			showMarkerInfo(this);
////			return false;
////		});
//		var	markers = mcOnSell.getMarkers();
////		var markers = oms.getMarkers();
////		if (markers.lenght == 0)
////		{
////			markers = mcOnSell.getMarkers();
////		}
//		jQuery(markers).each(function() {
//			if (this.extId != extId) return true; 
////			var offset = jQuery('#mappa').offset();
////			jQuery('html, body').scrollTop(offset.top-20);
//			showMarkerInfo(this);
//			return false;
//		});
//	}

	function showMarker(extId) {
		if(jQuery( "#mod_bookingformaps-popup").length ){
			jQuery( "#mod_bookingformaps-popup" ).dialog({
				open: function( event, ui ) {
					openGoogleMapSearch();
					if(!markersLoaded) {
						setTimeout(function() {showMarker(extId)},500);
						return;
					}
					var	markers = mcOnSell.getMarkers();
					//jQuery(oms.getMarkers()).each(function() {
					jQuery(markers).each(function() {
						if (this.extId != extId) return true; 
//						var offset = jQuery('#mod_bookingformaps-popup').offset();
//						jQuery('html, body').scrollTop(offset.top-20);
						showMarkerInfo(this);
						return false;
					});		
				},
				height: 500,
				width: 800,
			});
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
