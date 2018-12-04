<?php 
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
if(!empty( COM_BOOKINGFORCONNECTOR_CRAWLER )){
	$listCrawler = json_decode(COM_BOOKINGFORCONNECTOR_CRAWLER , true);
	foreach( $listCrawler as $key=>$crawler){
	if (preg_match('/'.$crawler['pattern'].'/', $_SERVER['HTTP_USER_AGENT'])) return;
	}
	
}

$pathbase = JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;

$document   = JFactory::getDocument();
$language = JFactory::getLanguage()->getTag();
$currModID = uniqid('bfisearch');

//$db   = JFactory::getDBO();
//$uri  = 'index.php?option=com_bookingforconnector&view=searchonsell';
//$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
//$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
//if ($itemId<>0){
//    $uri = 'index.php?Itemid='.$itemId ;
//}
//$url_page_RealEstate = JRoute::_($uri);

//$searchAvailability_page = get_post( bfi_get_page_id( 'searchavailability' ) );
//$url_page_Resources = get_permalink( $searchAvailability_page->ID );
//$uri  = 'index.php?option=com_bookingforconnector&view=search';
//$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
//$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
//if ($itemId<>0){
//    $uri = 'index.php?Itemid='.$itemId ;
//}
//$url_page_Resources = JRoute::_($uri);

$url_page_Resources = JRoute::_(COM_BOOKINGFORCONNECTOR_URISEARCH);
$url_page_RealEstate = JRoute::_(COM_BOOKINGFORCONNECTOR_URISEARCHONSELL);

$parsRealEstate = BFCHelper::getSearchOnSellParamsSession();
$parsResource = BFCHelper::getSearchParamsSession();

$searchtypetab = -1;

$contractTypeId = 0;
$searchType = "0";
$searchTypeonsell = "0";

$categoryIdRealEstate = 0;
$categoryIdResource = 0;
$merchantCategoryIdRealEstate = 0;
$merchantCategoryIdResource = 0;

$zoneId = 0;
$cityId = 0;
$zoneIds = '';
$pricemax = '';
$pricemin = '';
$areamin = '';
$areamax = '';
$points = '';
$pointsonsell = '';
$roomsmin = '';
$roomsmax = '';
$bathsmin = '';
$bathsmax = '';
$services = '';
$isnewbuilding='';
$zoneIdsSplitted = '';
$bedroomsmin = '';
$bedroomsmax = '';
$checkoutspan = '+1 day';
$checkin = new DateTime('UTC');
$checkout = new DateTime('UTC');
$paxes = 2;
$paxages = array();
$masterTypeId = '';
$checkinId = uniqid('checkin');
$checkoutId = uniqid('checkout');
$durationId = uniqid('duration');
$duration = 1;
$minqt = 1;
$bookableonly = 0;
$resourceName = '';
$refid = '';

if (!empty($parsRealEstate)){
	$contractTypeId = isset($parsRealEstate['contractTypeId']) ? $parsRealEstate['contractTypeId'] : 0;
	$categoryIdRealEstate = isset($parsRealEstate['unitCategoryId']) ? $parsRealEstate['unitCategoryId']: 0;

	$zoneId = isset($parsRealEstate['zoneId']) ? $parsRealEstate['zoneId'] :0;

	if(!empty($parsRealEstate['cityId'])){
		$cityId = $parsRealEstate['cityId'] ?: 0;
	}
	$searchTypeonsell = isset($parsRealEstate['searchType']) ? $parsRealEstate['searchType'] : 0;
//	$searchtypetab = isset($parsRealEstate['searchtypetab']) ? $parsRealEstate['searchtypetab'] : -1;
	$searchtypetab = BFCHelper::getVar('searchtypetab',(isset($parsRealEstate['searchtypetab']) ? $parsRealEstate['searchtypetab'] : -1));

	if(!empty($parsRealEstate['zoneIds'])){
		$zoneIds = $parsRealEstate['zoneIds'];
		$zoneIdsSplitted = explode(",",$zoneIds);
	}
	$pricemax = isset($parsRealEstate['pricemax']) ? $parsRealEstate['pricemax']: null;
	$pricemin = isset($parsRealEstate['pricemin']) ? $parsRealEstate['pricemin']: null;
	$areamin = isset($parsRealEstate['areamin']) ? $parsRealEstate['areamin']: null;
	$areamax = isset($parsRealEstate['areamax']) ? $parsRealEstate['areamax']: null;
	$roomsmin = isset($parsRealEstate['roomsmin']) ? $parsRealEstate['roomsmin']: null;
	$roomsmax = isset($parsRealEstate['roomsmax']) ? $parsRealEstate['roomsmax']: null;
	$bathsmin = isset($parsRealEstate['bathsmin']) ? $parsRealEstate['bathsmin']: null;
	$bathsmax = isset($parsRealEstate['bathsmax']) ? $parsRealEstate['bathsmax']: null;
	$pointsonsell = isset($parsRealEstate['points']) ? $parsRealEstate['points']: null;
	$services = isset($parsRealEstate['services']) ? $parsRealEstate['services']: null;
	if (isset($parsRealEstate['isnewbuilding']) && !empty($parsRealEstate['isnewbuilding']) && $parsRealEstate['isnewbuilding'] =="1") {
		$isnewbuilding = ' checked="checked"';
	}
	$bedroomsmin = isset($parsRealEstate['bedroomsmin']) ? $parsRealEstate['bedroomsmin']: null;
	$bedroomsmax = isset($parsRealEstate['bedroomsmax']) ? $parsRealEstate['bedroomsmax']: null;
}

if (!empty($parsResource)){
		
	$checkin = !empty($parsResource['checkin']) ? $parsResource['checkin'] : new DateTime('UTC');
	$checkout = !empty($parsResource['checkout']) ? $parsResource['checkout'] : new DateTime('UTC');
	
//	$searchtypetab = isset($parsResource['searchtypetab']) ? $parsResource['searchtypetab'] : -1;
//	$availabilitytype = isset($parsResource['availabilitytype']) ? $parsResource['availabilitytype'] : 1;
	$searchtypetab = BFCHelper::getVar('searchtypetab',(isset($parsResource['searchtypetab']) ? $parsResource['searchtypetab'] : -1));
	$searchType = isset($parsResource['searchType']) ? $parsResource['searchType'] : 0;
	$points = isset($parsResource['points']) ? $parsResource['points']: null;

	$zoneId = !empty($parsResource['zoneId']) ? $parsResource['zoneId'] :0;
	$paxes = !empty($parsResource['paxes']) ? $parsResource['paxes'] : 2;
	$minqt = !empty($parsResource['minqt']) ? $parsResource['minqt'] : 1;

	
	$paxages = !empty($parsResource['paxages'])? $parsResource['paxages'] :  array('18','18');
	$merchantCategoryIdResource = !empty($parsResource['merchantCategoryId'])? $parsResource['merchantCategoryId']: 0;
	$masterTypeId = !empty($parsResource['masterTypeId'])? $parsResource['masterTypeId']: 0;
	$bookableonly = !empty($parsResource['bookableonly'])? $parsResource['bookableonly']: 0;

	if (empty($parsResource['checkout'])){
		$checkout->modify($checkoutspan);
	}

	$resourceName = !empty($parsResource['resourceName'])? $parsResource['resourceName']: '';
	$refid = !empty($parsResource['refid'])? $parsResource['refid']: '';

}


$startDate =  new DateTime('UTC');
$startDate->setTime(0,0,0);
$checkin->setTime(0,0,0);
$checkout->setTime(0,0,0);

if ($checkin < $startDate){
	$checkin = $startDate;
	$checkout = clone $checkin;
    $checkout->modify($checkoutspan); 
}

//if ($checkin == $checkout){
//	$checkout->modify($checkoutspan);
//}

//only for Joomla
$checkin = new JDate($checkin->format('Y-m-d')); 
$checkout = new JDate($checkout->format('Y-m-d')); 

$duration = $checkin->diff($checkout);

$tablistSelected = ( ! empty( $instance['tablistSelected'] ) ) ? $instance['tablistSelected'] : array();

$tablistResources = array_intersect($tablistSelected,array(0,1,2,4));
$tablistRealEstate = array_intersect($tablistSelected, array(3));
$tablistCatalog = array_intersect($tablistSelected, array(5));

if(!in_array($searchtypetab,$tablistSelected)){
	$searchtypetab = -1;
}

$tabnamebooking = ( ! empty( $instance['tabnamebooking'] ) ) ? ($instance['tabnamebooking']) : 'MOD_BOOKINGFOR_TABLIST_BOOKING';
$tabnameservices = ( ! empty( $instance['tabnameservices'] ) ) ? ($instance['tabnameservices']) : 'MOD_BOOKINGFOR_TABLIST_SERVICES';
$tabnameactivities = ( ! empty( $instance['tabnameactivities'] ) ) ? ($instance['tabnameactivities']) : 'MOD_BOOKINGFOR_TABLIST_ACTIVITIES';
$tabnameothers = ( ! empty( $instance['tabnameothers'] ) ) ? ($instance['tabnameothers']) : 'MOD_BOOKINGFOR_TABLIST_OTHERS';

$tabiconbooking = ( ! empty( $instance['tabiconbooking'] ) ) ? ($instance['tabiconbooking']) : 'fa fa-suitcase';
$tabiconservices = ( ! empty( $instance['tabiconservices'] ) ) ? ($instance['tabiconservices']) : 'fa fa-calendar';
$tabiconactivities = ( ! empty( $instance['tabiconactivities'] ) ) ? ($instance['tabiconactivities']) : 'fa fa-calendar';
$tabiconothers = ( ! empty( $instance['tabiconothers'] ) ) ? ($instance['tabiconothers']) : 'fa fa-calendar';

$tabiconrealestate = ( ! empty( $instance['tabiconrealestate'] ) ) ? ($instance['tabiconrealestate']) : 'fa fa-home';

$tabnamecatalog = ( ! empty( $instance['tabnamecatalog'] ) ) ? ($instance['tabnamecatalog']) : 'MOD_BOOKINGFOR_TABLIST_CATALOG';
$tabiconcatalog = ( ! empty( $instance['tabiconcatalog'] ) ) ? ($instance['tabiconcatalog']) : 'fa fa-book';

$showdirection = ( ! empty( $instance['showdirection'] ) ) ? ($instance['showdirection']) : '0';
$fixedontop= ( ! empty( $instance['fixedontop'] ) ) ? ($instance['fixedontop']) : '0';
$fixedontopcorrection= ( ! empty( $instance['fixedontopcorrection'] ) ) ? ($instance['fixedontopcorrection']) : '0';

$style = '.bfi-calendar-affixtop'.$currModID.'{'
		. 'top:'.($fixedontopcorrection + 110).'px !important; position: fixed !important;'
		. '}';

if(!empty($fixedontop)){
// Add styles
$style .= '.bfi-affix-top'.$currModID.'.bfiAffixTop {'
        . 'top: '.$fixedontopcorrection.'px !important;'
        . '}' ;
}

$document->addStyleDeclaration($style);

$fixedonbottom= ( ! empty( $instance['fixedonbottom'] ) ) ? ($instance['fixedonbottom']) : '0';


$showLocation = (  !empty($tablistResources) && ! empty( $instance['showLocation'] ) ) ? ($instance['showLocation']) : '0';
$showMapIcon = (  !empty($tablistResources) && ! empty( $instance['showMapIcon'] ) ) ? ($instance['showMapIcon']) : '0';
$showSearchText = ( !empty($tablistResources) && ! empty( $instance['showSearchText'] ) ) ? ($instance['showSearchText']) : '0';
$showAccomodations = (  !empty($tablistResources) && ! empty( $instance['showAccomodations'] ) ) ? ($instance['showAccomodations']) : '0';
$showDateRange = ( !empty($tablistResources) && ! empty( $instance['showDateRange'] ) ) ? ($instance['showDateRange']) : '0';
$onlystay = ( !empty($tablistResources) && ! empty( $instance['onlystay'] ) ) ? ($instance['onlystay']) : '0';

if($showSearchText) {
	$showLocation = '0';
	$showAccomodations = '0';
}


$showResource = ( !empty($tablistResources) && ! empty( $instance['showResource'] ) ) ? ($instance['showResource']) : '0';
$showAdult = ( !empty($tablistResources) && ! empty( $instance['showAdult'] ) ) ? ($instance['showAdult']) : '0';
$showChildren = ( !empty($tablistResources) && ! empty( $instance['showChildren'] ) ) ? ($instance['showChildren']) : '0';
$showSenior = ( !empty($tablistResources) && ! empty( $instance['showSenior'] ) ) ? ($instance['showSenior']) : '0';
$showServices = ( !empty($tablistResources) && ! empty( $instance['showServices'] ) ) ? ($instance['showServices']) : '0';
$showOnlineBooking = ( !empty($tablistResources) && ! empty( $instance['showOnlineBooking'] ) ) ? ($instance['showOnlineBooking']) : '0';

$showSearchTextOnSell = ( !empty($tablistRealEstate) && ! empty( $instance['showSearchTextOnSell'] ) ) ? ($instance['showSearchTextOnSell']) : '0';
$showMapIconOnSell = ( !empty($tablistRealEstate) && ! empty( $instance['showMapIconOnSell'] ) ) ? ($instance['showMapIconOnSell']) : '0';
$showAccomodationsOnSell = ( !empty($tablistRealEstate) && ! empty( $instance['showAccomodationsOnSell'] ) ) ? ($instance['showAccomodationsOnSell']) : '0';
$showMaxPrice = ( !empty($tablistRealEstate) && ! empty( $instance['showMaxPrice'] ) ) ? ($instance['showMaxPrice']) : '0';
$showMinFloor = ( !empty($tablistRealEstate) && ! empty( $instance['showMinFloor'] ) ) ? ($instance['showMinFloor']) : '0';
$showContract = ( !empty($tablistRealEstate) && ! empty( $instance['showContract'] ) ) ? ($instance['showContract']) : '0';
$showBedRooms = ( !empty($tablistRealEstate) && ! empty( $instance['showBedRooms'] ) ) ? ($instance['showBedRooms']) : '0';
$showRooms = ( !empty($tablistRealEstate) && ! empty( $instance['showRooms'] ) ) ? ($instance['showRooms']) : '0';
$showBaths = ( !empty($tablistRealEstate) && ! empty( $instance['showBaths'] ) ) ? ($instance['showBaths']) : '0';
$showOnlyNew = ( !empty($tablistRealEstate) && ! empty( $instance['showOnlyNew'] ) ) ? ($instance['showOnlyNew']) : '0';
$showServicesList = ( !empty($tablistRealEstate) && ! empty( $instance['showServicesList'] ) ) ? ($instance['showServicesList']) : '0';

$merchantCategoriesSelectedBooking = ( ! empty( $instance['merchantcategoriesbooking'] ) ) ? $instance['merchantcategoriesbooking'] : array();
$merchantCategoriesSelectedServices = ( ! empty( $instance['merchantcategoriesservices'] ) ) ? $instance['merchantcategoriesservices'] : array();
$merchantCategoriesSelectedActivities = ( ! empty( $instance['merchantcategoriesactivities'] ) ) ? $instance['merchantcategoriesactivities'] : array();
$merchantCategoriesSelectedOthers = ( ! empty( $instance['merchantcategoriesothers'] ) ) ? $instance['merchantcategoriesothers'] : array();
$merchantCategoriesSelectedRealEstate = ( ! empty( $instance['merchantcategoriesrealestate'] ) ) ? $instance['merchantcategoriesrealestate'] : array();

