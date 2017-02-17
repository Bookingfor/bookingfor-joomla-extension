<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$resource = $this->item;
$merchant = $resource->Merchant;
$resource->Price = $resource->MinPrice;

$config = $this->config;
$XGooglePosDef = $config->get('posx', 0);
$YGooglePosDef = $config->get('posy', 0);

$startzoom = $config->get('startzoom',14);
$googlemapsapykey = $config->get('googlemapskey','');

//$resourceName = BFCHelper::getLanguage($resource->Name, $this->language);
$resourceName = BFCHelper::getLanguage($resource->Name, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
$resourceDescription = BFCHelper::getLanguage($resource->Description, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags'));
$route = JRoute::_('index.php?option=com_bookingforconnector&view=onsellunit&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName));


$typeName =  BFCHelper::getLanguage($resource->CategoryName, $this->language);
$zone = $resource->LocationZone;
$location = $resource->LocationName;
$contractType = ($resource->ContractType) ? JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_CONTRACTTYPE1')  : JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_CONTRACTTYPE');


/*---------------IMPOSTAZIONI SEO----------------------*/
	//<%=typeName %> <%=zone %> <%=location  %> - <%=typeName %> in <%=contractType %> <%=location  %>
	$titleHead = sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_HEAD_TITLE'), $typeName, $zone, $location, $typeName, $contractType, $location);
	//<%=typeName %> <%=location  %>, <%=typeName %> <%=contractType %> <%=location  %>, <%=contractType %> <%=location %>
	$keywordsHead = sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_HEAD_KEYWORDS'), $typeName, $location, $typeName, $contractType, $location, $contractType, $location);
	//CasaPadova propone <%=typeName %> in <%=contractType %> nel comune di <%=location  %>
	$descriptionHead = sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_HEAD_DESCRIPTION'), $typeName, $contractType, $location);
	if ($location != $zone) 
		$descriptionHead .= sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_HEAD_DESCRIPTION1'), $zone);
	if ($resource->Price != null && $resource->Price > 0 && isset($resource->IsReservedPrice) && $resource->IsReservedPrice!=1 ) 
		$descriptionHead .= sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_HEAD_DESCRIPTION2'),  number_format($resource->Price,0, ',', '.'));

	$this->document->setTitle($titleHead);
	$this->document->setMetadata('og:title', $titleHead);
	$this->document->setDescription($descriptionHead);
	$this->document->setMetadata('og:description', $resourceDescription);
	$this->document->setMetadata('keywords', $keywordsHead);
	$this->document->setMetadata('og:url', $route);
/*--------------- FINE IMPOSTAZIONI SEO----------------------*/
//echo "<pre>";
//echo print_r($resource);
//echo "</pre>";
$dateUpdate =  BFCHelper::parseJsonDate($resource->Created); 
if($resource->LastUpdate!=''){
	$dateUpdate =  BFCHelper::parseJsonDate($resource->LastUpdate);
}

$resourceLat = $resource->XGooglePos;
$resourceLon = $resource->YGooglePos;
$isMapVisible = $resource->IsMapVisible;
$isMapMarkerVisible = $resource->IsMapMarkerVisible;
$showResourceMap = (($resourceLat != null) && ($resourceLon !=null) && $isMapVisible);
if ($isMapMarkerVisible){
	$htmlmarkerpoint = "&markers=color:blue%7C" . $resourceLat . "," . $resourceLon;
}

//$addressData = $resource->AddressData;
//$merchantAddress = false;
//if ($addressData == '' || $addressData == null || $merchant->MerchantTypeId != 2) {
//	$addressData = $merchant->AddressData;
//	$merchantAddress = true;
//}


$addressData ="";
$arrData = array();
if ($resource->IsAddressVisible)
{
	if(!empty($resource->AddressData)){
		$arrData[] = ($resource->AddressData);
	}
}
if(!empty($zone)){
	$arrData[] = ($zone);
}
if(!empty($location)){
	$arrData[] = ($location);
}
$addressData = implode(" - ",$arrData);

/**** for search similar *****/
$document 	= JFactory::getDocument();
$db   = JFactory::getDBO();
$lang = JFactory::getLanguage()->getTag();

// i valori sono impostati dal modulo
//$XGooglePos = 45.406947; 
//$YGooglePos = 11.892443;

$uri  = 'index.php?option=com_bookingforconnector&view=searchonsell';

$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri .'%' ) .' AND (language='. $db->Quote($lang) .' OR language='.$db->Quote('*').') AND published = 1  LIMIT 1' );

