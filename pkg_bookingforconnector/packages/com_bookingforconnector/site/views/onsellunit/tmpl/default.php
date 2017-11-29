<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$base_url = JURI::root();

$currencyclass = bfi_get_currentCurrency();

$language = $this->language;
$resource = $this->item;
$resource_id = $resource->ResourceId; //per form contactpopup
$merchant = $resource->Merchant;

$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;
$showdata = COM_BOOKINGFORCONNECTOR_SHOWDATA;

$posx = COM_BOOKINGFORCONNECTOR_GOOGLE_POSX;
$posy = COM_BOOKINGFORCONNECTOR_GOOGLE_POSY;
$startzoom = COM_BOOKINGFORCONNECTOR_GOOGLE_STARTZOOM;
$googlemapsapykey = COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLEMAPSKEY;

$resourceName = BFCHelper::getLanguage($resource->Name, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
$merchantName = BFCHelper::getLanguage($merchant->Name, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
$resourceDescription = BFCHelper::getLanguage($resource->Description, $language, null, array('ln2br'=>'ln2br', 'bbcode'=>'bbcode', 'striptags'=>'striptags'));
$route = JRoute::_('index.php?option=com_bookingforconnector&view=onsellunit&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName));


$resource->Price = $resource->MinPrice;
$typeName =  BFCHelper::getLanguage($resource->CategoryName, $language);
$zone = $resource->LocationZone;
$location = $resource->CityName;

$resourceLat = "";
$resourceLon = "";
if(!empty($resource->XGooglePos)){
	$resourceLat = $resource->XGooglePos;
}
if(!empty($resource->YGooglePos)){
	$resourceLon = $resource->YGooglePos;
}

if(!empty($resource->XPos)){
	$resourceLat = $resource->XPos;
}
if(!empty($resource->YPos)){
	$resourceLon = $resource->YPos;
}

$isMapVisible = $resource->IsMapVisible;
$isMapMarkerVisible = $resource->IsMapMarkerVisible;
$showResourceMap = (($resourceLat != null) && ($resourceLon !=null) && $isMapVisible);
if ($isMapMarkerVisible){
	$htmlmarkerpoint = "&markers=color:blue%7C" . $resourceLat . "," . $resourceLon;
}

$indirizzo = isset($resource->Address)?$resource->Address:"";
$cap = isset($resource->ZipCode)?$resource->ZipCode:""; 
$comune = isset($resource->CityName)?$resource->CityName:"";
$provincia = isset($resource->RegionName)?$resource->RegionName:"";
$stato = isset($resource->StateName)?$resource->StateName:"";

$deltapricePerCent = 20;
$deltaprice = 1;
if($resource->Price>0){
	$deltaprice = $resource->Price * $deltapricePerCent / 100;
}
$contractTypeId = $resource->ContractType;

$contractType = ($resource->ContractType) ? JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_CONTRACTTYPE1')  : JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_CONTRACTTYPE');

$dateUpdate =  BFCHelper::parseJsonDate($resource->AddedOn); 
if($resource->UpdatedOn!=''){
	$dateUpdate =  BFCHelper::parseJsonDate($resource->UpdatedOn);
}

/**** for search similar *****/
$db   = JFactory::getDBO();

$uri  = 'index.php?option=com_bookingforconnector&view=searchonsell';

$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );

$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());

$currUriresource = $uri.'&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName);

if ($itemId<>0){
	$currUriresource.='&Itemid='.$itemId;
}
$resourceRoute = JRoute::_($currUriresource);

$categoryId = $resource->CategoryId;
$zoneId = $resource->ZoneId;

$pricemax = round(($resource->Price + $deltaprice), 0, PHP_ROUND_HALF_UP); 
$pricemin = round(($resource->Price - $deltaprice), 0, PHP_ROUND_HALF_DOWN); 
if (!empty($resource->ServiceIdList)){
	$services=BFCHelper::GetServicesByIds($resource->ServiceIdList, $language);
}

//-------------------pagina per il redirect di tutte le risorsein vendita

//-------------------pagina per il redirect di tutti i merchant

$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
$itemIdMerchant = ($db->getErrorNum())? 0 : intval($db->loadResult());
//-------------------pagina per il redirect di tutti i merchant

if ($itemIdMerchant<>0)
	$uriMerchant.='&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name).'&Itemid='.$itemIdMerchant;
else
	$uriMerchant.='&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name);

$routeMerchant = JRoute::_($uriMerchant,true, -1);