$unitCategoriesSelectedBooking = ( ! empty( $instance['unitcategoriesbooking'] ) ) ? $instance['unitcategoriesbooking'] : array();
$unitCategoriesSelectedServices = ( ! empty( $instance['unitcategoriesservices'] ) ) ? $instance['unitcategoriesservices'] : array();
$unitCategoriesSelectedActivities = ( ! empty( $instance['unitcategoriesactivities'] ) ) ? $instance['unitcategoriesactivities'] : array();
$unitCategoriesSelectedOthers = ( ! empty( $instance['unitcategoriesothers'] ) ) ? $instance['unitcategoriesothers'] : array();
$unitCategoriesSelectedRealEstate = ( ! empty( $instance['unitcategoriesrealestate'] ) ) ? $instance['unitcategoriesrealestate'] : array();


$merchantCategoriesResource = array();
$merchantCategoriesRealEstate = array();
$unitCategoriesResource = array();
$unitCategoriesRealEstate = array();

$listmerchantCategoriesResource = "";
$listmerchantCategoriesRealEstate = "";

$availabilityTypeList = array();
$availabilityTypeList['1'] = JTEXT::_('MOD_BOOKINGFORSEARCH_NIGHT');
$availabilityTypeList['0'] = JTEXT::_('MOD_BOOKINGFORSEARCH_DAYS');

$availabilityTypesSelectedBooking = ( ! empty( $instance['availabilitytypesbooking'] ) ) ? $instance['availabilitytypesbooking'] : array();
$availabilityTypesSelectedServices = ( ! empty( $instance['availabilitytypesservices'] ) ) ? $instance['availabilitytypesservices'] : array();
$availabilityTypesSelectedActivities = ( ! empty( $instance['availabilitytypesactivities'] ) ) ? $instance['availabilitytypesactivities'] : array();
$availabilityTypesSelectedOthers = ( ! empty( $instance['availabilitytypesothers'] ) ) ? $instance['availabilitytypesothers'] : array();

$itemTypesSelectedBooking = ( ! empty( $instance['itemtypesbooking'] ) ) ? $instance['itemtypesbooking'] : array();
$itemTypesSelectedServices = ( ! empty( $instance['itemtypesservices'] ) ) ? $instance['itemtypesservices'] : array();
$itemTypesSelectedActivities = ( ! empty( $instance['itemtypesactivities'] ) ) ? $instance['itemtypesactivities'] : array();
$itemTypesSelectedOthers = ( ! empty( $instance['itemtypesothers'] ) ) ? $instance['itemtypesothers'] : array();

$groupBySelectedBooking = ( ! empty( $instance['groupbybooking'] ) ) ? $instance['groupbybooking'] : 0;
$groupBySelectedServices = ( ! empty( $instance['groupbyservices'] ) ) ? $instance['groupbyservices'] : 0;
$groupBySelectedActivities = ( ! empty( $instance['groupbyactivities'] ) ) ? $instance['groupbyactivities'] : 0;
$groupBySelectedOthers = ( ! empty( $instance['groupbyothers'] ) ) ? $instance['groupbyothers'] : 0;


$tmpMerchantCategoryIdResource = (strpos($merchantCategoryIdResource, ',') !== FALSE )?"0":$merchantCategoryIdResource;
$tmpmasterTypeId = (strpos($masterTypeId, ',') !== FALSE )?"0":$masterTypeId;

