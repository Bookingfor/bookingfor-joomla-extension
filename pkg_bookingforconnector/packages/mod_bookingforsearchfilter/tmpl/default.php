<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
/*

CSS overrides star by merchantCategoryId

.com_bookingforconnector_merchantdetails-resourcerating5.com_bookingforconnector_merchantdetails-merchantCategoryId856 .com_bookingforconnector_merchantdetails-ratingText{
display:none;
}
.com_bookingforconnector_merchantdetails-resourcerating5.com_bookingforconnector_merchantdetails-merchantCategoryId856:before {content: "5 stars ";}{


*/

$pathbase = JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;

require_once $pathbase . '/helpers/htmlHelper.php';
$db   = JFactory::getDBO();
$language = $language;
$uri  = 'index.php?option=com_bookingforconnector&view=search';

$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri .'%' ) .' AND language='. $db->Quote($language) .' AND published = 1 LIMIT 1' );

$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());

if ($itemId<>0)
	$formAction = JRoute::_('index.php?Itemid='.$itemId.'' );
else
	$formAction = JRoute::_($uri);

// setting per slider prezzi
$priceScaleMin = 0;
$priceScaleMax = 300;
$priceScaleStep = 50;

//zone...
$locationZones = BFCHelper::getLocationZones();
$masterTypologies = BFCHelper::getMasterTypologies();

// elenco ulteriore filtri possibili
$merchantGroups = BFCHelper::getTagsForSearch($language,"1,4");

$bookingTypes = array();
//$bookingTypes[0] = JTEXT::_('MOD_BOOKINGFORSEARCHFILTER_BOOKINGTYPES_REQUEST');
$bookingTypes[1] = JTEXT::_('MOD_BOOKINGFORSEARCHFILTER_BOOKINGTYPES_BOOK');

$offers = array();
$offers[1] = JTEXT::_('MOD_BOOKINGFORSEARCHFILTER_OFFERS_OFFER');

$pars = BFCHelper::getSearchParamsSession();

// se  passo questi dati è una nuova ricerca
$masterTypeId = $pars['masterTypeId'];
$merchantCategoryId = $pars['merchantCategoryId'];


// TODO: SUSPENDED
//if (!empty($merchantCategoryId)) {
//	
////	$singeMerchantCategory = BFCHelper::getMerchantCategory($merchantCategoryId);
////	$services  = $singeMerchantCategory->Services;
//	$services  =  BFCHelper::getServicesByMerchantsCategoryId($merchantCategoryId,$language);
//}else{
//	$services  =  BFCHelper::getServicesForSearch($language);
//
//}
	$services  =  BFCHelper::getServicesForSearch($language);


$duration = 1;// $pars['duration'];
if (empty($duration)) {
	$duration =1;
}


// è il codice per l'ordinamento a random....
$searchid = BFCHelper::getVar('searchid');
//$searchid =  $this->params['searchid'];
$isMerchantResults = $pars['merchantResults'];

$filtersEnabledBookingTypes= array();


$filtersEnabledRooms = array();
$filtersEnabledRateplanName = array();
$filtersEnabledServices = array();
$filtersEnabledServicesMerchants = array();
$filtersEnabledMerchantGroups = array();
$filtersEnabledLocationZones = array();
$filtersEnabledMasterTypologies = array();
$filtersEnabledStars = array();

