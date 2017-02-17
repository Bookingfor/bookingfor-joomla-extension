<?php 
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');


$pathbase = JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;
require_once $pathbase . '/helpers/htmlHelper.php';

$params = $this->params;
$document = $this->document;
$language 	= $this->language;

$tag = $this->item;

$config = $this->config;
$isportal = $config->get('isportal', 1);
$enableFavorite = false;
$enablescalarrequest = $config->get('enablescalarrequest', 1);
$posx = $config->get('posx', 0);
$posy = $config->get('posy', 0);
$startzoom = $config->get('startzoom',14);
$googlemapsapykey = $config->get('googlemapskey','');

$showmap = true;

$params = $this->params;

$category = $this->category;

$show_grouped = $params["show_grouped"];

$total = 0;
if(isset($this->pagination)){
	$total = $this->pagination->total;
}
if($total<1){
	$showmap = false;
}



//$user = JFactory::getUser();
//if (!$user->guest)
//{
//	echo  $this->loadTemplate('user'); 
//}

?>   
<!-- {emailcloak=off} -->
<?php if (!empty($tag)): ?>

<div class="com_bookingforconnector_resource com_bookingforconnector_tagname">
	<h2 class="com_bookingforconnector_resource-name"><?php echo  $tag->Name ?> </h2>
	<div class="clear"></div>
	<?php if (!empty($tag->Description)):?>
	<div class="com_bookingforconnector_resource-description <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
        <?php echo $tag->Description ?>		
	</div>
	<div class="clear"></div>
	<?php endif; ?>
</div>
<script type="text/javascript">
<!--
var urlCheck = "<?php echo JRoute::_('index.php?option=com_bookingforconnector') ?>";	
var cultureCode = '<?php echo $language ?>';
//-->
</script>

<!-- MERCHANTS -->
<?php if ($category == 1 && $total > 0): ?>
	
	<?php echo $this->loadTemplate('merchants'); ?>
<?php endif; ?>
<!-- MERCHANTS -->

<!-- RESOURCES -->
<?php if ($category == 4 && $total > 0): ?>
		<div class="com_bookingforconnector-items-container">
			<?php 
				if  ($show_grouped == true) {
					echo  $this->loadTemplate('resources_merchants');
				}
//				elseif ($params['condominiumsResults'] == true) {
//					echo  $this->loadTemplate('condominiums');
//				}
				else {
					echo  $this->loadTemplate('resources');
				}
				if ($showmap) {
				  echo  $this->loadTemplate('googlemap');

					if  ($show_grouped == true) {
						echo  $this->loadTemplate('googlemap_merchants');
					}
//					elseif ($params['condominiumsResults'] == true) {
//						echo  $this->loadTemplate('googlemap_condominiums');
//					}
					else {
						echo  $this->loadTemplate('googlemap_resources');
					}
				}
				?>
				<?php if ($this->pagination->get('pages.total') > 1) : ?>
					<div class="text-center">
					<div class="pagination">
						<?php echo $this->pagination->getPagesLinks(); ?>
					</div>
					</div>
				<?php endif; ?>
		</div>
		<div class="clearboth"></div>
	<script type="text/javascript">
	<!--
	var urlCheck = "<?php echo JRoute::_('index.php?option=com_bookingforconnector') ?>";	

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

	function showResponse(responseText, statusText, xhr, $form)  { 
		jQuery('#bfcmerchantlist').unblock();
		if(typeof getAjaxInformations === 'function' ) {
			getAjaxInformations();
		}
			// reset map
			mapSearch = undefined;
			oms =  undefined;

			// Attach modal behavior to document
			if (typeof(SqueezeBox) !== 'undefined'){
				SqueezeBox.initialize({});
				SqueezeBox.assign($$('#bfcmerchantlist  a.boxed'), { //change the divid (#contentarea) as to the div that you use for refreshing the content
					parse: 'rel'
				});
			}

			if (jQuery.prototype.masonry){
				jQuery('.main-siderbar, .main-siderbar1').masonry('reload');
			}
	}
	function showError(responseText, statusText, xhr, $form)  { 
		jQuery('#bfcmerchantlist').html('<?php echo JText::_('MOD_BOOKINGFORSEARCHFILTER_NORESULT') ?>')
		jQuery('#bfcmerchantlist').unblock();
	}

	//-->
	</script>
<?php endif; ?>
<!-- RESOURCES -->
<?php endif; ?>