if($showAccomodations || $showAccomodationsOnSell){
	if(!empty($merchantCategoriesSelectedBooking) || !empty($merchantCategoriesSelectedServices) || !empty($merchantCategoriesSelectedActivities) || !empty($merchantCategoriesSelectedOthers) || !empty($merchantCategoriesSelectedRealEstate) ){
//		$allMerchantCategories = BFCHelper::getMerchantCategories();
		$allMerchantCategories = BFCHelper::getMerchantCategories($language);

		if(!empty($merchantCategoriesSelectedBooking) || !empty($merchantCategoriesSelectedServices) || !empty($merchantCategoriesSelectedActivities) || !empty($merchantCategoriesSelectedOthers) ){
			$listmerchantCategoriesResource = '<option value="0">'.($showdirection?JTEXT::_('MOD_BOOKINGFORSEARCH_TIPOLOGY'):JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE')).'</option>';
		}
		if(!empty($merchantCategoriesSelectedRealEstate) ){
			$listmerchantCategoriesRealEstate = '<option value="0">'.JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE').'</option>';
		}
		if (!empty($allMerchantCategories))
		{
			foreach($allMerchantCategories as $merchantCategory)
			{
				if(in_array($merchantCategory->MerchantCategoryId,$merchantCategoriesSelectedBooking) || in_array($merchantCategory->MerchantCategoryId,$merchantCategoriesSelectedServices) || in_array($merchantCategory->MerchantCategoryId,$merchantCategoriesSelectedActivities) || in_array($merchantCategory->MerchantCategoryId,$merchantCategoriesSelectedOthers)){
					$merchantCategoriesResource[$merchantCategory->MerchantCategoryId] = $merchantCategory->Name;
					$listmerchantCategoriesResource .= '<option value="'.$merchantCategory->MerchantCategoryId.'" ' . ($merchantCategory->MerchantCategoryId== $tmpMerchantCategoryIdResource? 'selected':'' ).'>'.$merchantCategory->Name.'</option>';
				}
				if(in_array($merchantCategory->MerchantCategoryId,$merchantCategoriesSelectedRealEstate)){
					$merchantCategoriesRealEstate[$merchantCategory->MerchantCategoryId] = $merchantCategory->Name;
					$listmerchantCategoriesRealEstate .= '<option value="'.$merchantCategory->MerchantCategoryId.'" ' . ($merchantCategory->MerchantCategoryId== $merchantCategoryIdRealEstate? 'selected':'' ).'>'.$merchantCategory->Name.'</option>';
				}
			}
		}

	}

	$listunitCategoriesResource = "";
	if(!empty($unitCategoriesSelectedBooking) || !empty($unitCategoriesSelectedServices) || !empty($unitCategoriesSelectedActivities) || !empty($unitCategoriesSelectedOthers)) {
		$allUnitCategories =  BFCHelper::GetProductCategoryForSearch($language,1);
		if (!empty($allUnitCategories))
		{
			$listunitCategoriesResource = '<option value="0">'.($showdirection?JTEXT::_('MOD_BOOKINGFORSEARCH_ACCOMODATION'):JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE')).'</option>';
			foreach($allUnitCategories as $unitCategory)
			{
				if(in_array($unitCategory->ProductCategoryId,$unitCategoriesSelectedBooking) || in_array($unitCategory->ProductCategoryId,$unitCategoriesSelectedServices) || in_array($unitCategory->ProductCategoryId,$unitCategoriesSelectedActivities) || in_array($unitCategory->ProductCategoryId,$unitCategoriesSelectedOthers)){
					$unitCategoriesResource[$unitCategory->ProductCategoryId] = $unitCategory->Name;
					$listunitCategoriesResource .= '<option value="'.$unitCategory->ProductCategoryId.'" ' . ($unitCategory->ProductCategoryId == $tmpmasterTypeId? 'selected':'' ).'>'.$unitCategory->Name.'</option>';
				}
			}
		}
	}


	$listunitCategoriesRealEstate = "";
	if(!empty($unitCategoriesSelectedRealEstate) ) {
		$allUnitCategoriesRealEstate =  BFCHelper::GetProductCategoryForSearch($language,2);
		if (!empty($allUnitCategoriesRealEstate))
		{
			$listunitCategoriesRealEstate = '<option value="0">'.($showdirection?JTEXT::_('MOD_BOOKINGFORSEARCH_ACCOMODATION'):JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE')).'</option>';
			foreach($allUnitCategoriesRealEstate as $unitCategory)
			{
				if(in_array($unitCategory->ProductCategoryId,$unitCategoriesSelectedRealEstate)){
					$unitCategoriesResource[$unitCategory->ProductCategoryId] = $unitCategory->Name;
					$listunitCategoriesRealEstate .= '<option value="'.$unitCategory->ProductCategoryId.'" ' . ($unitCategory->ProductCategoryId == $categoryIdRealEstate? 'selected':'' ).'>'.$unitCategory->Name.'</option>';
				}
			}
		}
	}
}

$blockmonths = '[14]';
$blockdays = '[7]';

if(!empty($instance['blockmonths']) && count($instance['blockmonths'])>0){
	$blockmonths = '[' . implode(',', $instance['blockmonths']) . ']';
}

if(!empty($instance['blockdays']) && count($instance['blockdays'])>0){
	$blockdays = '[' . implode(',', $instance['blockdays']) . ']';
}



if (!empty($services) ) {
	$filtersServices = explode(",", $services);
}

if (isset($filters)) {
	if (!empty($filters['services'])) {
		$filtersServices = explode(",", $filters['services']);
	}

}

$listlocations="";
$zonesString="";
$listzoneIds = '';

if($showLocation){
	$locations = BFCHelper::getLocations();
	$listlocations = '<option value="0">'.($showdirection?JTEXT::_('MOD_BOOKINGFORSEARCH_ZONE'):JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE')).'</option>';
	if(!empty($locations)){
		foreach ($locations as $lz) {
			if(empty($cityId) && $cityId != 0)
				$cityId = $lz->CityId;
			if($lz->CityId == $cityId){
				$listlocations .= '<option value="'.$lz->CityId.'" selected>'.$lz->Name.'</option>';
			}else{
				$listlocations .= '<option value="'.$lz->CityId.'">'.$lz->Name.'</option>';
			}
		}
	}
	if($showMapIcon){ 
		$listlocations .= '<option value="-1000" >'.JTEXT::_('MOD_BOOKINGFORSEARCH_ZONE_SEARCHBYMAP').'</option>';
	}
	
	$locationZones = BFCHelper::getLocationZones();
	
	if(!empty($locationZones)){
		$zonesString = '<option value="0" selected>'.($showdirection?JTEXT::_('MOD_BOOKINGFORSEARCH_LOCATIONZONE'):JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE')).'</option>';
		foreach ($locationZones as $lz) {
			if(empty($zoneId) && $zoneId != 0){
				$zoneId = $lz->LocationZoneID;
			}
			if($lz->LocationZoneID == $zoneId){
				$zonesString = $zonesString . '<option value="'.$lz->LocationZoneID.'" selected>'.$lz->Name.'</option>';
			}else{
				$zonesString = $zonesString . '<option value="'.$lz->LocationZoneID.'">'.$lz->Name.'</option>';
			}

		}
	}
	if($cityId>=-1) {
		$zoneIdsList = BFCHelper::getLocationZonesByLocationId($cityId);
		if(!empty($zoneIdsList)){
			foreach ($zoneIdsList as $lz) {
				if(is_array($zoneIdsSplitted) && in_array($lz->GeographicZoneId,$zoneIdsSplitted)){
					$listzoneIds .= '<option value="'.$lz->GeographicZoneId.'" selected >'.$lz->Name.'</option>';
				}else{
					$listzoneIds .= '<option value="'.$lz->GeographicZoneId.'">'.$lz->Name.'</option>';
				}
			}
		}
	}

} //if($showLocation)
		
$listcontractType = '<option value="0" selected>'.JTEXT::_('MOD_BOOKINGFORSEARCH_CONTRACTTYPE_SELL').'</option>';
$listcontractType .= '<option value="1">'.JTEXT::_('MOD_BOOKINGFORSEARCH_CONTRACTTYPE_RENT').'</option>';

if($contractTypeId ==1 ){
	$listcontractType = '<option value="0">'.JTEXT::_('MOD_BOOKINGFORSEARCH_CONTRACTTYPE_SELL').'</option>';
	$listcontractType .= '<option value="1" selected>'.JTEXT::_('MOD_BOOKINGFORSEARCH_CONTRACTTYPE_RENT').'</option>';
}


$baths = array(
	'|' =>  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE') ,
	'1|1' =>  JText::_('1') ,
	'2|2' =>  JText::_('2') ,
	'3|3' =>  JText::_('3') ,
	'3|' =>  JText::_('>3') 
);


//$show_direction = $params->get('show_direction');
$show_title = $params->get('show_title');




$nad = 0;
$nch = 0;
$nse = 0;
$countPaxes = 0;
$maxchildrenAge = (int)BFCHelper::$defaultAdultsAge-1;

$nchs = array(null,null,null,null,null,null);

if (empty($paxages)){
	$nad = 2;
	$paxages = array(BFCHelper::$defaultAdultsAge, BFCHelper::$defaultAdultsAge);

}else{
	if(is_array($paxages)){
		$countPaxes = array_count_values($paxages);
		$nchs = array_values(array_filter($paxages, function($age) {
			if ($age < (int)BFCHelper::$defaultAdultsAge)
				return true;
			return false;
		}));
	}
}
array_push($nchs, null,null,null,null,null,null);

if($countPaxes>0){
	foreach ($countPaxes as $key => $count) {
		if ($key >= BFCHelper::$defaultAdultsAge) {
			if ($key >= BFCHelper::$defaultSenioresAge) {
				$nse += $count;
			} else {
				$nad += $count;
			}
		} else {
			$nch += $count;
		}
	}
}

$showChildrenagesmsg = isset($_REQUEST['showmsgchildage']) ? $_REQUEST['showmsgchildage'] : 0;
$tabActive = "";
$totalTabs = count($tablistSelected);
$widthTabs = 100/$totalTabs;

?>
<div class="<?php echo $moduleclass_sfx ?> bfi-affix-top<?php echo $currModID ?> <?php echo ( ! empty( $fixedonbottom ) ) ? 'bfiAffixBottom' : '' ?>"><!-- per span8 e padding -->
<?php if($show_title) {?><h4 class="bookingfor_title"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_TITLE'); ?></h4><?php } ?>

<div class="bfi-mod-bookingforsearch" id="bfisearch<?php echo $currModID ?>" >
    <ul class="bfi-tabs" id="navbookingforsearch<?php echo $currModID ?>" style="<?php echo ($totalTabs>1) ?"": "display:none" ?>">
		<?php if(in_array(0, $tablistSelected)){ ?>
		<?php 
		if((empty($tabActive) && $searchtypetab==-1) || $searchtypetab == 0 ){
			$tabActive = "active";
			$searchtypetab = 0;
		}else{
			$tabActive = "";  
		}
		?>
		<li class="" data-searchtypeid="0" style="width:<?php echo $widthTabs ?>%">
            <a href="#bfisearchtab<?php echo $currModID ?>" data-toggle="tab" aria-expanded="true" class="searchResources">
                <?php if(!empty($tabiconbooking) && $tabiconbooking!='none') { ?><i class="<?php echo $tabiconbooking ?>" aria-hidden="true"></i><?php } ?>
                <?php echo JTEXT::_($tabnamebooking); ?>
            </a>
        </li>
		<?php }  ?>
		<?php if(in_array(1, $tablistSelected)){ ?>
		<?php 
		if((empty($tabActive) && $searchtypetab==-1) || $searchtypetab == 1 ){
			$tabActive = "active";
			$searchtypetab = 1;
		}else{
			$tabActive = "";  
		}
		?>
        <li class="" data-searchtypeid="1" style="width:<?php echo $widthTabs ?>%">
            <a href="#bfisearchtab<?php echo $currModID ?>" data-toggle="tab" aria-expanded="true" class="searchServices">
                <?php if(!empty($tabiconservices) && $tabiconservices!='none') { ?><i class="<?php echo $tabiconservices ?>" aria-hidden="true"></i><?php } ?>
                <?php echo JTEXT::_($tabnameservices); ?>
            </a>
        </li>
		<?php }  ?>
		<?php if(in_array(2, $tablistSelected)){ ?>
		<?php 
		if((empty($tabActive) && $searchtypetab==-1) || $searchtypetab == 2 ){
			$tabActive = "active";
			$searchtypetab = 2;
		}else{
			$tabActive = "";  
		}
		?>
        <li class="" data-searchtypeid="2" style="width:<?php echo $widthTabs ?>%">
            <a href="#bfisearchtab<?php echo $currModID ?>" data-toggle="tab" aria-expanded="true" class="searchTimeSlots">
                <?php if(!empty($tabiconactivities) && $tabiconactivities!='none') { ?><i class="<?php echo $tabiconactivities ?>" aria-hidden="true"></i><?php } ?>
                <?php echo JTEXT::_($tabnameactivities); ?>
            </a>
        </li>
		<?php }  ?>
		<?php if(in_array(4, $tablistSelected)){ ?>
		<?php 
		if((empty($tabActive) && $searchtypetab==-1) || $searchtypetab == 4 ){
			$tabActive = "active";
			$searchtypetab = 4;
		}else{
			$tabActive = "";  
		}
		?>
        <li class="" data-searchtypeid="4" style="width:<?php echo $widthTabs ?>%">
            <a href="#bfisearchtab<?php echo $currModID ?>" data-toggle="tab" aria-expanded="true" class="searchOthers">
                <?php if(!empty($tabiconothers) && $tabiconothers!='none') { ?><i class="<?php echo $tabiconothers ?>" aria-hidden="true"></i><?php } ?>
                <?php echo JTEXT::_($tabnameothers); ?>
            </a>
        </li>
		<?php }  ?>
		<?php if(in_array(5, $tablistSelected)){ //catalog ?>
		<?php 
		if((empty($tabActive) && $searchtypetab==-1) || $searchtypetab == 5 ){
			$tabActive = "active";
			$searchtypetab = 5;
		}else{
			$tabActive = "";  
		}
		?>
        <li class="" data-searchtypeid="5" style="width:<?php echo $widthTabs ?>%">
            <a href="#bfisearchcatalog<?php echo $currModID ?>" data-toggle="tab" aria-expanded="false" class="searchCatalog">
                <?php if(!empty($tabiconcatalog) && $tabiconcatalog!='none') { ?><i class="<?php echo $tabiconcatalog ?>" aria-hidden="true"></i><?php } ?>
                <?php echo JTEXT::_($tabnamecatalog); ?>
            </a>
        </li>
		<?php }  ?>
		<?php if(in_array(3, $tablistSelected)){ ?>
		<?php 
		if((empty($tabActive) && $searchtypetab==-1) || $searchtypetab == 3 ){
			$tabActive = "active";
			$searchtypetab = 3;
		}else{
			$tabActive = "";  
		}
		?>
        <li class="" data-searchtypeid="3" style="width:<?php echo $widthTabs ?>%">
            <a href="#bfisearchselling<?php echo $currModID ?>" data-toggle="tab" aria-expanded="false" class="searchSelling">
                <?php if(!empty($tabiconrealestate) && $tabiconrealestate!='none') { ?><i class="<?php echo $tabiconrealestate ?>" aria-hidden="true"></i><?php } ?>
                <?php echo JTEXT::_('MOD_BOOKINGFOR_TABLIST_REALESTATE'); ?>
            </a>
        </li>
		<?php }  ?>
    </ul>		
    <div class="bfi-tab-content tab-content">
<?php if(!empty($tablistResources)){ 
$totalfields=0;
?>
        <div id="bfisearchtab<?php echo $currModID ?>" class="tab-pane fade in">
		<form action="<?php echo $url_page_Resources; ?>" method="get" id="searchform<?php echo $currModID ?>" class="bfi-form-<?php echo $showdirection?"horizontal":"vertical"; ?> bfi-form-rental">
			<div class="bfi-row">
				<?php if($showSearchText) { 
					$totalfields +=2;
				?>
					<div class="bfi_destination bfi-col-sm-2">
						<label><?php echo JTEXT::_('MOD_BOOKINGFOR_SHOWSEARCHTEXT') ?></label>
						<input type="text" id="searchtext<?php echo $currModID ?>" name="searchterm" class="bfi-inputtext bfi-autocomplete" placeholder="<?php echo JTEXT::_('MOD_BOOKINGFOR_SHOWSEARCHTEXT_PLACEHOLDER') ?>" />
					</div>
					<input type="hidden" value="" name="locationzone" />
					<input type="hidden" value="" name="masterTypeId" />
					<input type="hidden" value="" name="merchantCategoryId" />
					<input type="hidden" value="" name="searchTermValue" />
				<?php }//$showSearchText ?>
				<?php if(!empty($zonesString) && $showLocation){  
					$totalfields +=2;
				?>
					<div class="bfi_destination bfi-col-sm-2">
						<label><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_LOCATIONZONE'); ?></label>
						<select name="locationzone" class="" data-live-search="true" data-width="99%">
						<?php echo $zonesString; ?>
						</select>
					</div>
				<?php } //$showLocation ?>
				<?php if(!empty($listunitCategoriesResource) && $showAccomodations){  
					$totalfields +=2;
				?>
					<div class="bfi_unitcategoriesresource bfi-col-sm-2">
						<label><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ACCOMODATION'); ?></label>
						<select id="masterTypeId<?php echo $currModID ?>" name="masterTypeId" class="">
							<?php echo $listunitCategoriesResource; ?>
						</select>
					</div>
				<?php } //$showAccomodations ?>				
				<?php if(!empty($listmerchantCategoriesResource) && $showAccomodations){  
					$totalfields +=2;
				?>
					<div class="bfi_merchantcategoriesresource bfi-col-sm-2">
						<label><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_TIPOLOGY'); ?></label>
						<select id="merchantCategoryId<?php echo $currModID ?>" name="merchantCategoryId" onchange="checkSelSearch<?php echo $currModID ?>();" class="hideRent">
							<?php echo $listmerchantCategoriesResource; ?>
						</select>
					</div>
				<?php } //$showAccomodations ?>
				<?php if($showMapIcon){  
					$totalfields +=1;
				?>
				<div class="bfi_listlocations bfi-col-sm-1">
					<input type="hidden" value="<?php echo $searchType ?>" name="searchType"  />
					<div class="bfi-btn bfi-mapsearchbtn <?php echo $searchType==1?"bfi-alternative":"bfi-alternative4"; ?>" onclick="javascript:bfiOpenGoogleMapDrawer('searchform<?php echo $currModID ?>','<?php echo $currModID ?>');">
						<i class="fa fa-map-marker fa-1"></i>
					</div>
				</div>
				<?php } //$showLocation ?>
				<?php if($showDateRange){  
					$totalfields +=4;
				?>
				<div class="bfi-showdaterange bfi-col-sm-2">
								<label><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_FROM'); ?></label>
								<div class="bfi-datepicker">
									<input name="checkin" type="hidden" value="<?php echo $checkin->format('d/m/Y'); ?>" id="<?php echo $checkinId; ?>" />
								</div>
							</div>
				<div class="bfi-showdaterange bfi-col-sm-2" id="divcheckoutsearch<?php echo $currModID ?>">
								<label><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_TO'); ?></label>
								<div class="bfi-datepicker">
									<input type="hidden" name="checkout" value="<?php echo $checkout->format('d/m/Y'); ?>" id="<?php echo $checkoutId; ?>" />
								</div>
							</div>
				<div class="bfi-hide ">
					<div  class="bfi-fields bfi-calendarnightsearch" id="divcalendarnightsearch<?php echo $currModID ?>">
								<div class="bfi-calendarnight" id="calendarnight<?php echo $durationId ?>"><?php echo $duration->format('%a') ?> <?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_NIGHT') ?></div>
							</div>
				</div>
				<?php } //$showDateRange ?>

				<?php if($showAdult){ 
					$totalfields +=2;
				?>
				<div class="bfi-col-sm-2 bfi-showperson-container">
					<label><?php echo JTEXT::_('MOD_BOOKINGFOR_GUEST'); ?></label>
					<div class="bfi-showperson-text bfi-container " id="bfi-showperson-text<?php echo $currModID ?>">
						<?php if($showResource){?><span id="bfi-room-info<?php echo $currModID ?>" class="bfi-comma"><span><?php echo $minqt ?></span> <?php echo JTEXT::_('MOD_BOOKINGFOR_RESOURCE') ?></span><?php }?>
						<span id="bfi-adult-info<?php echo $currModID ?>" class="bfi-comma"><span><?php echo $nad ?></span> <?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ADULTS'); ?></span>
						<?php if($showSenior){?><span id="bfi-senior-info<?php echo $currModID ?>" class="bfi-comma"><span><?php echo $nse ?></span> <?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_SENIORES'); ?></span><?php }?>
						<?php if($showChildren){?><span id="bfi-child-info<?php echo $currModID ?>" class="bfi-comma"><span><?php echo $nch ?></span> <?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_CHILDREN'); ?></span><?php }?>
					</div>
				</div>
					<div class="bfi-showperson" id="bfishowperson<?php echo $currModID ?>">
						<?php if($showResource){
						?>
							<div class="bfi-showresource "><!-- showresource -->
								<label><?php echo JTEXT::_('MOD_BOOKINGFOR_RESOURCE'); ?></label>
								<select id="bfi-minqt<?php echo $currModID ?>" name="minqt" onchange="quoteChanged<?php echo $currModID ?>();" class="" style="display:inline-block !important;">
									<?php
									foreach (range(1, 10) as $number) {
										?> <option value="<?php echo $number ?>" <?php echo ($minqt == $number)?"selected":""; ?>><?php echo $number ?></option><?php
									}
									?>
								</select>
						</div>
						<?php }?>
						<div class="bfi-row">
							<div class="bfi-showadult bfi-col-md-<?php echo ($showSenior)?"4":"6" ?> bfi-col-xs-<?php echo ($showSenior)?"4":"6" ?>"><!-- Adults -->
								<label><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ADULTS'); ?></label>
								<select id="bfi-adult<?php echo $currModID ?>" name="adultssel" onchange="quoteChanged<?php echo $currModID ?>();" class="" style="display:inline-block !important;">
									<?php
									foreach (range(1, 10) as $number) {
										?> <option value="<?php echo $number ?>" <?php echo ($nad == $number)?"selected":""; ?>><?php echo $number ?></option><?php
									}
									?>
								</select>
							</div>
						<?php if($showSenior){ 
						?>
							<div class="bfi-showsenior bfi-col-md-4 bfi-col-xs-4"><!-- Seniores -->
								<label><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_SENIORES'); ?></label>
								<select id="bfi-senior<?php echo $currModID ?>" name="senioressel" onchange="quoteChanged<?php echo $currModID ?>();" class="" style="display:inline-block !important;">
									<?php
									foreach (range(0, 10) as $number) {
										?> <option value="<?php echo $number ?>" <?php echo ($nse == $number)?"selected":""; ?>><?php echo $number ?></option><?php
									}
									?>
								</select>
							</div>
						<?php }?>
						<?php if($showChildren){ 
						?>
							<div class="bfi-showchildren bfi-col-md-<?php echo ($showSenior)?"4":"6" ?> bfi-col-xs-<?php echo ($showSenior)?"4":"6" ?>" id="mod_bookingforsearch-children<?php echo $currModID ?>" ><!-- n childrens -->
								<label><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_CHILDREN'); ?></label>
								<select id="bfi-child<?php echo $currModID ?>" name="childrensel" onchange="quoteChanged<?php echo $currModID ?>();" class="" style="display:inline-block !important;">
									<?php
									foreach (range(0, 4) as $number) {
										?> <option value="<?php echo $number ?>" <?php echo ($nch == $number)?"selected":""; ?>><?php echo $number ?></option><?php
									}
									?>
								</select>
							</div>
						<?php }?>
						</div>
						<?php if($showChildren){?>
						<div class="bfi-childrenages " style="display:none;"  id="mod_bookingforsearch-childrenages<?php echo $currModID ?>">
								
							<label ><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_CHILDRENAGE'); ?>
							<span id="bfi_lblchildrenagesat<?php echo $currModID ?>"><?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_CHILDRENAGESAT') . " " .$checkout->format("d"). " " .$checkout->format("M"). " " . $checkout->format("Y") ?></span></label><!-- Ages childrens -->	
									<select name="childages1sel" onchange="quoteChanged<?php echo $currModID ?>();" class="bfi-inputmini" style="display: none;">
										<option value="<?php echo COM_BOOKINGFORCONNECTOR_CHILDRENSAGE ?>" ></option>
										<?php
										foreach (range(0, $maxchildrenAge) as $number) {
									?> <option value="<?php echo $number ?>" <?php echo ($nchs[0] !== null && $nchs[0] == $number)?"selected":""; ?>><?php echo $number ?></option><?php
										}
										?>
									</select>
									<select name="childages2sel" onchange="quoteChanged<?php echo $currModID ?>();" class="bfi-inputmini" style="display: none;">
										<option value="<?php echo COM_BOOKINGFORCONNECTOR_CHILDRENSAGE ?>" ></option>
										<?php
										foreach (range(0, $maxchildrenAge) as $number) {
									?> <option value="<?php echo $number ?>" <?php echo ($nchs[1] !== null && $nchs[1] == $number)?"selected":""; ?>><?php echo $number ?></option><?php
										}
										?>
									</select>
									<select name="childages3sel" onchange="quoteChanged<?php echo $currModID ?>();" class="bfi-inputmini" style="display: none;">
										<option value="<?php echo COM_BOOKINGFORCONNECTOR_CHILDRENSAGE ?>" ></option>
										<?php
										foreach (range(0, $maxchildrenAge) as $number) {
									?> <option value="<?php echo $number ?>" <?php echo ($nchs[2] !== null && $nchs[2] == $number)?"selected":""; ?>><?php echo $number ?></option><?php
										}
										?>
									</select>
									<select name="childages4sel" onchange="quoteChanged<?php echo $currModID ?>();" class="bfi-inputmini" style="display: none;">
										<option value="<?php echo COM_BOOKINGFORCONNECTOR_CHILDRENSAGE ?>" ></option>
										<?php
										foreach (range(0, $maxchildrenAge) as $number) {
									?> <option value="<?php echo $number ?>" <?php echo ($nchs[3] !== null && $nchs[3] == $number)?"selected":""; ?>><?php echo $number ?></option><?php
										}
										?>
									</select>
									<select name="childages5sel" onchange="quoteChanged<?php echo $currModID ?>();" class="bfi-inputmini" style="display: none;">
										<option value="<?php echo COM_BOOKINGFORCONNECTOR_CHILDRENSAGE ?>" ></option>
										<?php
										foreach (range(0, $maxchildrenAge) as $number) {
									?> <option value="<?php echo $number ?>" <?php echo ($nchs[4] !== null && $nchs[4] == $number)?"selected":""; ?>><?php echo $number ?></option><?php
										}
										?>
									</select>
									</div>
									<span class="bfi-childmessage" id="bfi_lblchildrenages<?php echo $currModID ?>">&nbsp;</span>
							<?php }?>

					</div>

				<?php } //$showAdult?>
				<?php
					$widthbtn = 10-($totalfields % 12);
					if (($totalfields  <12)) {
						$widthbtn = 2;						
					}else{
					if (($widthbtn <4)) {
					    $widthbtn = 12;
					}
					}
				?>
				<div class="bfi-searchbutton-wrapper bfi-col-sm-<?php echo $showdirection? $widthbtn:"2"; $widthbtn ?>" id="divBtnResource<?php echo $currModID ?>">
					<a  id="BtnResource<?php echo $currModID ?>" class="bfi-btn" href="javascript: void(0);"><i class="fa fa-search" aria-hidden="true"></i> <?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_SEND'); ?></a>
				</div>
			</div>
			<div class="bfi-clearboth"></div>
			<div class="bfi-powered"><a href="https://www.bookingfor.com" target="_blank">Powered by Bookingfor</a></div>
			<input type="hidden" value="<?php echo uniqid('', true)?>" name="searchid" />
			<input type="hidden" name="onlystay" value="<?php echo $onlystay ?>">
			<input type="hidden" name="persons" value="<?php echo $nad + $nse + $nch?>" id="searchformpersons<?php echo $currModID ?>">
			<input type="hidden" name="adults" value="<?php echo $nad?>" id="searchformpersonsadult<?php echo $currModID ?>">
			<input type="hidden" name="seniores" value="<?php echo $nse?>" id="searchformpersonssenior<?php echo $currModID ?>">
			<input type="hidden" name="children" value="<?php echo $nch?>" id="searchformpersonschild<?php echo $currModID ?>">
			<input type="hidden" name="childages1" value="<?php echo $nchs[0]?>" id="searchformpersonschild1<?php echo $currModID ?>">
			<input type="hidden" name="childages2" value="<?php echo $nchs[1]?>" id="searchformpersonschild2<?php echo $currModID ?>">
			<input type="hidden" name="childages3" value="<?php echo $nchs[2]?>" id="searchformpersonschild3<?php echo $currModID ?>">
			<input type="hidden" name="childages4" value="<?php echo $nchs[3]?>" id="searchformpersonschild4<?php echo $currModID ?>">
			<input type="hidden" name="childages5" value="<?php echo $nchs[4]?>" id="searchformpersonschild5<?php echo $currModID ?>">
			

			<input type="hidden" value="1" name="newsearch" />
			<input type="hidden" value="0" name="limitstart" />
			<input type="hidden" name="filter_order" value="" />
			<input type="hidden" name="filter_order_Dir" value="" />
			<input type="hidden" value="<?php echo $language ?>" name="cultureCode" />
			<input type="hidden" value="<?php echo $points ?>" name="points" id="points<?php echo $currModID ?>" />
			<input type="hidden" value="<?php echo $searchtypetab ?>" name="searchtypetab" id="searchtypetab<?php echo $currModID ?>" />
			<input type="hidden" value="0" name="showmsgchildage" id="showmsgchildage<?php echo $currModID ?>"/>
			<input type="hidden" value="" name="stateIds" />
			<input type="hidden" value="" name="regionIds" />
			<input type="hidden" value="" name="cityIds" />
			<input type="hidden" value="" name="merchantIds" />
			<input type="hidden" value="" name="merchantTagIds" />
			<input type="hidden" value="" name="productTagIds" />
			<div class="bfi-hide" id="bfi_childrenagesmsg<?php echo $currModID ?>">
				<div style="line-height:0; height:0;"></div>
				<div class="bfi-pull-right" style="cursor:pointer;color:red">&nbsp;<i class="fa fa-times-circle" aria-hidden="true" onclick="jQuery('#bfi_lblchildrenages<?php echo $currModID ?>').webuiPopover('destroy');"></i></div>
				<?php echo sprintf(JTEXT::_('MOD_BOOKINGFORSEARCH_CHILDRENAGESMSG'),COM_BOOKINGFORCONNECTOR_CHILDRENSAGE) ?>
			</div>
			<input type="hidden" name="availabilitytype" class="resbynighthd" value="1" />
			<input type="hidden" name="itemtypes" class="itemtypeshd" value="0" id="hdItemTypes<?php echo $currModID; ?>" />
			<input type="hidden" name="groupresulttype" class="groupresulttypehd" value="1" id="hdSearchGroupby<?php echo $currModID; ?>" />
			<input type="hidden" name="getallresults" class="getallresultshd" value="<?php echo $showOnlineBooking; ?>" id="hdgetallresults<?php echo $currModID; ?>" />

		</form>
				   
        </div>
<?php }  ?>

<?php if(!empty($tablistCatalog)){
	$totalfields=0;			
?>
	<div id="bfisearchcatalog<?php echo $currModID ?>" class="tab-pane fade in">
		<form action="<?php echo $url_page_Resources; ?>" method="get" id="searchformcatalog<?php echo $currModID ?>" class="bfi-form-<?php echo $showdirection?"horizontal":"vertical"; ?> bfi-form-catalog">
			<!-- Catalogo -->
			<div class="bfi_destination bfi-col-sm-2">
				<label><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_REFID') ?></label>
				<input type="text" id="refid<?php echo $currModID ?>" name="refid" value="<?php echo $refid; ?>" class="bfi-inputtext" placeholder="<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_REFID') ?>" />
			</div>
			<div class="bfi_destination bfi-col-sm-2">
				<label><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_RESOURCENAME') ?></label>
				<input type="text" id="resourceName<?php echo $currModID ?>" name="resourceName" value="<?php echo $resourceName; ?>" class="bfi-inputtext" placeholder="<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_RESOURCENAME') ?>" />
			</div>

				<?php
					$widthbtn = ($totalfields % 12);
					if (($widthbtn >6)) {
					    $widthbtn = 12;
					}
				?>
				<div class="bfi-searchbutton-wrapper bfi-col-sm-<?php echo $showdirection? $widthbtn:"2"; $widthbtn ?>">
					<div class="" id="divBtnCatalog">
						<a  id="BtnCatalog<?php echo $currModID ?>" class="bfi-btn" href="javascript: void(0);"><i class="fa fa-search" aria-hidden="true"></i> <?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_SEND'); ?></a>
					</div>
				</div>
				<div class="bfi-clearboth"></div>
				<div class="bfi-powered"><a href="https://www.bookingfor.com" target="_blank">Powered by Bookingfor</a></div>


			<input type="hidden" value="<?php echo uniqid('', true)?>" name="searchid" />
			<input type="hidden" value="5" name="searchtypetab" />
			<input type="hidden" value="1" name="newsearch" />
			<input type="hidden" value="0" name="limitstart" />
			<input type="hidden" name="filter_order" value="" />
			<input type="hidden" name="filter_order_Dir" value="" />
			<input type="hidden" value="<?php echo $language ?>" name="cultureCode" />
			<input type="hidden" value="<?php echo $pointsonsell ?>" name="points" id="pointsonsell<?php echo $currModID ?>" />
			<input type="hidden" value="<?php echo $services ?>" name="servicesonsell" id="servicesonsell<?php echo $currModID ?>" />
			<input type="hidden" name="availabilitytype" class="resbynighthd" value="1" />
			<input type="hidden" value="" name="stateIds" />
			<input type="hidden" value="" name="regionIds" />
			<input type="hidden" value="" name="cityIds" />
		</form>
	</div><!--tab Catalogo -->

<?php }  ?>

<?php if(!empty($tablistRealEstate)){ 
$totalfields=0;			
?>
		<div id="bfisearchselling<?php echo $currModID ?>" class="tab-pane fade in">
		<form action="<?php echo $url_page_RealEstate; ?>" method="get" id="searchformonsellunit<?php echo $currModID ?>" class="bfi-form-<?php echo $showdirection?"horizontal":"vertical"; ?> bfi-form-sell ">			
			<div  id="searchBlock<?php echo $currModID ?>" class="bfi-row">
				<?php if($showContract){  
					$totalfields +=2;
				?>
				<div class="bfi_contracttypeid bfi-col-sm-2" >
					<label><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_CONTRACT'); ?></label>
					<select name="contractTypeId" class="">
								<?php echo $listcontractType; ?>
					</select>
				</div><!--/span-->
				<?php } //$showContract ?>
				<?php if($showSearchTextOnSell) {  
					$totalfields +=2;
				?>
					<div class="bfi_destination bfi-col-sm-2">
						<label><?php echo JTEXT::_('MOD_BOOKINGFOR_SHOWSEARCHTEXT') ?></label>
						<input type="text" id="searchtextonsell<?php echo $currModID ?>" name="searchterm" class="bfi-inputtext bfi-autocomplete" placeholder="<?php echo JTEXT::_('MOD_BOOKINGFOR_SHOWSEARCHTEXT_PLACEHOLDER') ?>" />
					</div>
					<input type="hidden" value="" name="locationzone" />
					<input type="hidden" value="" name="searchTermValue" />
				<?php }//$showSearchText ?>				
				<?php if($showMapIconOnSell){  
					$totalfields +=1;
				?>
				<div class="bfi_listlocations bfi-col-sm-1">
					<input type="hidden" value="<?php echo $searchTypeonsell ?>" name="searchType" id="mapSearch<?php echo $currModID ?>" />
					<div class="bfi-btn bfi-mapsearchbtn <?php echo $searchTypeonsell==1?"bfi-alternative":"bfi-alternative4"; ?> " onclick="javascript:bfiOpenGoogleMapDrawer('searchformonsellunit<?php echo $currModID ?>','<?php echo $currModID ?>');">
						<i class="fa fa-map-marker fa-1"></i>
					</div>
				</div>
				<?php } //$showLocation ?>
				<?php if(!empty($listunitCategoriesRealEstate) && $showAccomodationsOnSell){  
					$totalfields +=2;
				?>
				<div class="bfi_unitCategoryId bfi-col-sm-2">
					<label><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ACCOMODATION'); ?></label>
					<select name="unitCategoryId" class="">
						<?php echo $listunitCategoriesRealEstate; ?>
					</select>
				</div><!--/span-->
				<?php } //$listunitCategoriesRealEstate ?>
				<?php if($showMaxPrice){  
					$totalfields +=2;
				?>
				<div class="bfi-range-price bfi-col-sm-2" id="bfi-range-price<?php echo $currModID ?>">
					<label><?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_PRICE') ?></label>
					<div class="bfi-row">   
						<div class="bfi-col-md-6 bfi-col-sm-6">
							<input name="pricemin" type="text" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_FROM') ?>" value="<?php echo $pricemin;?>" class="bfi-inputtext" data-rule-digits="true" data-msg-digits="<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_DIGIT') ?>" rel="#bfi-range-pricemin<?php echo $currModID ?>"  > 
						</div><!--/span-->
						<div class="bfi-col-md-6 bfi-col-sm-6">
							<input name="pricemax" type="text" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_TO') ?>" value="<?php echo $pricemax;?>"  class="bfi-inputtext" data-rule-digits="true" data-msg-digits="<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_DIGIT') ?>"" rel="#bfi-range-pricemax<?php echo $currModID ?>" > 
						</div><!--/span-->
					</div>
				</div><!--/span-->
				<?php } //$showMaxPrice ?>
				<?php if($showMinFloor){  
					$totalfields +=2;
				?>
				<div class="bfi_floor_area  bfi-col-sm-2">
					<label><?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_AREA') ?></label>
					<div class="bfi-row">   
						<div class="bfi-col-md-6 bfi-col-sm-6">
							<input name="areamin" type="text" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_FROM') ?>" value="<?php echo $areamin;?>" class="bfi-inputtext" data-rule-digits="true" data-msg-digits="<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_DIGIT') ?>" > 
						</div><!--/span-->
						<div class="bfi-col-md-6 bfi-col-sm-6">
							<input name="areamax" type="text" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_TO') ?>" value="<?php echo $areamax;?>" class="bfi-inputtext" data-rule-digits="true" data-msg-digits="<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_DIGIT') ?>" > 
						</div><!--/span-->
					</div>
				</div><!--/span-->
				<?php } //$showMinFloor ?>
				<?php if($showBedRooms){  
					$totalfields +=2;
				?>
				<div class="bfi_bedrooms  bfi-col-sm-2">
					<label><?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_BEDROOMS') ?></label>
					<div class="bfi-row">   
						<div class="bfi-col-md-6 bfi-col-sm-6">
					<input name="bedroomsmin" type="text" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_FROM') ?>" value="<?php echo $bedroomsmin;?>" class="bfi-inputtext" data-rule-digits="true" data-msg-digits="<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_DIGIT') ?>" > 
						</div><!--/span-->
						<div class="bfi-col-md-6 bfi-col-sm-6">
					<input name="bedroomsmax" type="text" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_TO') ?>" value="<?php echo $bedroomsmax;?>" class="bfi-inputtext" data-rule-digits="true" data-msg-digits="<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_DIGIT') ?>" > 
						</div><!--/span-->
					</div>
				</div><!--/span-->
				<?php } //$showBedRooms ?>
				<?php if($showRooms){  
					$totalfields +=2;
				?>
				<div class="bfi_rooms  bfi-col-sm-2">
					<label><?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_ROOMS') ?></label>
					<div class="bfi-row">   
						<div class="bfi-col-md-6 bfi-col-sm-6">
					<input name="roomsmin" type="text" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_FROM') ?>" value="<?php echo $roomsmin;?>" class="bfi-inputtext" data-rule-digits="true" data-msg-digits="<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_DIGIT') ?>" > 
						</div><!--/span-->
						<div class="bfi-col-md-6 bfi-col-sm-6">
					<input name="roomsmax" type="text" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_TO') ?>" value="<?php echo $roomsmax;?>" class="bfi-inputtext" data-rule-digits="true" data-msg-digits="<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_DIGIT') ?>" > 
						</div><!--/span-->
					</div>
				</div><!--/span-->
				<?php } //$showRooms ?>
				<?php if($showBaths){  
					$totalfields +=2;
				?>
				<div class="bfi_bathrooms  bfi-col-sm-2">
					<label><?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_BATHS') ?></label>
					<select name="baths" onchange="bfi_changeBaths(this);" class="">
					<?php foreach ($baths as $key => $value):?>
						<option value="<?php echo $key ?>" <?php echo ($bathsmin ."|". $bathsmax == $key)?"selected":""; ?>><?php echo $value ?></option>
					<?php endforeach; ?>
					</select>
					<input name="bathsmin" type="hidden" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_FROM') ?>" value="<?php echo $bathsmin;?>" class="bfi-inputtext" > 
					<input name="bathsmax" type="hidden" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_TO') ?>" value="<?php echo $bathsmax;?>" class="bfi-inputtext" > 
				</div><!--/span-->
				<?php } //$showBaths ?>
				<?php if (isset($listServices) && $showServicesList) { 
					$totalfields +=2;
				?>
				<?php  $countServ=0;?>
				<div class="bfi_listservices  bfi-col-sm-2">
					<div class="bfi-row">   
						<?php foreach ($listServices as $singleService){?>
							<div class="bfi-col-md-6">
							<?php $checked = '';
								if (isset($filtersServices) &&  is_array($filtersServices) && in_array($singleService->ServiceId,$filtersServices)){
									$checked = ' checked="checked"';
								}
							?>
								<label class="checkbox"><input type="checkbox" name="services"  class="checkboxservices" value="<?php echo ($singleService->ServiceId) ?>" <?php echo $checked ?> /><?php echo BFCHelper::getLanguage($singleService->Name, $language) ?></label>
							</div>
						<?php  $countServ++;
						if($countServ%2==0){
						?>
						</div>
						<div class="bfi-row">	
						<?php } ?>

						<?php } ?>
					</div>
				</div><!--/span-->
				<?php } ?>
				<?php if($showOnlyNew){  
					$totalfields +=2;
				?>
				<div class="bfi_isnewbuilding  bfi-col-sm-2">  
					<label class="checkbox"><input type="checkbox" name="isnewbuilding" value="1" <?php echo $isnewbuilding ?> /><?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_ISNEWBUILDING') ?></label>
				</div><!--/span-->
				<?php } ?>

				<?php
					$widthbtn = ($totalfields % 12);
					if (($widthbtn >6)) {
					    $widthbtn = 12;
					}
				?>
				<div class="bfi-searchbutton-wrapper bfi-col-sm-<?php echo $showdirection? $widthbtn:"2"; $widthbtn ?>" id="searchButtonArea<?php echo $currModID ?>">
					<div class="" id="divBtnRealEstate">
						<a  id="BtnRealEstate<?php echo $currModID ?>" class="bfi-btn" href="javascript: void(0);"><i class="fa fa-search" aria-hidden="true"></i> <?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_SEND'); ?></a>
					</div>
				</div>
					<div class="bfi-clearboth"></div>
				<div class="bfi-powered"><a href="https://www.bookingfor.com" target="_blank">Powered by Bookingfor</a></div>

			</div><!--/span-->

			<input type="hidden" value="<?php echo uniqid('', true)?>" name="searchid" />
			<input type="hidden" value="3" name="searchtypetab" />
			<input type="hidden" value="1" name="newsearch" />
			<input type="hidden" value="0" name="limitstart" />
			<input type="hidden" name="filter_order" value="" />
			<input type="hidden" name="filter_order_Dir" value="" />
			<input type="hidden" value="<?php echo $language ?>" name="cultureCode" />
			<input type="hidden" value="<?php echo $pointsonsell ?>" name="points" id="pointsonsell<?php echo $currModID ?>" />
			<input type="hidden" value="<?php echo $services ?>" name="servicesonsell" id="servicesonsell<?php echo $currModID ?>" />
			<input type="hidden" name="availabilitytype" class="resbynighthd" value="1" />
			<input type="hidden" value="" name="stateIds" />
			<input type="hidden" value="" name="regionIds" />
			<input type="hidden" value="" name="cityIds" />
		</form>
		</div>  <!-- role="tabpanel" -->
<?php }  ?>
    </div>