$filtersEnabled = BFCHelper::getEnabledFilterSearchParamsSession();
//echo '<PRE>';print_r($filtersEnabled);die();
if (isset($filtersEnabled)) {
	if (!empty($filtersEnabled['stars'])) {
//		$filtersEnabledStars = explode(",", $filtersEnabled['stars']);
		$filtersEnabledStars = $filtersEnabled['stars'];
	}
	if (!empty($filtersEnabled['locationzones'])) {
//		$filtersEnabledLocationZones = explode(",", $filtersEnabled['locationzones']);
		$filtersEnabledLocationZones = $filtersEnabled['locationzones'];
	}
	if (!empty($filtersEnabled['mastertypologies'])) {
//		$filtersEnabledMasterTypologies = explode(",", $filtersEnabled['mastertypologies']);
		$filtersEnabledMasterTypologies = $filtersEnabled['mastertypologies'];
	}
	if (!empty($filtersEnabled['merchantgroups'])) {
//		$filtersEnabledMerchantGroups = explode(",", $filtersEnabled['merchantgroups']);
		$filtersEnabledMerchantGroups = $filtersEnabled['merchantgroups'];
	}
	if (!empty($filtersEnabled['services'])) {
//		$filtersEnabledServices = explode(",", $filtersEnabled['services']);
		$filtersEnabledServices = $filtersEnabled['services'];
	}
	if (!empty($filtersEnabled['servicesmerchants'])) {
//		$filtersEnabledServices = explode(",", $filtersEnabled['services']);
		$filtersEnabledServicesMerchants = $filtersEnabled['servicesmerchants'];
	}
	
	if (!empty($filtersEnabled['bookingtypes'])) {
//		$filtersEnabledBookingTypes = explode(",", $filtersEnabled['bookingtypes']);
		$filtersEnabledBookingTypes = $filtersEnabled['bookingtypes'];
	}
	if (!empty($filtersEnabled['pricemin'])) {
		$priceScaleMin = $filtersEnabled['pricemin'];
		if($priceScaleMin<0){
			$priceScaleMin =0;
		}
	}
	if (!empty($filtersEnabled['pricemax'])) {
		$priceScaleMax = $filtersEnabled['pricemax'];
	}
	
	if (!empty($filtersEnabled['offers'])) {
//		$filtersEnableOffers = explode(",", $filtersEnabled['offers']);
		$filtersEnableOffers = $filtersEnabled['offers'];
	}
	if (!empty($filtersEnabled['rooms'])) {
//		$filtersEnabledRooms = explode(",", $filtersEnabled['rooms']);
		$filtersEnabledRooms = $filtersEnabled['rooms'];
	}
	if (!empty($filtersEnabled['rateplanname'])) {
//		$filtersEnabledRateplanName = explode(",", $filtersEnabled['rateplanname']);
		$filtersEnabledRateplanName = $filtersEnabled['rateplanname'];
	}

}

$filtersPriceMin = $priceScaleMin;
$filtersPriceMax = $priceScaleMax;
if($filtersPriceMin ==$filtersPriceMax){
	$filtersPriceMax +=1;
}
if (($filtersPriceMax-$filtersPriceMin)<$priceScaleStep){
	$priceScaleStep = $filtersPriceMax-$filtersPriceMin;
}
if ($priceScaleStep<$priceScaleMax){
	$priceScaleStep= 1;
}


$filters = BFCHelper::getFilterSearchParamsSession();

$filtersStarsValue = "";
$filtersLocationZonesValue = "";
$filtersMerchantGroupsValue = "";
$filtersServicesValue = "";
$filtersServicesMerchantsValue = "";
$filtersMasterTypologiesValue = "";
$filtersBookingTypesValue = "";
$filtersOffersValue = "";

$filtersRateplanNameValue = "";
$filtersRoomsValue = "";


if (isset($filters)) {
	if (!empty($filters['stars'])) {
		$filtersStars = explode(",", $filters['stars']);
		$filtersStarsValue = $filters['stars'];
	}
	if (!empty($filters['locationzones'])) {
		$filtersLocationZones = explode(",", $filters['locationzones']);
		$filtersLocationZonesValue = $filters['locationzones'];
	}
	if (!empty($filters['merchantgroups'])) {
		$filtersMerchantGroups = explode(",", $filters['merchantgroups']);
		$filtersMerchantGroupsValue = $filters['merchantgroups'];
	}
	if (!empty($filters['services'])) {
		$filtersServices = explode(",", $filters['services']);
		$filtersServicesValue = $filters['services'];
	}
	if (!empty($filters['servicesmerchants'])) {
		$filtersServicesMerchants = explode(",", $filters['servicesmerchants']);
		$filtersServicesMerchantsValue = $filters['servicesmerchants'];
	}
	if (!empty($filters['mastertypologies'])) {
		$filtersMasterTypologies = explode(",", $filters['mastertypologies']);
		$filtersMasterTypologiesValue = $filters['mastertypologies'];
	}
	if (!empty($filters['pricemin'])) {
		$filtersPriceMin = 	$filters['pricemin'];// / $duration;
	}
	if (!empty($filters['pricemax'])) {
		$filtersPriceMax = 	$filters['pricemax'];// / $duration;
	}
	if (!empty($filters['bookingtypes'])) {
		$filtersBookingTypes = explode(",", $filters['bookingtypes']);
		$filtersBookingTypesValue = $filters['bookingtypes'];
	}
	if (!empty($filters['offers'])) {
		$filtersOffers = explode(",", $filters['offers']);
		$filtersOffersValue = $filters['offers'];
	}

	if (!empty($filters['rooms'])) {
		$filtersRooms = explode(",", $filters['rooms']);
		$filtersRoomsValue = $filters['rooms'];
	}
	if (!empty($filters['rateplanname'])) {
		$filtersRateplanName = explode(",", $filters['rateplanname']);
		$filtersRateplanNameValue = $filters['rateplanname'];
	}
}
//prezzi