$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
if ($itemId<>0)
	$formAction = JRoute::_('index.php?Itemid='.$itemId );
else
	$formAction = JRoute::_($uri);

$deltapricePerCent = 20;
$deltaprice = $resource->Price * $deltapricePerCent / 100;

$contractTypeId = $resource->ContractType;
$categoryId = $resource->CategoryId;
$zoneId = $resource->ZoneId;
$pricemax = $resource->Price + $deltaprice;
$pricemin = $resource->Price - $deltaprice;
//$areamin;
//$areamax;

//$services="";
//if (count($resource->Services) > 0){
//	$tmpservices = array();
//	foreach ($resource->Services as $service){
//		$tmpservices[] = BFCHelper::getLanguage($service->Name, $this->language);
//	}
//	$services = implode(', ',$tmpservices);
//}
//$resource->Price
/**** for search similar *****/
//add counter
$model      = $this->getModel();
$retCounter = $model->setCounterByResourceId($resource->ResourceId,"details",$this->language);

BFCHelper::setState($resource->Merchant, 'merchant', 'merchant');

//$test = BFCHelper::getState('merchant', 'merchant');
//
//echo "<pre>";
//echo print_r($test);
//echo "</pre>";
?>
<form action="<?php echo $formAction; ?>" method="post" id="searchformsimilaronsellunit">
	<input type="hidden" value="<?php echo uniqid('', true)?>" name="searchid" />
	<input type="hidden" value="1" name="newsearch" />
	<input type="hidden" value="0" name="limitstart" />
	<input type="hidden" name="filter_order" value="" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<input type="hidden" value="<?php echo $lang ?>" name="cultureCode" />
	<input type="hidden" value="<?php echo $contractTypeId ?>" name="contractTypeId" />
	<input type="hidden" value="<?php echo $zoneId ?>" name="zoneId" />
	<input type="hidden" value="<?php echo $categoryId ?>" name="unitCategoryId" />
	<input type="hidden" value="<?php echo $pricemin ?>" name="pricemin" />
	<input type="hidden" value="<?php echo $pricemax ?>" name="pricemax" />
	<input type="hidden" value="<?php echo $resource->ResourceId ?>" name="resourceid" />
</form>

