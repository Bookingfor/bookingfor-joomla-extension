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

$config = $this->config;
$isportal = $config->get('isportal', 1);


$merchant = $this->item;
$offer = $this->items;
$images = array();

if(!empty($offer->Images)) {
  $strImg = str_replace(' ', '', $offer->Images);
  foreach(explode(',', $strImg) as $image) {
      $images[] = array('type' => 'image', 'data' => $image);
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
			<img src="<?php echo BFCHelper::getImageUrlResized('variationplans', $main_img['data'],'big')?>" alt="">
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
			$srcImage = BFCHelper::getImageUrlResized('variationplans', $sub_img['data'],'small');
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

<script type="text/javascript">
<!--
jQuery(document).ready(function() {

	jQuery('.showall, .launch-fullscreen').magnificPopup({
		items: [
		<?php foreach ($images as $image):?>
		<?php if($image['type'] != 'video') { ?>
		  {
			src: '<?php echo BFCHelper::getImageUrlResized('merchant', $image['data'], '')?>'
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

<?php } elseif ($merchant!= null && $merchant->LogoUrl != '') { ?>
	<img src="<?php echo BFCHelper::getImageUrlResized('merchant', $merchant->LogoUrl , 'big')?>" onerror="this.onerror=null;this.src='<?php echo BFCHelper::getImageUrl('merchant', $merchant->LogoUrl, 'big')?>'" />
<?php } ?>