//le stelle..... recupero eventualmente la prima checcata...
//				for ($star = 1; $star <= 5; ++$star) {
//					$checked = '';
//					if ( isset($filters) && !empty($filters['stars']) && $filters['stars']==$star) {
//						$checked = ' checked="checked"';
//					}
//                }
//			echo "<pre>filtersParam:";
//			echo print_r($pars);
//			echo "</pre>";
//			echo "<pre>filters:";
//			echo print_r($filters);
//			echo "</pre>";
//			echo "<pre>locationZones:";
//			echo print_r($locationZones);
//			echo "</pre>";
//			echo "<pre>filtersEnabled:";
//			echo print_r($filtersEnabled);
//			echo "</pre>";
//// ---------------------------------------------
//			echo "<pre>services:";
//			echo print_r($services);
//			echo "</pre>";
//			echo "<pre>filtersEnabledServices:";
//			echo print_r($filtersEnabledServices);
//			echo "</pre>";
//
//			echo "<pre>filtersBookingTypes:";
//			echo print_r($filtersBookingTypes);
//			echo "</pre>";
//			echo "<pre>filtersEnabledBookingTypes:";
//			echo print_r($filtersEnabledBookingTypes);
//			echo "</pre>";

//			echo "<pre>filtersRooms:";
//			echo print_r($filtersRooms);
//			echo "</pre>";
//			echo "<pre>filtersEnabledRooms:";
//			echo print_r($filtersEnabledRooms);
//			echo "</pre>";


?>
<div class="mod_bookingforsearchfilter<?php echo $moduleclass_sfx ?>">
<h3><?php echo JText::_('MOD_BOOKINGFORSEARCHFILTER_TITLE') ?></h3>

<form action="<?php echo $formAction; ?>" method="post" id="searchformfilter" name="searchformfilter" >
<!-- hidden file -->
	<input type="hidden" value="<?php echo $searchid; ?>" name="searchid" />
	<input type="hidden" value="0" name="limitstart" />
	<input type="hidden" name="filter_order" id="filter_order_filter" value="" />
	<input type="hidden" name="filter_order_Dir" id="filter_order_Dir_filter" value="" />
	<input type="hidden" name="format" value="raw" />
	<input type="hidden" name="tmpl" value="component" />