</div>
<div class="bfi-clearboth"></div>

</div>
<script type="text/javascript">
var $dialog;
var currentLocation=0;
$Lng = <?php echo COM_BOOKINGFORCONNECTOR_GOOGLE_POSX?>;
$Lat = <?php echo COM_BOOKINGFORCONNECTOR_GOOGLE_POSY?>;
$googlemapsapykey = '<?php echo COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLEMAPSKEY?>';
$startzoom = <?php echo COM_BOOKINGFORCONNECTOR_GOOGLE_STARTZOOM?>;
msg1 = "<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_MSG1') ?>";
msg2 = "<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_MSG2') ?>";
if(typeof jQuery.fn.button !== 'undefined' && typeof jQuery.fn.button.noConflict !== 'undefined'){
	var btn = jQuery.fn.button.noConflict(); // reverts $.fn.button to jqueryui btn
	jQuery.fn.btn = btn; // assigns bootstrap button functionality to $.fn.btn
}

//-->
	</script>

<?php if(!empty($tablistRealEstate)){ ?>
<script type="text/javascript">


function bfi_updateHiddenValue(who,whohidden) {         
     var allVals = [];
     jQuery(who).each(function() {
       allVals.push(jQuery(this).val());
     });
     jQuery(whohidden).val(allVals.join(","));
  }

function bfi_changeBaths(currObj){
	var bathsselect = jQuery(currObj).val();
	var vals = bathsselect.split("|"); 
	var closestDiv = jQuery(currObj).closest("div");
	closestDiv.find("input[name='bathsmin']").first().val(vals[0]);
	closestDiv.find("input[name='bathsmax']").first().val(vals[1]);
}


jQuery(function($)
		{
			jQuery('#BtnRealEstate<?php echo $currModID ?>').click(function(e) {
				e.preventDefault();
				jQuery("#searchformonsellunit<?php echo $currModID ?>").submit(); 
			});
						
			jQuery('.checkboxservices').on('click',function() {
				bfi_updateHiddenValue('.checkboxservices:checked','#servicesonsell<?php echo $currModID ?>')	
			});
						
			jQuery("#searchformonsellunit<?php echo $currModID ?>").validate(
		    {
		    	invalidHandler: function(form, validator) {
                    var errors = validator.numberOfInvalids();
                    if (errors) {
                        alert(validator.errorList[0].message);

                        validator.errorList[0].element.focus();
                    }
                },
				showErrors: function(errorMap, errorList) {

						// Clean up any tooltips for valid elements
						jQuery.each(this.validElements(), function (index, element) {
							var $element = jQuery(element);

							$element.prop("title", "") // Clear the title - there is no error associated anymore
								.removeClass("bfi-error")
							if ($element.is(':data(tooltip)')) {
								$element.tooltip('destroy');
							}								  
						});
						// Create new tooltips for invalid elements
						jQuery.each(errorList, function (index, error) {
							var $element = jQuery(error.element);
							if ($element.is(':data(tooltip)')) {
								$element.tooltip('destroy');
							}								  

							$element.prop("title", error.message)
								.addClass("bfi-error")
							$element.tooltip({
								position : { my: 'center bottom', at: 'center top-10' },
								tooltipClass: 'bfi-tooltip bfi-tooltip-top '
							});
							$element.tooltip("open");

						  });
					  },
				errorClass: "bfi-error",
				highlight: function(label) {
			    },
			    success: function(label) {
					jQuery(label).remove();
			    },
				submitHandler: function(form) {
					var $form = jQuery(form);
					if($form.valid()){
						var isMacLike = navigator.platform.match(/(Mac|iPhone|iPod|iPad)/i)?true:false;
						var isIOS = navigator.platform.match(/(iPhone|iPod|iPad)/i)?true:false;				
						if(!isMacLike){
							bookingfor.waitBlockUI(msg1, msg2,img1); 
							jQuery("#BtnRealEstate<?php echo $currModID ?>").hide();
						}
						form.submit();
					}
				}

		    });

		});
		
	//-->
	</script>
<?php }  ?>

	<div id="bfi_MapDrawer<?php echo $currModID ?>" style="width:100%; height:400px; display:none;">
		<div style="width:100%; height:50px; position:relative;">
			<div class="bfi-row"> 
				<div class="bfi-col-md-6 bfi-col-sm-6">
					<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ZONE_SEARCHBYMAP_TITLE') ?>
					<a class="bfi-btn bfi-select-figure bfi-drawpoligon" onclick="javascript: drawPoligon()"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ZONE_SEARCHBYMAP_AREA') ?></a>
					<a class="bfi-btn bfi-select-figure bfi-drawcircle" onclick="javascript: drawCircle()"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ZONE_SEARCHBYMAP_CIRCLE'); ?></a>
				</div><!--/span-->
				<div class="bfi-col-md-6 bfi-col-sm-6 bfi-text-right">
					<input type="text" class="bfi-map-addresssearch" placeholder="<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ZONE_SEARCHBYMAP_ADDRESS_BUTTON') ?>" />
					<div class="bfi-btnCompleta" style="display:none;">
						<a class="bfi-btn bfi-btndelete" href="javascript: void(0);" ><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ZONE_SEARCHBYMAP_DELETE'); ?></a>
						<a class="bfi-btn bfi-btnconfirm" type="button" href="javascript: void(0);" ><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ZONE_SEARCHBYMAP_SUBMIT'); ?></a>
						<span class="bfi-spanarea"></span>
					</div>
				
				</div><!--/span-->
			</div>

		</div>
		<div class="bfi-map-canvas" style="width:100%; height:350px;"></div>
		<div class="bfi-map-tooltip"><strong><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ZONE_SEARCHBYMAP_USERGUIDE'); ?></strong></div>
	</div>


