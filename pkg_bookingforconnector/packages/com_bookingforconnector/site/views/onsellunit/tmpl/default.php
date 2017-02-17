<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$language = $this->language;
$resource = $this->item;
$merchant = $resource->Merchant;
$resource->Price = $resource->MinPrice;

$config = $this->config;
$isportal = $config->get('isportal', 1);

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
	$titleHead = sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_HEAD_TITLE'), $resourceName, $zone, $location, $typeName, $contractType, $location);
	$keywordsHead = sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_HEAD_KEYWORDS'), $typeName, $location, $typeName, $contractType, $location, $contractType, $location);
	$descriptionHead = sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_HEAD_DESCRIPTION'), $resourceName, $typeName, $contractType, $location);
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


$dateUpdate =  BFCHelper::parseJsonDate($resource->AddedOn); 
if($resource->UpdatedOn!=''){
	$dateUpdate =  BFCHelper::parseJsonDate($resource->UpdatedOn);
}

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

//$addressData ="";
//$arrData = array();
//if ($resource->IsAddressVisible)
//{
//	if(!empty($resource->Address)){
//		$arrData[] = $resource->Address;
//	}
//	if(!empty($resource->CityName)){
//		$arrData[] = $resource->CityName;
//	}
//	if(!empty($resource->CityName)){
//		$arrData[] = $resource->CityName;
//	}
//	if(!empty($resource->RegionName)){
//		$arrData[] = $resource->RegionName;
//	}
//}
//if(!empty($zone)){
//	$arrData[] = ($zone);
//}
//if(!empty($location)){
//	$arrData[] = ($location);
//}
//$addressData = implode(" - ",$arrData);

$indirizzo = $resource->Address;
$cap = $resource->ZipCode;
$comune = $resource->CityName;
$provincia = $resource->RegionName;

/**** for search similar *****/
$document 	= JFactory::getDocument();
$db   = JFactory::getDBO();
$lang = JFactory::getLanguage()->getTag();

$uri  = 'index.php?option=com_bookingforconnector&view=searchonsell';

$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri .'%' ) .' AND (language='. $db->Quote($lang) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );

$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
if ($itemId<>0)
	$formAction = JRoute::_('index.php?Itemid='.$itemId );
else
	$formAction = JRoute::_($uri);

$deltapricePerCent = 20;
$deltaprice = 1;
if($resource->Price>0){
	$deltaprice = $resource->Price * $deltapricePerCent / 100;
}
$contractTypeId = $resource->ContractType;
$categoryId = $resource->CategoryId;
$zoneId = $resource->ZoneId;

$pricemax = round(($resource->Price + $deltaprice), 0, PHP_ROUND_HALF_UP); 
$pricemin = round(($resource->Price - $deltaprice), 0, PHP_ROUND_HALF_DOWN); 

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

//-------------------pagina per il redirect di tutte le risorsein vendita

$uriFav = 'index.php?option=com_bookingforconnector&view=onsellunits&layout=favorites';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriFav ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
$itemIdFav = ($db->getErrorNum())? 0 : intval($db->loadResult());
//-------------------pagina per i l redirect di tutte le risorsein vendita

if ($itemIdFav<>0)
	$routeFav = JRoute::_($uriFav.'&Itemid='.$itemIdFav );
else
	$routeFav = JRoute::_($uriFav);

/*-----------------------------------*/
//-------------------pagina per il redirect di tutti i merchant

$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
$itemIdMerchant = ($db->getErrorNum())? 0 : intval($db->loadResult());
//-------------------pagina per il redirect di tutti i merchant

if ($itemIdMerchant<>0)
	$uriMerchant.='&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name).'&Itemid='.$itemIdMerchant;
else
	$uriMerchant.='&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name);

$routeMerchant = JRoute::_($uriMerchant);


$merchantLogo = JURI::base() . "images/default.png";
$merchantLogoError = JURI::base() . "images/default.png";
if ($merchant->LogoUrl != '') {
	$merchantLogo = BFCHelper::getImageUrlResized('merchant',$merchant->LogoUrl, 'merchant_logo_small_top');
	$merchantLogoError = BFCHelper::getImageUrl('merchant',$merchant->LogoUrl, 'merchant_logo_small_top');
}
$addressDataMerchant = $merchant->AddressData;

$PlanimetryData = $this->item->PlanimetryData;
$showResourcePlanimetria= (!empty($PlanimetryData));
$VideoData = $this->item->VideoData;
$showResourceVideo= (!empty($VideoData));


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

<div class="com_bookingforconnector_resource com_bookingforconnector_resource-mt<?php echo  $merchant->MerchantTypeId?> com_bookingforconnector_resource-t<?php echo  $resource->CategoryId?>">
   <h2 class="com_bookingforconnector_resource-name"><?php echo  $resourceName?> </h2>
	<div class="com_bookingforconnector_resource-address">
		<span class="street-address"><?php echo $indirizzo ?></span>, <span class="postal-code "><?php echo  $cap ?></span> <span class="locality"><?php echo $comune ?></span> <span class="region">(<?php echo  $provincia ?>)</span></strong>
		<?php if(COM_BOOKINGFORCONNECTOR_ENABLEFAVORITES):?>
			 - <a href="javascript:addCustomURlfromfavTranfert('.com_bookingforconnector_resource-name','<?php echo JURI::current() ?>','<?php echo  $resourceName ?>')" class="com_bookingforconnector_resource_addfavorites"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_RESOURCE_ADDFAVORITES') ?></a>
		<?php endif ?>
	</div>