<div id="filtertoggle">
	<ul>

		<?php if (isset($filtersEnableOffers) &&  is_array($filtersEnableOffers)) : ?>
			<li><?php echo JText::_('MOD_BOOKINGFORSEARCHFILTER_OFFERS') ?></li>
			<div class="mod_bookingforsearchfilter_offers divtoggle">
				<?php foreach ($offers as $offer => $offerText):?>
					<?php if (isset($filtersEnableOffers) &&  is_array($filtersEnableOffers) && array_key_exists($offer,$filtersEnableOffers)) :?>
						<?php $checked = '';
							if (isset($filtersOffers) &&  is_array($filtersOffers) && in_array($offer,$filtersOffers)){
								$checked = ' checked="checked"';
							}
						?>
							<label class="checkbox"><input type="checkbox" name="filtersOffers"  class="checkboxoffers" value="<?php echo $offer ?>" <?php echo $checked ?> /><div class="checkboxofferslabel"><?php echo $offerText ?> <span class="countvalues" style="display:none;">(<?php echo $filtersEnableOffers[$offer] ?>)</span></div></label>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php endif ?>
			<?php if (!empty($filtersEnabledBookingTypes) &&  is_array($filtersEnabledBookingTypes))  : ?>
				<li><?php echo JText::_('MOD_BOOKINGFORSEARCHFILTER_BOOKINGTYPES') ?></li>
				<div class="mod_bookingforsearchfilter_bookingtypes divtoggle">
					<?php foreach ($bookingTypes as $bookingType => $bookingTypeText):?>
						<?php if (isset($filtersEnabledBookingTypes) &&  is_array($filtersEnabledBookingTypes) && array_key_exists($bookingType,$filtersEnabledBookingTypes)) :?>
							<?php $checked = '';
								if (isset($filtersBookingTypes) &&  is_array($filtersBookingTypes) && in_array($bookingType,$filtersBookingTypes)){
									$checked = ' checked="checked"';
								}
							?>
								<label class="checkbox"><input type="checkbox" name="filtersBookingTypes"  class="checkboxbookingtypes" value="<?php echo $bookingType ?>" <?php echo $checked ?> /><?php echo $bookingTypeText ?>  <span class="countvalues" style="display:none;">(<?php echo $filtersEnabledBookingTypes[$bookingType] ?>)</span></label>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			<?php else: ?>
				<li><?php echo JText::_('MOD_BOOKINGFORSEARCHFILTER_BOOKINGTYPES') ?></li>
				<div class="mod_bookingforsearchfilter_bookingtypes divtoggle">
					<?php foreach ($bookingTypes as $bookingType => $bookingTypeText):?>
							<?php $checked = '';
								if (isset($filtersBookingTypes) &&  is_array($filtersBookingTypes) && in_array($bookingType,$filtersBookingTypes)){
									$checked = ' checked="checked"';
								}
							?>
								<label class="checkbox"><input type="checkbox" name="filtersBookingTypes"  class="checkboxbookingtypes" value="<?php echo $bookingType ?>" <?php echo $checked ?> /><?php echo $bookingTypeText ?></label>
					<?php endforeach; ?>
				</div>

			<?php endif ?>
<!-- RATEPLANNAME -->
		<?php if (isset($filtersEnabledRateplanName) &&  !empty($filtersEnabledRateplanName) && count($filtersEnabledRateplanName)>1 ) : ?>
			<li><?php echo JText::_('MOD_BOOKINGFORSEARCHFILTER_RATEPLANNAME') ?></li>
			<div class="mod_bookingforsearchfilter_RateplanName divtoggle">
				<?php foreach ($filtersEnabledRateplanName as $rateplan => $countrateplan):?>
						<?php $checked = '';
							if (isset($filtersRateplanName) &&  is_array($filtersRateplanName) && in_array($rateplan,$filtersRateplanName)){
								$checked = ' checked="checked"';
							}
						?>
							<label class="checkbox"><input type="checkbox" name="filtersRateplanName"  class="checkboxrateplans" value="<?php echo $rateplan ?>" <?php echo $checked ?> /><div class="checkboxroomlabel"><?php echo $rateplan ?> <span class="countvalues" style="display:none;">(<?php echo $countrateplan ?>)</span></div></label>
				<?php endforeach; ?>
			</div>
		<?php endif ?>

		<?php if (isset($isMerchantResults) && $isMerchantResults) :?>
		   <?php if(isset($filtersEnabledStars) && !empty($filtersEnabledStars) && count($filtersEnabledStars)>1) : ?>
			<li><?php echo JText::_('MOD_BOOKINGFORSEARCHFILTER_MERCHANTSTARS') ?></li>
			<div class="mod_bookingforsearchfilter_star divtoggle">
				<?php for ($star = 1; $star <= 5; ++$star) :?>
					<?php if (isset($filtersEnabledStars) &&  is_array($filtersEnabledStars) && array_key_exists($star,$filtersEnabledStars)) :?>
						<?php $checked = '';
							if (isset($filtersStars) &&  is_array($filtersStars) && in_array($star,$filtersStars)){
								$checked = ' checked="checked"';
							}
						?>
							<label class="checkbox"><input type="checkbox" name="filtersStars"  class="checkboxstar" value="<?php echo $star ?>" <?php echo $checked ?> /><span class="com_bookingforconnector_merchantdetails-rating com_bookingforconnector_merchantdetails-rating<?php echo $star ?>"><span class="com_bookingforconnector_merchantdetails-ratingText">
							<?php for($i=0; $i < $star; $i++) { ?>
							  <i class="fa fa-star"></i>
							<?php } ?>
							</span></span> <span class="countvalues" style="display:none;">(<?php echo $filtersEnabledStars[$star] ?>)</span></label>
					<?php endif; ?>
				<?php endfor; ?>
			</div>
			<?php endif; ?>
		<?php else :?>
			<?php if (isset($filtersEnabledStars) &&  !empty($filtersEnabledStars) && count($filtersEnabledStars)>1 )  :?>
			<li><?php echo JText::_('MOD_BOOKINGFORSEARCHFILTER_RESOURCESTARS') ?></li>
			<div class="mod_bookingforsearchfilter_star divtoggle">
				<?php for ($star = 1; $star <= 5; ++$star) :?>
					<?php if (isset($filtersEnabledStars) &&  is_array($filtersEnabledStars) && array_key_exists($star,$filtersEnabledStars)) :?>
						<?php $checked = '';
							if (isset($filtersStars) &&  is_array($filtersStars) && in_array($star,$filtersStars)){
								$checked = ' checked="checked"';
							}
						?>
							<label class="checkbox"><input type="checkbox" name="filtersStars"  class="checkboxstar" value="<?php echo $star ?>" <?php echo $checked ?> /><span class="com_bookingforconnector_merchantdetails-resourcerating com_bookingforconnector_merchantdetails-resourcerating<?php echo $star ?> com_bookingforconnector_merchantdetails-merchantCategoryId<?php echo $merchantCategoryId ?>"><span class="com_bookingforconnector_merchantdetails-ratingText"><?php for($i=0; $i < $star; $i++) { ?>
							  <i class="fa fa-star"></i>
							<?php } ?></span></span> <span class="countvalues" style="display:none;">(<?php echo $filtersEnabledStars[$star] ?>)</span></label>
					<?php endif; ?>
				<?php endfor; ?>
			</div>
			<?php endif ?>