<script type="text/javascript">
<!--
var img1 = new Image(); 
var localeSetting = "<?php echo substr($language,0,2); ?>";
<?php if($showDateRange){ ?>
function insertNight<?php echo $currModID ?>(){
		var checkindate = jQuery('#<?php echo $checkinId; ?>').val();
		var checkoutdate = jQuery('#<?php echo $checkoutId; ?>').val();
		var d1 = checkindate.split("/");
		var d2 = checkoutdate.split("/");

		var from = new Date(Date.UTC(d1[2], d1[1]-1, d1[0]));
		var to   = new Date(Date.UTC(d2[2], d2[1]-1, d2[0]));

		diff  = new Date(to - from),
		days  = Math.ceil(diff/1000/60/60/24);

		var resbynight = jQuery(jQuery('#<?php echo $checkinId; ?>')).closest("form").find(".resbynighthd").first();
		if(resbynight.length){
			var resbynight_str = resbynight.val().split(",");
			if(resbynight_str.indexOf("0") !== -1 || resbynight_str.indexOf("1") !== -1 ){
				jQuery("#divcalendarnightsearch<?php echo $currModID ?>").show();
				var strSummaryDays = "" +days+" <?php echo strtolower (JTEXT::_('MOD_BOOKINGFORSEARCH_NIGHT')) ?>";
				if (jQuery(resbynight).val() == 0) {
					days += 1;
					strSummaryDays ="" +days+" <?php echo strtolower (JTEXT::_('MOD_BOOKINGFORSEARCH_DAYS')) ?>";
				}
				if(days<1){strSummaryDays ="";}
				jQuery('#calendarnight<?php echo $durationId ?>').html(strSummaryDays);
			}
		}


}

function updateTitle<?php echo $currModID ?>(classToAdd,classToRemove,title) {
	setTimeout(function() {
		bfiCalendarCheck();
		jQuery("#ui-datepicker-div").addClass(classToAdd);
		jQuery("#ui-datepicker-div").removeClass(classToRemove);

		jQuery("#ui-datepicker-div").prepend( "<div class=\"bfi-title-arrow\"></div>" );

		var resbynight = jQuery(jQuery('#<?php echo $checkinId; ?>')).closest("form").find(".resbynighthd").first();
		var checkindate = jQuery('#<?php echo $checkinId; ?>').val();
		var checkoutdate = jQuery('#<?php echo $checkoutId; ?>').val();

		var d1 = checkindate.split("/");
		var d2 = checkoutdate.split("/");

		var from = new Date(Date.UTC(d1[2], d1[1]-1, d1[0]));
		var to   = new Date(Date.UTC(d2[2], d2[1]-1, d2[0]));
		month1 = ('0' + d1[1]).slice(-2);
		month2 = ('0' + d2[1]).slice(-2);
		if (typeof Intl == 'object' && typeof Intl.NumberFormat == 'function') {
			month1 = from.toLocaleString("<?php echo substr($language,0,2); ?>", { month: "short" });              
			month2 = to.toLocaleString("<?php echo substr($language,0,2); ?>", { month: "short" });            
		}

		diff  = new Date(to - from),
		days  = Math.ceil(diff/1000/60/60/24);
		var strSummary = 'Check-in '+('0' + from.getDate()).slice(-2)+' '+ month1;
		var strSummaryDays = "(" +days+" <?php echo strtolower (JTEXT::_('MOD_BOOKINGFORSEARCH_NIGHT')) ?>)";
		if (jQuery(resbynight).val() == 0) {
			days += 1;
			strSummaryDays ="(" +days+" <?php echo strtolower (JTEXT::_('MOD_BOOKINGFORSEARCH_DAYS')) ?>)";
		}
		if(days<1){strSummaryDays ="";}

		strSummary += ' Check-out '+('0' + to.getDate()).slice(-2)+' '+ month2 +' '+d2[2]+' ' + strSummaryDays;
		jQuery('#ui-datepicker-div').attr('data-before',strSummary);
	}, 1);
}

function insertCheckinTitle<?php echo $currModID ?>() {
	setTimeout(function() {updateTitle<?php echo $currModID ?>("bfi-checkin","bfi-checkout","Checkin")}, 1);
}
function insertCheckoutTitle<?php echo $currModID ?>() {
	setTimeout(function() {updateTitle<?php echo $currModID ?>("bfi-checkout","bfi-checkin","Checkout")}, 1);
}

function closed<?php echo $currModID ?>(date) {
	var checkindate = jQuery('#<?php echo $checkinId; ?>').val();
	var checkoutdate = jQuery('#<?php echo $checkoutId; ?>').val();
	var strDate = ("0" + date.getDate()).slice(-2) + "/" + ("0" + (date.getMonth()+1)).slice(-2) + "/" + date.getFullYear();

	var d1 = checkindate.split("/");
	var d2 = checkoutdate.split("/");
	var c = strDate.split("/");

	var from = new Date(d1[2], d1[1]-1, d1[0]);
	var to   = new Date(d2[2], d2[1]-1, d2[0]);
	var check = new Date(c[2], c[1]-1, c[0]);
	var daysToDisable = <?php echo $blockdays;?>;
	var monthsToDisable = <?php echo $blockmonths;?>;
	var day = date.getDay();
	var dayEnabled = true
	if (jQuery.inArray(day, daysToDisable) != -1) {
		dayEnabled = false;
	}

	var month = date.getMonth()+1;
	if (jQuery.inArray(month, monthsToDisable) != -1) {
		dayEnabled = false;
	}
	var holydayTitle = "";
	var holydayCss = "";
	
	var currDay =  ("0" + date.getDate()).slice(-2) + "" + ("0" + (date.getMonth()+1)).slice(-2);
	var currIdxHoliday = jQuery.inArray(currDay, bookingfor.holydays);
//	console.log(currDay);
	if (currIdxHoliday != -1) {
		holydayTitle = bookingfor.holydaysTitle[currIdxHoliday];
		holydayCss = "bfi-date-holidays ";
	}
	currDay =  ("0" + date.getDate()).slice(-2) + "" + ("0" + (date.getMonth()+1)).slice(-2) + date.getFullYear();
	currIdxHoliday = jQuery.inArray(currDay, bookingfor.holydays);
//	console.log(currDay);
	if (currIdxHoliday != -1) {
		holydayTitle = bookingfor.holydaysTitle[currIdxHoliday];
		holydayCss = "bfi-date-holidays ";
	}
	
	arr = [dayEnabled, holydayCss, holydayTitle];  
	if(check.getTime() == from.getTime()) {
		arr = [dayEnabled, holydayCss + ' date-start-selected', holydayTitle ];
	}
	if(check.getTime() == to.getTime()) {
		arr = [dayEnabled, holydayCss + ' date-end-selected', holydayTitle];  
	}
	if(check > from && check < to) {
		arr = [dayEnabled, holydayCss + ' date-selected', holydayTitle];
	}
	return arr;
}

function printChangedDate<?php echo $currModID ?>() {
	var checkindate = jQuery('#<?php echo $checkinId; ?>').val();
	var checkoutdate = jQuery('#<?php echo $checkoutId; ?>').val();

	var d1 = checkindate.split("/");
	var d2 = checkoutdate.split("/");

	var from = new Date(d1[2], d1[1]-1, d1[0]);
	var to   = new Date(d2[2], d2[1]-1, d2[0]);

	day1  = ('0' + from.getDate()).slice(-2),  
	month1 = from.toLocaleString("<?php echo substr($language,0,2); ?>", { month: "short" }),              
	year1 =  from.getFullYear(),
	weekday1 = from.toLocaleString("<?php echo substr($language,0,2); ?>", { weekday: "short" });

	day2  = ('0' + to.getDate()).slice(-2),  
	month2 = to.toLocaleString("<?php echo substr($language,0,2); ?>", { month: "short" }),              
	year2 =  to.getFullYear(),
	weekday2 = to.toLocaleString("<?php echo substr($language,0,2); ?>", { weekday: "short" });

	var btnTextCheckin = "<span class='bfi-weekdayname'>"+weekday1+" </span>"+day1+" "+month1+"<span class='bfi-year'> "+year1+"</span>";
	var btnTextCheckout = "<span class='bfi-weekdayname'>"+weekday2+" </span>"+day2+" "+month2+"<span class='bfi-year'> "+year2+"</span>";
	var btnTextChildrenagesat = "<?php echo strtolower (JTEXT::_('MOD_BOOKINGFOR_ON')) ?> " + day2 + " " + month2 + " " + year2;
	
	if (typeof Intl == 'object' && typeof Intl.NumberFormat == 'function') {
		btnTextCheckin = "<span class='bfi-weekdayname'>"+weekday1+" </span>"+day1+" "+month1+"<span class='bfi-year'> "+year1+"</span>";
		btnTextCheckout = "<span class='bfi-weekdayname'>"+weekday2+" </span>"+day2+" "+month2+"<span class='bfi-year'> "+year2+"</span>";
		btnTextChildrenagesat = "<?php echo strtolower (JTEXT::_('MOD_BOOKINGFOR_ON')) ?> " + day2 + " " + month2 + " " + year2;
	} else {
		btnTextCheckin = "<span class='bfi-weekdayname'>"+weekday1+" </span>"+day1+"/"+d1[1]+"<span class='bfi-year'>/"+d1[2]+"</span>";
		btnTextCheckout = "<span class='bfi-weekdayname'>"+weekday2+" </span>"+day2+"/"+d2[1]+"<span class='bfi-year'>/"+d2[2]+"</span>";
		btnTextChildrenagesat = "<?php echo strtolower (JTEXT::_('MOD_BOOKINGFOR_ON')) ?> " +  day2 + " " + d2[1] + " " + d2[2];
	}
	var windowsize =  jQuery(window).width();
	if(windowsize > 769 && windowsize < 1300){
		jQuery('.checkinli<?php echo $currModID ?>').html(checkindate);
		jQuery('.checkoutli<?php echo $currModID ?>').html(checkoutdate);

	}else{
	jQuery('.checkinli<?php echo $currModID ?>').html(btnTextCheckin);
	jQuery('.checkoutli<?php echo $currModID ?>').html(btnTextCheckout);
	}
//	jQuery('.checkinli<?php echo $currModID ?>').html(btnTextCheckin);
//	jQuery('.checkoutli<?php echo $currModID ?>').html(btnTextCheckout);
	jQuery('#bfi_lblchildrenagesat<?php echo $currModID ?>').html(btnTextChildrenagesat);

}

function checkDate<?php echo $checkinId; ?>($, obj, selectedDate) {
	instance = obj.data("datepicker");
	date = $.datepicker.parseDate(
			instance.settings.dateFormat ||
			$.datepicker._defaults.dateFormat,
			selectedDate, instance.settings);
	var d = new Date(date);
	d.setDate(d.getDate());
	jQuery("#<?php echo $checkoutId; ?>").datepicker("option", "minDate", d);
}
<?php } ?>

