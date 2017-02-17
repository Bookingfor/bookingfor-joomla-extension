<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$resource = $this->item;
//$merchant = $resource->Merchant;
$language = $this->language;

//$resourceName = BFCHelper::getLanguage($resource->Name, $this->language);
$resourceName = BFCHelper::getLanguage($resource->Name, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
//$resourceDescription = BFCHelper::getLanguage($resource->Description, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags'));
$route = JRoute::_('index.php?option=com_bookingforconnector&view=condominium&resourceId=' . $resource->CondominiumId . ':' . BFCHelper::getSlug($resourceName));


//$typeName =  BFCHelper::getLanguage($resource->CategoryName, $this->language);
//$zone = $resource->LocationZone;
//$location = $resource->LocationName;
//$contractType = ($resource->ContractType) ? JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_CONTRACTTYPE1')  : JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_CONTRACTTYPE');


//$addressData ="";
//$arrData = array();
////if ($resource->IsAddressVisible)
////{
//	if(!empty($resource->AddressData)){
//		$arrData[] = ($resource->AddressData);
//	}
//}
//if(!empty($zone)){
//	$arrData[] = ($zone);
//}
//if(!empty($location)){
//	$arrData[] = ($location);
//}
//$addressData = implode(" - ",$arrData);
//
//$resourceImageUrl = JURI::base() . "media/com_bookingfor/images/default.png";
//if ($resource->ImageUrl != '') {
//	$resourceImageUrl = BFCHelper::getImageUrl('condominiums',$resource->ImageUrl, 'condominium_map_default');		
//}elseif ($merchant->LogoUrl != ''){
//	$resourceImageUrl = BFCHelper::getImageUrl('merchant',$merchant->LogoUrl, 'condominium_map_default');		
//}

//-------------------pagina per i l redirect di tutte le risorse in vendita

$db   = JFactory::getDBO();
$uri  = 'index.php?option=com_bookingforconnector&view=condominium';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1  LIMIT 1' );
$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
//-------------------pagina per i l redirect di tutte le risorsein vendita

if ($itemId<>0)
	$uri.='&resourceId=' . $resource->CondominiumId . ':' . BFCHelper::getSlug($resourceName).'&Itemid='.$itemId;
else
	$uri.='&resourceId=' . $resource->CondominiumId . ':' . BFCHelper::getSlug($resourceName);

$route = JRoute::_($uri);
$indirizzo = "";
$cap = "";
$comune = "";
$provincia = "";

if (empty($resource->AddressData)){
	$indirizzo = $resource->Address;
	$cap = $resource->ZipCode;
	$comune = $resource->CityName;
	$provincia = $resource->RegionName;
}else{
	$addressData = $resource->AddressData;
	$indirizzo = BFCHelper::getItem($addressData, 'indirizzo');
	$cap = BFCHelper::getItem($addressData, 'cap');
	$comune =  BFCHelper::getItem($addressData, 'comune');
	$provincia = BFCHelper::getItem($addressData, 'provincia');
}
if ( empty($indirizzo)){
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
<div class="mapdetails">
<div class="com_bookingforconnector_map_resource" style="display:block;height:150px;overflow:auto; width: 500px;">
	<div class="com_bookingforconnector_resource">
		<h3 style="margin:0;" class="com_bookingforconnector_resource-name"><a class="com_bookingforconnector_resource-resource-nameAnchor" href="<?php echo $route ?>"><?php echo  $resourceName?></a> </h3>
		<div class="com_bookingforconnector_resource-address">
			<strong><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_ADDRESS') ?>:</strong> <?php echo $indirizzo ?> - <?php echo  $cap ?> - <?php echo $comune ?> (<?php echo  $provincia ?>)</div>	
		</div>	
	</div>
</div>
</div>























