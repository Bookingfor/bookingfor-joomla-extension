<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$basPath = JURI::base(false) . 'components/com_bookingforconnector/assets/';

$this->document->addScript('components/com_bookingforconnector/assets/js/jquery.magnific-popup.min.js');
$this->document->addStyleSheet('components/com_bookingforconnector/assets/css/magnific-popup.css');

$resource = $this->item;
$images = array();
$config = $this->config;
$isportal = $config->get('isportal', 1);

$resource->ImageUrl = $resource->DefaultImg;

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
<div class="com_bookingforconnector_resource-initialgallery nopadding row-fluid">
<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 com_bookingforconnector_resource-initialgallery-fullrow nopadding">
<div class="launch-fullscreen">
<img src="<?php echo BFCHelper::getImageUrlResized('condominium', $main_img['data'],'big')?>" alt="">
</div>
  <div class="caption">
    <div class="captiondiv">
      <div class="caption-merchant-details">
	     <span>
        <?php for($i = 0; $i < $merchant->Rating; $i++) { ?>
        <i class="fa fa-star"></i>
        <?php } ?>	     
	     </span>
	   </div>
      <div class="caption-merchant-details">
	     <span><?php echo $merchant->Name; ?></span><span></span>
	   </div>
    </div>
      
	  <?php
	if($isportal){
		$showMerchantReviews = FALSE;
		if($merchant->RatingsContext != NULL) {
		  $showMerchantReviews = TRUE;
		  $reviewavg = $merchant->Avg->Average;
		 $reviewcount = $merchant->Avg->Count;
		  if($reviewcount>0){
		 ?>
		<div class="ratingdiv">
		  <div class="avgreview">
			<?php echo number_format($reviewavg, 1); ?>
		  </div>
		  <div class="reviewcount">
		  <?php echo $reviewcount; ?> Comments
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
	  }
	 ?>
  </div>
</div>
<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> nopadding">
<?php
foreach($sub_images as $sub_img) {
?>
<div class="<?php echo str_replace("no-gutter","",COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL) ?>3 com_bookingforconnector_resource-initialgallery-subrow nopadding">
  <span class="launch-fullscreen">
<?php 
if($sub_img['type'] == 'image' || $sub_img['type'] == 'planimetry') {
?>
  <img src="<?php echo BFCHelper::getImageUrlResized('condominium', $sub_img['data'],'small')?>" alt="">
<?php 
}else{
$url = $sub_img["data"];
parse_str( parse_url( $url, PHP_URL_QUERY ), $arrUrl );
$idyoutube = $arrUrl['v'];

?>
  <img src="http://img.youtube.com/vi/<?php echo $idyoutube ?>/1.jpg" alt="">
<?php 
}
?>
    <div class="hover-div">
      <i class="fa fa-search tour-search"></i>
      <p><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_GALLERY_SHOWALL') ?></p>
    </div>
  </span>
</div>
<?php } ?>
</div>
</div>

<script type="text/javascript">
<!--
jQuery(document).ready(function() {

	jQuery('.showall, .launch-fullscreen').magnificPopup({
		items: [
		<?php foreach ($images as $image):?>
		<?php if($image['type'] != 'video') { ?>
		  {
			src: '<?php echo BFCHelper::getImageUrlResized('condominium', $image['data'], '')?>'
		  },
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
		  {
			src: '<?php echo $url ?>',
			type: 'iframe' // this overrides default type
		  },
		<?php } ?>	
		<?php endforeach?>
		],
		gallery: {
		  enabled: true
		},
		type: 'image' // this is default type
	});

});
 //-->
</script>


<?php } elseif ($resource->Merchant!= null && $resource->Merchant->LogoUrl != '') { ?>
	<img src="<?php echo BFCHelper::getImageUrlResized('merchant', $resource->Merchant->LogoUrl , 'merchant_gallery_full')?>"  onerror="this.onerror=null;this.src='<?php echo BFCHelper::getImageUrl('merchant', $resource->Merchant->LogoUrl , 'onsellunit_default_logo')?>'"  />
<?php } else { ?>
	<img class="com_bookingforconnector_resource-img" style="float:none;" src="<?php echo JURI::base()?>/media/com_bookingfor/images/default.png" />
<?php } ?>