<!-- ROOMS -->
		<?php if (isset($filtersEnabledRooms) &&  is_array($filtersEnabledRooms)) : ?>
			<li><?php echo JText::_('MOD_BOOKINGFORSEARCHFILTER_ROOMS') ?></li>
			<div class="mod_bookingforsearchfilter_Rooms divtoggle">
				<?php foreach ($filtersEnabledRooms as $room => $countroom):?>
					<?php if (!empty($room)) : ?>
						<?php $checked = '';
							if (isset($filtersRooms) &&  is_array($filtersRooms) && in_array($room,$filtersRooms)){
								$checked = ' checked="checked"';
							}
						?>
							<label class="checkbox"><input type="checkbox" name="filtersRooms"  class="checkboxrooms" value="<?php echo $room ?>" <?php echo $checked ?> /><div class="checkboxroomlabel"><?php echo $room ?> <?php echo JTEXT::_('MOD_BOOKINGFORSEARCHFILTER_ROOMS_EQUALMORE') ?> <span class="countvalues" style="display:none;">(<?php echo $countroom ?>)</span></div></label>
					<?php endif ?>
				<?php endforeach; ?>
			</div>
		<?php endif ?>

		<?php endif ?>
			<li><?php echo JText::_('MOD_BOOKINGFORSEARCHFILTER_PRICE') ?></li>
			<div class="mod_bookingforsearchfilter_slider divtoggle">
				<div id="amountrange"></div>
				<div id="sliderfilerting"></div>
			</div>
		<?php if (isset($locationZones) && !empty($filtersEnabledLocationZones) && count($filtersEnabledLocationZones)>1 ) :?>
			<li><?php echo JText::_('MOD_BOOKINGFORSEARCHFILTER_LOCATION') ?></li>
			<div class="mod_bookingforsearchfilter_location divtoggle">
				<?php foreach ($locationZones as $locationZone):?>
					<?php if (isset($filtersEnabledLocationZones) &&  is_array($filtersEnabledLocationZones) && array_key_exists($locationZone->LocationZoneID,$filtersEnabledLocationZones)) :?>
						<?php $checked = '';
							if (isset($filtersLocationZones) &&  is_array($filtersLocationZones) && in_array($locationZone->LocationZoneID,$filtersLocationZones)){
								$checked = ' checked="checked"';
							}
						?>
							<label class="checkbox"><input type="checkbox" name="filtersLocationZones"  class="checkboxlocationzones" value="<?php echo ($locationZone->LocationZoneID) ?>" <?php echo $checked ?> /><?php echo $locationZone->Name ?> <span class="countvalues" style="display:none;">(<?php echo $filtersEnabledLocationZones[$locationZone->LocationZoneID] ?>)</span></label>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php endif ?>

		<?php if (isset($merchantGroups) && !empty($filtersEnabledMerchantGroups) && count($filtersEnabledMerchantGroups)>1 ) :?>
			<li><?php echo JText::_('MOD_BOOKINGFORSEARCHFILTER_MERCHANTGROUPS') ?></li>
			<div class="mod_bookingforsearchfilter_merchantGroups divtoggle">
				<?php foreach ($merchantGroups as $merchantGroup):?>
					<?php if (isset($filtersEnabledMerchantGroups) &&  is_array($filtersEnabledMerchantGroups) && array_key_exists($merchantGroup->TagId,$filtersEnabledMerchantGroups)) :?>
						<?php $checked = '';
							if (isset($filtersMerchantGroups) &&  is_array($filtersMerchantGroups) && in_array($merchantGroup->TagId,$filtersMerchantGroups)){
								$checked = ' checked="checked"';
							}
						?>
							<label class="checkbox"><input type="checkbox" name="filtersMerchantGroups"  class="checkboxmerchantgroups" value="<?php echo ($merchantGroup->TagId) ?>" <?php echo $checked ?> /><?php echo BFCHelper::getLanguage($merchantGroup->Name, $language) ?> <span class="countvalues" style="display:none;"> (<?php echo $filtersEnabledMerchantGroups[$merchantGroup->TagId] ?>)</label>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php endif ?>
		<?php if (isset($services) && !empty($filtersEnabledServicesMerchants) && count($filtersEnabledServicesMerchants)>1 ) :?>
			<li><?php echo JText::_('MOD_BOOKINGFORSEARCHFILTER_SERVICESMERCHANTS') ?></li>
			<div class="mod_bookingforsearchfilter_services divtoggle">
				<?php foreach ($services as $service):?>
					<?php if (isset($filtersEnabledServicesMerchants) &&  is_array($filtersEnabledServicesMerchants) && array_key_exists($service->ServiceId,$filtersEnabledServicesMerchants)) :?>
						<?php $checked = '';
							if (isset($filtersServices) &&  is_array($filtersServices) && in_array($service->ServiceId,$filtersServices)){
								$checked = ' checked="checked"';
							}
						?>
							<label class="checkbox"><input type="checkbox" name="filtersServicesMerchants"  class="checkboxservicesmerchants" value="<?php echo ($service->ServiceId) ?>" <?php echo $checked ?> /><?php echo BFCHelper::getLanguage($service->Name, $language) ?> <span class="countvalues" style="display:none;"> (<?php echo $filtersEnabledServicesMerchants[$service->ServiceId] ?>)</label>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php endif ?>
		<?php if (isset($services) && !empty($filtersEnabledServices) && count($filtersEnabledServices)>1 ) :?>
			<li><?php echo JText::_('MOD_BOOKINGFORSEARCHFILTER_SERVICES') ?></li>
			<div class="mod_bookingforsearchfilter_services divtoggle">
				<?php foreach ($services as $service):?>
					<?php if (isset($filtersEnabledServices) &&  is_array($filtersEnabledServices) && array_key_exists($service->ServiceId,$filtersEnabledServices)) :?>
						<?php $checked = '';
							if (isset($filtersServices) &&  is_array($filtersServices) && in_array($service->ServiceId,$filtersServices)){
								$checked = ' checked="checked"';
							}
						?>
							<label class="checkbox"><input type="checkbox" name="filtersServices"  class="checkboxservices" value="<?php echo ($service->ServiceId) ?>" <?php echo $checked ?> /><?php echo BFCHelper::getLanguage($service->Name, $language) ?> <span class="countvalues" style="display:none;"> (<?php echo $filtersEnabledServices[$service->ServiceId] ?>)</label>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php endif ?>

		<?php if (isset($masterTypologies) && !empty($filtersEnabledMasterTypologies) && count($filtersEnabledMasterTypologies)>1 ) :?>
			<li><?php echo JText::_('MOD_BOOKINGFORSEARCHFILTER_MASTERTYPOLOGIES') ?></li>
			<div class="mod_bookingforsearchfilter_mastertypologies divtoggle">
				<?php foreach ($masterTypologies as $masterTypology):?>
					<?php if (isset($filtersEnabledMasterTypologies) &&  is_array($filtersEnabledMasterTypologies) && array_key_exists($masterTypology->MasterTypologyId,$filtersEnabledMasterTypologies)) :?>
						<?php $checked = '';
							if (isset($filtersMasterTypologies) &&  is_array($filtersMasterTypologies) && in_array($masterTypology->MasterTypologyId,$filtersMasterTypologies)){
								$checked = ' checked="checked"';
							}
						?>
						<label class="checkbox"><input type="checkbox" name="filtersMasterTypologies"  class="checkboxmastertypologies" value="<?php echo ($masterTypology->MasterTypologyId) ?>" <?php echo $checked ?> /><?php echo BFCHelper::getLanguage($masterTypology->Name, $language) ?> <span class="countvalues" style="display:none;">(<?php echo $filtersEnabledMasterTypologies[$masterTypology->MasterTypologyId] ?>)</label>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php endif ?>
	</ul>
