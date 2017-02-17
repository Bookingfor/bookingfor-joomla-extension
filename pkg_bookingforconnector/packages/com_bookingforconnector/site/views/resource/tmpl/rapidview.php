<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// add stylesheet
JHTML::stylesheet('components/com_bookingforconnector/assets/css/flexslider.css');
$basPath = JURI::base(false) . 'components/com_bookingforconnector/assets/';

$resource = $this->item;
$merchant = $resource->Merchant;
$language = $this->language;

//$resourceName = BFCHelper::getLanguage($resource->Name, $this->language);
$resourceName = BFCHelper::getLanguage($resource->Name, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 

$this->document->setTitle($resourceName . ' - ' . $merchant->Name);
$this->document->setDescription( BFCHelper::getLanguage($resource->Description, $this->language));

$resourceLat=0;
$resourceLon=0;
$htmlmarkerpoint='';
if (!empty($resource->XGooglePos) && !empty($resource->YGooglePos)) {
	$resourceLat = $resource->XGooglePos;
	$resourceLon = $resource->YGooglePos;
}
if(!empty($resource->XPos)){
	$resourceLat = $merchant->XPos;
}
if(!empty($resource->YPos)){
	$resourceLon = $merchant->YPos;
}

//if ($merchant->MerchantTypeId==1 || $merchant->MerchantTypeId==3){
if (BFCHelper::getAddressDataByMerchant( $merchant->MainMerchantCategoryId)){
	if (!empty($merchant->XGooglePos) && !empty($merchant->YGooglePos)) {
		$resourceLat = $merchant->XGooglePos;
		$resourceLon = $merchant->YGooglePos;
	}
	if(!empty($merchant->XPos)){
		$resourceLat = $merchant->XPos;
	}
	if(!empty($merchant->YPos)){
		$resourceLon = $merchant->YPos;
	}
}

$showResourceMap = (!empty($resourceLat) && !empty($resourceLon) );
if ($showResourceMap){
$htmlmarkerpoint = "&markers=color:blue%7C" . $resourceLat . "," . $resourceLon;
}
$indirizzo = "";
$cap = "";
$comune = "";
$provincia = "";
$doc = false;

if (empty($resource->AddressData)){
	$indirizzo = $resource->Address;
	$cap = $resource->ZipCode;
	$comune = $resource->CityName;
	$provincia = $resource->RegionName;
	if (empty($indirizzo)){
		$indirizzo = $resource->MrcAddress;
		$cap = $resource->MrcZipCode;
		$comune = $resource->MrcCityName;
		$provincia = $resource->MrcRegionName;
	}

}else{
	$addressData = $resource->AddressData;
	$indirizzo = BFCHelper::getItem($addressData, 'indirizzo');
	$cap = BFCHelper::getItem($addressData, 'cap');
	$comune =  BFCHelper::getItem($addressData, 'comune');
	$provincia = BFCHelper::getItem($addressData, 'provincia');
}
if (BFCHelper::getAddressDataByMerchant( $merchant->MainMerchantCategoryId)){
	if (empty($merchant->AddressData)){
		$indirizzo = $merchant->Address;
		$cap = $merchant->ZipCode;
		$comune = $merchant->CityName;
		$provincia = $merchant->RegionName;
	}else{
		$addressData = $merchant->AddressData;
		$indirizzo = BFCHelper::getItem($addressData, 'indirizzo');
		$cap = BFCHelper::getItem($addressData, 'cap');
		$comune =  BFCHelper::getItem($addressData, 'comune');
		$provincia = BFCHelper::getItem($addressData, 'provincia');

	}
}

$images = array();

if ($resource->ImageUrl != null && $resource->ImageUrl != '') {
	$images[] = $resource->ImageUrl;
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
					if (!empty($images[0]) && basename($image) != basename($images[0]) && $images[0] ) { 
						$images[] = $image;
					}
				}		
		}
	}
} 
catch (Exception $e) {
	// suppressing any errors

}

$db   = JFactory::getDBO();
$uri  = 'index.php?option=com_bookingforconnector&view=resource';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').')  AND published = 1 LIMIT 1' );
$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
//-------------------pagina per i l redirect di tutte le risorsein vendita


//-------------------pagina per il redirect di tutti i merchant

$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1  LIMIT 1' );
$itemIdMerchant = ($db->getErrorNum())? 0 : intval($db->loadResult());
if ($itemId<>0)
	$uri.='&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName).'&Itemid='.$itemId;
else
	$uri.='&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName);

$route = JRoute::_($uri);

