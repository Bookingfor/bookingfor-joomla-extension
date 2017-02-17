<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$basPath = JURI::base(false) . 'components/com_bookingforconnector/assets/';

$this->document->addScript('components/com_bookingforconnector/assets/js/royalslider/jquery.royalslider.min.js');
$this->document->addStyleSheet('components/com_bookingforconnector/assets/js/royalslider/royalslider.css');
$this->document->addStyleSheet('components/com_bookingforconnector/assets/js/royalslider/skins/universal/rs-universal.css');

$resource = $this->item;
$images = array();
try {
//	$imageData = new SimpleXmlElement($resource->PlanimetryData);
	if (!empty($resource->PlanimetryData)){
		$imageData = simpledom_load_string($resource->PlanimetryData);
		//$nodes = $imageData;
//		$nodes = $imageData->sortedXPath('//image', '@order');  //dati ordinati per "order"
		if (strpos($resource->ImageData,'order') !== false) {
			$nodes = $imageData->sortedXPath('//image', '@order');  //dati ordinati per "order"
		}else{
			$nodes = $imageData;
		}
		foreach ($nodes as $image) {
			$images[] = $image;
		}
	}
} 
catch (Exception $e) {
	// suppressing any errors

}

?>

<?php if (count ($images)>0){ ?>
<div class="royalSlider rsUni" id="resourcePlanimetrygallery">
	<?php foreach ($images as $image):?>
	<a class="rsImg" href="<?php echo BFCHelper::getImageUrlResized('merchant', $image,'')?>"><img class="rsTmb" src="<?php echo BFCHelper::getImageUrlResized('merchant', $image, 'resource_gallery_thumb')?>" onerror="this.onerror=null;this.src='<?php echo BFCHelper::getImageUrl('merchant', $image, 'resource_gallery_thumb')?>'" /></a>		
	<?php endforeach?>
</div>

<script type="text/javascript">
<!--
jQuery(document).ready(function() {
  jQuery('#resourcePlanimetrygallery').royalSlider({
    fullscreen: {
      enabled: true,
      nativeFS: true
    },
    controlNavigation: 'thumbnails',
    thumbs: {
      orientation: 'vertical',
      paddingBottom: 4,
      appendSpan: true
    },
    transitionType:'fade',
    autoScaleSlider: true, 
    autoScaleSliderWidth: 960,     
    autoScaleSliderHeight: 600,
    loop: true,
    arrowsNav: false,
    globalCaption: true,
    navigateByClick: true,
    keyboardNavEnabled: true,
    autoScaleSlider: true, 
    arrowsNav:true

  });
});

 //-->
</script>
<?php } ?>