/*---------------IMPOSTAZIONI SEO----------------------*/
	$merchantDescriptionSeo = BFCHelper::getLanguage($merchant->Description, $language, null, array( 'nobr'=>'nobr', 'bbcode'=>'bbcode', 'striptags'=>'striptags')) ;
	$resourceDescriptionSeo = BFCHelper::getLanguage($resource->Description, $language, null, array( 'nobr'=>'nobr', 'bbcode'=>'bbcode', 'striptags'=>'striptags')) ;
	if (!empty($merchantDescriptionSeo) && strlen($merchantDescriptionSeo) > 170) {
	    $merchantDescriptionSeo = substr($merchantDescriptionSeo,0,170);
	}
	if (!empty($resourceDescriptionSeo) && strlen($resourceDescriptionSeo) > 170) {
	    $resourceDescriptionSeo = substr($resourceDescriptionSeo,0,170);
	}

//	$titleHead = "$resourceName ($comune, $stato) - $sitename";
//	$keywordsHead = "$resourceName, $comune, $stato, $merchant->MainCategoryName";
	$titleHead = sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_HEAD_TITLE'), $resourceName, $zone, $location, $typeName, $contractType, $location);
	$keywordsHead = sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_HEAD_KEYWORDS'), $typeName, $location, $typeName, $contractType, $location, $contractType, $location);
	$descriptionHead = sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_HEAD_DESCRIPTION'), $resourceName, $typeName, $contractType, $location);
	if ($location != $zone) 
		$descriptionHead .= sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_HEAD_DESCRIPTION1'), $zone);
	if ($resource->Price != null && $resource->Price > 0 && isset($resource->IsReservedPrice) && $resource->IsReservedPrice!=1 ) 
		$descriptionHead .= sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_HEAD_DESCRIPTION2'),  number_format($resource->Price,0, ',', '.'));

	$routeSeo = ($isportal)? $routeMerchant: $base_url;
	$resourceRouteSeo = JRoute::_($currUriresource,true, -1);

	$this->document->setTitle($titleHead);
	$this->document->setDescription($descriptionHead);
	$this->document->setMetadata('keywords', $keywordsHead);
	$this->document->setMetadata('robots', "index,follow");
	$this->document->setMetadata('og:title', $titleHead);
	$this->document->setMetadata('og:description', $descriptionHead);
	$this->document->setMetadata('og:url', $resourceRouteSeo);

	$payload["@type"] = "Organization";
	$payload["@context"] = "http://schema.org";
	$payload["name"] = $merchantName;
	$payload["description"] = $merchantDescriptionSeo;
	$payload["url"] = $routeSeo; 
	if (!empty($merchant->LogoUrl)){
		$payload["logo"] = "https:".BFCHelper::getImageUrlResized('merchant',$merchant->LogoUrl, 'logobig');
	}

	$payloadresource["@type"] = "Product";
	$payloadresource["@context"] = "http://schema.org";
	$payloadresource["name"] = $resourceName;
	$payloadresource["description"] = $resourceDescriptionSeo;
	$payloadresource["url"] = $resourceRouteSeo; 
	if (!empty($resource->ImageUrl)){
		$payloadresource["image"] = "https:".BFCHelper::getImageUrlResized('onsellunits',$resource->ImageUrl, 'logobig');
	}
/*--------------- FINE IMPOSTAZIONI SEO----------------------*/


?>
<script type="application/ld+json">// <![CDATA[
<?php echo json_encode($payloadresource,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE); ?>
// ]]></script>
<script type="application/ld+json">// <![CDATA[
<?php echo json_encode($payload,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE); ?>
// ]]></script>

<div class="bfi-content">	

	<div class="bfi-title-name"><h1><?php echo  $resourceName?></h1> - <h2 class="bfi-cursor"><?php echo  $merchantName?></h2></div>
<?php if ($resource->IsAddressVisible) { ?>
	<div class="bfi-address">
				<i class="fa fa-map-marker fa-1"></i> <?php if (($showResourceMap)) {?><a class="bfi-map-link" rel="#resource_map"><?php } ?><span class="street-address"><?php echo $indirizzo ?></span>, <span class="postal-code "><?php echo  $cap ?></span> <span class="locality"><?php echo $comune ?></span>, <span class="region"><?php echo  $stato ?></span>
				<?php if (($showResourceMap)) {?></a><?php } ?>
	</div>	
<?php } ?>
	<div class="bfi-clearboth"></div>
<!-- Navigation -->	
	<ul class="bfi-menu-top">
		<?php if (!empty($resourceDescription)):?><li><a rel=".bfi-description-data"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_ANCHORS_DESCRIPTION') ?></a></li><?php endif; ?>
		<?php if($isportal): ?><li ><a rel=".bfi-merchant-simple"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_ANCHORS_HOST') ?></a></li><?php endif; ?>
		<?php if (($showResourceMap)) :?><li><a rel="#resource_map"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_ANCHORS_MAP') ?></a></li><?php endif; ?>
	</ul>