//$routeMerchant = JRoute::_('index.php?option=com_bookingforconnector&view=merchantdetails&merchantId=' . $resource->MerchantId . ':' . BFCHelper::getSlug($merchant->Name));
if ($itemIdMerchant<>0)
	$uriMerchant.='&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name).'&Itemid='.$itemIdMerchant;
else
	$uriMerchant.='&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name);

$routeMerchant = JRoute::_($uriMerchant);


//$route = JRoute::_('index.php?option=com_bookingforconnector&view=onsellunit&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName));
//$routeMerchant = JRoute::_('index.php?option=com_bookingforconnector&view=merchantdetails&merchantId=' . $resource->MerchantId . ':' . BFCHelper::getSlug($merchant->Name));
$resourceImageUrl = Juri::root() . "media/com_bookingfor/images/default.png";
$merchantLogoPath = Juri::root() . "media/com_bookingfor/images/default.png";
//	if ($resource->ImageUrl != '') {
//		$resourceImageUrl = BFCHelper::getImageUrl('resources',$resource->ImageUrl, 'resource_list_default');		
if ($merchant->LogoUrl != ''){
		$merchantLogoPath = BFCHelper::getImageUrlResized('merchant',$merchant->LogoUrl, 'merchant_logo_small_rapidview');
		$merchantLogoPathError = BFCHelper::getImageUrl('merchant',$merchant->LogoUrl, 'merchant_logo_small_rapidview');
}

if (!empty($resource->ServiceIdList)){
	$services=BFCHelper::GetServicesByIds($resource->ServiceIdList);
}
$showdefault = false;
if (count ($images)===0){
	$showdefault = true;
}

?>
<style>
body{
	background: none;
	background: #ffffff;
	padding-top:0;

}
.flexslider { 
	margin: 0; background: #fff; 
	border: 0px solid #fff; 
	border-right: 1px solid #fff; 
	position: relative; 
	-webkit-border-radius: 0px; 
	-moz-border-radius: 0px;
	-o-border-radius: 0px;
	border-radius: 0px; 
	-webkit-box-shadow: 0 0px 0px rgba(0,0,0,.2);
	-moz-box-shadow: 0 0px 0px rgba(0,0,0,.2);
	-o-box-shadow: 0 0px 0px rgba(0,0,0,.2);
	box-shadow: 0 0px 0px rgba(0,0,0,.2);
	zoom: 1; 
	}
