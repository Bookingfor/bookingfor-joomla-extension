<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$merchant = $this->item;
$resource = $merchant->currentResource;
$unit = $resource;
$language = $this->language;

//$resourceName = BFCHelper::getLanguage($resource->Name, $this->language);
$resourceName = BFCHelper::getLanguage($resource->Name, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 

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

//-------------------pagina per i l redirect di tutte le risorse 

$db   = JFactory::getDBO();
$uri  = 'index.php?option=com_bookingforconnector&view=resource';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1  LIMIT 1' );
//$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
$itemId = intval($db->loadResult());
//------------------- pagina per i l redirect di tutte le risorse 

//$route = JRoute::_('index.php?option=com_bookingforconnector&view=resource&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName));
if ($itemId<>0)
	$route = JRoute::_($uri.'&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName).'&Itemid='.$itemId );
else
	$route = JRoute::_($uri.'&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName));


		$img = JURI::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";
		$imgError = JURI::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";

		if ($resource->ImageUrl != ''){
			$img = BFCHelper::getImageUrlResized('resources',$resource->ImageUrl , 'medium');
			$imgError = BFCHelper::getImageUrl('resources',$resource->ImageUrl , 'medium');
		}
		
	   $imageData = $resource->ImageData;
	   $images = array();
	   $merchantLogoPath = BFCHelper::getImageUrlResized('merchant',$merchant->LogoUrl, 'logomedium');
	   if($resource->ImageData != null) {
	   foreach(explode(',', $imageData) as $image) {
	     $images[] = BFCHelper::getImageUrlResized('resources',$image, 'medium');
	   }
	   }
	   else {
        $images[] = $img;	   
	   }
?>
	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 com_bookingforconnector-item-col" style="padding: 10px !important;" >
        <div class="com_bookingforconnector-offers-item com_bookingforconnector-item <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
          <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4">
            <div class="com_bookingforconnector-offers-item-carousel com_bookingforconnector-item-carousel">
              <?php if (count ($images)>0): ?>					
	             <div id="com_bookingforconnector-offers-item-carousel<?php echo $resource->ResourceId; ?>" class="carousel" data-ride="carousel" data-interval="false">
                  <div class="carousel-inner" role="listbox">
                    <?php foreach($images as $id => $image) : ?>
                    <?php
                      $active_class = '';
                      if($id == 0) { $active_class = ' active'; }
                    ?>
                    <div class="item<?php echo $active_class; ?>"><img src="<?php echo $image; ?>"></div>
                    <?php endforeach;  ?>
                  </div>
                  <a class="left carousel-control" href="#com_bookingforconnector-offers-item-carousel<?php echo $resource->ResourceId; ?>" role="button" data-slide="prev">
                    <i class="fa fa-chevron-left"></i>
                  </a>
                  <a class="right carousel-control" href="#com_bookingforconnector-offers-item-carousel<?php echo $resource->ResourceId; ?>" role="button" data-slide="next">
                    <i class="fa fa-chevron-right"></i>
                  </a>
                </div> 					
              <?php endif; ?>
            </div>
          </div>
          <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>8">
          <div class="com_bookingforconnector-offers-item-primary com_bookingforconnector-item-primary">
            <span class="showcaseresource hidden" id="showcaseresource<?php echo $resource->ResourceId?>"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_SHOWCASERESOURCE') ?></span>
		      <span class="topresource hidden" id="topresource<?php echo $resource->ResourceId?>"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_TOPRESOURCE') ?></span>
		      <span class="newbuildingresource hidden" id="newbuildingresource<?php echo $resource->ResourceId?>"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_SEARCHONSELL_VIEW_NEWBUILDINGRESOURCE') ?></span>
            <div class="com_bookingforconnector-offers-item-primary-name com_bookingforconnector-item-primary-name">
              <div class="com_bookingforconnector-offers-item-primary-nameAnchor"><a class="com_bookingforconnector-item-primary-nameAnchor" href="<?php echo $route ?>"><?php echo  $resourceName ?></a></div>
            </div>
            <div class="com_bookingforconnector-onsell-address com_bookingforconnector-item-primary-address">         
              <span id="address<?php echo $resource->ResourceId?>"><span class="street-address"><?php echo $indirizzo ?></span>, <span class="postal-code "><?php echo  $cap ?></span> <span class="locality"><?php echo $comune ?></span> <span class="region">(<?php echo  $provincia ?>)</span></strong></span>
            </div>
            <div class="com_bookingforconnector-onsell-description com_bookingforconnector-item-primary-description">
              <p class="com_bookingforconnector_merchantdetails-resource-desc com_bookingforconnector_loading" id="descr<?php echo $resource->ResourceId?>"><?php echo  BFCHelper::getLanguage($resource->Description, $this->language, null, array('nomore1br'=>'nomore1br','ln2br'=>'ln2br',    'striptags'=>'striptags')) ?></p>                 
            </div>
          </div>
      </div>   
	<div class="com_bookingforconnector-search-resource com_bookingforconnector-item-secondary <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>"  style="padding-top: 10px !important;padding-bottom: 10px !important;">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>9">				
			<div class="com_bookingforconnector-search-resource-paxes com_bookingforconnector-item-secondary-paxes">
				<i class="fa fa-user"></i>
				<?php if ($resource->MinCapacityPaxes == $resource->MaxCapacityPaxes):?>
					<?php echo  $resource->MaxCapacityPaxes ?>
				<?php else: ?>
					<?php echo  $resource->MinCapacityPaxes ?>-<?php echo  $resource->MaxCapacityPaxes ?>
				<?php endif; ?>
			</div>
			<?php if (isset($resource->Area) && $resource->Area>0):?>
			<div class="com_bookingforconnector_merchantdetails-resource-area minheight34 ">
					<?php echo  $resource->Area ?> m<sup>2</sup>
			</div>
			<?php else: ?>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3  minheight34 "> </div>
			<?php endif; ?>
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3">
				<a href="<?php echo $route ?>" class=" com_bookingforconnector-item-secondary-more"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_OFFER_DETAILS')?></a>
		</div>
	</div>
    </div>
  </div>