</div>
<div class="clearboth"></div>
	<input type="hidden" id="filtersStarsHidden" name="filters[stars]" value="<?php echo $filtersStarsValue ?>" />
	<input type="hidden" id="filtersLocationzoneHidden" name="filters[locationzones]" value="<?php echo $filtersLocationZonesValue ?>" />
	<input type="hidden" id="filtersMerchantGroupsHidden" name="filters[merchantgroups]" value="<?php echo $filtersMerchantGroupsValue ?>" />
	<input type="hidden" id="filtersServicesHidden" name="filters[services]" value="<?php echo $filtersServicesValue ?>" />
	<input type="hidden" id="filtersServicesMerchantsHidden" name="filters[servicesmerchants]" value="<?php echo $filtersServicesMerchantsValue ?>" />
	<input type="hidden" id="filtersMasterTypologiesHidden" name="filters[mastertypologies]" value="<?php echo $filtersMasterTypologiesValue ?>" />
	<input type="hidden" id="filtersPriceminHidden" name="filters[pricemin]" value="<?php echo $filtersPriceMin ?>"/>
	<input type="hidden" id="filtersPricemaxHidden" name="filters[pricemax]" value="<?php echo $filtersPriceMax ?>" />
	<input type="hidden" id="filtersBookingTypesHidden" name="filters[bookingtypes]" value="<?php echo $filtersBookingTypesValue ?>" />
	<input type="hidden" id="filtersOffersHidden" name="filters[offers]" value="<?php echo $filtersOffersValue ?>" />
	<input type="hidden" id="filtersRateplanNameHidden" name="filters[rateplanname]" value="<?php echo $filtersRateplanNameValue ?>" />
	<input type="hidden" id="filtersRoomsHidden" name="filters[rooms]" value="<?php echo $filtersRoomsValue ?>" />