</div>
	<div class="bfi-resourcecontainer-gallery">
		<?php  include('resource-gallery.php');  ?>
	</div>

<div class="bfi-content">	
	<div class="bfi-row bfi-hideonextra">
		<div class="bfi-col-md-8 bfi-description-data">
			<?php echo $resourceDescription ?>		
		</div>	
		<div class="bfi-col-md-4">
			<div class=" bfi-feature-data">
				<strong><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_INSHORT') ?></strong>
				<?php if(isset($resource->Area) && $resource->Area>0  ): ?><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_AREA') ?>: <?php echo $resource->Area ?> m&sup2; <br /><?php endif ?>
				<?php if ($resource->MaxCapacityPaxes>0){?>
					<br />
					<?php if ($resource->MinCapacityPaxes<$resource->MaxCapacityPaxes){?>
						<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_MINPAXES') ?>: <?php echo $resource->MinCapacityPaxes ?><br />
					<?php } ?>
					<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_MAXPAXES') ?>: <?php echo $resource->MaxCapacityPaxes ?><br />
				<?php } ?>
			</div>
					<!-- AddToAny BEGIN -->
					<a class="bfi-btn bfi-alternative2 bfi-pull-right a2a_dd"  href="http://www.addtoany.com/share_save" ><i class="fa fa-share-alt"></i> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_SHARE') ?></a>
					<script async src="https://static.addtoany.com/menu/page.js"></script>
					<!-- AddToAny END -->
		</div>	
	</div>	
	<table class="bfi-table bfi-table-striped bfi-resourcetablefeature ">
		<tr>
			<td class="bfi-col-md-3"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_CONTRACT') ?>:</td>
			<td class="bfi-col-md-3"><?php echo  $contractType?></td>
			<td class="bfi-col-md-3"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_PRICE') ?></td>
			<td class="bfi-col-md-3">
			<?php if ($resource->Price != null && $resource->Price > 0 && isset($resource->IsReservedPrice) && $resource->IsReservedPrice!=1 ) {?>
						 <span class="bfi_<?php echo $currencyclass ?>"> <?php echo BFCHelper::priceFormat($resource->Price,0, ',', '.')?></span>
			<?php }else{ ?>
					<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_ISRESERVEDPRICE')?>
			<?php } ?>
			</td>
		</tr>
		<tr>
			<td class="bfi-col-md-3"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_TYPE') ?>:</td>
			<td class="bfi-col-md-3"><?php echo  $typeName?></td>
			<td class="bfi-col-md-3"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_AREA') ?> (m&sup2;)</td>
			<td class="bfi-col-md-3"><?php echo $resource->Area?></td>
		</tr>
		<tr>
			<td class="bfi-col-md-3"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_LOCATION') ?>:</td>
			<td class="bfi-col-md-3"><?php echo  $location?></td>
			<td class="bfi-col-md-3"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_NROOMS') ?>:</td>
			<td class="bfi-col-md-3"><?php echo $resource->Rooms?></td>
		</tr>
		<tr>
			<td class="bfi-col-md-3"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_ZONE') ?>:</td>
			<td class="bfi-col-md-3"><?php echo  $zone?></td>
			<td class="bfi-col-md-3"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_DATEUPDATE') ?>:</td>
			<td class="bfi-col-md-3"><?php echo $dateUpdate?></td>
		</tr>
		<tr>
			<td class="bfi-col-md-3"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_FLOOR') ?>:</td>
			<td class="bfi-col-md-3">
				<?php echo (($resource->Floor >0 )? $resource->Floor ."&#176;" : JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_UNITFLOOR'.$resource->Floor) ) ?>
			</td>
			<td class="bfi-col-md-3"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_STATUS') ?>:</td>
			<td class="bfi-col-md-3"><?php 
				echo ( 
					( $resource->IsNewBuilding ) ? JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_ISNEWBUILDING'): 
						(empty($resource->Status)? JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_NOSTATUS') : JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_STATUS'.$resource->Status))
					);
			?></td>
		</tr>
		<tr>
			<td class="bfi-col-md-3"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_BATHS') ?>:</td>
			<td class="bfi-col-md-3">
				<?php echo (($resource->Baths >-1 )? $resource->Baths : JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_NOVALUE') ) ?>
			</td>
			<td class="bfi-col-md-3">&nbsp;</td>
			<td class="bfi-col-md-3">&nbsp;</td>
		</tr>
		<tr>
			<td class="bfi-col-md-3"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_CENTRALIZEDHEATING') ?>:</td>
			<td class="bfi-col-md-3">
				<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_CENTRALIZEDHEATING'.$resource->CentralizedHeating) ?>
			</td>
			<td class="bfi-col-md-3"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_GARAGES') ?>:</td>
			<td class="bfi-col-md-3"><?php 
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
		<?php if((isset($resource->EnergyClass) && $resource->EnergyClass>0 ) || (isset($resource->EpiValue) && $resource->EpiValue>0 ) ){ ?>
		<tr>
			<?php if(isset($resource->EnergyClass) && $resource->EnergyClass>0){ ?>
			<td class="bfi-col-md-3"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_ENERGYCLASS') ?></td>
			<td class="bfi-col-md-3" <?php if(!isset($resource->EpiValue)) {echo "colspan=\"3\"";}?>>
				<div class="bfi-energyClass bfi-energyClass<?php echo $resource->EnergyClass?>">
				<?php 
					switch ($resource->EnergyClass) {
						case 0: echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_ENERGYCLASS_NOTSET') ; break;
						case 1: echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_ENERGYCLASS_NONDESCRIPT') ; break;
						case 2: echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_ENERGYCLASS_FREEPROPERTY') ; break;
						case 3: echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_ENERGYCLASS_UNDEREVALUATION') ; break;
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
			<?php } ?>
			<?php if(isset($resource->EpiValue) && $resource->EpiValue>0){ ?>
			<td class="bfi-col-md-3"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_EPIVALUE') ?>:</td>
			<td class="bfi-col-md-3" <?php if(!isset($resource->EnergyClass)) {echo "colspan=\"3\"";}?>><?php echo $resource->EpiValue?> <?php echo $resource->EpiUnit?></td>
			<?php } ?>
		</tr>
		<?php } ?>
<?php 
if(!empty($resource->Services)){
echo ("<tr>\n");
$i = 0;
foreach($resource->Services as $service) {
?>
			<td class="bfi-col-md-3"><?php echo BFCHelper::getLanguage($service->Name, $language) ?>:</td>
			<td class="bfi-col-md-3"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_YES') ?></td>
<?php
    if ($i % 2 === 1) {
        echo("</tr>\n<tr>");
    }
    $i++;
}
echo("</tr>\n");
				} ?>
	</table>

	<div class="bfi-clearboth"></div>
	<?php  include(JPATH_COMPONENT.'/views/shared/merchant_small_details.php');  ?>

<?php if (($showResourceMap)) {?>
<br /><br />
<div id="resource_map" style="width:100%;height:350px"></div>
	<script type="text/javascript">
	<!--
		var mapUnit;
		var myLatlng;

		// make map
		function handleApiReady() {
			myLatlng = new google.maps.LatLng(<?php echo $resourceLat?>, <?php echo $resourceLon?>);
			var myOptions = {
					zoom: <?php echo $startzoom ?>,
					center: myLatlng,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				}
			mapUnit = new google.maps.Map(document.getElementById("resource_map"), myOptions);
			if ('<?php echo $isMapMarkerVisible?>' == "1") {
				var marker = new google.maps.Marker({
					position: myLatlng,
					map: mapUnit,
					title: '<?php echo $resourceName?>'
				});
			}
			else {
				var circle = new google.maps.Circle({
					strokeColor: "#FF0000",
					strokeWeight: 2,
					fillOpacity: 0,
					center: myLatlng,
					radius: 300, //in metri (1000m = 1Km)
					map: mapUnit,
					title: '<?php echo $resourceName?>'
				});
			}
			redrawmap()
		}

		function openGoogleMapResource() {
			if (typeof google !== 'object' || typeof google.maps !== 'object'){
				var script = document.createElement("script");
				script.type = "text/javascript";
				script.src = "https://maps.google.com/maps/api/js?key=<?php echo $googlemapsapykey ?>&libraries=drawing,places&callback=handleApiReady";
				document.body.appendChild(script);
			}else{
				if (typeof mapUnit !== 'object'){
					handleApiReady();
				}
			}
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
		jQuery(document).ready(function(){
			openGoogleMapResource();
		});

	//-->

	</script>
<?php } ?>



<br>
<br>
<script type="text/javascript">
<!--
jQuery(function($) {
	jQuery('.bfi-menu-top li a,.bfi-map-link').click(function(e) {
		e.preventDefault();
		jQuery('html, body').animate({ scrollTop: jQuery(jQuery(this).attr("rel")).offset().top }, 2000);
	});
	jQuery('#bfi-avgreview').click(function() {
		jQuery('html, body').animate({ scrollTop: jQuery(".bfi-ratingslist").offset().top }, 2000);
	});

	jQuery('.bfi-title-name h2').click(function() {
		jQuery('html, body').animate({ scrollTop: jQuery(".bfi-merchant-simple").offset().top }, 2000);
	});

	var shortenOption = {
			moreText: "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_VIEW_READMORE')?>",
			lessText: "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_VIEW_READLESS')?>",
			showChars: '250'
	};

	jQuery(".bfi-description-data").shorten(shortenOption);

});
//-->
</script>
</div>
