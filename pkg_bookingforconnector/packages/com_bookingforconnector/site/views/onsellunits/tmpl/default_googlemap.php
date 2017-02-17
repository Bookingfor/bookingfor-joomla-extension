<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

//$searchid =  $this->params['searchid'];
//$locationzones =  $this->params['locationzones'];
$locationzones = "";
$config = $this->config;
$XGooglePosDef = $config->get('posx', 0);
$YGooglePosDef = $config->get('posy', 0);

$startzoom = $config->get('startzoom',14);
$googlemapsapykey = $config->get('googlemapskey','');

?>
		<div id="map_canvassearch" class="searchmap" style="width:100%; height:400px"></div>

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
			myLatlngsearch = new google.maps.LatLng(1, <?php echo $YGooglePosDef ?>);
			var myOptions = {
					zoom: <?php echo $startzoom ?>,
					maxZoom: 17,
					minZoom:14,
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
				script.src = "https://maps.google.com/maps/api/js?key=<?php echo $googlemapsapykey ?>&callback=handleApiReadySearch";
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
			if (typeof oms !== 'object'){
				jQuery.getScript("<?php echo JURI::root()?>components/com_bookingforconnector/assets/js/oms.min.js", function(data, textStatus, jqxhr) {
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
						jQuery.getJSON("<?php echo JRoute::_('index.php?option=com_bookingforconnector&format=json&view=onsellunits&locationzones=' . $locationzones ); ?>", function(data) {
								createMarkers(data, oms, bounds, mapSearch);
								if (oms.getMarkers().length > 0) {
									mapSearch.fitBounds(bounds);
								}
								markersLoaded = true;
						});
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
		if(jQuery( "#mod_bookingformaps-popup").length ){
			jQuery( "#mod_bookingformaps-popup" ).dialog({
				open: function( event, ui ) {
					openGoogleMapSearch();
					if(!markersLoaded) {
						setTimeout(function() {showMarker(extId)},500);
						return;
					}
					jQuery(oms.getMarkers()).each(function() {
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

	function createMarkers(data, oms, bounds, currentMap) {
		jQuery.each(data, function(key, val) {
			if (val.X == '' || val.Y == '' || val.X == null || val.Y == null)
				return true;

			var url = "<?php echo JRoute::_('index.php?option=com_bookingforconnector&format=raw&view=onsellunit') ?>";
			url += '&layout=map&resourceId=' + val.Id;
			
//			var marker = new google.maps.Marker({
//				position: new google.maps.LatLng(val.X, val.Y),
//				map: currentMap
//			});

			var marker = new google.maps.Marker({
				position: new google.maps.LatLng(val.X, val.Y),
				map: currentMap
			});

			marker.url = url;
			marker.extId = val.Id;
			
//			google.maps.event.addListener(marker, 'click', (function(marker, key) {
//				return function() {
//				  showMarkerInfo(marker);
////				  infowindow.setContent(marker.position.toString());
////				  infowindow.open(map, marker);
//				}
//			  })(marker, key));

			oms.addMarker(marker,true);
					
			bounds.extend(marker.position);
		});
	}


//-->
</script>