</form>
</div>

<script type="text/javascript">
duration = <?php echo $duration; ?>;
ajaxFormAction = '<?php echo $formAction; ?>' + '';
priceScaleMin = <?php echo $priceScaleMin; ?>;
priceScaleMax = <?php echo $priceScaleMax; ?>;
priceScaleStep = <?php echo $priceScaleStep; ?>;
filtersPriceMin = <?php echo $filtersPriceMin; ?>;
filtersPriceMax = <?php echo $filtersPriceMax; ?>;


function applyfilterdata(){ 		

		//show order filter
		jQuery('.com_bookingforconnector-sort-item').show();
		
		jQuery("#filtertoggle li").click(function(){
			jQuery(this).toggleClass("active");
			jQuery(this).next("div").stop('true','true').slideToggle("slow",function() {
				if (jQuery.prototype.masonry){
					jQuery('.main-siderbar, .main-siderbar1').masonry('reload');
				}
			});
		});
	
	jQuery('#searchformfilter').ajaxForm({
			target:     '#bfcmerchantlist',
			replaceTarget: true, 
			url:        ajaxFormAction, 
			beforeSerialize:function() {
				try
				{
//					jQuery("#filter_order_filter").val(jQuery("#bookingforsearchForm input[name='filter_order']").val());					
//					jQuery("#filter_order_Dir_filter").val(jQuery("#bookingforsearchForm input[name='filter_order_Dir']").val());	
					jQuery("#filter_order_filter").val(jQuery("#bookingforsearchFilterForm input[name='filter_order']").val());					
					jQuery("#filter_order_Dir_filter").val(jQuery("#bookingforsearchFilterForm input[name='filter_order_Dir']").val());	
					
				}
				catch (e)
				{
				}
			},
			beforeSend: function() {
				jQuery('#bfcmerchantlist').block({
					message:"",
						overlayCSS: {backgroundColor: '#ffffff', opacity: 0.7}  
				}
				);
			},
			success: showResponse,
			error: showError
	});
		
		// bug in jQuery slider con mootools da aggiungere questa riga
		jQuery('#sliderfilerting')[0].slide = null;
		jQuery( "#sliderfilerting" ).slider({      
				range: true,      
				min: priceScaleMin,      
				max: priceScaleMax,
				step: priceScaleStep,      
				values: [ filtersPriceMin, filtersPriceMax ],      
				slide: function( event, ui ) {        
					jQuery( "#amountrange" ).html( "&euro;" + ui.values[ 0 ] + " - &euro;" + ui.values[ 1 ] );      
					jQuery( "#filtersPriceminHidden" ).val(ui.values[0] * duration);						
					jQuery( "#filtersPricemaxHidden" ).val(ui.values[1] * duration);      
				},
				change: function( event, ui ) {
					jQuery("#filtersPriceminHidden").closest('form').submit();
				} 
			});
			jQuery( "#amountrange" ).html( "&euro;" + jQuery( "#sliderfilerting" ).slider( "values", 0 ) +  " - &euro;" + jQuery( "#sliderfilerting" ).slider( "values", 1 ) );  
			jQuery( "#filtersPriceminHidden" ).val(jQuery( "#sliderfilerting" ).slider( "values", 0 ) * duration);      
			jQuery( "#filtersPricemaxHidden" ).val(jQuery( "#sliderfilerting" ).slider( "values", 1 ) * duration);      
		


			jQuery('.checkboxstar').on('click',function() {
				updateHiddenValue('.checkboxstar:checked','#filtersStarsHidden')	
				jQuery(this).closest('form').submit();
			});
			jQuery('.checkboxlocationzones').on('click',function() {
				updateHiddenValue('.checkboxlocationzones:checked','#filtersLocationzoneHidden')	
				jQuery(this).closest('form').submit();
			});
			jQuery('.checkboxmastertypologies').on('click',function() {
				updateHiddenValue('.checkboxmastertypologies:checked','#filtersMasterTypologiesHidden')	
				jQuery(this).closest('form').submit();
			});
			jQuery('.checkboxmerchantgroups').on('click',function() {
				updateHiddenValue('.checkboxmerchantgroups:checked','#filtersMerchantGroupsHidden')	
				jQuery(this).closest('form').submit();
			});
			jQuery('.checkboxservices').on('click',function() {
				updateHiddenValue('.checkboxservices:checked','#filtersServicesHidden')	
				jQuery(this).closest('form').submit();
			});
			jQuery('.checkboxservicesmerchants').on('click',function() {
				updateHiddenValue('.checkboxservicesmerchants:checked','#filtersServicesMerchantsHidden')	
				jQuery(this).closest('form').submit();
			});
			jQuery('.checkboxbookingtypes').on('click',function() {
				updateHiddenValue('.checkboxbookingtypes:checked','#filtersBookingTypesHidden')	
				jQuery(this).closest('form').submit();
			});
			jQuery('.checkboxoffers').on('click',function() {
				updateHiddenValue('.checkboxoffers:checked','#filtersOffersHidden')	
				jQuery(this).closest('form').submit();
			});
			jQuery('.checkboxrooms').on('click',function() {
				jQuery('.checkboxrooms').not(this).prop('checked', false);  				
				updateHiddenValue('.checkboxrooms:checked','#filtersRoomsHidden')	
				jQuery(this).closest('form').submit();
			});
			jQuery('.checkboxrateplans').on('click',function() {
				updateHiddenValue('.checkboxrateplans:checked','#filtersRateplanNameHidden')	
				jQuery(this).closest('form').submit();
			});
	
			if (jQuery.prototype.masonry){
				jQuery('.main-siderbar, .main-siderbar1').masonry('reload');
			}

//		jQuery(".divtoggle").hide();
//	});
}


function updateHiddenValue(who,whohidden) {         
     var allVals = [];
     jQuery(who).each(function() {
       allVals.push(jQuery(this).val());
     });
     jQuery(whohidden).val(allVals.join(","));
  }

jQuery(document).ready(function() {
	applyfilterdata();
});  


</script>