function checkSelSearch<?php echo $currModID ?>() {
	var sel = jQuery("#merchantCategoryId<?php echo $currModID ?>")
	if (sel.val()==="0")
	{
		sel.addClass("mod_bookingforsearcherror");
	}else{
		sel.removeClass("mod_bookingforsearcherror");
	}
}

function checkChildrenSearch<?php echo $currModID ?>(nch,showMsg) {
	jQuery("#mod_bookingforsearch-childrenages<?php echo $currModID ?>").hide();
	jQuery("#mod_bookingforsearch-childrenages<?php echo $currModID ?> select").hide();
	if (nch > 0) {
		jQuery("#mod_bookingforsearch-childrenages<?php echo $currModID ?> select").each(function(i) {
			if (i < nch) {
				var id=jQuery(this).attr('id');
				jQuery(this).css('display', 'inline-block');
			}
		});
		jQuery("#mod_bookingforsearch-childrenages<?php echo $currModID ?>").show();
		if(showMsg===1) { 
			showpopover<?php echo $currModID ?>();
		}
	}

}
	function bfiAffix<?php echo $currModID ?>() {
		var windowsize =  jQuery(window).width();
		if (windowsize > 767) {
			if (window.pageYOffset >= 180) {
				jQuery(".bfi-affix-top<?php echo $currModID ?>").addClass("bfiAffixTop");
			} else {
				jQuery(".bfi-affix-top<?php echo $currModID ?>").removeClass("bfiAffixTop");
			}
		}else{
				jQuery(".bfi-affix-top<?php echo $currModID ?>").removeClass("bfiAffixTop");
		}
		//hide calendar
		jQuery("#<?php echo $checkinId; ?>").datepicker("hide");
		jQuery("#<?php echo $checkoutId; ?>").datepicker("hide");

	}