<!-- Navigation -->	
	<ul class="nav nav-pills nav-justified bfcmenu ">
		<li role="presentation" class="active"><a rel=".resourcecontainer-gallery" data-toggle="tab"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_ANCHORS_PHOTO') ?></a></li>
		<?php if (!empty($resourceDescription)):?><li role="presentation" ><a rel=".com_bookingforconnector_resource-description" data-toggle="tab"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_ANCHORS_DESCRIPTION') ?></a></li><?php endif; ?>
		<?php if (($showResourceMap)) :?><li role="presentation" ><a rel="#resource_map" data-toggle="tab"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_ANCHORS_MAP') ?></a></li><?php endif; ?>
	</ul>

	<div class="com_bookingforconnector_onsell_feature" style="display:none;">
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
	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>"  style="display:none;">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 com_bookingforconnector_resource_options">
				 <code class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3">
					<a class="com_bookingforconnector_searchsimilar"  href="javascript:void(0);" onclick="javascript: searchsimilar()" ><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_FINDSIMILAR') ?></a>
				 </code>&nbsp;
				 <code class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3">
					<!-- AddToAny BEGIN -->
					<a class="com_bookingforconnector_share a2a_dd"  href="http://www.addtoany.com/share_save" ><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_SHARE') ?></a>
					<script type="text/javascript" src="//static.addtoany.com/menu/page.js"></script>
					<!-- AddToAny END -->
				 </code>&nbsp;
				 <code class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3">
					<a class="com_bookingforconnector_print"  href="javascript:void(0);" onclick="javascript:window.print()"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_PRINT') ?></a>
				 </code>&nbsp;
				<?php if(COM_BOOKINGFORCONNECTOR_ENABLEFAVORITES):?>
				 <code class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3">
					<?php if(BFCHelper::IsInFavourites($resource->ResourceId)):?>
						<a class="com_bookingforconnector_fav com_bookingforconnector_favadded " href="<?php echo $routeFav ?>" ><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_FAVORITES_ADDED')?></a>
					<?php else:?>
						<a class="com_bookingforconnector_fav " href="javascript:addCustomURlfromfavTranfert('#favAnchor<?php echo $resource->ResourceId?>',<?php echo $resource->ResourceId?>,'<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_FAVORITES_ADDED')?>')" id="favAnchor<?php echo $resource->ResourceId?>" ><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_FAVORITES_ADD')?></a>
					<?php endif ?>
				 </code>
				<?php endif ?>
		</div>
	</div>
	<div class="resourcecontainer-gallery">
	  <?php echo  $this->loadTemplate('gallery'); ?>
	</div>
	<script type="text/javascript">
	<!--
	jQuery('.bfcmenu li a').click(function(e) {
		e.preventDefault();
		jQuery('html, body').animate({ scrollTop: jQuery(jQuery(this).attr("rel")).offset().top }, 2000);
	});
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
		if( jQuery("#resourcegallery")){
			try
			{
				var slider = jQuery("#resourcegallery").data('royalSlider');
				slider.updateSliderSize(); // updates size of slider. Use after you resize slider with js. 
			}
			catch (err)
			{
			}
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

<!-- Dettagli --><br />	
	<table class="table table-striped resourcetablefeature ">
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
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_AREA') ?> (m&sup2;)</td>
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
						(empty($resource->Status)? JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_NOSTATUS') : $resource->Status)
					);
			?></td>
		</tr>
		<tr>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_BATHS') ?>:</td>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3">
				<?php echo (($resource->Baths >-1 )? $resource->Baths : JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_NOVALUE') ) ?>
			</td>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3">&nbsp;</td>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3">&nbsp;</td>
		</tr>
		<tr>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_CENTRALIZEDHEATING') ?>:</td>
			<td class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3">
				<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_CENTRALIZEDHEATING'.$resource->CentralizedHeating) ?>
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
						case 0: echo "Non impostato"; break;
						case 1: echo "Non classificabile"; break;
						case 2: echo "Immobile esente"; break;
						case 3: echo "In fase di valutazione"; break;
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
if(!empty($resource->Services)){
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
				} ?>
	</table>

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
	<div class="clear"></div>

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
					zoom: <?php echo $startzoom ?>,
<?php if ($isMapMarkerVisible) :?>
					maxZoom: 17,
<?php else : ?>
					maxZoom: 14,
<?php endif; ?>
					minZoom:7,
					center: myLatlng,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				}
			mapUnit = new google.maps.Map(document.getElementById("resource_map"), myOptions);
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
				radius: 300, //in metri (1000m = 1Km)
				map: mapUnit
			});
<?php endif; ?>
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
<?php else: ?>
<script type="text/javascript">
<!--
		function openGoogleMapResource() {
		}
	
//-->
</script>

<?php endif; ?>

</div>
<div id="dialogiframe" style="display:none;">
    <iframe id="iframeToload" src="" height="100%" width="100%" frameborder="0" marginheight="0" marginwidth="0"></iframe>
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
