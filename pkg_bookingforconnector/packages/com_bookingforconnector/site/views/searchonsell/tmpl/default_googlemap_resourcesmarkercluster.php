<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

?>
<!--
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */-->
<script type="text/javascript">
<!--
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
				position: new google.maps.LatLng(val.X, val.Y)
			});
			marker.url = url;
			marker.extId = val.Id;
			
			google.maps.event.addListener(marker, 'click', (function(marker, key) {
				return function() {
				  showMarkerInfo(marker);
//				  infowindow.setContent(marker.position.toString());
//				  infowindow.open(map, marker);
				}
			  })(marker, key));

			oms.addMarker(marker,true);
					
			bounds.extend(marker.position);
		});
	}
//-->
</script>
