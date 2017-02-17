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
		if (strpos($resource->PlanimetryData,'<xmlhashtable>') !== false) {
			$imageData = simpledom_load_string($resource->PlanimetryData);
			if(!empty($imageData)){  // valore xml
				//$nodes = $imageData;
				if (strpos($resource->ImageData,'order') !== false) {
					$nodes = $imageData->sortedXPath('//image', '@order');  //dati ordinati per "order"
				}else{
					$nodes = $imageData;
				}
			}
			foreach ($nodes as $image) {
				$images[] = $image;
			}
		}else{
			foreach(explode(',', $resource->PlanimetryData) as $image) {
				$images[] = $image;
			}		
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
	<a class="rsImg" href="<?php echo BFCHelper::getImageUrlResized('resources', $image,'')?>"><img class="rsTmb" src="<?php echo BFCHelper::getImageUrlResized('resources', $image, 'onsellunit_gallery_thumb')?>" onerror="this.onerror=null;this.src='<?php echo BFCHelper::getImageUrl('resources', $image, 'onsellunit_gallery_thumb')?>'" /></a>		
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