<div class="com_bookingforconnector_resource com_bookingforconnector_resource-mt<?php echo  $merchant->MerchantTypeId?> com_bookingforconnector_resource-t<?php echo  $resource->MasterTypologyId?>">
	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
			<h2 class="com_bookingforconnector_resource-name"><?php echo  $resourceName?></h2>
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 com_bookingforconnector_resource_options" >
				 <code>
					 <a class="com_bookingforconnector_searchsimilar"  href="javascript:void(0);" onclick="javascript: searchsimilar()" ><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_FINDSIMILAR') ?></a>
				 </code>&nbsp;
				 <code>
					 <!-- AddToAny BEGIN -->
					 <a class="com_bookingforconnector_share a2a_dd"  href="http://www.addtoany.com/share_save" ><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_SHARE') ?></a>
					<script type="text/javascript" src="//static.addtoany.com/menu/page.js"></script>
					<!-- AddToAny END -->
				 </code>&nbsp;
				 <code>
					 <a class="com_bookingforconnector_print"  href="javascript:void(0);" onclick="javascript:window.print()"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_PRINT') ?></a>
				 </code>&nbsp;
				 <code>
					 <a class="com_bookingforconnector_fav"  href="javascript:void(0);" onclick="javascript:addCustomURlfromfav('<?php echo $route ?>','<?php echo  $resourceName ?>')"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_FAVORITES_ADD')?></a>
				 </code>
		</div>
	</div>
	<div class="com_bookingforconnector_resource_feature">
		<span>
			<?php echo $resource->Area?> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_AREAMQ') ?>
		</span>
		<span>
			<?php echo $resource->Rooms?> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_ROOMS') ?>
		</span>
		<span>
			<?php echo  $contractType?>
		</span>
		<span>
			<?php if ($resource->Price != null && $resource->Price > 0 && isset($resource->IsReservedPrice) && $resource->IsReservedPrice!=1 ) :?>
						<strong>&euro; <?php echo number_format($resource->Price,0, ',', '.')?></strong>
			<?php else: ?>
					<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_ISRESERVEDPRICE')?>
			<?php endif; ?>
		</span>
	</div>
	<div style="margin-bottom:5px;">
		<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTLIST_ADDRESS') ?>: <?php echo  $addressData?>
	</div>
	<div class="clear"></div>

	<div class="resourcecontainer">
		<div class="resourcetabmenu">
			<a class="foto selected" rel="foto">foto</a><a class="video" rel="video">video</a><a class="planimetria" rel="planimetria">Planimetria</a><a class="mappa" rel="mappa">mappa</a> 
		</div>
		<div class="resourcetabcontainer">
			<div id="foto" class="tabcontent">
				<?php echo  $this->loadTemplate('gallery_'.COM_BOOKINGFORCONNECTOR_GALLERY); ?>
			</div>
			<div id="planimetria" class="tabcontent">
				planimetria
			</div>
			<div id="video" class="tabcontent">
				video
			</div>
			<div id="mappa" class="tabcontent">
				mappa
			</div>
		</div>
	</div>
	<script type="text/javascript">
	<!--
	jQuery(document).ready(function(){
			jQuery('.tabcontent').hide();
			jQuery(".tabcontent:first").show(); 

			// accrocchio per zindex dei video..
			jQuery("iframe").each(function(){ 
				var ifr_source = jQuery(this).attr('src');  
				var wmode = "wmode=transparent";  
				try
				{
					if(ifr_source.indexOf('?') != -1) jQuery(this).attr('src',ifr_source+'&'+wmode);  
					else jQuery(this).attr('src',ifr_source+'?'+wmode);
					
				}
				catch (err)
				{
				}
			});

		jQuery(".resourcetabmenu a").click(function() {
			jQuery('.tabcontent').hide();
			var activeTab = jQuery(this).attr("rel"); 
			jQuery(".resourcetabmenu a").removeClass("selected");
			jQuery("#"+activeTab).fadeIn();
			jQuery(this).addClass("selected");
			try
			{
//				markertoremove.setMap(null);
			}
			catch (err)
			{
			}
			if (activeTab=='mappa')
			{
//				google.maps.event.trigger(sharedMap, 'resize');
//				sharedMap.setCenter(new google.maps.LatLng(<?php echo $mappa; ?>));
			}
			var slider = jQuery(".royalSlider").data('royalSlider');
			slider.updateSliderSize(); // updates size of slider. Use after you resize slider with js. 

		});
	});
	//-->
	</script>
	<?php if ($showResourceMap) :?>
		<a class="lightboxlink" onclick='javascript:openGoogleMapResource();' href="javascript:void(0)"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_SHOWONMAP') ?></a><br />
	<?php endif?>

<!-- Gallery -->


