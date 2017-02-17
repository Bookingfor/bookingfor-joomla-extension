<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$merchant = $this->item;

//$maxCapacityPaxes = $resource->MaxCapacityPaxes;
//$minCapacityPaxes = $resource->MinCapacityPaxes;

$Extras =  null; //$this->Extras;
$PriceTypes =  null; //$this->PriceTypes;
$MerchantBookingTypes = null; //$this->MerchantBookingTypes;

?>
<div id="calculator" class="ajaxReload"><?php echo  $this->loadTemplate('resource'); ?></div>

<script type="text/javascript">
<!--
	jQuery(document).ready(function(){

		jQuery('a.boxedpopup').on('click', function (e) {
			var width = jQuery(window).width()*0.9;
			var height = jQuery(window).height()*0.9;
				if(width>800){width=870;}
				if(height>600){height=600;}

			e.preventDefault();
			var page = jQuery(this).attr("href")
			var pagetitle = jQuery(this).attr("title")
			var $dialog = jQuery('<div id="boxedpopupopen"></div>')
				.html('<iframe style="border: 0px; " src="' + page + '" width="100%" height="100%"></iframe>')
				.dialog({
					autoOpen: false,
					modal: true,
					height:height,
					width: width,
					fluid: true, //new option
					title: pagetitle
				});
			$dialog.dialog('open');
		});

		jQuery(window).resize(function() {
			var bpOpen = jQuery("#boxedpopupopen");
				var wWidth = jQuery(window).width();
				var dWidth = wWidth * 0.9;
				var wHeight = jQuery(window).height();
				var dHeight = wHeight * 0.9;
				if(dWidth>800){dWidth=870;}
				if(dHeight>600){dHeight=600;}
					bpOpen.dialog("option", "width", dWidth);
					bpOpen.dialog("option", "height", dHeight);
					bpOpen.dialog("option", "position", "center");
		});


	});
	
//-->
</script>