jQuery(function() {
<?php 
if($showdirection && $fixedontop){
?>
	window.onscroll = function() {bfiAffix<?php echo $currModID ?>()};
<?php 
}
?>
	//correction for joomla!
	var baseElements = document.getElementsByTagName("base"); 
	if( baseElements.length>0 ) {
		baseElements[0].href = document.location.href;
	}
<?php if($showDateRange){ ?>
	jQuery("#<?php echo $checkinId; ?>").datepicker({
		defaultDate: "+2d"
		,dateFormat: "dd/mm/yy"
		, numberOfMonths: parseInt("<?php echo COM_BOOKINGFORCONNECTOR_MONTHINCALENDAR;?>")
		, minDate: '+0d'
		, onClose: function(dateText, inst) { 
			jQuery(this).attr("disabled", false); 
			insertNight<?php echo $currModID ?>() 
			//jQuery("#<?php echo $checkoutId; ?>").datepicker("show");
				}
		, beforeShow: function(dateText, inst) { 
			jQuery(this).attr("disabled", true); 
			jQuery(inst.dpDiv).addClass('bfi-calendar'); 
			insertCheckinTitle<?php echo $currModID ?>(); 
			var windowsize =  jQuery(window).width();
			jQuery(inst.dpDiv)[0].className = jQuery(inst.dpDiv)[0].className.replace(/(^|\s)bfi-calendar-affixtop\S+/g, '');
			if(jQuery(this).closest("div.bfiAffixTop").length || windowsize < 767){
				jQuery(inst.dpDiv).addClass('bfi-calendar-affixtop<?php echo $currModID; ?>'); 
			}
		}
		, onChangeMonthYear: function(dateText, inst) { insertCheckinTitle<?php echo $currModID ?>(); }
		, showOn: "button"
		, beforeShowDay: closed<?php echo $currModID ?>
		, buttonText: "<div class='checkinli<?php echo $currModID; ?>'><span class='bfi-weekdayname'><?php echo $checkin->format("D");?> </span><?php echo $checkin->format("d") ;?> <?php echo $checkin->format("M");?><span class='bfi-year'> <?php echo $checkin->format("Y"); ?></span></div>"
		, onSelect: function(date) { 
			checkDate<?php echo $checkinId; ?>(jQuery, jQuery(this), date); 
			printChangedDate<?php echo $currModID ?>();
			}
		, firstDay: 1
	});
	jQuery("#<?php echo $checkoutId; ?>").datepicker({
		defaultDate: "+2d"
		,dateFormat: "dd/mm/yy"
		, numberOfMonths: parseInt("<?php echo COM_BOOKINGFORCONNECTOR_MONTHINCALENDAR;?>")
		, onClose: function(dateText, inst) { jQuery(this).attr("disabled", false); insertNight<?php echo $currModID ?>();  }
		, beforeShow: function(dateText, inst) { 
			jQuery(this).attr("disabled", true); 
			jQuery(inst.dpDiv).addClass('bfi-calendar'); 
			insertCheckoutTitle<?php echo $currModID ?>(); 
			var windowsize =  jQuery(window).width();
			jQuery(inst.dpDiv)[0].className = jQuery(inst.dpDiv)[0].className.replace(/(^|\s)bfi-calendar-affixtop\S+/g, '');
			if(jQuery(this).closest("div.bfiAffixTop").length || windowsize < 767){
				jQuery(inst.dpDiv).addClass('bfi-calendar-affixtop<?php echo $currModID; ?>'); 
			}
		}
		, onSelect: function(date) { printChangedDate<?php echo $currModID ?>(); }
		, onChangeMonthYear: function(dateText, inst) { insertCheckoutTitle<?php echo $currModID ?>(); }
		, minDate: '+0d'
		, showOn: "button"
		, beforeShowDay: closed<?php echo $currModID ?>
		, buttonText: "<div class='checkoutli<?php echo $currModID; ?>'><span class='bfi-weekdayname'><?php echo $checkout->format("D");?> </span><?php echo $checkout->format("d") ;?> <?php echo $checkout->format("M");?><span class='bfi-year'> <?php echo $checkout->format("Y"); ?></span></div>"
		, firstDay: 1
	});
<?php } ?>
	jQuery.widget.bridge('bfiTabs', jQuery.ui.tabs );

	jQuery("#bfisearch<?php echo $currModID ?>").bfiTabs();


	function bfiCheckTabsCollapsible<?php echo $currModID ?>() {
		var windowsize =  jQuery(window).width();
		var collapsibleTabs = true;
		var isMacLike = navigator.platform.match(/(Mac|iPhone|iPod|iPad)/i)?true:false;

		var tabCount = jQuery('#bfisearch<?php echo $currModID ?> >ul >li').length;
		if (tabCount>1)
		{
		if ((jQuery("#bfisearch<?php echo $currModID ?>").closest("div.bfialwaisopen").length || windowsize > 767) && !isMacLike) {
			collapsibleTabs = false;
		}
		if ((jQuery("#bfisearch<?php echo $currModID ?>").closest("div.bfiAffixBottom").length && windowsize < 769)) {
			collapsibleTabs = true;
		}
		}else{
				collapsibleTabs = false;
		}
		
		jQuery("#bfisearch<?php echo $currModID ?>").bfiTabs("option", "collapsible", collapsibleTabs);
	}

	jQuery(window).resize(function(){
		jQuery('#bfi_lblchildrenages<?php echo $currModID ?>').webuiPopover("hide");
		if (typeof bfiCheckTabsCollapsible<?php echo $currModID ?> !== "undefined" ){bfiCheckTabsCollapsible<?php echo $currModID ?>() ;}
<?php if($showDateRange){ ?>
		if (typeof printChangedDate<?php echo $currModID ?> !== "undefined" ){printChangedDate<?php echo $currModID ?>() ;}
<?php } ?>

	});
	if (typeof bfiCheckTabsCollapsible<?php echo $currModID ?> !== "undefined" ){bfiCheckTabsCollapsible<?php echo $currModID ?>() ;}
<?php if($showDateRange){ ?>
	if (typeof printChangedDate<?php echo $currModID ?> !== "undefined" ){setTimeout(function() {printChangedDate<?php echo $currModID ?>()}, 1);}
<?php } ?>

	var windowsizeStart =  jQuery(window).width();
	if (windowsizeStart > 767) {
	var index = jQuery('#bfisearch<?php echo $currModID ?> li[data-searchtypeid="<?php echo $searchtypetab ?>"] a').parent().index();
	jQuery("#bfisearch<?php echo $currModID ?>").bfiTabs("option", "active", index);
	}else{
		jQuery("#bfisearch<?php echo $currModID ?>").bfiTabs("option", "active", false);
	}

	jQuery('#BtnCatalog<?php echo $currModID ?>').click(function(e) {
		e.preventDefault();
		jQuery("#searchformcatalog<?php echo $currModID ?>").submit(); 
	});
	jQuery("#searchformcatalog<?php echo $currModID ?>").validate(
	{
		invalidHandler: function(form, validator) {
			var errors = validator.numberOfInvalids();
			if (errors) {
				validator.errorList[0].element.focus();
			}
		},
		errorClass: "bfi-error",
		highlight: function(label) {
		},
		success: function(label) {
			jQuery(label).remove();
		},
		submitHandler: function(form) {
			var $form = jQuery(form);
			if($form.valid()){
				if ($form.data('submitted') === true) {
					 return false;
				} else {
					// Mark it so that the next submit can be ignored
					$form.data('submitted', true);
					var isMacLike = navigator.platform.match(/(Mac|iPhone|iPod|iPad)/i)?true:false;
					var isIOS = navigator.platform.match(/(iPhone|iPod|iPad)/i)?true:false;				
					if(!isMacLike){
							//bookingfor.waitBlockUI(msg1, msg2,img1); 
							//jQuery("#BtnResource<?php echo $currModID ?>").hide();
							var iconBtn = jQuery("#BtnCatalog<?php echo $currModID ?>").find("i").first();
							iconBtn.removeClass("fa-search").addClass("fa-spinner fa-spin ");
							jQuery("#BtnCatalog<?php echo $currModID ?>").prop('disabled', true);

					}
					form.submit();
				}
			}
		}
	});


	jQuery('#BtnResource<?php echo $currModID ?>').click(function(e) {
		e.preventDefault();
		jQuery("#searchform<?php echo $currModID ?>").submit(); 
	});
	jQuery("#searchform<?php echo $currModID ?>").validate(
	{
		invalidHandler: function(form, validator) {
			var errors = validator.numberOfInvalids();
			if (errors) {
				validator.errorList[0].element.focus();
			}
		},
		errorClass: "bfi-error",
		highlight: function(label) {
		},
		success: function(label) {
			jQuery(label).remove();
		},
		submitHandler: function(form) {
			var $form = jQuery(form);
			if($form.valid()){
				if ($form.data('submitted') === true) {
					 return false;
				} else {
					// Mark it so that the next submit can be ignored
					$form.data('submitted', true);
				var isMacLike = navigator.platform.match(/(Mac|iPhone|iPod|iPad)/i)?true:false;
				var isIOS = navigator.platform.match(/(iPhone|iPod|iPad)/i)?true:false;				
				if(!isMacLike){
						//bookingfor.waitBlockUI(msg1, msg2,img1); 
						//jQuery("#BtnResource<?php echo $currModID ?>").hide();
						var iconBtn = jQuery("#BtnResource<?php echo $currModID ?>").find("i").first();
						iconBtn.removeClass("fa-search").addClass("fa-spinner fa-spin ");
						jQuery("#BtnResource<?php echo $currModID ?>").prop('disabled', true);

				}
				form.submit();
				}
			}
		}

	});
	
	<?php if($showSearchText) { ?>

	jQuery("#searchtext<?php echo $currModID ?>").autocomplete({
        source: function( request, response ) {
          jQuery.getJSON(bfi_variable.bfi_urlCheck, {
            task: "SearchByText",
            bfi_term: request.term,
			bfi_maxresults: 5
          }, function(data) {
			  if (data.length) {
				jQuery.each(data, function(key, item) {
					var currentVal = "";
					if (item.StateId) { currentVal = "stateIds|" + item.StateId; }
					if (item.RegionId) { currentVal = "regionIds|" + item.RegionId; }
					if (item.CityId) { currentVal = "cityIds|" + item.CityId; }
					if (item.ZoneId) { currentVal = "locationzone|" + item.ZoneId; }
					if (item.MerchantCategoryId) { currentVal = "merchantCategoryId|" + item.MerchantCategoryId; }
					if (item.ProductCategoryId) { currentVal = "masterTypeId|" + item.ProductCategoryId; }
					if (item.MerchantId) { currentVal = "merchantIds|" + item.MerchantId; }
					if (item.MerchantTagId) { currentVal = "merchantTagIds|" + item.MerchantTagId; }
					if (item.ProductTagId) { currentVal = "productTagIds|" + item.ProductTagId; }
					item.Value=currentVal;
				});				  
				response(data);
			  } else {
				  response([{
					  Name: "<?php echo JTEXT::_('MOD_BOOKINGFOR_NORESULT') ?>"
				  }]);
			  }
		  });
        },
		/*response: function( event, ui ) {
			jQuery(this).removeClass("ui-autocomplete-loading");
		},*/
		minLength: 2,
		delay: 250,
		select: function( event, ui ) {
			var selectedVal = ui.item.Value;
//			var selectedVal = jQuery(event.srcElement).attr("data-value");
			if (selectedVal.length) {
				jQuery("#searchtext<?php echo $currModID ?>").closest("form").find("[name=stateIds],[name=regionIds],[name=cityIds],[name=locationzone],[name=merchantCategoryId],[name=masterTypeId],[name=merchantIds],[name=merchantTagIds],[name=productTagIds]").val("");
				jQuery("#searchtext<?php echo $currModID ?>").closest("form").find("[name=searchTermValue]").val(selectedVal);
				jQuery("#searchtext<?php echo $currModID ?>").closest("form").find("[name=" + selectedVal.split('|')[0] + "]").val(selectedVal.split('|')[1]);
				event.preventDefault();
				jQuery(this).val(ui.item.Name);
			}
			//log( "Selected: " + ui.item.value + " aka " + ui.item.id );
		},
		open: function () {
			jQuery(this).data("uiAutocomplete").menu.element.addClass("bfi-autocomplete");
	    }
	});

	jQuery("#searchtext<?php echo $currModID ?>").data( "ui-autocomplete" )._renderItem = function( ul, item ) {
		var currentVal = "";
		if (item.StateId) { currentVal = "stateIds|" + item.StateId; }
		if (item.RegionId) { currentVal = "regionIds|" + item.RegionId; }
		if (item.CityId) { currentVal = "cityIds|" + item.CityId; }
		if (item.ZoneId) { currentVal = "locationzone|" + item.ZoneId; }
		if (item.MerchantCategoryId) { currentVal = "merchantCategoryId|" + item.MerchantCategoryId; }
		if (item.ProductCategoryId) { currentVal = "masterTypeId|" + item.ProductCategoryId; }
		if (item.MerchantId) { currentVal = "merchantIds|" + item.MerchantId; }
		if (item.MerchantTagId) { currentVal = "merchantTagIds|" + item.MerchantTagId; }
		if (item.ProductTagId) { currentVal = "productTagIds|" + item.ProductTagId; }
		
		var text = item.Name;
		if (item.StateId || item.RegionId || item.CityId || item.ZoneId) { text = '<i class="fa fa-map-marker"></i>&nbsp;' + text; }
		if (item.MerchantCategoryId || item.ProductCategoryId || item.MerchantId) { text = '<i class="fa fa-building"></i>&nbsp;' + text; }
		if (item.MerchantTagId || item.ProductTagId) { text = '<i class="fa fa-tag"></i>&nbsp;' + text; }
		if (currentVal.length) {
			return jQuery( "<li>" ).attr( "data-value", currentVal).html(text).appendTo(ul);
		} else {
			return jQuery( "<li>" ).attr( "data-value", "").html(text).addClass("ui-state-disabled").appendTo(ul);
		}
	};

	<?php } ?>

	<?php if($showSearchTextOnSell) { ?>
	// vendite
	jQuery("#searchtextonsell<?php echo $currModID ?>").autocomplete({
        source: function( request, response ) {
          jQuery.getJSON(bfi_variable.bfi_urlCheck, {
            task: "SearchByText",
            bfi_term: request.term,
			bfi_maxresults: 5,
			bfi_onlyLocations: 1,
          }, function(data) {
			  if (data.length) {
				jQuery.each(data, function(key, item) {
					var currentVal = "";
					if (item.StateId) { currentVal = "stateIds|" + item.StateId; }
					if (item.RegionId) { currentVal = "regionIds|" + item.RegionId; }
					if (item.CityId) { currentVal = "cityIds|" + item.CityId; }
					if (item.ZoneId) { currentVal = "locationzone|" + item.ZoneId; }
			//		if (item.MerchantCategoryId) { currentVal = "merchantCategoryId|" + item.MerchantCategoryId; }
			//		if (item.ProductCategoryId) { currentVal = "masterTypeId|" + item.ProductCategoryId; }
			//		if (item.MerchantId) { currentVal = "merchantIds|" + item.MerchantId; }
			//		if (item.MerchantTagId) { currentVal = "merchantTagIds|" + item.MerchantTagId; }
			//		if (item.ProductTagId) { currentVal = "productTagIds|" + item.ProductTagId; }
					item.Value=currentVal;
				});				  
				response(data);
			  } else {
				  response([{
					  Name: "<?php echo JTEXT::_('MOD_BOOKINGFOR_NORESULT') ?>"
				  }]);
			  }
		  });
        },
		/*response: function( event, ui ) {
			jQuery(this).removeClass("ui-autocomplete-loading");
		},*/
		minLength: 2,
		delay: 250,
		select: function( event, ui ) {
			var selectedVal = ui.item.Value;
//			var selectedVal = jQuery(event.srcElement).attr("data-value");
			if (selectedVal.length) {
				jQuery("#searchtextonsell<?php echo $currModID ?>").closest("form").find("[name=stateIds],[name=regionIds],[name=cityIds],[name=locationzone]").val("");
				jQuery("#searchtextonsell<?php echo $currModID ?>").closest("form").find("[name=searchTermValue]").val(selectedVal);
				jQuery("#searchtextonsell<?php echo $currModID ?>").closest("form").find("[name=" + selectedVal.split('|')[0] + "]").val(selectedVal.split('|')[1]);
				event.preventDefault();
				jQuery(this).val(ui.item.Name);
			}
			//log( "Selected: " + ui.item.value + " aka " + ui.item.id );
		},
		open: function () {
			jQuery(this).data("uiAutocomplete").menu.element.addClass("bfi-autocomplete");
	    }
	});
	jQuery("#searchtextonsell<?php echo $currModID ?>").data( "ui-autocomplete" )._renderItem = function( ul, item ) {
		var currentVal = "";
		if (item.StateId) { currentVal = "stateIds|" + item.StateId; }
		if (item.RegionId) { currentVal = "regionIds|" + item.RegionId; }
		if (item.CityId) { currentVal = "cityIds|" + item.CityId; }
		if (item.ZoneId) { currentVal = "locationzone|" + item.ZoneId; }
//		if (item.MerchantCategoryId) { currentVal = "merchantCategoryId|" + item.MerchantCategoryId; }
//		if (item.ProductCategoryId) { currentVal = "masterTypeId|" + item.ProductCategoryId; }
//		if (item.MerchantId) { currentVal = "merchantIds|" + item.MerchantId; }
//		if (item.MerchantTagId) { currentVal = "merchantTagIds|" + item.MerchantTagId; }
//		if (item.ProductTagId) { currentVal = "productTagIds|" + item.ProductTagId; }
		
		var text = item.Name;
		if (item.StateId || item.RegionId || item.CityId || item.ZoneId) { text = '<i class="fa fa-map-marker"></i>&nbsp;' + text; }
//		if (item.MerchantCategoryId || item.ProductCategoryId || item.MerchantId) { text = '<i class="fa fa-building"></i>&nbsp;' + text; }
//		if (item.MerchantTagId || item.ProductTagId) { text = '<i class="fa fa-tag"></i>&nbsp;' + text; }
		if (currentVal.length) {
			return jQuery( "<li>" ).attr( "data-value", currentVal).html(text).appendTo(ul);
		} else {
			return jQuery( "<li>" ).attr( "data-value", "").html(text).addClass("ui-state-disabled").appendTo(ul);
		}
	};


	<?php } ?>
	
	showhideCategories<?php echo $currModID ?>();
<?php if($showDateRange){ ?>	
	insertNight<?php echo $currModID ?>();
<?php } ?>
	checkChildrenSearch<?php echo $currModID ?>(<?php echo $nch ?>,<?php echo $showChildrenagesmsg ?>);
	jQuery("#bfi-child<?php echo $currModID ?>").change(function() {
		checkChildrenSearch<?php echo $currModID ?>(jQuery(this).val(),0);
	});
	jQuery(".btnservices<?php echo $currModID ?>").click(function(e) {
		e.preventDefault();
		jQuery(this).toggleClass("active");
		var active_keys = [];
		active_keys = jQuery(".btnservices.active.btnservices<?php echo $currModID ?>").map(function(index, value){
			return jQuery(value).attr('rel');
		});
		 jQuery("#filtersServicesSearch<?php echo $currModID ?>").val(jQuery.unique(active_keys.toArray()).join(","));
	});

	//disable choosen

	try
	{
	jQuery("#bfisearch<?php echo $currModID ?>").find("select").chosen("destroy") ;
	}
	catch (e) {
	}
});

function countPersone<?php echo $currModID ?>() {
	jQuery('#bfi_lblchildrenages<?php echo $currModID ?>').webuiPopover("hide");
	var numAdults = new Number(jQuery("#bfi-adult<?php echo $currModID ?>").val() || 0);
	var numSeniores = new Number(jQuery("#bfi-senior<?php echo $currModID ?>").val() || 0);
	var numChildren = new Number(jQuery("#bfi-child<?php echo $currModID ?>").val() || 0);
	jQuery('#bfi-adult-info<?php echo $currModID ?> span').html(numAdults);
	jQuery('#bfi-senior-info<?php echo $currModID ?> span').html(numSeniores);
	jQuery('#bfi-child-info<?php echo $currModID ?> span').html(numChildren);

	
	checkChildrenSearch<?php echo $currModID ?>(numChildren,0);
	jQuery('#searchformpersons<?php echo $currModID ?>').val(numAdults + numChildren + numSeniores);
	jQuery('#searchformpersonsadult<?php echo $currModID ?>').val(numAdults);
	jQuery('#searchformpersonssenior<?php echo $currModID ?>').val(numSeniores);
	jQuery('#searchformpersonschild<?php echo $currModID ?>').val(numChildren);
	
	jQuery("#mod_bookingforsearch-childrenages<?php echo $currModID ?> select").each(function(i) {
		jQuery('#searchformpersonschild'+(i+1)+'<?php echo $currModID ?>').val(jQuery(this).val());
	});

	jQuery('#showmsgchildage<?php echo $currModID ?>').val("0");
	jQuery("#mod_bookingforsearch-childrenages<?php echo $currModID ?> select:visible option:selected").each(function(i) {
		if(jQuery(this).text()==""){
			jQuery('#showmsgchildage<?php echo $currModID ?>').val(1);
			return;
		}
	});
}

function quoteChanged<?php echo $currModID ?>() {
	countPersone<?php echo $currModID ?>();
}
function showpopover<?php echo $currModID ?>() {
		jQuery('#bfi_lblchildrenages<?php echo $currModID ?>').webuiPopover({
			content : jQuery("#bfi_childrenagesmsg<?php echo $currModID ?>").html(),
			container: document.body,
			cache: false,
			placement:"auto-bottom",
			maxWidth: "300px",
			type:'html',
			style:'bfi-webuipopover'
		});
		jQuery('#bfi_lblchildrenages<?php echo $currModID ?>').webuiPopover("show");
}

