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
$XGooglePosDef = $config->get('posx', 0);
$YGooglePosDef = $config->get('posy', 0);

$startzoom = $config->get('startzoom',14);
$googlemapsapykey = $config->get('googlemapskey','');
//$XGooglePos = htmlspecialchars($this->item->params->get('XGooglePos')); //45.406947;
//$YGooglePos = htmlspecialchars($this->item->params->get('YGooglePos')); //11.892443;
// Menu parameters
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

	<div class="com_bookingforconnector_search-map" id="mapcontainer">
		<div id="map_canvassearch" class="searchmap" style="width:100%; height:400px"></div>
	</div>

<script type="text/javascript">
<!--
		var mapSearch;
		var myLatlngsearch;
		var mc;
		var oms; // old version 
		var markersLoading = false;
		var infowindow = null;
		var markersLoaded = false;

$XGooglePos = <?php echo $XGooglePosDef?>;
$YGooglePos = <?php echo $YGooglePosDef?>;

		// make map
		function handleApiReadySearch() {

			myLatlngsearch = new google.maps.LatLng($XGooglePos, $YGooglePos);
			var myOptions = {
					zoom: <?php echo $startzoom ?>,
					maxZoom: 17,
					minZoom:7,
					center: myLatlngsearch,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				}
			mapSearch = new google.maps.Map(document.getElementById("map_canvassearch"), myOptions);
			loadMarkers();
		}
		
		function openGoogleMapSearch() {

			if (typeof google !== 'object' || typeof google.maps !== 'object'){
				var script = document.createElement("script");
				script.type = "text/javascript";
				script.src = "https://maps.google.com/maps/api/js?key=<?php echo $googlemapsapykey ?>&libraries=drawing&sensor=false&callback=handleApiReadySearch";
				document.body.appendChild(script);
			}else{
				if (typeof mapSearch !== 'object' ){
					handleApiReadySearch();
				}
			}
		}


	function loadMarkers() {
		var isVisible = jQuery('#map_canvassearch').is(":visible");
		if (mapSearch != null && !markersLoaded && isVisible) {
			if (typeof mc !== 'object'){
				jQuery.getScript("<?php echo JURI::root()?>components/com_bookingforconnector/assets/js/markerclusterer_packed.js", function(data, textStatus, jqxhr) {
					var bounds = new google.maps.LatLngBounds();
					mc = new MarkerClusterer(mapSearch);
					if (!markersLoading) {
						jQuery.getJSON("<?php echo JRoute::_('index.php?option=com_bookingforconnector&format=json&view=searchonsell&searchid=' . $searchid); ?>", function(data) {
								createMarkers(data, mc, bounds, mapSearch);
								if (mc.getMarkers().length > 0) {
									mapSearch.fitBounds(bounds);
								}
								markersLoaded = true;
								
//								google.maps.event.addListener(mc, 'click', function(cluster){
//									alert("click")
////									var clickedMarkers = cluster.getMarkers();
////									if( clickedMarkers.length==1){
////										showMarkerInfo(clickedMarkers[0]);
////									}
//								});


						});
					}
					markersLoading = true;
				}); //end load javascript
			} // end check mc is object
			
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
		jQuery('#mapcontainer').hide();
		toggleMap();
	}

	function showMarker(extId) {
		alert("click")
		showMap();
		if(!markersLoaded) {
			setTimeout(function() {showMarker(extId)},500);
			return;
		}
//		jQuery(oms.getMarkers()).each(function() {
//			if (this.extId != extId) return true; 
//			var offset = jQuery('#mapcontainer').offset();
//			jQuery('html, body').scrollTop(offset.top-20);
//			showMarkerInfo(this);
//			return false;
//		});
		jQuery(mc.getMarkers()).each(function() {
			if (this.extId != extId) return true; 
			var offset = jQuery('#mapcontainer').offset();
			jQuery('html, body').scrollTop(offset.top-20);
			showMarkerInfo(this);
			return false;
		});
	}

	function showMarkerInfo(marker) {
		if (infowindow) infowindow.close();
		jQuery.get(marker.url, function (data) {
			infowindow = new google.maps.InfoWindow({ content: data });
			infowindow.open(mapSearch, marker);
		});		
	}
//-->
</script>