/*
.flexslider .slides img {
    width: 398px;
    height: 298px;
}
#carouselResource .slides img {
    width: 78px;
    height: 58px;
}
*/
#carouselResource{
	border-top: 1px solid #fff; 
}
.title1{
	font-weight:bold;
	margin-bottom:10px;
}
.minheight10{
	min-height:10px !important;
	font-size:12px;
}
.details{
	background-color: #f1f1f1;
	border-right: 1px solid #fff; 
}
/*override rating*/
.bfcrating{
	width:100%;
}
.bfcrating div{
	display: inline-block;
	margin-left:10px;
}
.component .container{
	padding-top:0;
	padding-bottom:0;

}
</style>
<div style="max-width:870px;margin:auto;">
	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
		<?php if (!$showdefault): ?>
			<div id="gallery" >
				<div id="sliderResource" class="flexslider">
					<ul class="slides">
						<?php foreach ($images as $image):?>
						<li>
							<img src="<?php echo BFCHelper::getImageUrlResized('resources', $image, 'resource_gallery_full_rapidview')?>" onerror="this.onerror=null;this.src='<?php echo BFCHelper::getImageUrl('resources', $image, 'resource_gallery_full_rapidview')?>'" />
						</li>		
						<?php endforeach?>
					</ul>
				</div>
				<div id="carouselResource" class="flexslider">
					<ul class="slides">
						<?php foreach ($images as $image):?>
						<li>
							<img src="<?php echo BFCHelper::getImageUrlResized('resources', $image, 'resource_gallery_thumb_rapidview')?>" onerror="this.onerror=null;this.src='<?php echo BFCHelper::getImageUrl('resources', $image, 'resource_gallery_thumb_rapidview')?>'" />
						</li>		
						<?php endforeach?>
					</ul>
				</div>
			</div>
		<?php else :?>
			<div id="gallery" >
				<div id="sliderResource" class="flexslider">
					<ul class="slides">
						<li>
							<img src="<?php echo  Juri::root() . "media/com_bookingfor/images/full_rapidview.png"; ?>" />
						</li>		
					</ul>
				</div>
				<div id="carouselResource" class="flexslider">
					<ul class="slides">
						<li>
							<img src="<?php echo Juri::root() . "media/com_bookingfor/images/full_rapidview_thumb.png";?>" />
						</li>		
					</ul>
				</div>
			</div>
		<?php endif; ?>
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6" id="divmapstatic">
			<img src="<?php echo Juri::root()?>media/com_bookingfor/images/nomap.png" alt="nomap.jpg" style="max-width:100%;" />
		</div>
	</div>
	<!--  -->
	<div class="details" >
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>" >
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<div style="padding:5px;">
				<?php if (!empty($services) && count($services) > 0):?>
					<div class="title1"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_SERVICES') ?></div>
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
						<?php $count=1; ?>
						<?php foreach ($services as $service):?>
							<?php
							if ($count > 5) { 
								break;
							}
							?>			
							<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 minheight10"><?php echo BFCHelper::getLanguage($service->Name, $this->language) ?></div>
							<?php
							if ($count % 2 === 0) {
								print("</div>\n<div class='<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>'>");
							}
							$count++;

							?>			
						<?php endforeach?>
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 minheight10"><a href="<?php echo $route ?>" target="_top"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_ALLSERVICES') ?></a></div>
					</div>
				<?php endif; ?>
				</div>
			</div>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3">
				<div style="padding:5px;">
					<b><a href="<?php echo $routeMerchant ?>"  target="_top"><img class="com_bookingforconnector_logo_img" src="<?php echo $merchantLogoPath?>"   onerror="this.onerror=null;this.src='<?php echo $merchantLogoPathError ?>'" /></b></a>
				</div>
			</div>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3">
				<div style="padding:5px;">
					<div><a style="color:#666666;" class="com_bookingforconnector_merchantdetails-name" href="<?php echo $routeMerchant?>" target="_top"><?php echo $merchant->Name?></a></div>
					<div><?php echo $indirizzo ?> - <?php echo  $cap ?> - <?php echo $comune ?> (<?php echo  $provincia ?>)</div>	
					<div><a style="color:#666666;font-size:12px;" href="javascript:void(0);" onclick="getData(urlCheck,'merchantid=<?php echo $resource->MerchantId?>&task=GetPhoneByMerchantId&language=' + cultureCode,this)"  id="phone<?php echo $resource->ResourceId?>"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_SHOWPHONE') ?></a></div>
				</div>
			</div>
		</div>
	</div>

	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>" style="margin-top:10px;" >
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
		<!-- valutazione
			{bfcrating <?php echo $resource->MerchantId?>} -->
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
			<a href="<?php echo $route ?>" class="btn btn-info pull-right" target="_top" style="text-transform:uppercase;"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_DETAILS') ?></a>
		</div>
	</div>
</div>

<!--  -->
<script type="text/javascript">
<!--
var urlCheck = "<?php echo JRoute::_('index.php?option=com_bookingforconnector') ?>";	
var cultureCode = '<?php echo $language ?>';
var defaultcultureCode = '<?php echo BFCHelper::$defaultFallbackCode ?>';
var listToCheck = "<?php echo $resource->ResourceId ?>";
var imgPathmerchant = "<?php echo $merchantLogoPath ?>";
var strAddressSimple = "<strong><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTLIST_ADDRESS') ?>:</strong> ";
var strAddress = "<strong><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTLIST_ADDRESS') ?>:</strong> [indirizzo] - [cap] - [comune] ([provincia])";

jQuery(function( $ ) {
	jQuery("body section:first").css("width","100%");
<?php if ($showResourceMap): ?>
	var mapdiv = jQuery("#divmapstatic");
	var mapdivwidth= mapdiv.width();
	var mapdivheight= mapdiv.height(); //368;//jQuery(window).height();
	var markerpoint = "<?php echo $htmlmarkerpoint?>";
	var imghtmol = "<img src='//maps.googleapis.com/maps/api/staticmap?center=<?php echo $resourceLat?>,<?php echo $resourceLon?>&zoom=14&size=" + mapdivwidth + "x" + mapdivheight + "&sensor=false" + markerpoint + "' />"
	jQuery("#divmapstatic").html(imghtmol);
<?php endif; ?>
<?php if ($showdefault || count ($images)>0): ?>	
	jQuery.getScript("<?php echo $basPath?>js/jquery.flexslider-min.js", function(data, textStatus, jqxhr) {
		// The slider being synced must be initialized first
		jQuery('#carouselResource').flexslider({  
			animation: "slide",    
			controlNav: false,
			animationLoop: false,
			slideshow: false,
			itemWidth: 79,
			itemMargin: 1,
			asNavFor: '#sliderResource',
			nextText:"",
			prevText: ""
		});     
		jQuery('#sliderResource').flexslider({
			animation: "slide",
			controlNav: false,
			animationLoop: true,
			slideshow: false,
			sync: "#carouselResource"
		});
	}); //end load javascript
<?php endif; ?>

});


	
//-->
</script>
