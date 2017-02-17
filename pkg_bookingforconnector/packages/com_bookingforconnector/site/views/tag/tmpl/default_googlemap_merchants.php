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
 */
-->
<script type="text/javascript">
<!--
	function createMarkers(data, oms, bounds, currentMap) {
		jQuery.each(data, function(key, val) {
			if (val.XGooglePos == '' || val.YGooglePos == '' || val.XGooglePos == null || val.YGooglePos == null)
				return true;

			var url = "<?php echo JRoute::_('index.php?option=com_bookingforconnector&view=merchantdetails') ?>";
			url += '?format=raw&layout=map&merchantId=' + val.MerchantId;
			
			var marker = new google.maps.Marker({
				position: new google.maps.LatLng(val.XGooglePos, val.YGooglePos),
				map: currentMap
			});

			marker.url = url;
			marker.extId = val.MerchantId;

			oms.addMarker(marker);
					
			bounds.extend(marker.position);
		});
	}
//-->
</script>
