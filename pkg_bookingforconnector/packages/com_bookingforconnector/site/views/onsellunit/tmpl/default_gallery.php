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

$resource = $this->item;
$images = array();

if ($resource->ImageUrl != null && $resource->ImageUrl != '') {
	$images[] = array('type' => 'image', 'data' => $resource->ImageUrl);
}

try {
//	$imageData = new SimpleXmlElement($resource->ImageData);
	if (!empty($resource->ImageData)){
		if (strpos($resource->ImageData,'<xmlhashtable>') !== false) {
			$imageData = simpledom_load_string($resource->ImageData);
			if(!empty($imageData)){  // valore xml
				//$nodes = $imageData;
				if (strpos($resource->ImageData,'order') !== false) {
					$nodes = $imageData->sortedXPath('//image', '@order');  //dati ordinati per "order"
				}else{
					$nodes = $imageData;
				}
			}
		
		
	//controllo che il nome del file non esista già (il nome del file non il path quindi "images/file.jpg" è uguale a "images/thumb/file.jpg")
		foreach ($nodes as $image) {
			//if (!empty($images[0]) && $image != $images[0] && $images[0] ) { 
			if (!empty($images[0]) && basename($image) != basename($images[0]) && $images[0] ) { 
				$images[] = $image;
			}
		}
		}else{
				foreach(explode(',', $resource->ImageData) as $image) {
					if (!empty($images[0]) && basename($image) != basename($images[0]['data']) && $images[0] ) { 
						$images[] = array('type' => 'image', 'data' => $image);
					}
				}		
		}
	}
} 
catch (Exception $e) {
	// suppressing any errors

}
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
		     if (!empty($image)){
			    $images[] =  array('type' => 'planimetry', 'data' => $image);
			  }
			}		
		}		

	}
} 
catch (Exception $e) {
	// suppressing any errors

}
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
				if (!empty($image)){
				  $images[] =  array('type' => 'video', 'data' => $image);
				}
			}		
		}		

	}
} 
catch (Exception $e) {
}

$merchant = $resource->Merchant;
?>
<?php if (count ($images)>0){ ?>
<?php
$main_img = $images[0];
$sub_images = array_slice($images, 1, 4);
?>
<div class="com_bookingforconnector_resource-initialgallery nopadding <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 com_bookingforconnector_resource-initialgallery-fullrow nopadding">
		<div class="launch-fullscreen">
			<img src="<?php echo BFCHelper::getImageUrlResized('onsellunits', $main_img['data'],'big')?>" alt="">
		</div>
		<div class="caption captionresource">
			<div class="captiondiv">
				<div class="caption-merchant-details">
					<span><?php echo $merchant->Name; ?></span>
					<span><br />
					<?php for($i = 0; $i < $merchant->Rating; $i++) { ?>
					<i class="fa fa-star"></i>
					<?php } ?>
					</span>
				</div>
			</div>
			<?php
			if ($isportal && $merchant->RatingsContext != NULL && ($merchant->RatingsContext ==1 || $merchant->RatingsContext ==3) && !empty($merchant->Avg)) {
				$reviewavg = $merchant->Avg->Average;
				$reviewcount = $merchant->Avg->Count;
				if($reviewcount>0){
			?>
				<div class="separator">&nbsp;</div>
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
$tdWidth = 100/$totalsub_images;
?>	
	<table class="table imgsmallgallery" style="<?php echo $widthtable ?>"> 
		<tr>
<?php
	foreach($sub_images as $sub_img) {
		$srcImage = "";
		if($sub_img['type'] == 'image' || $sub_img['type'] == 'planimetry') {
			$srcImage = BFCHelper::getImageUrlResized('onsellunits', $sub_img['data'],'small');
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
	  <a class="rsImg" href="<?php echo BFCHelper::getImageUrlResized('onsellunits', $image['data'],'')?>"><img class="rsTmb" src="<?php echo BFCHelper::getImageUrlResized('onsellunits', $image['data'], 'logomedium')?>" onerror="this.onerror=null;this.src='<?php echo BFCHelper::getImageUrl('onsellunits', $image['data'], 'logobig')?>'" /></a>
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

<?php } elseif ($resource->Merchant!= null && $resource->Merchant->LogoUrl != '') { ?>
	<img src="<?php echo BFCHelper::getImageUrlResized('merchant', $resource->Merchant->LogoUrl , 'merchant_gallery_full')?>"  onerror="this.onerror=null;this.src='<?php echo BFCHelper::getImageUrl('merchant', $resource->Merchant->LogoUrl , 'onsellunit_default_logo')?>'"  />
<?php } else { ?>
	<img class="com_bookingforconnector_resource-img" style="float:none;" src="<?php echo JURI::base()?>/media/com_bookingfor/images/default.png" />
<?php } ?>
