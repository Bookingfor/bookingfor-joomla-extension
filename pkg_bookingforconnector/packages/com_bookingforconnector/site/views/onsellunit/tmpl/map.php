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
//$resource->ResourceId = $resource->OnSellUnitId;
//$resource = $this->item;
//$merchant = $resource->Merchant;
$resource->Price = $resource->MinPrice;

$language = $this->language;

//$resourceName = BFCHelper::getLanguage($resource->Name, $this->language);
$resourceName = BFCHelper::getLanguage($resource->Name, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
$resourceDescription = BFCHelper::getLanguage($resource->Description, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags'));
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


//-------------------pagina per i l redirect di tutte le risorse in vendita

$db   = JFactory::getDBO();
$uri  = 'index.php?option=com_bookingforconnector&view=onsellunit';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
//-------------------pagina per i l redirect di tutte le risorsein vendita


//-------------------pagina per il redirect di tutti i merchant

$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
$itemIdMerchant = ($db->getErrorNum())? 0 : intval($db->loadResult());
//-------------------pagina per il redirect di tutti i merchant

//$route = JRoute::_('index.php?option=com_bookingforconnector&view=onsellunit&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName));

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
$isMerchantAnonymous = BFCHelper::isMerchantAnonymous($merchant->MerchantTypeId);
?>
<div class="com_bookingforconnector_map_resource" style="display:block;height:150px;overflow:auto; width: 300px;">
	<div><a href="<?php echo $route?>"><b><?php echo  $resourceName?></b></a></div><br />
<!-- 	<img class="com_bookingforconnector_resource-img" src="<?php echo $img?>" onerror="this.onerror=null;this.src='<?php echo $imgError?>'"  style="margin-bottom:10px;margin-right:10px;"/> -->
	<div style="margin-bottom:5px;line-height:normal;">
				<?php if ($resource->Price != null && $resource->Price > 0 && isset($resource->IsReservedPrice) && $resource->IsReservedPrice!=1 ) :?>
							&euro; <?php echo number_format($resource->Price,0, ',', '.')?>
				<?php else: ?>
						<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_ISRESERVEDPRICE')?>
				<?php endif; ?>	
			<br />
			<?php if (!$isMerchantAnonymous) :?>
			<a class="com_bookingforconnector_merchantdetails-name" href="<?php echo $routeMerchant?>"> <?php echo $merchant->Name?></a>
			<?php endif ?>
			<br />
			<br />
			<a href="<?php echo $route?>"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_OFFER_DETAILS')?></a>

	</div>
</div>
