<!-- Dettagli --><br />	
	<table class="table table-striped">
		<tr>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_CONTRACT') ?>:</td>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3"><?php echo  $contractType?></td>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_PRICE') ?></td>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3">
			<?php if ($resource->Price != null && $resource->Price > 0 && isset($resource->IsReservedPrice) && $resource->IsReservedPrice!=1 ) :?>
						&euro; <?php echo number_format($resource->Price,0, ',', '.')?>
			<?php else: ?>
					<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_ISRESERVEDPRICE')?>
			<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_TYPE') ?>:</td>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3"><?php echo  $typeName?></td>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_AREA') ?> (mq)</td>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3"><?php echo $resource->Area?></td>
		</tr>
		<tr>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_LOCATION') ?>:</td>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3"><?php echo  $location?></td>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_NROOMS') ?>:</td>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3"><?php echo $resource->Rooms?></td>
		</tr>
		<tr>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_ZONE') ?>:</td>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3"><?php echo  $zone?></td>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_DATEUPDATE') ?>:</td>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3"><?php echo $dateUpdate?></td>
		</tr>
		<tr>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_FLOOR') ?>:</td>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3">
				<?php echo (($resource->Floor >0 )? $resource->Floor ."&#176;" : JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_UNITFLOOR'.$resource->Floor) ) ?>
			</td>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_STATUS') ?>:</td>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3"><?php 
				echo ( 
					( $resource->IsNewBuilding ) ? JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_ISNEWBUILDING'): 
						(empty($resource->Status)? JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_STATUS') : $resource->Status)
					);
			?></td>
		</tr>
		<tr>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_CENTRALIZEDHEATING') ?>:</td>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3">
				<?php echo (
					(isset($resource->CentralizedHeating)? 
						(
							($resource->CentralizedHeating)? JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_CENTRALIZEDHEATING_TRUE'): JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_CENTRALIZEDHEATING_FALSE')
						)
						: JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_CENTRALIZEDHEATING_NOVALUE') )
					) ?>
			</td>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_GARAGES') ?>:</td>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3"><?php 
				if(!isset($resource->Garages) && !isset($resource->ParkingPlaces)){
					echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_NOVALUE');
				}else{
					if ($resource->Garages>0) {
						echo $resource->Garages;
					}
					if ($resource->Garages>0 && $resource->ParkingPlaces>0) {
						echo " + ";
					}
					if ($resource->ParkingPlaces>0) {
						echo $resource->ParkingPlaces .  JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_PARKINGPLACES');
					}

				}
				?>
			</td>
		</tr>
		<?php if((isset($resource->EnergyClass) && $resource->EnergyClass>0 ) || (isset($resource->EpiValue) && $resource->EpiValue>0 ) ): ?>
		<tr>
			<?php if(isset($resource->EnergyClass) && $resource->EnergyClass>0): ?>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_ENERGYCLASS') ?>:</td>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3" <?php if(!isset($resource->EpiValue)) {echo "colspan=\"3\"";}?>>
				<div class="energyClass energyClass<?php echo $resource->EnergyClass?>">
				<?php 
					switch ($resource->EnergyClass) {
						case 100: echo "A+"; break;
						case 101: echo "A"; break;
						case 102: echo "B"; break;
						case 103: echo "C"; break;
						case 104: echo "D"; break;
						case 105: echo "E"; break;
						case 106: echo "F"; break;
						case 107: echo "G"; break;
					}
				?>
				</div>
			</td>
			<?php endif ?>
			<?php if(isset($resource->EpiValue) && $resource->EpiValue>0): ?>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_EPIVALUE') ?>:</td>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3" <?php if(!isset($resource->EnergyClass)) {echo "colspan=\"3\"";}?>><?php echo $resource->EpiValue?> <?php echo $resource->EpiUnit?></td>
			<?php endif ?>
		</tr>
		<?php endif ?>
<?php 
print("<tr>\n");
$i = 0;
foreach($resource->Services as $service) {
?>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3"><?php echo BFCHelper::getLanguage($service->Name, $this->language) ?>:</td>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_YES') ?></td>
<?php
    if ($i % 2 === 1) {
        print("</tr>\n<tr>");
    }
    $i++;
}
print("</tr>\n");
 ?>
	</table>

	<div class="com_bookingforconnector_resource-description">
		<h4 class="underlineborder"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_DESC') ?></h4>
		<?php echo  BFCHelper::getLanguage($resource->Description, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')) ?>
	</div>
	<?php if(isset($resource->CanCollaborate) && $resource->CanCollaborate) :?>
		<div class="pull-right cancollaborate"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_CANCOLLABORATE'); ?></div>
		<br />
	<?php endif ?>
	<div class="clear"></div>
	<?php if (!empty($services)):?>
	<div class="com_bookingforconnector_resource-services">
		<h4 class="underlineborder"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_SERVICES') ?></h4>
		<span class="com_bookingforconnector_resource-services-service"><?php echo $services ?></span>
	</div>
	<?php endif; ?>
	<br />
	{loadposition bannerdetailssell}
	<div >
		<a name="reqform" ></a>
		{rsform 5}
	</div>
	<div class="clear"></div>

</div>
<div id="dialogiframe" style="display:none;">
    <iframe id="iframeToload" src="" height="100%" width="100%" frameborder="0" marginheight="0" marginwidth="0"></iframe>
</div>

<div id="resoursedisabled" style="display:none;"> 
	<br /><br />
    <h1>Risorsa disabilitata</h1><br /><br /><br />
	<p>La risorsa non &egrave; pi&ugrave; disponibile</p><br /><br /><br />
</div> 

<script type="text/javascript">

<?php if (!$resource->Enabled) :?>
	jQuery('.main-content').block({
		css: {	
				width:	'90%'
			},
		overlayCSS:  { 
				opacity:	0.9 
			}, 
		message: jQuery('#resoursedisabled')
		});
<?php endif?>

var imgSimilar = new Image(); 
imgSimilar.src = "<?php echo JURI::root();?>media/com_bookingfor/images/loader.gif";

	jQuery(function($) {
		$('.moduletable-insearch').show();
	});

	function searchsimilar(){
		msg1 = "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_BOOKINGFORSEARCH_MSG1'); ?>";
		msg2 = "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_BOOKINGFORSEARCH_MSG2'); ?>";
		var img1 = new Image(); 
		img1.src = "<?php echo JURI::root();?>media/com_bookingfor/images/loader.gif";
		waitBlockUIsimilar(msg1, msg2,img1); 
		jQuery("#searchformsimilaronsellunit").submit();
	}

	function waitBlockUIsimilar(msg1 ,msg2, img1){
		jQuery.blockUI({
			message: '<h1 style="font-size: 15px;">'+msg1+'<br />'+msg2+'</h1><br /><img src="'+imgSimilar.src+'" width="48" height="48" alt="" border="0" />', 
			css: {border: '2px solid #1D668B', padding: '20px', backgroundColor: '#fff', '-webkit-border-radius': '10px', '-moz-border-radius': '10px', color: '#1D668B'},
			overlayCSS: {backgroundColor: '#1D668B', opacity: .7}  
			});
		}


	function openDialogiframe(urltoopen, titleurl) {
		var width = jQuery(window).width()*0.9;
		var height = jQuery(window).height()*0.9;
		var $idialog = jQuery("#dialogiframe").dialog({
				autoOpen: false,
				modal: true,
				height:height,
				width: width,
				fluid: true, //new option
				title: titleurl,
				open: function(ev, ui){
					jQuery('#iframeToload').attr('src',urltoopen);
				}
			});
		$idialog.dialog('open');
	}
		jQuery(window).resize(function() {
			var wWidth = jQuery(window).width();
			var dWidth = wWidth * 0.9;
			var wHeight = jQuery(window).height();
			var dHeight = wHeight * 0.9;
			jQuery("#dialogiframe").dialog("option", "width", dWidth);
			jQuery("#dialogiframe").dialog("option", "height", dHeight);
			jQuery("#dialogiframe").dialog("option", "position", "center");
		});

</script>
<?php if (($showResourceMap)) :?>


	<script type="text/javascript">
	<!--
		var mapUnit;
		var myLatlng;

		// make map
		function handleApiReady() {
			myLatlng = new google.maps.LatLng(<?php echo $resourceLat?>, <?php echo $resourceLon?>);
			var myOptions = {
					zoom: 15,
<?php if ($isMapMarkerVisible) :?>
					maxZoom: 17,
<?php else : ?>
					maxZoom: 14,
<?php endif; ?>
					minZoom:7,
					center: myLatlng,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				}
			mapUnit = new google.maps.Map(document.getElementById("map_canvasresource"), myOptions);
<?php if ($isMapMarkerVisible) :?>
			var marker = new google.maps.Marker({
				  position: myLatlng,
				  map: mapUnit
			  });
<?php else : ?>
			var circle = new google.maps.Circle({
				strokeColor: "#FF0000",
				strokeWeight: 2,
				fillOpacity: 0,
				center: myLatlng,
				radius: 1000, //in metri (1000m = 1Km)
				map: mapUnit
			});
<?php endif; ?>
		}

		function openGoogleMapResource() {
			var width = jQuery(window).width()*0.9;
			var height = jQuery(window).height()*0.9;

			var $dialog = jQuery("#map_canvasresource").dialog({
					autoOpen: false,
					modal: true,
					resize: function( event, ui ) {
						google.maps.event.trigger(mapUnit, 'resize');
						mapUnit.setCenter(myLatlng);
					},
					height:height,
					width: width,
					fluid: true, //new option
					title: '<?php echo htmlspecialchars($resourceName, ENT_QUOTES); ?>'
				});
			if (typeof google !== 'object' || typeof google.maps !== 'object'){
				var script = document.createElement("script");
				script.type = "text/javascript";
				script.src = "https://maps.google.com/maps/api/js?libraries=drawing&callback=handleApiReady";
				document.body.appendChild(script);
			}else{
				if (typeof mapUnit !== 'object'){
					handleApiReady();
				}
			}
			$dialog.dialog('open');
			google.maps.event.trigger(mapUnit, 'resize');
			mapUnit.setCenter(myLatlng);
		}
		jQuery(window).resize(function() {
			var wWidth = jQuery(window).width();
			var dWidth = wWidth * 0.9;
			var wHeight = jQuery(window).height();
			var dHeight = wHeight * 0.9;
			jQuery("#map_canvasresource").dialog("option", "width", dWidth);
			jQuery("#map_canvasresource").dialog("option", "height", dHeight);
			jQuery("#map_canvasresource").dialog("option", "position", "center");
			if (typeof google !== "undefined")
			{
				if (typeof google !== 'object' || typeof google.maps !== 'object'){
					google.maps.event.trigger(mapUnit, 'resize');
					mapUnit.setCenter(myLatlng);
				}
			}
		});


	//-->
	</script>
	<div id="map_canvasresource" style="width:100%; height:400px"></div>
<?php endif; ?>

