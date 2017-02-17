<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.mootools'); // this will make sure mootools loads first so we can load jquery with noconflict

// add stylesheet
$document->addStylesheet(COM_BOOKINGFORCONNECTOR_SCRIPT_PATH.'assets/css/flexslider.css');
$basPath = JURI::base(false) . 'components/com_bookingforconnector/assets/';



$resource = $this->item;
$merchant = $resource->Merchant;
//$resource->ResourceId = $resource->OnSellUnitId;
$resource->Price = $resource->MinPrice;

//$resourceName = BFCHelper::getLanguage($resource->Name, $this->language);
$resourceName = BFCHelper::getLanguage($resource->Name, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 

$addressData = $resource->AddressData;
$merchantAddress = false;
if ($addressData == '' || $addressData == null || $merchant->MerchantTypeId != 2) {
	$addressData = $merchant->AddressData;
	$merchantAddress = true;
}

$this->document->setTitle($resourceName . ' - ' . $merchant->Name);
$this->document->setDescription( BFCHelper::getLanguage($resource->Description, $this->language));

$resourceLat = $resource->XGooglePos;
$resourceLon = $resource->YGooglePos;
$isMapVisible = $resource->IsMapVisible;
$isMapMarkerVisible = $resource->IsMapMarkerVisible;
$showResourceMap = (($resourceLat != null) && ($resourceLon !=null) && $isMapVisible);
$htmlmarkerpoint = "";
if ($isMapMarkerVisible){
	$htmlmarkerpoint = "&markers=color:blue%7C" . $resourceLat . "," . $resourceLon;
}

$images = array();

if ($resource->ImageUrl != null && $resource->ImageUrl != '') {
	$images[] = $resource->ImageUrl;
}

try {
	$imageData = new SimpleXmlElement($resource->ImageData);
	
	foreach ($imageData as $image) {
		if ($image != $images[0]) {
			$images[] = $image;
		}
	}
} 
catch (Exception $e) {
	// suppressing any errors
}


$db   = JFactory::getDBO();
$uri  = 'index.php?option=com_bookingforconnector&view=onsellunit';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1  LIMIT 1' );
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
$resourceImageUrl = Juri::base() . "/images/default.jpg";
$merchantLogoPath = Juri::base() . "/images/default.jpg";
//	if ($resource->ImageUrl != '') {
//		$resourceImageUrl = BFCHelper::getImageUrl('resources',$resource->ImageUrl, 'resource_list_default');		
if ($merchant->LogoUrl != ''){
		$merchantLogoPath = BFCHelper::getImageUrl('merchant',$merchant->LogoUrl, 'merchant_logo_small_rapidview');		
}
$isMerchantAnonymous = BFCHelper::isMerchantAnonymous($merchant->MerchantTypeId);
$contractType = ($resource->ContractType) ? JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_CONTRACTTYPE1')  : JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_CONTRACTTYPE');
$typeName =  BFCHelper::getLanguage($resource->CategoryName, $this->language);

//echo "<pre>";
//echo print_r($resource);
//echo "</pre>";
//$showResourceMap = false;
?>
<style>
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
</style>
<div style="max-width:800px;margin:auto;">
	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
		<?php if (count ($images)>1): ?>
		<div id="gallery" >
			<div id="sliderResource" class="flexslider">
				<ul class="slides">
					<?php foreach ($images as $image):?>
					<li>
						<img src="<?php echo BFCHelper::getImageUrl('onsellunits', $image, 'resource_gallery_full_rapidview')?>" />
					</li>		
					<?php endforeach?>
				</ul>
			</div>
			<div id="carouselResource" class="flexslider">
				<ul class="slides">
					<?php foreach ($images as $image):?>
					<li>
						<img src="<?php echo BFCHelper::getImageUrl('onsellunits', $image, 'resource_gallery_thumb_rapidview')?>" />
					</li>		
					<?php endforeach?>
				</ul>
			</div>
		</div>
		<?php elseif (count ($images) == 1):?>
		<div class="com_bookingforconnector_resource-image">
			<img src="<?php echo BFCHelper::getImageUrl('onsellunits', $images[0], 'resource_mono_full_rapidview')?>" />
		</div>
		<?php endif; ?>
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6" id="divmapstatic">
			<img src="<?php echo Juri::base()?>images/nomap.jpg" alt="nomap.jpg" style="max-width:100%;" />
		</div>
	</div>
	<!--  -->
	<div class="details" >
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>" >
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
				<div style="padding:5px;">
					<div class="title1">Annuncio</div>
