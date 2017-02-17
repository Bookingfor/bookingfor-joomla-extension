<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$resource = $this->item;
$language = $this->language;
$resource->Merchant=BFCHelper::getMerchantFromServicebyId($resource->MerchantId);
$merchant = $resource->Merchant;

$config = $this->config;
$XGooglePosDef = $config->get('posx', 0);
$YGooglePosDef = $config->get('posy', 0);

$startzoom = $config->get('startzoom',14);
$googlemapsapykey = $config->get('googlemapskey','');

if(!empty($resource)){


//$merchant = $resource->Merchant;

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

$indirizzo = "";

/**** for search similar *****/
$document 	= JFactory::getDocument();
$db   = JFactory::getDBO();

$uri  = 'index.php?option=com_bookingforconnector&view=condominium';

$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').')  AND published = 1 LIMIT 1' );

$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
if ($itemId<>0)
	$formAction = JRoute::_('index.php?Itemid='.$itemId );
else
	$formAction = JRoute::_($uri);

$zoneId = $resource->ZoneId;

if (!empty($resource->ServiceIdList)){
	$services=BFCHelper::GetServicesByIds($resource->ServiceIdList, $this->language);
}

//$services="";
//if (count($resource->Services) > 0){
//	$tmpservices = array();
//	foreach ($resource->Services as $service){
//		$tmpservices[] = BFCHelper::getLanguage($service->Name, $this->language);
//	}
//	$services = implode(', ',$tmpservices);
//}


//BFCHelper::setState($resource->Merchant, 'merchant', 'merchant');

//-------------------pagina per il redirect di tutte le risorsein vendita

$uriFav = 'index.php?option=com_bookingforconnector&view=condominiums&layout=favorites';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriFav ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1  LIMIT 1' );
$itemIdFav = ($db->getErrorNum())? 0 : intval($db->loadResult());
//-------------------pagina per i l redirect di tutte le risorsein vendita

if ($itemIdFav<>0)
	$routeFav = JRoute::_($uriFav.'&Itemid='.$itemIdFav );
else
	$routeFav = JRoute::_($uriFav);

$indirizzo = "";
$cap = "";
$comune = "";
$provincia = "";
//if (empty($resource->AddressData)){
	$indirizzo = $resource->Address;
	if(!empty($resource->ZipCode)){
		$cap = $resource->ZipCode;
	}
	$comune = $resource->CityName;
	$provincia = $resource->RegionName;
//}else{
//	$addressData = $resource->AddressData;
//	$indirizzo = BFCHelper::getItem($addressData, 'indirizzo');
//	$cap = BFCHelper::getItem($addressData, 'cap');
//	$comune =  BFCHelper::getItem($addressData, 'comune');
//	$provincia = BFCHelper::getItem($addressData, 'provincia');
//}
	if(empty($indirizzo) && empty($comune)){
		if (empty($merchant->AddressData)){
			$indirizzo = $merchant->Address;
			$cap = $merchant->ZipCode;
			$comune = $merchant->CityName;
			$provincia = $merchant->RegionName;
			if (empty($indirizzo)){
				$indirizzo = $resource->MrcAddress;
				$cap = $resource->MrcZipCode;
				$comune = $resource->MrcCityName;
				$provincia = $resource->MrcRegionName;
			}
		}else{
			$addressData = $merchant->AddressData;
			$indirizzo = BFCHelper::getItem($addressData, 'indirizzo');
			$cap = BFCHelper::getItem($addressData, 'cap');
			$comune =  BFCHelper::getItem($addressData, 'comune');
			$provincia = BFCHelper::getItem($addressData, 'provincia');
		}
	}


