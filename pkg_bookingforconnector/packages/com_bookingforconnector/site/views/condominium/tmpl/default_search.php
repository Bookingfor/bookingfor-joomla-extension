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


//$typeName =  BFCHelper::getLanguage($resource->CategoryName, $this->language);
//$zone = $resource->LocationZone;
//$location = $resource->LocationName;
//
/*---------------IMPOSTAZIONI SEO----------------------*/
//	$this->document->setTitle($titleHead);
//	$this->document->setMetadata('og:title', $titleHead);
//	$this->document->setDescription($descriptionHead);
//	$this->document->setMetadata('og:description', $resourceDescription);
//	$this->document->setMetadata('keywords', $keywordsHead);
//	$this->document->setMetadata('og:url', $route);
/*--------------- FINE IMPOSTAZIONI SEO----------------------*/

//echo "<pre>resource: <br />";
//echo print_r($resource);
//echo "</pre>";

$resourceLat = $resource->XGooglePos;
$resourceLon = $resource->YGooglePos;
$showResourceMap = (($resourceLat != null) && ($resourceLon !=null) );
$htmlmarkerpoint = "&markers=color:blue%7C" . $resourceLat . "," . $resourceLon;

$addressData ="";
$arrData = array();
if(!empty($resource->AddressData)){
	$arrData[] = ($resource->AddressData);
}
//if(!empty($zone)){
//	$arrData[] = ($zone);
//}
//if(!empty($location)){
//	$arrData[] = ($location);
//}
$addressData = implode(" - ",$arrData);

/**** for search similar *****/
$document 	= JFactory::getDocument();
$db   = JFactory::getDBO();
$lang = JFactory::getLanguage()->getTag();

// i valori sono impostati dal modulo
//$XGooglePos = 45.406947; 
//$YGooglePos = 11.892443;

$uri  = 'index.php?option=com_bookingforconnector&view=condominium';

$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri .'%' ) .' AND (language='. $db->Quote($lang) .' OR language='.$db->Quote('*').') AND published = 1  LIMIT 1' );

$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
if ($itemId<>0)
	$formAction = JRoute::_('index.php?Itemid='.$itemId );
else
	$formAction = JRoute::_($uri);

$zoneId = $resource->ZoneId;

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

BFCHelper::setState($resource->Merchant, 'merchant', 'merchant');

//-------------------pagina per il redirect di tutte le risorsein vendita

$uriFav = 'index.php?option=com_bookingforconnector&view=condominiums&layout=favorites';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriFav ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1  LIMIT 1' );
$itemIdFav = ($db->getErrorNum())? 0 : intval($db->loadResult());
//-------------------pagina per i l redirect di tutte le risorsein vendita

if ($itemIdFav<>0)
	$routeFav = JRoute::_($uriFav.'&Itemid='.$itemIdFav );
else
	$routeFav = JRoute::_($uriFav);

/*-----------------------------------*/
//-------------------pagina per il redirect di tutti i merchant

$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1  LIMIT 1' );
$itemIdMerchant = ($db->getErrorNum())? 0 : intval($db->loadResult());
//-------------------pagina per il redirect di tutti i merchant

if ($itemIdMerchant<>0)
	$uriMerchant.='&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name).'&Itemid='.$itemIdMerchant;
else
	$uriMerchant.='&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name);

$routeMerchant = JRoute::_($uriMerchant);

//$route = JRoute::_('index.php?option=com_bookingforconnector&view=merchantdetails&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name));

$merchantLogo = JURI::base() . "media/com_bookingfor/images/default.png";
if ($merchant->LogoUrl != '') {
	$merchantLogo = BFCHelper::getImageUrl('merchant',$merchant->LogoUrl, 'merchant_logo_small_top');
}
$addressDataMerchant = $merchant->AddressData;

//$test = BFCHelper::getState('merchant', 'merchant');
//
//echo "<pre>";
//echo print_r($test);
//echo "</pre>";

//$PlanimetryData = $this->item->OnSellUnit->PlanimetryData;
//$showResourcePlanimetria= (!empty($PlanimetryData) && stristr($PlanimetryData, 'image'));
//$VideoData = $this->item->OnSellUnit->VideoData;
//$showResourceVideo= (!empty($VideoData) && stristr($VideoData, 'video'));