<?php if(false) : ?>					
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
							<table>
								<tr>
									<td style="padding:2px;font-size:12px;"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_CONTRACT') ?>:</td>
									<td style="padding:2px;font-size:12px;"><?php echo  $contractType?></td>
								</tr>
								<tr>
									<td style="padding:2px;font-size:12px;"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_PRICE') ?></td>
									<td style="padding:2px;font-size:12px;"><?php if ($resource->Price != null && $resource->Price > 0 && isset($resource->IsReservedPrice) && $resource->IsReservedPrice!=1 ) :?>
										&euro; <?php echo number_format($resource->Price,0, ',', '.')?>
							<?php else: ?>
									<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_ISRESERVEDPRICE')?>
							<?php endif; ?></td>
								</tr>
							</table>
						</div>
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
							<table>
								<tr>
									<td style="padding:2px;font-size:12px;"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_TYPE') ?>:</td>
									<td style="padding:2px;font-size:12px;"><?php echo  $typeName?></td>
								</tr>
								<tr>
									<td style="padding:2px;font-size:12px;"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_AREA') ?> (m&sup2;)</td>
									<td style="padding:2px;font-size:12px;"><?php echo $resource->Area?></td>
								</tr>
							</table>
						</div>
					</div>
<?php endif ?>				
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3 minheight10"><b><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_CONTRACT') ?>:</b></div>
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3 minheight10"><?php echo  $contractType?></div>
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3 minheight10"><b><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_TYPE') ?>:</b></div>
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3 minheight10"><?php echo  $typeName?></div>
					</div>
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3 minheight10"><b><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_PRICE') ?></b></div>
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3 minheight10"><?php if ($resource->Price != null && $resource->Price > 0 && isset($resource->IsReservedPrice) && $resource->IsReservedPrice!=1 ) :?>
										&euro; <?php echo number_format($resource->Price,0, ',', '.')?>
							<?php else: ?>
									<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_ISRESERVEDPRICE')?>
							<?php endif; ?></div>
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3 minheight10"><b><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_AREA') ?> (m&sup2;)</b></div>
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3 minheight10"><?php echo $resource->Area?></div>
					</div>
				</div>
			</div>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3">
				<div style="padding:5px;">
					<?php if (!$isMerchantAnonymous) :?><b><a href="<?php echo $routeMerchant ?>"  target="_top"><img class="com_bookingforconnector_logo_img" src="<?php echo $merchantLogoPath?>" /></b></a><?php endif ?>
				</div>
			</div>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3">
				<div style="padding:5px;">
					<?php if (!$isMerchantAnonymous) :?>
						<div><a style="color:#666666;" class="com_bookingforconnector_merchantdetails-name" href="<?php echo $routeMerchant?>" target="_top"><?php echo $merchant->Name?></a></div>
					<?php endif ?>
					<div><a style="color:#666666;font-size:12px;" href="javascript:void(0);" onclick="getData(urlCheck,'merchantid=<?php echo $resource->MerchantId?>&task=GetPhoneByMerchantId&language=' + cultureCode,this)"  id="phone<?php echo $resource->ResourceId?>"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_SHOWPHONE') ?></a></div>
					<div><a style="color:#666666;font-size:12px;" href="javascript:void(0);" onclick="getData(urlCheck,'merchantid=<?php echo $resource->MerchantId?>&task=GetPhoneByMerchantId&n=1&language=' + cultureCode,this)"  id="phone<?php echo $resource->ResourceId?>"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_SHOWPHONE1') ?></a></div>
				</div>
			</div>
		</div>
	</div>
	<div style="text-align:center;"><br />
		<a href="<?php echo $route ?>" class="btn" target="_top">&nbsp;&nbsp;&nbsp;Mostra dettagli annuncio&nbsp;&nbsp;&nbsp;</a>
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
<?php if (count ($images)>1): ?>	
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