function showhideCategories<?php echo $currModID ?>() {
	var currTab = jQuery('#navbookingforsearch<?php echo $currModID ?> li.ui-tabs-active a[data-toggle="tab"]').first();
    var target = jQuery(currTab).attr("class");

	var merchantCategoriesResource =  <?php echo json_encode($merchantCategoriesResource) ?> ;

	var merchantCategoriesSelectedBooking = [<?php echo implode(',', $merchantCategoriesSelectedBooking) ?>];
    var merchantCategoriesSelectedServices = [<?php echo implode(',', $merchantCategoriesSelectedServices) ?>];
    var merchantCategoriesSelectedActivities = [<?php echo implode(',', $merchantCategoriesSelectedActivities) ?>];
    var merchantCategoriesSelectedOthers = [<?php echo implode(',', $merchantCategoriesSelectedOthers) ?>];

	var unitCategoriesResource = <?php echo json_encode($unitCategoriesResource) ?>;
	
	var unitCategoriesSelectedBooking = [<?php echo implode(',', $unitCategoriesSelectedBooking) ?>];
	var unitCategoriesSelectedServices = [<?php echo implode(',', $unitCategoriesSelectedServices) ?>];
	var unitCategoriesSelectedActivities = [<?php echo implode(',', $unitCategoriesSelectedActivities) ?>];
	var unitCategoriesSelectedOthers = [<?php echo implode(',', $unitCategoriesSelectedOthers) ?>];
	
	var currentMerchantCategoriesSelected = jQuery("#merchantCategoryId<?php echo $currModID ?>").val()?jQuery("#merchantCategoryId<?php echo $currModID ?>").val():"0";
	var currentUnitCategoriesSelected = jQuery("#masterTypeId<?php echo $currModID ?>").val()?jQuery("#masterTypeId<?php echo $currModID ?>").val():"0";

	jQuery("#merchantCategoryId<?php echo $currModID ?>").val("0");
	jQuery("#masterTypeId<?php echo $currModID ?>").val("0");
	
	var currMerchantCategory = jQuery("#merchantCategoryId<?php echo $currModID ?>");
	currMerchantCategory.find('option:gt(0)').remove().end();
	var currUnitCategory = jQuery("#masterTypeId<?php echo $currModID ?>");
	currUnitCategory.find('option:gt(0)').remove().end();

	var resbynight = jQuery(jQuery(currTab).attr("href")).find(".resbynighthd").first();
	var availabilityTypesSelectedBooking = '<?php echo implode(',', $availabilityTypesSelectedBooking) ?>';
	var availabilityTypesSelectedServices = '<?php echo implode(',', $availabilityTypesSelectedServices) ?>';
	var availabilityTypesSelectedActivities = '<?php echo implode(',', $availabilityTypesSelectedActivities) ?>';
	var availabilityTypesSelectedOthers = '<?php echo implode(',', $availabilityTypesSelectedOthers) ?>';

	var itemTypes = jQuery(jQuery(currTab).attr("href")).find(".itemtypeshd").first();
	var itemTypesSelectedBooking = '<?php echo implode(',', $itemTypesSelectedBooking) ?>';
	var itemTypesSelectedServices = '<?php echo implode(',', $itemTypesSelectedServices) ?>';
	var itemTypesSelectedActivities = '<?php echo implode(',', $itemTypesSelectedActivities) ?>';
	var itemTypesSelectedOthers = '<?php echo implode(',', $itemTypesSelectedOthers) ?>';
	var currentitemTypesSelected = itemTypes.val()?itemTypes.val():0;

	var groupResultType = jQuery(jQuery(currTab).attr("href")).find(".groupresulttypehd").first();
	var groupBySelectedBooking = '<?php echo $groupBySelectedBooking ?>';
	var groupBySelectedServices = '<?php echo $groupBySelectedServices ?>';
	var groupBySelectedActivities = '<?php echo $groupBySelectedActivities ?>';
	var groupBySelectedOthers = '<?php echo $groupBySelectedOthers ?>';
	var currentgroupResultTypeSelected = groupResultType.val()?groupResultType.val():1;

	/* -- searchtab 0 -- */
	if (currTab.hasClass("searchResources")) {		
		if(availabilityTypesSelectedBooking.length>0){
			resbynight.val(availabilityTypesSelectedBooking);
			if((availabilityTypesSelectedBooking =="0" || availabilityTypesSelectedBooking =="1" || availabilityTypesSelectedBooking =="0,1" ) ){
				jQuery("#divcalendarnightsearch<?php echo $currModID ?>").show();
			}
		}


		jQuery("#searchtypetab<?php echo $currModID ?>").val("0");
<?php if($showDateRange){ ?>	

		var d = jQuery('#<?php echo $checkinId; ?>').datepicker('getDate');
		if (jQuery(resbynight).val() == 1) {
			d.setDate(d.getDate() + 1);
		}
		jQuery('#<?php echo $checkoutId; ?>').datepicker("option", "minDate", d);
		jQuery('#<?php echo $checkoutId; ?>').datepicker("option", "maxDate", Infinity);
		if (jQuery('#<?php echo $checkoutId; ?>').datepicker("getDate") <= d) {
			jQuery('#<?php echo $checkoutId; ?>').datepicker("setDate", Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
		}
<?php } ?>

		if(itemTypesSelectedBooking.length>0){
			itemTypes.val(itemTypesSelectedBooking);
		}
		if(groupBySelectedBooking.length>0){
			groupResultType.val(groupBySelectedBooking);
		}
		if(merchantCategoriesSelectedBooking.length>0){
			jQuery("#merchantCategoryId<?php echo $currModID ?>").closest("div").show();
            for (var i = 0; i < merchantCategoriesSelectedBooking.length; i++) {
				var currMC = merchantCategoriesResource[merchantCategoriesSelectedBooking[i]];
                currMerchantCategory.append(jQuery('<option>').text(currMC).attr('value', merchantCategoriesSelectedBooking[i]));
            }
			currMerchantCategory.find('option:eq(0)').val('<?php echo implode(',', $merchantCategoriesSelectedBooking) ?>');

		}else{
			jQuery("#merchantCategoryId<?php echo $currModID ?>").closest("div").hide();
			currMerchantCategory.find('option:eq(0)').val("0");
		}
		if(unitCategoriesSelectedBooking.length>0){
			jQuery("#masterTypeId<?php echo $currModID ?>").closest("div").show();
            for (var i = 0; i < unitCategoriesSelectedBooking.length; i++) {
				var currUC = unitCategoriesResource[unitCategoriesSelectedBooking[i]];
                currUnitCategory.append(jQuery('<option>').text(currUC).attr('value', unitCategoriesSelectedBooking[i]));
            }
			currUnitCategory.find('option:eq(0)').val('<?php echo implode(',', $unitCategoriesSelectedBooking) ?>');
		}else{
			jQuery("#masterTypeId<?php echo $currModID ?>").closest("div").hide();
			currUnitCategory.find('option:eq(0)').val("0");
		}
		if(currentMerchantCategoriesSelected.indexOf(",")==-1 && jQuery.inArray(Number(currentMerchantCategoriesSelected), merchantCategoriesSelectedBooking) != -1){
			jQuery("#merchantCategoryId<?php echo $currModID ?>").val(currentMerchantCategoriesSelected);
		}
		if(currentUnitCategoriesSelected.indexOf(",")==-1 && jQuery.inArray(Number(currentUnitCategoriesSelected), unitCategoriesSelectedBooking) != -1){
			jQuery("#masterTypeId<?php echo $currModID ?>").val(currentUnitCategoriesSelected);
		}
	}
	/* -- searchtab 1 -- */
	if (currTab.hasClass("searchServices")) {
		if(availabilityTypesSelectedServices.length>0){
			resbynight.val(availabilityTypesSelectedServices);
			if((availabilityTypesSelectedServices =="0" || availabilityTypesSelectedServices =="1"  || availabilityTypesSelectedServices =="0,1" ) ){
				jQuery("#divcalendarnightsearch<?php echo $currModID ?>").show();
			}
		}
	   jQuery("#searchtypetab<?php echo $currModID ?>").val("1");

		if(itemTypesSelectedServices.length>0){
			itemTypes.val(itemTypesSelectedServices);
		}
		if(groupBySelectedServices.length>0){
			groupResultType.val(groupBySelectedServices);
		}
		if(merchantCategoriesSelectedServices.length>0){
			jQuery("#merchantCategoryId<?php echo $currModID ?>").closest("div").show();
            for (var i = 0; i < merchantCategoriesSelectedServices.length; i++) {
				var currMC = merchantCategoriesResource[merchantCategoriesSelectedServices[i]];
                currMerchantCategory.append(jQuery('<option>').text(currMC).attr('value', merchantCategoriesSelectedServices[i]));
            }
			currMerchantCategory.find('option:eq(0)').val('<?php echo implode(',', $merchantCategoriesSelectedServices) ?>');
		}else{
			jQuery("#merchantCategoryId<?php echo $currModID ?>").closest("div").hide();
			currMerchantCategory.find('option:eq(0)').val("0");
		}
		if(unitCategoriesSelectedServices.length>0){
			jQuery("#masterTypeId<?php echo $currModID ?>").closest("div").show();
            for (var i = 0; i < unitCategoriesSelectedServices.length; i++) {
				var currUC = unitCategoriesResource[unitCategoriesSelectedServices[i]];
                currUnitCategory.append(jQuery('<option>').text(currUC).attr('value', unitCategoriesSelectedServices[i]));
            }
			currUnitCategory.find('option:eq(0)').val('<?php echo implode(',', $unitCategoriesSelectedServices) ?>');
		}else{
			jQuery("#masterTypeId<?php echo $currModID ?>").closest("div").hide();
			currUnitCategory.find('option:eq(0)').val("0");
		}
		if(currentMerchantCategoriesSelected.indexOf(",")==-1 && jQuery.inArray(Number(currentMerchantCategoriesSelected), merchantCategoriesSelectedServices) != -1){
			jQuery("#merchantCategoryId<?php echo $currModID ?>").val(currentMerchantCategoriesSelected);
		}
		if(currentUnitCategoriesSelected.indexOf(",")==-1 && jQuery.inArray(Number(currentUnitCategoriesSelected), unitCategoriesSelectedServices) != -1){
			jQuery("#masterTypeId<?php echo $currModID ?>").val(currentUnitCategoriesSelected);
		}
	}
	/* -- searchtab 2 -- */
	if (currTab.hasClass("searchTimeSlots")) {
		if(availabilityTypesSelectedActivities.length>0){
			resbynight.val(availabilityTypesSelectedActivities);
			if((availabilityTypesSelectedActivities =="0" || availabilityTypesSelectedActivities =="1"  || availabilityTypesSelectedActivities =="0,1" ) ){
				jQuery("#divcalendarnightsearch<?php echo $currModID ?>").show();
			}
		}
	   jQuery("#searchtypetab<?php echo $currModID ?>").val("2");

		if(itemTypesSelectedActivities.length>0){
			itemTypes.val(itemTypesSelectedActivities);
		}
		if(groupBySelectedActivities.length>0){
			groupResultType.val(groupBySelectedActivities);
		}
		if(merchantCategoriesSelectedActivities.length>0){
			jQuery("#merchantCategoryId<?php echo $currModID ?>").closest("div").show();
            for (var i = 0; i < merchantCategoriesSelectedActivities.length; i++) {
				var currMC = merchantCategoriesResource[merchantCategoriesSelectedActivities[i]];
                currMerchantCategory.append(jQuery('<option>').text(currMC).attr('value', merchantCategoriesSelectedActivities[i]));
            }
			currMerchantCategory.find('option:eq(0)').val('<?php echo implode(',', $merchantCategoriesSelectedActivities) ?>');
		}else{
			jQuery("#merchantCategoryId<?php echo $currModID ?>").closest("div").hide();
			currMerchantCategory.find('option:eq(0)').val("0");
		}
		if(unitCategoriesSelectedActivities.length>0){
			jQuery("#masterTypeId<?php echo $currModID ?>").closest("div").show();
            for (var i = 0; i < unitCategoriesSelectedActivities.length; i++) {
				var currUC = unitCategoriesResource[unitCategoriesSelectedActivities[i]];
                currUnitCategory.append(jQuery('<option>').text(currUC).attr('value', unitCategoriesSelectedActivities[i]));
            }
			currUnitCategory.find('option:eq(0)').val('<?php echo implode(',', $unitCategoriesSelectedActivities) ?>');
		}else{
			jQuery("#masterTypeId<?php echo $currModID ?>").closest("div").hide();
			currUnitCategory.find('option:eq(0)').val("0");
		}
		if(currentMerchantCategoriesSelected.indexOf(",")==-1 && jQuery.inArray(Number(currentMerchantCategoriesSelected), merchantCategoriesSelectedActivities) != -1){
			jQuery("#merchantCategoryId<?php echo $currModID ?>").val(currentMerchantCategoriesSelected);
		}
		if(currentUnitCategoriesSelected.indexOf(",")==-1 && jQuery.inArray(Number(currentUnitCategoriesSelected), unitCategoriesSelectedActivities) != -1){
			jQuery("#masterTypeId<?php echo $currModID ?>").val(currentUnitCategoriesSelected);
		}
	}
	/* -- searchtab 4 -- */
	if (currTab.hasClass("searchOthers")) {
		//currTabImg.attr('src', "<?php echo JURI::base() ?>/images/hp/2018/tab/tab4_" + localeSetting +".png");
		if(availabilityTypesSelectedOthers.length>0){
			resbynight.val(availabilityTypesSelectedOthers);
			if((availabilityTypesSelectedOthers =="0" || availabilityTypesSelectedOthers =="1"  || availabilityTypesSelectedOthers =="0,1" ) ){
				jQuery("#divcalendarnightsearch<?php echo $currModID ?>").show();
			}
		}
	   jQuery("#searchtypetab<?php echo $currModID ?>").val("4");

		if(itemTypesSelectedOthers.length>0){
			itemTypes.val(itemTypesSelectedOthers);
		}
		if(groupBySelectedOthers.length>0){
			groupResultType.val(groupBySelectedOthers);
		}
		if(merchantCategoriesSelectedOthers.length>0){
			jQuery("#merchantCategoryId<?php echo $currModID ?>").closest("div").show();
            for (var i = 0; i < merchantCategoriesSelectedOthers.length; i++) {
				var currMC = merchantCategoriesResource[merchantCategoriesSelectedOthers[i]];
                currMerchantCategory.append(jQuery('<option>').text(currMC).attr('value', merchantCategoriesSelectedOthers[i]));
            }
			currMerchantCategory.find('option:eq(0)').val('<?php echo implode(',', $merchantCategoriesSelectedOthers) ?>');
		}else{
			jQuery("#merchantCategoryId<?php echo $currModID ?>").closest("div").hide();
			currMerchantCategory.find('option:eq(0)').val("0");
		}
		if(unitCategoriesSelectedOthers.length>0){
			jQuery("#masterTypeId<?php echo $currModID ?>").closest("div").show();
            for (var i = 0; i < unitCategoriesSelectedOthers.length; i++) {
				var currUC = unitCategoriesResource[unitCategoriesSelectedOthers[i]];
                currUnitCategory.append(jQuery('<option>').text(currUC).attr('value', unitCategoriesSelectedOthers[i]));
            }
			currUnitCategory.find('option:eq(0)').val('<?php echo implode(',', $unitCategoriesSelectedOthers) ?>');
		}else{
			jQuery("#masterTypeId<?php echo $currModID ?>").closest("div").hide();
			currUnitCategory.find('option:eq(0)').val("0");
		}
		if(currentMerchantCategoriesSelected.indexOf(",")==-1 && jQuery.inArray(Number(currentMerchantCategoriesSelected), merchantCategoriesSelectedOthers) != -1){
			jQuery("#merchantCategoryId<?php echo $currModID ?>").val(currentMerchantCategoriesSelected);
		}
		if(currentUnitCategoriesSelected.indexOf(",")==-1 && jQuery.inArray(Number(currentUnitCategoriesSelected), unitCategoriesSelectedOthers) != -1){
			jQuery("#masterTypeId<?php echo $currModID ?>").val(currentUnitCategoriesSelected);
		}
	}
	
// da nascondere se solo 1;
	var currMerchantCategoryOption = jQuery("#merchantCategoryId<?php echo $currModID ?> option").length ;
	var currUnitCategoryOption = jQuery("#masterTypeId<?php echo $currModID ?> option").length ;
	if (currMerchantCategoryOption<3)
	{
			jQuery("#merchantCategoryId<?php echo $currModID ?>").closest("div").hide();
	}
	if (currUnitCategoryOption<3)
	{
			jQuery("#masterTypeId<?php echo $currModID ?>").closest("div").hide();
	}

<?php if($showDateRange){ ?>	
	jQuery("#divcalendarnightsearch<?php echo $currModID ?>").hide();
	if(resbynight.length){
		var resbynight_str = resbynight.val().split(",");
		if(resbynight_str.indexOf("0") !== -1 || resbynight_str.indexOf("1") !== -1 ){
			jQuery("#divcalendarnightsearch<?php echo $currModID ?>").show();
		}
	}
<?php } ?>

	if (currTab.hasClass("searchSelling")) {
		jQuery("#searchtypetab<?php echo $currModID ?>").val("3");
	}
}


    jQuery('#bfisearch<?php echo $currModID ?>').on('tabsactivate', function (event, ui) {
<?php if($showDateRange){ ?>	
		insertNight<?php echo $currModID ?>();
<?php } ?>
		showhideCategories<?php echo $currModID ?>();
    })

    jQuery('#bfi-showperson-text<?php echo $currModID ?>').on('click', function (event, ui) {
		jQuery( "#bfishowperson<?php echo $currModID ?>" ).dialog({
			title: "<?php echo JTEXT::_('MOD_BOOKINGFOR_GUEST') ?>",
			height: 'auto',
			width:'auto',
			resizable: true,
			position:{
				my: "center top", 
				at: "center bottom",
				of: jQuery(this)
			},
			dialogClass: 'bfi-dialog bfi-guest',
			clickOutside: true,
			clickOutsideTrigger: ".bfi-showperson-text",
		});
    })
//-->
</script>