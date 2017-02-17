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
			if (val.Resource.XGooglePos == '' || val.Resource.YGooglePos == '' || val.Resource.XGooglePos == null || val.Resource.YGooglePos == null)
				return true;

			var url = "<?php echo JRoute::_('index.php?option=com_bookingforconnector&format=raw&view=condominium') ?>";
			url += '&layout=map&resourceId=' + val.Resource.ResourceId;
			
			var marker = new google.maps.Marker({
				position: new google.maps.LatLng(val.Resource.XGooglePos, val.Resource.YGooglePos),
				map: currentMap
			});

			marker.url = url;
			marker.extId = val.Resource.ResourceId;

			oms.addMarker(marker);
					
			bounds.extend(marker.position);
		});
	}
//-->
</SCRIPT>
