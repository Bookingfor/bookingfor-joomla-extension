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
$images = array();

if(!empty($merchant->ImageData)) {
	$strImg = str_replace(' ', '', $merchant->ImageData);
	foreach(explode(',', $strImg) as $image) {
		if (!empty($image)){
			$images[] = array('type' => 'image', 'data' => $image);
		}
	}
}
?>
<?php if (count ($images)>0){ ?>
<?php
$main_img = $images[0];
$sub_images = array_slice($images, 1, 4);
$rating = $merchant->Rating;
$reviewavg = isset($merchant->Avg) ? $merchant->Avg->Average : 0;
$reviewcount = isset($merchant->Avg) ? $merchant->Avg->Count : 0;
?>
<div class="com_bookingforconnector_resource-initialgallery nopadding <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 com_bookingforconnector_resource-initialgallery-fullrow nopadding">
		<div class="launch-fullscreen">
			<img src="<?php echo BFCHelper::getImageUrlResized('merchant', $main_img['data'],'big')?>" alt="">
		</div>
		<div class="caption">
			<?php
			if ($isportal && $merchant->RatingsContext != NULL && ($merchant->RatingsContext ==1 || $merchant->RatingsContext ==3) && !empty($merchant->Avg)) {
				$reviewavg = $merchant->Avg->Average;
				$reviewcount = $merchant->Avg->Count;
				if($reviewcount>0){
			?>
				<div class="ratingdiv">
					<div class="avgreview">
						<?php echo number_format($reviewavg, 1); ?>
					</div>
					<div class="reviewcount">
						<i class="fa fa-comments-o" aria-hidden="true"></i> <?php echo $reviewcount; ?> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_ANCHORS_REVIEWS') ?>
					</div>
				</div>
				<?php } else { ?>
					<div class="ratingdiv">
						<div class="noreviewtext">
							<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_NO_RESULT'); ?>
						</div>
					</div>
				<?php 
				} 
			}
			?>
		</div>
	</div>
</div>
<div class="table-responsive">
<?php 
$widthtable = "";
$totalsub_images= count($sub_images);
if($totalsub_images<3){
	$widthtable = "width:auto;";
}
$tdWidth = 100;

if(!empty($totalsub_images)){
	$tdWidth = 100/$totalsub_images;
}
?>	
	<table class="table imgsmallgallery" style="<?php echo $widthtable ?>"> 
		<tr>
<?php
	foreach($sub_images as $sub_img) {
		$srcImage = "";
		if($sub_img['type'] == 'image' || $sub_img['type'] == 'planimetry') {
			$srcImage = BFCHelper::getImageUrlResized('merchant', $sub_img['data'],'small');
		}else{
			$url = $sub_img["data"];
			parse_str( parse_url( $url, PHP_URL_QUERY ), $arrUrl );
			$idyoutube = $arrUrl['v'];
			$srcImage = "http://img.youtube.com/vi/" . $idyoutube ."/mqdefault.jpg";
		}
?>
			<td style="width:<?php echo $tdWidth ?>%;">
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
	  <a class="rsImg" href="<?php echo BFCHelper::getImageUrlResized('merchant', $image['data'],'')?>"><img class="rsTmb" src="<?php echo BFCHelper::getImageUrlResized('merchant', $image['data'],'logobig')?>" onerror="this.onerror=null;this.src='<?php echo BFCHelper::getImageUrl('merchant', $image['data'], 'logobig')?>'" /></a>
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
	<a class="rsImg" href="http://img.youtube.com/vi/<?php echo $idyoutube ?>/sddefault.jpg" data-rsVideo="<?php echo $url ?>" ><img class="rsTmb" src="http://img.youtube.com/vi/<?php echo $idyoutube ?>/1.jpg"></a>	
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
	<img src="<?php echo BFCHelper::getImageUrlResized('merchant', $merchant->LogoUrl , 'resource_mono_full')?>" onerror="this.onerror=null;this.src='<?php echo BFCHelper::getImageUrl('merchant', $merchant->LogoUrl, 'resource_mono_full')?>'" />
<?php } ?>

