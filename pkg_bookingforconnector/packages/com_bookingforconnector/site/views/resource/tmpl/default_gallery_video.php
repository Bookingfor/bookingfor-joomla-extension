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
//	$imageData = new SimpleXmlElement($resource->VideoData);
	if (!empty($resource->VideoData)){
		if (strpos($resource->VideoData,'<xmlhashtable>') !== false) {
			$imageData = simpledom_load_string($resource->VideoData);
	//		$nodes = $imageData->sortedXPath('//video', '@order');  //dati ordinati per "order"
			if (strpos($resource->VideoData,'order') !== false) {
				$nodes = $imageData->sortedXPath('//video', '@order');  //dati ordinati per "order"
			}else{
				$nodes = $imageData;
			}
					
			foreach ($nodes as $image) {
				$images[] = $image;
			}
		}else{
			foreach(explode(',', $resource->VideoData) as $image) {
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
<div class="royalSlider rsUni" id="resourceVideogallery">
	<?php foreach ($images as $image):?>
<?php 
	$url='';
if(is_array($image)){
	$url = $image["url"];
}else{
	$url = $image;
}
parse_str( parse_url( $url, PHP_URL_QUERY ), $arrUrl );
$idyoutube = $arrUrl['v'];

?>
	<a class="rsImg" href="http://img.youtube.com/vi/<?php echo $idyoutube ?>/0.jpg" data-rsVideo="<?php echo $url ?>" ><img class="rsTmb" src="http://img.youtube.com/vi/<?php echo $idyoutube ?>/1.jpg"></a>		
	<?php endforeach?>
</div>

<script type="text/javascript">
<!--
jQuery(document).ready(function() {
  jQuery('#resourceVideogallery').royalSlider({
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

