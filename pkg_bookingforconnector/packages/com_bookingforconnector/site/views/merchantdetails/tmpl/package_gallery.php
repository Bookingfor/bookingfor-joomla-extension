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
$this->document->addStyleSheet('components/com_bookingforconnector/assets/css/royalslider-overrides.css');

$config = $this->config;
$isportal = $config->get('isportal', 1);


$merchant = $this->item;
$offer = $this->items;
$images = array();

if(!empty($offer->Images)) {
  $strImg = str_replace(' ', '', $offer->Images);
  foreach(explode(',', $strImg) as $image) {
    if (!empty($image)){
      $images[] = array('type' => 'image', 'data' => $image);
    }
  }
}
?>
<?php if (count ($images)>0){

$main_img = $images[0];
$sub_images = array_slice($images, 1, 4);
		
?>
<div class="com_bookingforconnector_resource-initialgallery nopadding <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 com_bookingforconnector_resource-initialgallery-fullrow nopadding">
		<div class="launch-fullscreen">
			<img src="<?php echo BFCHelper::getImageUrlResized('offers', $main_img['data'],'big')?>" alt="">
		</div>
	</div>
</div>
<div class="table-responsive">
<?php 
$widthtable = "";
if(count($sub_images)<3){
	$widthtable = "width:auto;";

}
?>	
	<table class="table imgsmallgallery" style="<?php echo $widthtable ?>"> 
		<tr>
<?php
	foreach($sub_images as $sub_img) {
		$srcImage = "";
		if($sub_img['type'] == 'image' || $sub_img['type'] == 'planimetry') {
			$srcImage = BFCHelper::getImageUrlResized('offers', $sub_img['data'],'small');
		}else{
			$url = $sub_img["data"];
			parse_str( parse_url( $url, PHP_URL_QUERY ), $arrUrl );
			$idyoutube = $arrUrl['v'];
			$srcImage = "http://img.youtube.com/vi/" . $idyoutube ."/mqdefault.jpg";
		}
?>
			<td>
				<img src="<?php echo $srcImage?>" alt="">
				<div class="showall">
					<i class="fa fa-search tour-search"></i><br />
					<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_GALLERY_SHOWALL') ?>
				</div>
			</td>
<?php } ?>
		</tr>
	</table>
</div>

<div class="royalSlider rsUni" id="resourcegallery" style="display: none">
	<?php foreach ($images as $image):?>
	<?php if($image['type'] != 'video') { ?>
	<div>
	  <a class="rsImg" href="<?php echo BFCHelper::getImageUrlResized('offers', $image['data'],'')?>"><img class="rsTmb" src="<?php echo BFCHelper::getImageUrlResized('offers', $image['data'],'logomedium')?>" onerror="this.onerror=null;this.src='<?php echo BFCHelper::getImageUrl('offers', $image['data'], 'logomedium')?>'" /></a>
	</div>	
	<?php  } else { ?>
	<?php
	$url='';
   if(is_array($image['data'])){
	  $url = $image['data']["url"];
   }else{
	  $url = $image['data'];
   }
   parse_str( parse_url( $url, PHP_URL_QUERY ), $arrUrl );
   $idyoutube = $arrUrl['v'];	
	?>
	<a class="rsImg" href="http://img.youtube.com/vi/<?php echo $idyoutube ?>/0.jpg" data-rsVideo="<?php echo $url ?>" ><img class="rsTmb" src="http://img.youtube.com/vi/<?php echo $idyoutube ?>/sddefault.jpg"></a>	
	<?php } ?>	
	<?php endforeach?>
</div>
<script type="text/javascript">
<!--
jQuery(document).ready(function() {
  jQuery('#resourcegallery').royalSlider({
    fullscreen: {
      enabled: true,
      nativeFS: true
    },
    controlNavigation: 'thumbnails',
    thumbs: {
      orientation: 'horizontal',
      paddingBottom: 4,
      appendSpan: true
    },
    transitionType:'fade',
    autoScaleSliderWidth: 822,     
    autoScaleSliderHeight: 355,
    loop: true,
    arrowsNav: false,
    globalCaption: true,
    navigateByClick: true,
    keyboardNavEnabled: true,
    autoScaleSlider: true, 
    arrowsNav:true
  });
  
    var slider = jQuery('#resourcegallery').data('royalSlider');
  slider.exitFullscreen = function(preventNative) {
    jQuery.rsProto.exitFullscreen.call(this, preventNative);
    jQuery('#resourcegallery').hide();
  };
  slider.enterFullscreen = function(preventNative) {
    jQuery.rsProto.enterFullscreen.call(this, preventNative);
    jQuery('#resourcegallery').show();
  };
  jQuery('.launch-fullscreen').click(function() { jQuery('#resourcegallery').royalSlider('enterFullscreen'); });
  jQuery('.showall').click(function() { jQuery('#resourcegallery').royalSlider('enterFullscreen'); });
});

 //-->
</script>

<?php } elseif ($merchant!= null && $merchant->LogoUrl != '') { ?>
	<img src="<?php echo BFCHelper::getImageUrlResized('merchant', $merchant->LogoUrl , 'big')?>" onerror="this.onerror=null;this.src='<?php echo BFCHelper::getImageUrl('merchant', $merchant->LogoUrl, 'big')?>'" />
<?php } ?>

