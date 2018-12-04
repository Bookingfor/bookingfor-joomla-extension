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
$language = $this->language;
$resource->Price = $resource->MinPrice;

$indirizzo = isset($resource->Address)?$resource->Address:"";
$cap = isset($resource->ZipCode)?$resource->ZipCode:""; 
$comune = isset($resource->CityName)?$resource->CityName:"";
$provincia = isset($resource->RegionName)?$resource->RegionName:"";
$stato = isset($resource->StateName)?$resource->StateName:"";

//$resourceName = BFCHelper::getLanguage($resource->Name, $this->language);
$resourceName = BFCHelper::getLanguage($resource->Name, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
$resourceDescription = BFCHelper::getLanguage($resource->Description, $this->language, null, array('ln2br'=>'ln2br', 'bbcode'=>'bbcode', 'striptags'=>'striptags'));
$route = JRoute::_('index.php?option=com_bookingforconnector&view=onsellunit&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName));


$typeName =  BFCHelper::getLanguage($resource->CategoryName, $this->language);
$zone = $resource->LocationZone;
$location = $resource->LocationName;
$contractType = ($resource->ContractType) ? JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_CONTRACTTYPE1')  : JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_CONTRACTTYPE');

	$img = JURI::root() . "images/default.png";
	$imgError = JURI::root() . "images/default.png";

	if ($resource->ImageUrl != ''){
		$img = BFCHelper::getImageUrlResized('onsellunits',$resource->ImageUrl , 'onsellunit_map_default');
		$imgError = BFCHelper::getImageUrl('onsellunits',$resource->ImageUrl , 'onsellunit_map_default');
	}elseif ($merchant->LogoUrl != ''){
		$img = BFCHelper::getImageUrlResized('merchant',$merchant->LogoUrl, 'onsellunit_map_default');
		$imgError = BFCHelper::getImageUrl('merchant',$merchant->LogoUrl, 'onsellunit_map_default');

	}


////-------------------pagina per i l redirect di tutte le risorse in vendita
//
//$db   = JFactory::getDBO();
//$uri  = 'index.php?option=com_bookingforconnector&view=onsellunit';
//$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
//$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
////-------------------pagina per i l redirect di tutte le risorsein vendita
//
//
////-------------------pagina per il redirect di tutti i merchant
//
//$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
//$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
//$itemIdMerchant = ($db->getErrorNum())? 0 : intval($db->loadResult());
////-------------------pagina per il redirect di tutti i merchant
//
////$route = JRoute::_('index.php?option=com_bookingforconnector&view=onsellunit&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName));
//
//if ($itemId<>0)
//	$uri.='&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName).'&Itemid='.$itemId;
//else
//	$uri.='&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName);
//
//
////$routeMerchant = JRoute::_('index.php?option=com_bookingforconnector&view=merchantdetails&merchantId=' . $resource->MerchantId . ':' . BFCHelper::getSlug($merchant->Name));
//if ($itemIdMerchant<>0)
//	$uriMerchant.='&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name).'&Itemid='.$itemIdMerchant;
//else
//	$uriMerchant.='&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name);

$uri = COM_BOOKINGFORCONNECTOR_URIONSELLUNIT .'&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName);
$uriMerchant  = COM_BOOKINGFORCONNECTOR_URIMERCHANTDETAILS .'&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name);

$route = JRoute::_($uri);
$routeMerchant = JRoute::_($uriMerchant);
$isMerchantAnonymous = BFCHelper::isMerchantAnonymous($merchant->MerchantTypeId);
?>

<div class="bfi-mapdetails">
	<div class="bfi-item-title">
		<a href="<?php echo $route ?>" target="_blank"><?php echo  $resourceName?></a>
	</div>
	<div class="bfi-item-address"><span class="street-address"><?php echo $indirizzo ?></span>, <span class="postal-code "><?php echo  $cap ?></span> <span class="locality"><?php echo $comune ?></span>, <span class="region"><?php echo  $provincia ?></span></div>
			<?php if ($resource->Price != null && $resource->Price > 0 && isset($resource->IsReservedPrice) && $resource->IsReservedPrice!=1 ) :?>
						&euro; <?php echo number_format($resource->Price,0, ',', '.')?>
			<?php else: ?>
					<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_ISRESERVEDPRICE')?>
			<?php endif; ?>	
</div>