?>
<div class="com_bookingforconnector_resource">
	<h2 class="com_bookingforconnector_resource-name"><?php echo  $resourceName?> </h2>
	<div class="com_bookingforconnector_resource-address">
		<span class="street-address"><?php echo $indirizzo ?></span>, <span class="postal-code "><?php echo  $cap ?></span> <span class="locality"><?php echo $comune ?></span> <span class="region">(<?php echo  $provincia ?>)</span></strong>
		<?php if(COM_BOOKINGFORCONNECTOR_ENABLEFAVORITES):?>
			 - <a href="javascript:addCustomURlfromfavTranfert('.com_bookingforconnector_resource-name','<?php echo JURI::current() ?>','<?php echo  $resourceName ?>')" class="com_bookingforconnector_resource_addfavorites"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_RESOURCE_ADDFAVORITES') ?></a>
		<?php endif ?>
	</div>	
	<div class="clear"></div>
	<ul class="nav nav-pills nav-justified bfcmenu ">
		<li role="presentation" class="active"><a rel=".resourcecontainer-gallery" data-toggle="tab"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_ANCHORS_PHOTO') ?></a></li>
		<?php if (!empty($resourceDescription)):?><li role="presentation" ><a rel=".com_bookingforconnector_resource-description" data-toggle="tab"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_ANCHORS_DESCRIPTION') ?></a></li><?php endif; ?>
		<?php if (($showResourceMap)) :?><li role="presentation" ><a rel="#resource_map" data-toggle="tab"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_ANCHORS_MAP') ?></a></li><?php endif; ?>
		<?php if($this->items != null): ?><li role="presentation" class="book"><a rel=".com_bookingforconnector_merchantdetails-resources" data-toggle="tab"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_ANCHORS_BOOK_NOW') ?></a></li><?php endif; ?>
	</ul>
	<div class="resourcecontainer-gallery">
	  <?php echo  $this->loadTemplate('gallery'); ?>
	</div>

	<script type="text/javascript">
	<!--
	var urlCheck = "<?php echo JRoute::_('index.php?option=com_bookingforconnector') ?>";	
	var cultureCode = '<?php echo $language ?>';
	var defaultcultureCode = '<?php echo BFCHelper::$defaultFallbackCode ?>';

	jQuery(document).ready(function(){
		openGoogleMapResource();
		if (cultureCode.length>1)
		{
			cultureCode = cultureCode.substring(0, 2).toLowerCase();
		}
		if (defaultcultureCode.length>1)
		{
			defaultcultureCode = defaultcultureCode.substring(0, 2).toLowerCase();
		}

	});

	//-->
	</script>
<!-- Gallery -->
<div class="clear"></div>

<!-- Dettagli --><br />	
	<?php if (!empty($resourceDescription)):?>
	<div class="com_bookingforconnector_resource-description <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
		<h4 class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_DESC') ?></h4>
		<div class="com_bookingforconnector_resource-description-data <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>10">
        <?php echo $resourceDescription ?>		
		</div>
	</div>
	<div class="clear"></div>
	<?php endif; ?>

	<div class="clear"></div>
	<?php if (!empty($services) && count($services) > 0):?>
	<div class="com_bookingforconnector_resource-services <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
		<h4 class="underlineborder <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_SERVICES') ?></h4>
		<?php $count=0; ?>
		<div class="servicesdata <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>10">
		<?php foreach ($services as $service):?>
			<?php
			if ($count > 0) { 
				echo ',';
			}
			?>			
			<span class="com_bookingforconnector_resource-services-service"><?php echo BFCHelper::getLanguage($service->Name, $this->language) ?></span>
			<?php $count += 1; ?>
		<?php endforeach?>
		</div>
	</div>
	<?php endif; ?>
	<div class="clear"></div>
	<br /><br />	
<?php if (($showResourceMap)) :?>
<div id="resource_map" style="width:100%;height:350px"></div>
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
			mapUnit = new google.maps.Map(document.getElementById("resource_map"), myOptions);
			var marker = new google.maps.Marker({
				  position: myLatlng,
				  map: mapUnit
			  });
		}

		function openGoogleMapResource() {
			if (typeof google !== 'object' || typeof google.maps !== 'object'){
				var script = document.createElement("script");
				script.type = "text/javascript";
				script.src = "https://maps.google.com/maps/api/js?key=<?php echo $googlemapsapykey ?>&libraries=drawing&callback=handleApiReady";
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

	<?php if ($this->items != null): ?>
	<div class="com_bookingforconnector_merchantdetails-resources">
<br />
<br /><div id="bfcmerchantlist">
<div id="com_bookingforconnector-items-container-wrapper">
		<div class="com_bookingforconnector-items-container">
		<?php echo  $this->loadTemplate('resources'); ?>
</div>
</div>
</div>
		<?php if (!$this->isFromSearch && $this->pagination->get('pages.total') > 1) : ?>
			<div class="pagination">
				<?php echo $this->pagination->getPagesLinks(); ?>
			</div>
		<?php endif; ?>
	</div>	
	<?php else:?>
	<div class="com_bookingforconnector_merchantdetails-noresources">
		<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_OFFER_NORESULT')?>
	</div>
	<?php endif?>
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

	jQuery('.bfcmenu li a').click(function(e) {
		e.preventDefault();
		jQuery('html, body').animate({ scrollTop: jQuery(jQuery(this).attr("rel")).offset().top }, 2000);
	});
<?php if (!$resource->Enabled) :?>
	jQuery('.com_bookingforconnector_resource').block({
		css: {	
				width:	'90%'
			},
		overlayCSS:  { 
				opacity:	0.9 
			}, 
		message: jQuery('#resoursedisabled')
		});
<?php endif?>
</script>
<?php 
	} //if empty

?>