?>
<div class="com_bookingforconnector_resource com_bookingforconnector_resource-mt<?php echo  $merchant->MerchantTypeId?> com_bookingforconnector_resource-t<?php echo  $resource->MasterTypologyId?>">
	<h2 class="com_bookingforconnector_resource-name"><?php echo  $resourceName?> 
		<span class="com_bookingforconnector_resource-rating com_bookingforconnector_resource-rating<?php echo  $resource->Rating ?>">
			<!-- <span class="com_bookingforconnector_resource-ratingText">Rating <?php echo  $merchant->Rating ?></span> -->
		</span>
	</h2>
	<div class="com_bookingforconnector_resource-address">
		<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTLIST_ADDRESS') ?>: <?php echo  $addressData?>
	</div>
	<div class="clear"><br /></div>
	<div class="resourcecontainer">
		<div class="resourcetabmenu">
			<a class="foto selected" rel="foto"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_TAB_PHOTO') ?></a><?php if (($showResourcePlanimetria)) :?><a class="planimetria" rel="planimetria"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_TAB_PLANIMETRIA') ?></a><?php endif?><?php if (($showResourceVideo)) :?><a class="video" rel="video"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_TAB_VIDEO') ?></a><?php endif?><?php if (($showResourceMap)) :?><a class="mappa" rel="mappa"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_TAB_MAP') ?></a><?php endif?>
		</div>
		<div class="resourcetabcontainer">
			<div id="foto" class="tabcontent">
				<?php echo  $this->loadTemplate('gallery'); ?>
			</div>
			<div id="mappa" class="tabcontent">
				<div id="map_canvasresource" style="width:100%; height:400px"></div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
	<!--
	var urlCheck = "<?php echo JRoute::_('index.php?option=com_bookingforconnector') ?>";	

	jQuery(document).ready(function(){
			//jQuery('.tabcontent').hide();
		jQuery(".tabcontent:first").show(); 
		
		jQuery(".resourcetabmenu a").click(function() {
			jQuery('.tabcontent').hide();
			var activeTab = jQuery(this).attr("rel"); 
			jQuery(".resourcetabmenu a").removeClass("selected");
			jQuery("#"+activeTab).fadeIn();
			jQuery(this).addClass("selected");
			currentslider="";
			if (activeTab=='mappa')
			{
				openGoogleMapResource()
			}
			if (activeTab=='planimetria')
			{
				currentslider = "#resourcePlanimetrygallery";
			}
			if (activeTab=='video')
			{
				currentslider = "#resourceVideogallery";
			}
			if (activeTab=='foto')
			{
				currentslider = "#resourcegallery";
			}

		});
	});
	//-->
	</script>
<!-- Gallery -->
<div class="clear"></div>

<!-- Dettagli --><br />	
	<div class="com_bookingforconnector_resource-description">
		<h4 class="underlineborder"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_DESC') ?></h4>
		<?php echo  BFCHelper::getLanguage($resource->Description, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')) ?>
	</div>
	<div class="clear"></div>
	<?php if (!empty($services)):?>
	<div class="com_bookingforconnector_resource-services">
		<h4 class="underlineborder"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_SERVICES') ?></h4>
		<span class="com_bookingforconnector_resource-services-service"><?php echo $services ?></span>
	</div>
	<?php endif; ?>
	<div class="clear"></div>
	<br /><br />
	<div id="firstresources">Loading....</div>
	<br /><br />

</div>
<?php if (!$resource->Enabled) :?>

<div id="resoursedisabled"> 
	<br /><br />
    <h1>Risorsa disabilitata</h1><br /><br /><br />
	<p>La risorsa non &egrave; pi&ugrave; disponibile</p><br /><br /><br />
</div> 
<?php endif?>

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
	jQuery(function($) {
		$('.moduletable-insearch').show();
	});
	//load first resourses
	var pagelist = "<?php echo JRoute::_('index.php?option=com_bookingforconnector&layout=resourcesajax&tmpl=component&format=raw&view=condominium&resourceId=' . $resource->CondominiumId . ':' . BFCHelper::getSlug($resourceName)) ?>";
	jQuery("#firstresources").load(pagelist, function() {
//			SqueezeBox.initialize({});
//			SqueezeBox.assign($$('#firstresources  a.boxed'), { //change the divid (#contentarea) as to the div that you use for refreshing the content
//				parse: 'rel'
//			});
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
					maxZoom: 17,
					minZoom:7,
					center: myLatlng,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				}
			mapUnit = new google.maps.Map(document.getElementById("map_canvasresource"), myOptions);
			var marker = new google.maps.Marker({
				  position: myLatlng,
				  map: mapUnit
			  });
		}

		function openGoogleMapResource() {
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
			redrawmap()
		}
		function redrawmap() {
			if (typeof google !== "undefined")
			{
				if (typeof google === 'object' || typeof google.maps === 'object'){
					google.maps.event.trigger(mapUnit, 'resize');
					mapUnit.setCenter(myLatlng);
				}
			}
		}

		jQuery(window).resize(function() {
			redrawmap()
		});

	//-->
	</script>
<?php endif; ?>

