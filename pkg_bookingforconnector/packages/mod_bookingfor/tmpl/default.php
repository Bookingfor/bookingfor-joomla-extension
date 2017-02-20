<?php 
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$pathbase = JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;
require_once $pathbase . '/helpers/htmlHelper.php';

$document   = JFactory::getDocument();
$db   = JFactory::getDBO();
$language = JFactory::getLanguage()->getTag();
$currModID = uniqid('bfisearch');

//$uri  = 'index.php?option=com_bookingforconnector&view=searchonsell';
//$formMethod= 'post';
//$formID = 'searchformonsellunit';
//
//if($params->get("type")=='multi' OR $params->get("type") == 'mono') {
//    $uri  = 'index.php?option=com_bookingforconnector&view=search';
//    $formMethod = 'get';
//    $formID = 'searchform';
//    $currModID = uniqid('search');
//}
//
//$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
//
//$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
//if ($itemId<>0)
//    $formAction = JRoute::_('index.php?Itemid='.$itemId );
//else
//    $formAction = JRoute::_($uri);


// get searchresult page...
//$searchOnSell_page = get_post( bfi_get_page_id( 'searchonsell' ) );
//$url_page_RealEstate = get_permalink( $searchOnSell_page->ID );

$uri  = 'index.php?option=com_bookingforconnector&view=searchonsell';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
if ($itemId<>0){
    $uri = 'index.php?Itemid='.$itemId ;
}
$url_page_RealEstate = JRoute::_($uri);

//$searchAvailability_page = get_post( bfi_get_page_id( 'searchavailability' ) );
//$url_page_Resources = get_permalink( $searchAvailability_page->ID );
$uri  = 'index.php?option=com_bookingforconnector&view=search';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
if ($itemId<>0){
    $uri = 'index.php?Itemid='.$itemId ;
}
$url_page_Resources = JRoute::_($uri);



//if($params->get('type')=='real'):
//    $pars = BFCHelper::getSearchOnSellParamsSession();
//else:
//    $pars = BFCHelper::getSearchParamsSession();
//endif;

// i valori sono impostati dal modulo
//$XGooglePos = 45.406947; 
//$YGooglePos = 11.892443;

$useragent=$_SERVER['HTTP_USER_AGENT'];
if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){
$numberOfMonth = 1;
}else{
    $numberOfMonth = 2;
}
$parsRealEstate = BFCHelper::getSearchOnSellParamsSession();
$parsResource = BFCHelper::getSearchParamsSession();

$searchtypetab = -1;

$contractTypeId = 0;
$searchType = "0";
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
$checkin = new DateTime();
$checkout = new DateTime();
$paxes = 2;
$paxages = array();
$masterTypeId = '';
$checkinId = uniqid('checkin');
$checkoutId = uniqid('checkout');
$durationId = uniqid('duration');
$duration = 1;


if (!empty($parsRealEstate)){
	$contractTypeId = isset($parsRealEstate['contractTypeId']) ? $parsRealEstate['contractTypeId'] : 0;
	$categoryIdRealEstate = isset($parsRealEstate['unitCategoryId']) ? $parsRealEstate['unitCategoryId']: 0;

	$zoneId = isset($parsRealEstate['zoneId']) ? $parsRealEstate['zoneId'] :0;

	if(!empty($parsRealEstate['cityId'])){
		$cityId = $parsRealEstate['cityId'] ?: 0;
	}
	$searchType = isset($parsRealEstate['searchType']) ? $parsRealEstate['searchType'] : 0;
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
	$points = isset($parsRealEstate['points']) ? $parsRealEstate['points']: null;
	$services = isset($parsRealEstate['services']) ? $parsRealEstate['services']: null;
	if (isset($parsRealEstate['isnewbuilding']) && !empty($parsRealEstate['isnewbuilding']) && $parsRealEstate['isnewbuilding'] =="1") {
		$isnewbuilding = ' checked="checked"';
	}
	$bedroomsmin = isset($parsRealEstate['bedroomsmin']) ? $parsRealEstate['bedroomsmin']: null;
	$bedroomsmax = isset($parsRealEstate['bedroomsmax']) ? $parsRealEstate['bedroomsmax']: null;
}

if (!empty($parsResource)){
		
	$checkin = !empty($parsResource['checkin']) ? $parsResource['checkin'] : new DateTime();
	$checkout = !empty($parsResource['checkout']) ? $parsResource['checkout'] : new DateTime();
	
//	$searchtypetab = isset($parsResource['searchtypetab']) ? $parsResource['searchtypetab'] : -1;

	$searchtypetab = BFCHelper::getVar('searchtypetab',(isset($parsResource['searchtypetab']) ? $parsResource['searchtypetab'] : -1));

	$zoneId = !empty($parsResource['zoneId']) ? $parsResource['zoneId'] :0;
	$paxes = !empty($parsResource['paxes']) ? $parsResource['paxes'] : 2;
	$paxages = !empty($parsResource['paxages'])? $parsResource['paxages'] :  array('18','18');
	$merchantCategoryIdResource = !empty($parsResource['merchantCategoryId'])? $parsResource['merchantCategoryId']: 0;
	$masterTypeId = !empty($parsResource['masterTypeId'])? $parsResource['masterTypeId']: 0;

	if (empty($parsResource['checkout'])){
		$checkout->modify($checkoutspan);
	}
}
$startDate =  new DateTime();
$startDate->setTime(0,0,0);
$checkin->setTime(0,0,0);
$checkout->setTime(0,0,0);

if ($checkin < $startDate){
	$checkin = $startDate;
	$checkout = clone $checkin;
    $checkout->modify($checkoutspan); 
}

if ($checkin == $checkout){
	$checkout->modify($checkoutspan);
}



$duration = $checkin->diff($checkout);

$tablistSelected = ( ! empty( $instance['tablistSelected'] ) ) ? $instance['tablistSelected'] : array();

$tablistResources = array_intersect($tablistSelected,array(0,1,2));
$tablistRealEstate = array_intersect($tablistSelected, array(3));

if(!in_array($searchtypetab,$tablistSelected)){
	$searchtypetab = -1;
}


$groupbycondominium = ( ! empty( $instance['groupbycondominium'] ) ) ? ($instance['groupbycondominium']) : '0';
$showdirection = ( ! empty( $instance['showdirection'] ) ) ? ($instance['showdirection']) : '0';
$showLocation = ( ! empty( $instance['showLocation'] ) ) ? ($instance['showLocation']) : '0';
$showMapIcon = ( ! empty( $instance['showMapIcon'] ) ) ? ($instance['showMapIcon']) : '0';
$showAccomodations = ( ! empty( $instance['showAccomodations'] ) ) ? ($instance['showAccomodations']) : '0';
$showDateRange = ( ! empty( $instance['showDateRange'] ) ) ? ($instance['showDateRange']) : '0';

$showAdult = ( ! empty( $instance['showAdult'] ) ) ? ($instance['showAdult']) : '0';
$showChildren = ( ! empty( $instance['showChildren'] ) ) ? ($instance['showChildren']) : '0';
$showSenior = ( ! empty( $instance['showSenior'] ) ) ? ($instance['showSenior']) : '0';
$showServices = ( ! empty( $instance['showServices'] ) ) ? ($instance['showServices']) : '0';
$showOnlineBooking = ( ! empty( $instance['showOnlineBooking'] ) ) ? ($instance['showOnlineBooking']) : '0';
$showMaxPrice = ( ! empty( $instance['showMaxPrice'] ) ) ? ($instance['showMaxPrice']) : '0';
$showMinFloor = ( ! empty( $instance['showMinFloor'] ) ) ? ($instance['showMinFloor']) : '0';
$showContract = ( ! empty( $instance['showContract'] ) ) ? ($instance['showContract']) : '0';

$showBedRooms = ( ! empty( $instance['showBedRooms'] ) ) ? ($instance['showBedRooms']) : '0';
$showRooms = ( ! empty( $instance['showRooms'] ) ) ? ($instance['showRooms']) : '0';
$showBaths = ( ! empty( $instance['showBaths'] ) ) ? ($instance['showBaths']) : '0';
$showOnlyNew = ( ! empty( $instance['showOnlyNew'] ) ) ? ($instance['showOnlyNew']) : '0';
$showServicesList = ( ! empty( $instance['showServicesList'] ) ) ? ($instance['showServicesList']) : '0';

$merchantCategoriesSelectedBooking = ( ! empty( $instance['merchantcategoriesbooking'] ) ) ? $instance['merchantcategoriesbooking'] : array();
$merchantCategoriesSelectedServices = ( ! empty( $instance['merchantcategoriesservices'] ) ) ? $instance['merchantcategoriesservices'] : array();
$merchantCategoriesSelectedActivities = ( ! empty( $instance['merchantcategoriesactivities'] ) ) ? $instance['merchantcategoriesactivities'] : array();
$merchantCategoriesSelectedRealEstate = ( ! empty( $instance['merchantcategoriesrealestate'] ) ) ? $instance['merchantcategoriesrealestate'] : array();
$unitCategoriesSelectedBooking = ( ! empty( $instance['unitcategoriesbooking'] ) ) ? $instance['unitcategoriesbooking'] : array();
$unitCategoriesSelectedServices = ( ! empty( $instance['unitcategoriesservices'] ) ) ? $instance['unitcategoriesservices'] : array();
$unitCategoriesSelectedActivities = ( ! empty( $instance['unitcategoriesactivities'] ) ) ? $instance['unitcategoriesactivities'] : array();
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

if($showAccomodations){
	if(!empty($merchantCategoriesSelectedBooking) || !empty($merchantCategoriesSelectedServices) || !empty($merchantCategoriesSelectedActivities) || !empty($merchantCategoriesSelectedRealEstate) ){
//		$allMerchantCategories = BFCHelper::getMerchantCategories();
		$allMerchantCategories = BFCHelper::getMerchantCategoriesForRequest($language);

		if(!empty($merchantCategoriesSelectedBooking) || !empty($merchantCategoriesSelectedServices) || !empty($merchantCategoriesSelectedActivities) ){
			$listmerchantCategoriesResource = '<option value="0">'.JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE').'</option>';
		}
		if(!empty($merchantCategoriesSelectedRealEstate) ){
			$listmerchantCategoriesRealEstate = '<option value="0">'.JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE').'</option>';
		}
		if (!empty($allMerchantCategories))
		{
			foreach($allMerchantCategories as $merchantCategory)
			{
				if(in_array($merchantCategory->MerchantCategoryId,$merchantCategoriesSelectedBooking) || in_array($merchantCategory->MerchantCategoryId,$merchantCategoriesSelectedServices) || in_array($merchantCategory->MerchantCategoryId,$merchantCategoriesSelectedActivities)){
					$merchantCategoriesResource[$merchantCategory->MerchantCategoryId] = $merchantCategory->Name;
					$listmerchantCategoriesResource .= '<option value="'.$merchantCategory->MerchantCategoryId.'" ' . ($merchantCategory->MerchantCategoryId== $merchantCategoryIdResource? 'selected':'' ).'>'.$merchantCategory->Name.'</option>';
				}
				if(in_array($merchantCategory->MerchantCategoryId,$merchantCategoriesSelectedRealEstate)){
					$merchantCategoriesRealEstate[$merchantCategory->MerchantCategoryId] = $merchantCategory->Name;
					$listmerchantCategoriesRealEstate .= '<option value="'.$merchantCategory->MerchantCategoryId.'" ' . ($merchantCategory->MerchantCategoryId== $merchantCategoryIdRealEstate? 'selected':'' ).'>'.$merchantCategory->Name.'</option>';
				}
			}
		}

	}

	$listunitCategoriesResource = "";
	if(!empty($unitCategoriesSelectedBooking) || !empty($unitCategoriesSelectedServices) || !empty($unitCategoriesSelectedActivities)) {
		$allUnitCategories =  BFCHelper::GetProductCategoryForSearch($language,1);
		if (!empty($allUnitCategories))
		{
			$listunitCategoriesResource = '<option value="0">'.JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE').'</option>';
			foreach($allUnitCategories as $unitCategory)
			{
				if(in_array($unitCategory->ProductCategoryId,$unitCategoriesSelectedBooking) || in_array($unitCategory->ProductCategoryId,$unitCategoriesSelectedServices) || in_array($unitCategory->ProductCategoryId,$unitCategoriesSelectedActivities)){
					$unitCategoriesResource[$unitCategory->ProductCategoryId] = $unitCategory->Name;
					$listunitCategoriesResource .= '<option value="'.$unitCategory->ProductCategoryId.'" ' . ($unitCategory->ProductCategoryId == $masterTypeId? 'selected':'' ).'>'.$unitCategory->Name.'</option>';
				}
			}
		}
	}


	$listunitCategoriesRealEstate = "";
	if(!empty($unitCategoriesSelectedRealEstate) ) {
		$allUnitCategoriesRealEstate =  BFCHelper::GetProductCategoryForSearch($language,2);
		if (!empty($allUnitCategoriesRealEstate))
		{
			$listunitCategoriesRealEstate = '<option value="0">'.JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE').'</option>';
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
	$listlocations = '<option value="0">'.JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE').'</option>';
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
		$zonesString = '<option value="0" selected>'.JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE').'</option>';
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

if (empty($paxages)){
	$nad = 2;
}
$countPaxes = array_count_values($paxages);
$maxchildrenAge = (int)BFCHelper::$defaultAdultsAge-1;

$nchs = array(null,null,null,null,null,null);

$nchs = array_values(array_filter($paxages, function($age) {
	if ($age < (int)BFCHelper::$defaultAdultsAge)
		return true;
	return false;
}));

array_push($nchs,null,null,null,null,null,null);

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


$showChildrenagesmsg = isset($_REQUEST['showmsgchildage']) ? $_REQUEST['showmsgchildage'] : 0;


?>
<div class="<?php echo $moduleclass_sfx ?>"><!-- per span8 e padding -->
<?php if($show_title) :?><h4 class="bookingfor_title"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_TITLE'); ?></h4><?php endif; ?>

<div class="mod_bookingforsearch">
    <ul class="nav nav-tabs nav-justified" role="tablist" id="navbookingforsearch<?php echo $currModID ?>" style="<?php echo (count($tablistSelected)>1) ?"": "display:none" ?>">
		<?php if(in_array(0, $tablistSelected)): ?>
		<?php 
		if((empty($tabActive) && $searchtypetab==-1) || $searchtypetab == 0 ){
			$tabActive = "active";
			$searchtypetab = 0;
		}else{
			$tabActive = "";  
		}
		?>
		<li class="<?php echo $tabActive ?>" role="presentation">
            <a href="#bfisearch<?php echo $currModID ?>" data-toggle="tab" aria-expanded="true" class="searchResources">
                <i class="fa fa-suitcase" aria-hidden="true"></i><br />
                <?php echo JTEXT::_('MOD_BOOKINGFOR_TABLIST_BOOKING'); ?>
            </a>
        </li>
		<?php endif;  ?>
		<?php if(in_array(1, $tablistSelected)): ?>
		<?php 
		if((empty($tabActive) && $searchtypetab==-1) || $searchtypetab == 1 ){
			$tabActive = "active";
			$searchtypetab = 1;
		}else{
			$tabActive = "";  
		}
		?>
        <li class="<?php echo $tabActive ?>" role="presentation">
            <a href="#bfisearch<?php echo $currModID ?>" data-toggle="tab" aria-expanded="true" class="searchServices">
                <i class="fa fa-suitcase" aria-hidden="true"></i><i class="fa fa-calendar" aria-hidden="true"></i><br />
                <?php echo JTEXT::_('MOD_BOOKINGFOR_TABLIST_SERVICES'); ?>
            </a>
        </li>
		<?php endif;  ?>
		<?php if(in_array(2, $tablistSelected)): ?>
		<?php 
		if((empty($tabActive) && $searchtypetab==-1) || $searchtypetab == 2 ){
			$tabActive = "active";
			$searchtypetab = 2;
		}else{
			$tabActive = "";  
		}
		?>
        <li class="<?php echo $tabActive ?>" role="presentation">
            <a href="#bfisearch<?php echo $currModID ?>" data-toggle="tab" aria-expanded="true" class="searchTimeSlots">
                <i class="fa fa-calendar" aria-hidden="true"></i><br />
                <?php echo JTEXT::_('MOD_BOOKINGFOR_TABLIST_ACTIVITIES'); ?>
            </a>
        </li>
		<?php endif;  ?>
		<?php if(in_array(3, $tablistSelected)): ?>
		<?php 
		if((empty($tabActive) && $searchtypetab==-1) || $searchtypetab == 3 ){
			$tabActive = "active";
			$searchtypetab = 3;
		}else{
			$tabActive = "";  
		}
		?>
        <li class="<?php echo $tabActive ?>" role="presentation">
            <a href="#bfisearchselling<?php echo $currModID ?>" data-toggle="tab" aria-expanded="false" class="searchSelling">
                <i class="fa fa-home" aria-hidden="true"></i><br />
                <?php echo JTEXT::_('MOD_BOOKINGFOR_TABLIST_REALESTATE'); ?>
            </a>
        </li>
		<?php endif;  ?>
    </ul>
    <div class="tab-content">
<?php if(!empty($tablistResources)): ?>
        <div role="tabpanel" id="bfisearch<?php echo $currModID ?>" class="tab-pane fade active in">
		<form action="<?php echo $url_page_Resources; ?>" method="get" id="searchform<?php echo $currModID ?>" class="bfi_form_<?php echo $showdirection?"horizontal":"vertical"; ?> ">
				<?php if(!empty($zonesString) && $showLocation){ ?>
					<div class="bfi_destination bfi_container">
						<label><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_LOCATIONZONE'); ?></label>
						<select id="locationzone" name="locationzone" class="selectinputtotal " data-live-search="true" data-width="99%">
						<?php echo $zonesString; ?>
						</select>
					</div>
				<?php } //$showLocation ?>
				<?php if(!empty($listunitCategoriesResource) && $showAccomodations){ ?>
					<div class="bfi_unitcategoriesresource bfi_container">
						<label><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ACCOMODATION'); ?></label>
						<select id="masterTypeId<?php echo $currModID ?>" name="masterTypeId" class="inputtotal">
							<?php echo $listunitCategoriesResource; ?>
						</select>
					</div>
				<?php } //$showAccomodations ?>
				<?php if(!empty($listmerchantCategoriesResource) && $showAccomodations){ ?>
					<div class="bfi_merchantcategoriesresource bfi_container">
						<label><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_TIPOLOGY'); ?></label>
						<select id="merchantCategoryId<?php echo $currModID ?>" name="merchantCategoryId" onchange="checkSelSearch<?php echo $currModID ?>();" class="inputtotal hideRent">
							<?php echo $listmerchantCategoriesResource; ?>
						</select>
					</div>
				<?php } //$showAccomodations ?>
				<?php if($showDateRange){ ?>
				<div class="bfi_showdaterange bfi_container">
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> flexalignend ">
							<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>5 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMALL ?>5">
								<label><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_FROM'); ?></label>
								<div class="dateone lastdate dateone_div checking-container">
									<input name="checkin" type="hidden" value="<?php echo $checkin->format('d/m/Y'); ?>" id="<?php echo $checkinId; ?>" />
								</div>
							</div>
							<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>5 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMALL ?>5" id="divcheckoutsearch<?php echo $currModID ?>">
								<label><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_TO'); ?></label>
								<div class="dateone lastdate lastdate_div">
									<input type="hidden" name="checkout" value="<?php echo $checkout->format('d/m/Y'); ?>" id="<?php echo $checkoutId; ?>" />
								</div>
							</div>
							
							<input type="hidden" name="AvailabilityType" class="resbynighthd" value="<?php echo $checkout->format('d/m/Y'); ?>" id="hdAvailabilityType<?php echo $checkoutId; ?>" />

							<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMALL ?>2" id="divcalendarnightsearch<?php echo $currModID ?>">
								<div class="calendarnight" id="calendarnight<?php echo $durationId ?>"><?php echo $duration->format('%a') ?></div>
								<div class="calendarnightlabel"><select data-val="true" class="resbynight" name="AvailabilityTypeselected" onchange="insertNight<?php echo $currModID ?>();">
									<option value="1"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_NIGHT') ?></option>
									<option value="0"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_DAYS') ?></option>
								</select></div>
								<div class="clearboth"></div>
							</div>
					</div>
				</div>
				<?php } //$showDateRange ?>
				
				<?php if($showAdult){?>
					<div class="bfi_showperson bfi_container">
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> form-group">
							<div class="bfi_showadult <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4"><!-- Adults -->
								<label><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ADULTS'); ?></label>
								<select name="adults" onchange="quoteChanged<?php echo $currModID ?>();" class="inputmini" style="display:inline-block !important;">
									<?php
									foreach (range(1, 10) as $number) {
										?> <option value="<?php echo $number ?>" <?php echo ($nad == $number)?"selected":""; //selected( $nad, $number ); ?>><?php echo $number ?></option><?php
									}
									?>
								</select>
							</div>
						<?php if($showSenior){?>
							<div class="bfi_showsenior <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4"><!-- Seniores -->
								<label><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_SENIORES'); ?></label>
								<select  name="seniores" onchange="quoteChanged<?php echo $currModID ?>();" class="inputmini" style="display:inline-block !important;">
									<?php
									foreach (range(0, 10) as $number) {
										?> <option value="<?php echo $number ?>" <?php echo ($nse == $number)?"selected":""; //selected( $nad, $number ); ?>><?php echo $number ?></option><?php
									}
									?>
								</select>
							</div>
						<?php }?>
						<?php if($showChildren){?>
							<div class="bfi_showchildren <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4" id="mod_bookingforsearch-children<?php echo $currModID ?>"  class="col-sm-4"><!-- n childrens -->
								<label><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_CHILDREN'); ?></label>
								<select name="children" onchange="quoteChanged<?php echo $currModID ?>();" class="inputmini" style="display:inline-block !important;">
									<?php
									foreach (range(0, 4) as $number) {
										?> <option value="<?php echo $number ?>" <?php echo ($nch == $number)?"selected":""; //selected( $nch, $number ); ?>><?php echo $number ?></option><?php
									}
									?>
								</select>
							</div>
						<?php }?>
						<?php if(!empty($services) && $showServices){?>
							<div class="bfi_showservices icons_right">
								<?php 
									foreach ($services as $service){
										$serviceActive ="";
										if (isset($filtersServices) &&  is_array($filtersServices) && in_array($service->ServiceId,$filtersServices)){
											$serviceActive =" active";			
										}
								  ?>
										<a href="javascript: void(0);" class="btn btn-xs btnservices <?php echo $serviceActive ?> btnservices<?php echo $currModID ?>" rel="<?php echo $service->ServiceId ?>"  aria-pressed="false"><i class="fa <?php echo $service->IconSrc ?>" aria-hidden="true"></i></a>
								<?php
									  }
								  ?>				
							</div>
						<?php }?>
						</div>
					</div>
					<?php if($showChildren){?>
						<div class="mod_bookingforsearch-childrenages" style="display:none;"  id="mod_bookingforsearch-childrenages<?php echo $currModID ?>">
						
					<span ><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_CHILDRENAGE'); ?></span>
					<span id="bfi_lblchildrenagesat<?php echo $currModID ?>"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_CHILDRENAGESAT') . " " .$checkout->format("d"). " " .$checkout->format("M"). " " . $checkout->format("Y") ?></span><br /><!-- Ages childrens -->	
							<div class="select_box">
							<select id="childages1" name="childages1" onchange="quoteChanged<?php echo $currModID ?>();" class="inputmini" style="display: none;">
								<option value="<?php echo COM_BOOKINGFORCONNECTOR_CHILDRENSAGE ?>" ></option>
								<?php
								foreach (range(0, $maxchildrenAge) as $number) {
									?> <option value="<?php echo $number ?>" <?php echo ($nchs[0] === $number)?"selected":""; //selected( $nchs[0], $number ); ?>><?php echo $number ?></option><?php
								}
								?>
							</select>
							<select id="childages2" name="childages2" onchange="quoteChanged<?php echo $currModID ?>();" class="inputmini" style="display: none;">
								<option value="<?php echo COM_BOOKINGFORCONNECTOR_CHILDRENSAGE ?>" ></option>
								<?php
								foreach (range(0, $maxchildrenAge) as $number) {
									?> <option value="<?php echo $number ?>" <?php echo ($nchs[1] === $number)?"selected":""; //selected( $nchs[1], $number ); ?>><?php echo $number ?></option><?php
								}
								?>
							</select>
							<?php echo $showdirection?"":"<br>"; ?> 
							<select id="childages3" name="childages3" onchange="quoteChanged<?php echo $currModID ?>();" class="inputmini" style="display: none;">
								<option value="<?php echo COM_BOOKINGFORCONNECTOR_CHILDRENSAGE ?>" ></option>
								<?php
								foreach (range(0, $maxchildrenAge) as $number) {
									?> <option value="<?php echo $number ?>" <?php echo ($nchs[2] === $number)?"selected":""; //selected( $nchs[2], $number ); ?>><?php echo $number ?></option><?php
								}
								?>
							</select>
							<select id="childages4" name="childages4" onchange="quoteChanged<?php echo $currModID ?>();" class="inputmini" style="display: none;">
								<option value="<?php echo COM_BOOKINGFORCONNECTOR_CHILDRENSAGE ?>" ></option>
								<?php
								foreach (range(0, $maxchildrenAge) as $number) {
									?> <option value="<?php echo $number ?>" <?php echo ($nchs[3] === $number)?"selected":""; //selected( $nchs[3], $number ); ?>><?php echo $number ?></option><?php
								}
								?>
							</select>
							<select id="childages5" name="childages5" onchange="quoteChanged<?php echo $currModID ?>();" class="inputmini" style="display: none;">
								<option value="<?php echo COM_BOOKINGFORCONNECTOR_CHILDRENSAGE ?>" ></option>
								<?php
								foreach (range(0, $maxchildrenAge) as $number) {
									?> <option value="<?php echo $number ?>" <?php echo ($nchs[4] === $number)?"selected":""; //selected( $nchs[4], $number ); ?>><?php echo $number ?></option><?php
								}
								?>
							</select>
							</div>
							<div class="clearboth"></div>
							<br />
						</div>
					<?php }?>
				<?php } //$showAdult?>
	        <?php if($showOnlineBooking){ ?>
	            <div class="bfi_showonlinebooking bfi_container bfsearchfilter">
					<input type="checkbox" name="bookableonly" id="bookableonly<?php echo $currModID ?>" value="1"  <?php if(!empty($bookableonly)){ echo ' checked'; }   ?>/><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_BOOKABLEONLY') ?>
				</div>
			<?php } ?>
			<div class="mod_bookingforsearch-searchbutton-wrapper bfi_container" id="divBtnResource<?php echo $currModID ?>">
				<a  id="BtnResource<?php echo $currModID ?>" class="mod_bookingforsearch-searchbutton" href="javascript: void(0);"><i class="fa fa-search" aria-hidden="true"></i> <?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_SEND'); ?></a>
			</div>
			<div class="clearboth"></div>
			<input type="hidden" value="<?php echo uniqid('', true)?>" name="searchid" />
			<input type="hidden" name="onlystay" value="1">
			<input type="hidden" name="persons" value="<?php echo $nad + $nch?>" id="searchformpersons<?php echo $currModID ?>">
			<input type="hidden" value="1" name="newsearch" />
			<input type="hidden" value="0" name="limitstart" />
			<input type="hidden" name="filter_order" value="" />
			<input type="hidden" name="filter_order_Dir" value="" />
			<input type="hidden" value="<?php echo $language ?>" name="cultureCode" />
			<input type="hidden" value="<?php echo $points ?>" name="points" id="points" />
			<input type="hidden" value="<?php echo $searchtypetab ?>" name="searchtypetab" id="searchtypetab<?php echo $currModID ?>" />
			<input type="hidden" value="0" name="showmsgchildage" id="showmsgchildage<?php echo $currModID ?>"/>
			<div class="hide" id="bfi_childrenagesmsg<?php echo $currModID ?>">
				<div class="pull-right" style="cursor:pointer;color:red">&nbsp;<i class="fa fa-times-circle" aria-hidden="true" onclick="jQuery('#mod_bookingforsearch-childrenages<?php echo $currModID ?>').popover('destroy');"></i></div>
				<?php echo sprintf(JTEXT::_('MOD_BOOKINGFORSEARCH_CHILDRENAGESMSG'),COM_BOOKINGFORCONNECTOR_CHILDRENSAGE) ?>
			</div>

		</form>
				   
        </div>
<?php endif;  ?>
<?php if(!empty($tablistRealEstate)): ?>
		<div role="tabpanel" id="bfisearchselling<?php echo $currModID ?>" class="tab-pane fade <?php echo (empty($tablistResources)) ?"active in": "" ?>">
		<form action="<?php echo $url_page_RealEstate; ?>" method="get" id="searchformonsellunit<?php echo $currModID ?>" class="searchformonsellunit ">			
			<div  id="searchBlock<?php echo $currModID ?>" class="bfi_form_<?php echo $showdirection?"horizontal":"vertical"; ?> ">
				<?php if($showContract){ ?>
				<div class="bfi_contracttypeid bfi_container" >
					<label><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_CONTRACT'); ?></label>
					<select name="contractTypeId" onchange="checkSelSearchOnsell<?php echo $currModID ?>();" class="inputtotal">
								<?php echo $listcontractType; ?>
					</select>
				</div><!--/span-->
				<?php } //$showContract ?>
				<?php if($showLocation){ ?>
				<div class="bfi_listlocations bfi_container">
					<label><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ZONE'); ?></label>
					<select id="cityId<?php echo $currModID ?>" name="cityId" onchange="checkSelSearchOnsell<?php echo $currModID ?>();" class="inputtotal">
						<?php echo $listlocations; ?>
					</select>
				</div>
				<div class="bfi_listlocations bfi_container">
					<div id="btnZones<?php echo $currModID ?>">
						<input type="radio" name="searchType" id="zoneSearch<?php echo $currModID ?>" value="0" <?php echo $searchType=="0"? "checked":""; ?> />
						<label for="zoneSearch<?php echo $currModID ?>" id="lblzoneSearch<?php echo $currModID ?>"  class="lblcheckzone"><?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_ALL_ZONES') ?></label>
					</div>
					<?php if($showMapIcon){ ?>
					<div onclick='javascript:openGoogleMapBFSSell();' href="javascript:void(0)" >
						<input type="radio" name="searchType" id="mapSearch<?php echo $currModID ?>" value="1" <?php echo $searchType=="1"? "checked":""; ?> />
						<label id="lblmapSearch<?php echo $currModID ?>"  class="lblcheckzone"><?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DRAW_MAP') ?></label>
					</div>
					<input id="zoneIds<?php echo $currModID ?>" name="zoneIds" type="hidden" value="<?php echo $zoneIds; ?>" />
					<?php } //$showMapIcon ?>
				</div><!--/span-->
				<?php } //$showLocation ?>
				<?php if(!empty($listunitCategoriesRealEstate) && $showAccomodations){ ?>
				<div class="bfi_unitCategoryId bfi_container">
					<label><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ACCOMODATION'); ?></label>
					<select name="unitCategoryId" onchange="checkSelSearchOnsell<?php echo $currModID ?>();" class="inputtotal">
						<?php echo $listunitCategoriesRealEstate; ?>
					</select>
				</div><!--/span-->
				<?php } //$listunitCategoriesRealEstate ?>
				<?php if($showMaxPrice){ ?>
				<div class="bfi_price bfi_container">
					<label><?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_PRICE') ?></label>
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6">
							<input name="pricemin" type="text" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_FROM') ?>" value="<?php echo $pricemin;?>" class="inputtext" > 
						</div><!--/span-->
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6">
							<input name="pricemax" type="text" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_TO') ?>" value="<?php echo $pricemax;?>"  class="inputtext" > 
						</div><!--/span-->
					</div>
				</div><!--/span-->
				<?php } //$showMaxPrice ?>
				<?php if($showMinFloor){ ?>
				<div class="bfi_floor_area  bfi_container">
					<label><?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_AREA') ?></label>
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6">
							<input name="areamin" type="text" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_FROM') ?>" value="<?php echo $areamin;?>" class="inputtext" > 
						</div><!--/span-->
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6">
							<input name="areamax" type="text" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_TO') ?>" value="<?php echo $areamax;?>" class="inputtext" > 
						</div><!--/span-->
					</div>
				</div><!--/span-->
				<?php } //$showMinFloor ?>
				<?php if($showBedRooms){ ?>
				<div class="bfi_bedrooms  bfi_container">
					<label><?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_BEDROOMS') ?></label>
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6">
					<input name="bedroomsmin" type="text" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_FROM') ?>" value="<?php echo $bedroomsmin;?>" class="inputtext" > 
						</div><!--/span-->
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6">
					<input name="bedroomsmax" type="text" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_TO') ?>" value="<?php echo $bedroomsmax;?>" class="inputtext" > 
						</div><!--/span-->
					</div>
				</div><!--/span-->
				<?php } //$showBedRooms ?>
				<?php if($showRooms){ ?>
				<div class="bfi_rooms  bfi_container">
					<label><?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_ROOMS') ?></label>
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6">
					<input name="roomsmin" type="text" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_FROM') ?>" value="<?php echo $roomsmin;?>" class="inputtext" > 
						</div><!--/span-->
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6">
					<input name="roomsmax" type="text" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_TO') ?>" value="<?php echo $roomsmax;?>" class="inputtext" > 
						</div><!--/span-->
					</div>
				</div><!--/span-->
				<?php } //$showRooms ?>
				<?php if($showBaths){ ?>
				<div class="bfi_bathrooms  bfi_container">
					<label><?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_BATHS') ?></label>
					<select name="baths" onchange="changeBaths(this);" class="inputtotal">
					<?php foreach ($baths as $key => $value):?>
						<option value="<?php echo $key ?>" <?php echo ($bathsmin ."|". $bathsmax == $key)?"selected":""; //selected( $bathsmin ."|". $bathsmax, $key ); ?>><?php echo $value ?></option>
					<?php endforeach; ?>
					</select>
					<input name="bathsmin" type="hidden" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_FROM') ?>" value="<?php echo $bathsmin;?>" class="inputtext" > 
					<input name="bathsmax" type="hidden" placeholder="<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_DEFAULTVALUE_TO') ?>" value="<?php echo $bathsmax;?>" class="inputtext" > 
				</div><!--/span-->
				<?php } //$showBaths ?>
				<?php if (isset($listServices) && $showServicesList) :?>
				<?php  $countServ=0;?>
				<div class="bfi_listservices  bfi_container">
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
						<?php foreach ($listServices as $singleService):?>
							<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
							<?php $checked = '';
								if (isset($filtersServices) &&  is_array($filtersServices) && in_array($singleService->ServiceId,$filtersServices)){
									$checked = ' checked="checked"';
								}
							?>
								<label class="checkbox"><input type="checkbox" name="services"  class="checkboxservices" value="<?php echo ($singleService->ServiceId) ?>" <?php echo $checked ?> /><?php echo BFCHelper::getLanguage($singleService->Name, $language) ?></label>
							</div>
						<?php  $countServ++;
						if($countServ%2==0):?>
					</div>
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">	
						<?php endif ?>

						<?php endforeach; ?>
					</div>
				</div><!--/span-->
				<?php endif ?>
				<?php if($showOnlyNew){ ?>
				<div class="bfi_isnewbuilding  bfi_container">  
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
							<label class="checkbox"><input type="checkbox" name="isnewbuilding" value="1" <?php echo $isnewbuilding ?> /><?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_ISNEWBUILDING') ?></label>
						</div>
					</div>
				</div><!--/span-->
				<?php } ?>

				<div id="searchButtonArea<?php echo $currModID ?>" class=" bfi_container">
					<div class="" id="divBtnRealEstate">
						&nbsp;<br />
						<a  id="BtnRealEstate<?php echo $currModID ?>" class="mod_bookingforsearch-searchbutton" href="javascript: void(0);"  style="width:100%;"><i class="fa fa-search" aria-hidden="true"></i> <?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_SEND'); ?></a>
					</div>
				</div>

			</div><!--/span-->

			<div id="zonePopup<?php echo $currModID ?>" class="zone-dialog" style="width:100%">
				<div class="dialog-header">
					<div class="header-content">
						<?php echo JText::_('MOD_BOOKINGFORSEARCH_ZONE_POPUP_TITLE') ?>
						<div class="pull-right dialog-closer">x</div>
						<div class="clearfix"></div>
					</div>
				</div>
				<div class="dialog-container">
					<div class="dialog-content">
						<?php if ($cityId >= -1): ?>
						<select id="zoneIdsList<?php echo $currModID ?>" name="zoneIdsList" onchange="checkSelSearchOnsell<?php echo $currModID ?>();" class="select90percent multiselect" multiple="multiple">
							<?php echo $listzoneIds ?>
						</select>				
						<?php endif ?>
					</div>
				</div>
			</div>
			<input type="hidden" value="<?php echo uniqid('', true)?>" name="searchid" />
			<input type="hidden" value="3" name="searchtypetab" />
			<input type="hidden" value="1" name="newsearch" />
			<input type="hidden" value="0" name="limitstart" />
			<input type="hidden" name="filter_order" value="" />
			<input type="hidden" name="filter_order_Dir" value="" />
			<input type="hidden" value="<?php echo $language ?>" name="cultureCode" />
			<input type="hidden" value="<?php echo $points ?>" name="points" id="points" />
			<input type="hidden" value="<?php echo $services ?>" name="servicesonsell" id="servicesonsell<?php echo $currModID ?>" />

		</form>
		</div>  <!-- role="tabpanel" -->
<?php endif;  ?>
    </div>
</div>

</div>
<script type="text/javascript">
var $dialog;
var currentLocation=0;
$Lng = <?php echo $XGooglePosDef?>;
$Lat = <?php echo $YGooglePosDef?>;
$googlemapsapykey = '<?php echo $googlemapsapykey?>';
$startzoom = <?php echo $startzoom?>;

var urlCheck = "<?php echo Juri::root()?>index.php?option=com_bookingforconnector";	
var cultureCode = '<?php echo $language ?>';
var defaultcultureCode = '<?php echo BFCHelper::$defaultFallbackCode ?>';
	//-->
	</script>

<?php if(!empty($tablistRealEstate)): ?>
<script type="text/javascript">


function checkSelSearchOnsell<?php echo $currModID ?>(){
	var vals=[];
	jQuery("#zonePopup<?php echo $currModID ?> .multiselect option:selected").each(function(i,selected){
		vals[i]=jQuery(this).val();
	});
	jQuery("#zoneIds<?php echo $currModID ?>").val(vals.toString());
	if(jQuery("#cityId<?php echo $currModID ?>").val()!= 0 && jQuery("#cityId<?php echo $currModID ?>").val()>=-1 && vals.length>0){
		jQuery("#lblzoneSearch<?php echo $currModID ?>").text(vals.length+" <?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_SELECTED_ZONES') ?>");
	} else if(jQuery("#cityId<?php echo $currModID ?>").val()!= 0 && jQuery("#cityId<?php echo $currModID ?>").val()>=-1) {
		jQuery("#lblzoneSearch<?php echo $currModID ?>").text("<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_CHOOSE_ZONES') ?>");
	} else {
		jQuery("#lblzoneSearch<?php echo $currModID ?>").text("<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_ALL_ZONES') ?>");
	}
	currentLocation=parseInt(jQuery("#cityId<?php echo $currModID ?>").val());
	if(currentLocation<-1){
		jQuery('#btnZones<?php echo $currModID ?>').attr("onclick","");
		jQuery('#btnZones<?php echo $currModID ?>').attr("href","");
		if(jQuery("#points").val() ==""){
			openGoogleMapBFSSell();
		}
	} else if(currentLocation>=-1){
		jQuery('#btnZones<?php echo $currModID ?>').attr("onclick","javascript:openZonesPopup();");
		jQuery('#btnZones<?php echo $currModID ?>').attr("href","javascript:void(0);");
	}else{
		jQuery('#btnZones<?php echo $currModID ?>').attr("onclick","");
		jQuery('#btnZones<?php echo $currModID ?>').attr("href","");
	}
}

function openZonesPopup(){
	jQuery('#zonePopup<?php echo $currModID ?>').show();
}

function updateHiddenValue(who,whohidden) {         
     var allVals = [];
     jQuery(who).each(function() {
       allVals.push(jQuery(this).val());
     });
     jQuery(whohidden).val(allVals.join(","));
  }

function changeBaths(currObj){
	var bathsselect = jQuery(currObj).val();
	var vals = bathsselect.split("|"); 
	var closestDiv = jQuery(currObj).closest("div");
	closestDiv.find("input[name='bathsmin']").first().val(vals[0]);
	closestDiv.find("input[name='bathsmax']").first().val(vals[1]);
}


function getBottomPosition(elm){
	return jQuery(window).height() - top - elm.height();
}

function resizeZoneTitle(){
	if(jQuery(window).width()>=600){
		jQuery('#row-zones .<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3').css("line-height",jQuery('#row-zones .<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>9').height()+"px");
		jQuery('#row-zones .<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3 label').css("line-height",jQuery('#row-zones .<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>9').height()+"px");
	} else{
		jQuery('#row-zones .<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3').css("line-height","");
		jQuery('#row-zones .<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3 label').css("line-height","");
	}
			jQuery("#zonePopup<?php echo $currModID ?>").css({
				left:jQuery("#searchBlock<?php echo $currModID ?>").offset().left-jQuery("#searchBlock<?php echo $currModID ?>").offset().left+15
			});
			
			if ((jQuery("#searchBlock<?php echo $currModID ?>").height()-jQuery(window).height())>0) {
				jQuery("#zonePopup<?php echo $currModID ?>").css({
					bottom:80+(jQuery("#searchBlock<?php echo $currModID ?>").height()-jQuery(window).height())
				});
			} else {
				jQuery("#zonePopup<?php echo $currModID ?>").css({
					height:"",
					bottom:80
				});
			}
			
}

jQuery(window).resize(function() {
	resizeZoneTitle();
});

jQuery(function($)
		{
			resizeZoneTitle();
			jQuery(".zone-dialog .dialog-closer").click(function(e){
				jQuery(this).closest(".zone-dialog").hide();
			});
			
			jQuery('.btn-radio input').click(function(e){
				//if($dialog==null){
					var searchBoxOffset=jQuery("#searchBlock<?php echo $currModID ?>").offset();
					var width=jQuery("#searchBlock<?php echo $currModID ?>").width();
					var height=(jQuery("#searchButtonArea<?php echo $currModID ?>").offset().top)-(searchBoxOffset.top);
//					console.log(searchBoxOffset.top);
			});
			jQuery('#BtnRealEstate<?php echo $currModID ?>').click(function(e) {
				e.preventDefault();
				jQuery("#searchformonsellunit<?php echo $currModID ?>").submit(); 
			});
			
			var searchBoxOffset=jQuery("#searchBlock<?php echo $currModID ?>").offset();
			var width=jQuery("#searchBlock<?php echo $currModID ?>").width();
			var height=(jQuery("#searchButtonArea<?php echo $currModID ?>").offset().top)-(searchBoxOffset.top);
			jQuery('#zonePopup<?php echo $currModID ?> .multiselect').multiSelectToCheckboxes();
			
			checkSelSearchOnsell<?php echo $currModID ?>();
			jQuery('.checkboxservices').on('click',function() {
				updateHiddenValue('.checkboxservices:checked','#servicesonsell<?php echo $currModID ?>')	
			});
			
			jQuery("#btnconfirm").click(function(e){
				jQuery("#lblmapSearch<?php echo $currModID ?>").text("<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_DRAWN_AREA') ?>");
			});
			
			
			jQuery("#cityId<?php echo $currModID ?>").change(function(){
				var searchBoxOffset=jQuery("#searchBlock<?php echo $currModID ?>").offset();
				var width=jQuery("#searchBlock<?php echo $currModID ?>").width();
				var height=(jQuery("#searchButtonArea<?php echo $currModID ?>").offset().top)-(searchBoxOffset.top);
				if(jQuery(this).val()>=-1){
					jQuery('#zonePopup<?php echo $currModID ?> .dialog-content').empty();
					var queryL = "task=getLocationZone&locationId=" + jQuery("#cityId<?php echo $currModID ?>").val();
					jQuery.post(urlCheck, queryL, function(result) {
							var select=jQuery("<select>");
							select.addClass("multiselect");
							select.attr('onchange','checkSelSearchOnsell<?php echo $currModID ?>();');
							select.attr("multiple","multiple");
							jQuery(result).each(function(i,itm){
								var opt=jQuery("<option>");
								opt.val(itm.LocationZoneID);
								opt.text(itm.Name);
								select.append(opt);
							});
							jQuery("#zonePopup<?php echo $currModID ?> .dialog-content").append(select);
							select.multiSelectToCheckboxes();

					},'json');
				} 
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
		        rules:
		        {
		        	pricemin:
					{
		                required: false,
		                digits: true
		            },
		        	pricemax:
					{
		                required: false,
		                digits: true
		            },
		            areamin:
		            {
		                required: false,
		                digits: true
		            },
		            areamax:
		            {
		                required: false,
		                digits: true
		            },
		            roomsmin:
		            {
		                required: false,
		                digits: true
		            },
		            roomsmax:
		            {
		                required: false,
		                digits: true
		            },
		            bedroomsmin:
		            {
		                required: false,
		                digits: true
		            },
		            bedroomsmin:
		            {
		                required: false,
		                digits: true
		            }
		        },
		        messages:
		        {
		        	pricemin: {
		        		required:"<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_REQUIRED') ?>",
		        		digits:"<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_DIGIT') ?>"
		        		},
		        	pricemax: {
		        		required:"<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_REQUIRED') ?>",
		        		digits:"<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_DIGIT') ?>"
		        		},
		        	areamin: {
		        		required:"<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_REQUIRED') ?>",
		        		digits:"<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_DIGIT') ?>"
		        		},
		        	areamax: {
		        		required:"<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_REQUIRED') ?>",
		        		digits:"<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_DIGIT') ?>"
		        		},
		        	roomsmin: {
		        		required:"<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_REQUIRED') ?>",
		        		digits:"<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_DIGIT') ?>"
		        		},
		        	roomsmax: {
		        		required:"<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_REQUIRED') ?>",
		        		digits:"<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_DIGIT') ?>"
		        		},
		        	bedroomsmin: {
		        		required:"<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_REQUIRED') ?>",
		        		digits:"<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_DIGIT') ?>"
		        		},
		        	bedroomsmax: {
		        		required:"<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_REQUIRED') ?>",
		        		digits:"<?php echo  JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_DIGIT') ?>"
		        		}
		        },
				highlight: function(label) {
			    },
			    success: function(label) {
					jQuery(label).remove();
			    },
				submitHandler: function(form) {
						msg1 = "<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_MSG1') ?>";
						msg2 = "<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_MSG2') ?>";
						jQuery("#zonePopup<?php echo $currModID ?>").hide();

						bookingfor.waitBlockUI(msg1, msg2,img1); 
						jQuery("#BtnRealEstate<?php echo $currModID ?>").hide();
						form.submit();
				}

		    });

		});
		
		

jQuery.fn.multiselect = function() {
    jQuery(this).each(function() {
        var checkboxes = jQuery(this).find("input:checkbox");
        checkboxes.each(function() {
            var checkbox = jQuery(this);
            // Highlight pre-selected checkboxes
            if (checkbox.prop("checked"))
                checkbox.parent().addClass("multiselect-on");
 
            // Highlight checkboxes that the user selects
            checkbox.click(function() {
                if (checkbox.prop("checked"))
                    checkbox.parent().addClass("multiselect-on");
                else
                    checkbox.parent().removeClass("multiselect-on");
            });
        });
    });
};

var methods = {
        init: function() {
            var $ul = jQuery("<ul/>").insertAfter(this);
			$ul.addClass(jQuery(this).attr("class"));
            var baseId = "_" + jQuery(this).attr("id");
            jQuery(this).children("option").each(function(index) {
                var $option = jQuery(this);
                var id = baseId + index;
                var $li = jQuery("<li/>").appendTo($ul);
                var $label = jQuery("<label for='" + id + "' class='aligncheckbox' >" + $option.text() + "</label>").appendTo($li);
				var $checkbox = jQuery("<input type='checkbox' id='" + id + "'/>").prependTo($label).change(function() {
                    if (jQuery(this).is(":checked")) {
                        $option.attr("selected", "selected");
                    } else {
                        $option.removeAttr("selected");
                    }
					checkSelSearchOnsell<?php echo $currModID ?>();
                });
                if ($option.is(":selected")) {
                    $checkbox.attr("checked", "checked");
                }

//                $checkbox.after("<label for='" + id + "' style='display:inline;'>" + $option.text() + "</label>");
            });
            jQuery(this).hide();
        }
    };

    jQuery.fn.multiSelectToCheckboxes = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.multiSelectToCheckboxes');
        }

    };

	//-->
	</script>
	<div id="divBFSSell" style="width:100%; height:400px; display:none;">
		<div style="width:100%; height:50px; position:relative;">
			<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ZONE_SEARCHBYMAP_TITLE') ?>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>"> 
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6">
					<ul class="nav nav-pills">
						<li><a class="btn select-figure" id="btndrawpoligon" onclick="javascript: drawPoligon()"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ZONE_SEARCHBYMAP_AREA') ?></a></li>
						<li><a class="btn select-figure" id="btndrawcircle" onclick="javascript: drawCircle()"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ZONE_SEARCHBYMAP_CIRCLE'); ?></a></li>
					</ul>				
				</div><!--/span-->
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMEDIUM ?>6 text-right">
					<input type="text" id="addresssearch" placeholder="<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ZONE_SEARCHBYMAP_ADDRESS_BUTTON') ?>" />
					<div id="btnCompleta" class="input-prepend input-append" style="display:none;">
						<a class="btn btn-delete" id="btndelete" href="javascript: void(0);" ><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ZONE_SEARCHBYMAP_DELETE'); ?></a>
						<a class="btn" id="btnconfirm" type="button" href="javascript: void(0);" ><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ZONE_SEARCHBYMAP_SUBMIT'); ?></a>
						<span class="add-on" id="spanArea"></span>
					</div>
				
				</div><!--/span-->
			</div>

		</div>
		<div id="map_canvasBFSSell" class="map_canvasBFSSell" style="width:100%; height:350px;"></div>
		<div class="map-tooltip"><strong><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ZONE_SEARCHBYMAP_USERGUIDE'); ?></strong></div>
	</div>
<?php endif;  ?>

<script type="text/javascript">
<!--
var img1 = new Image(); 
var localeSetting = "<?php echo substr($language,0,2); ?>";
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
        if (jQuery(resbynight).val() == 0) {
            days += 1;
        }
		jQuery('#calendarnight<?php echo $durationId ?>').html(days);
}
                
function insertCheckinTitle<?php echo $currModID ?>() {
	setTimeout(function() {
		jQuery("#ui-datepicker-div").addClass("checkin");
		jQuery("#ui-datepicker-div").removeClass("checkout");
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
		var currTab = jQuery('#navbookingforsearch<?php echo $currModID ?> li.active a[data-toggle="tab"]').first();
		var target = jQuery(currTab).attr("class");
        if (target == "searchResources" || target == "searchServices") {
			strSummary += ' Check-out '+('0' + to.getDate()).slice(-2)+' '+ month2 +' '+d2[2]+' ' + strSummaryDays;
        }
		jQuery('#ui-datepicker-div').attr('data-before',strSummary);

//		jQuery('#ui-datepicker-div').attr('data-before','Check-in '+('0' + from.getDate()).slice(-2)+' '+from.toLocaleString(locale, { month: "short" })+' Check-out '+('0' + to.getDate()).slice(-2)+' '+to.toLocaleString(locale, { month: "short" })+' '+d2[2]+' (soggiorno di '+days+' notti)');
	}, 1);
		
}
function insertCheckoutTitle<?php echo $currModID ?>() {
	setTimeout(function() {
		jQuery("#ui-datepicker-div").addClass("checkout");
		jQuery("#ui-datepicker-div").removeClass("checkin");
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
		var currTab = jQuery('#navbookingforsearch<?php echo $currModID ?> li.active a[data-toggle="tab"]').first();
		var target = jQuery(currTab).attr("class");
        if (target == "searchResources" || target == "searchServices") {
			strSummary += ' Check-out '+('0' + to.getDate()).slice(-2)+' '+ month2 +' '+d2[2]+' ' + strSummaryDays;
        }
		jQuery('#ui-datepicker-div').attr('data-before',strSummary);
//		jQuery('#ui-datepicker-div').attr('data-before','Check-in '+('0' + from.getDate()).slice(-2)+' '+from.toLocaleString(locale, { month: "long" })+' Check-out '+('0' + to.getDate()).slice(-2)+' '+to.toLocaleString(locale, { month: "long" })+' '+d2[2]+' (soggiorno di '+days+' notti)');
	}, 1);
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

	arr = [dayEnabled, ''];  
	if(check.getTime() == from.getTime()) {

		arr = [dayEnabled, 'date-start-selected', 'date-selected'];
	}
	if(check.getTime() == to.getTime()) {

		arr = [dayEnabled, 'date-end-selected', 'date-selected'];  
	}
	if(check > from && check < to) {
		arr = [dayEnabled, 'date-selected', 'date-selected'];
	}
	return arr;
}

function printChangedDate<?php echo $currModID ?>(date, elem) {
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

	jQuery('.checkinli<?php echo $currModID ?>').find('.day span').html(day1);
	jQuery('.checkoutli<?php echo $currModID ?>').find('.day span').html(day2);
	if (typeof Intl == 'object' && typeof Intl.NumberFormat == 'function') {
		jQuery('.checkinli<?php echo $currModID ?>').find('.monthyear p').html(weekday1 + "<br />" + month1+" "+year1); 
		jQuery('.checkoutli<?php echo $currModID ?>').find('.monthyear p').html(weekday2 + "<br />" + month2+" "+year2);
		jQuery('#bfi_lblchildrenagesat<?php echo $currModID ?>').html("<?php echo strtolower (JTEXT::_('MOD_BOOKINGFORSEARCH_CHILDRENAGESAT')) ?>" + day2 + " " + month2 + " " + year2);
	} else {
		jQuery('.checkinli<?php echo $currModID ?>').find('.monthyear p').html(d1[1]+"/"+d1[2]);  
		jQuery('.checkoutli<?php echo $currModID ?>').find('.monthyear p').html(d2[1]+"/"+d2[2]);
		jQuery('#bfi_lblchildrenagesat<?php echo $currModID ?>').html("<?php echo strtolower (JTEXT::_('MOD_BOOKINGFORSEARCH_CHILDRENAGESAT')) ?>" + day2 + " " + d2[1] + " " + d2[2]);
	}
}

function checkDate<?php echo $checkinId; ?>($, obj, selectedDate) {
	instance = obj.data("datepicker");
	date = $.datepicker.parseDate(
			instance.settings.dateFormat ||
			$.datepicker._defaults.dateFormat,
			selectedDate, instance.settings);
	var d = new Date(date);
	d.setDate(d.getDate() + 1);
	jQuery("#<?php echo $checkoutId; ?>").datepicker("option", "minDate", d);
}

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
//				jQuery(this).show();
			}
		});
		jQuery("#mod_bookingforsearch-childrenages<?php echo $currModID ?>").show();
		if(showMsg===1) { 
			showpopover<?php echo $currModID ?>();
		}
	}
//	if (jQuery.prototype.masonry){
//		jQuery('.main-siderbar, .main-siderbar1').masonry('reload');
//	}

}
jQuery(function() {
	jQuery("#<?php echo $checkinId; ?>").datepicker({
		defaultDate: "+2d"
		,dateFormat: "dd/mm/yy"
		, numberOfMonths: parseInt("<?php echo $numberOfMonth;?>"), minDate: '+0d'
		, onClose: function(dateText, inst) { jQuery(this).attr("disabled", false); insertNight<?php echo $currModID ?>() }
		, beforeShow: function(dateText, inst) { jQuery(this).attr("disabled", true); insertCheckinTitle<?php echo $currModID ?>(); }
		, onChangeMonthYear: function(dateText, inst) { insertCheckinTitle<?php echo $currModID ?>(); }, showOn: "button"
		, beforeShowDay: closed<?php echo $currModID ?>
		, buttonText: "<div class='buttoncalendar checkinli<?php echo $currModID; ?> checkinli_div'><div class='dateone day '><span><?php echo $checkin->format("d") ;?></span></div><div class='dateone daterwo monthyear first_monthyear'><p><?php echo $checkin->format("D");?><br /><?php echo $checkin->format("M");?> <?php echo $checkin->format("Y"); ?> </p></div></div>"
		, onSelect: function(date) { checkDate<?php echo $checkinId; ?>(jQuery, jQuery(this), date); printChangedDate<?php echo $currModID ?>(date, jQuery(this)); }
		, firstDay: 1
	});
	jQuery("#<?php echo $checkoutId; ?>").datepicker({
		defaultDate: "+2d"
		,dateFormat: "dd/mm/yy"
		, numberOfMonths: parseInt("<?php echo $numberOfMonth;?>")
		, onClose: function(dateText, inst) { jQuery(this).attr("disabled", false); insertNight<?php echo $currModID ?>();  }
		, beforeShow: function(dateText, inst) { jQuery(this).attr("disabled", true); insertCheckoutTitle<?php echo $currModID ?>(); }
		, onSelect: function(date) { printChangedDate<?php echo $currModID ?>(date, jQuery(this)); }
		, onChangeMonthYear: function(dateText, inst) { insertCheckoutTitle<?php echo $currModID ?>(); }, minDate: '+1d', showOn: "button"
		, beforeShowDay: closed<?php echo $currModID ?>, buttonText: "<div class='buttoncalendar checkoutli<?php echo $currModID; ?> checkoutli_div'><div class='dateone day lastdate'><span><?php echo $checkout->format("d"); ?></span></div><div class='dateone daterwo monthyear last_monthyear'><p><?php echo $checkout->format("D");?><br /><?php echo $checkout->format("M");?> <?php echo $checkout->format("Y"); ?> </p></div></div>"
		, firstDay: 1
	});

	jQuery('#BtnResource<?php echo $currModID ?>').click(function(e) {
		e.preventDefault();
		jQuery("#searchform<?php echo $currModID ?>").submit(); 
	});
	jQuery("#navbookingforsearch<?php echo $currModID ?> li[data-searchtypeid=<?php echo $searchtypetab ?>] a[data-toggle=tab]").tab("show");
	jQuery("#searchform<?php echo $currModID ?>").validate(
	{
		invalidHandler: function(form, validator) {
			var errors = validator.numberOfInvalids();
			if (errors) {
				validator.errorList[0].element.focus();
			}
		},
//		rules:
//		{
//			merchantCategoryId: {
//				notEqual: "0"
//				},
//			masterTypeId: {
//				notEqual: "0"
//				},
//		},
//		messages:
//		{
//			merchantCategoryId: {
//				notEqual:"<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_REQUIRED') ?>",
//				},
//			masterTypeId: {
//				notEqual:"<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_ERROR_REQUIRED') ?>",
//				},
//		},
		highlight: function(label) {
		},
		success: function(label) {
			jQuery(label).remove();
		},
		submitHandler: function(form) {
				msg1 = "<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_MSG1') ?>";
				msg2 = "<?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_MSG2') ?>";
				bookingfor.waitBlockUI(msg1, msg2,img1); 
				jQuery("#BtnResource<?php echo $currModID ?>").hide();
				form.submit();
		}

	});
	showhideCategories<?php echo $currModID ?>();
	checkChildrenSearch<?php echo $currModID ?>(<?php echo $nch ?>,<?php echo $showChildrenagesmsg ?>);
	jQuery("#mod_bookingforsearch-children<?php echo $currModID ?> select[name='children']").change(function() {
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
});

function countPersone<?php echo $currModID ?>() {
	var numAdults = new Number(jQuery("#searchform<?php echo $currModID ?> select[name='adults']").val());
	var numSeniores = new Number(jQuery("#searchform<?php echo $currModID ?> select[name='seniores']").val());
	var numChildren = new Number(jQuery("#mod_bookingforsearch-children<?php echo $currModID ?> select[name='children']").val());
	jQuery('#searchformpersons<?php echo $currModID ?>').val(numAdults + numChildren + numSeniores);
	jQuery('#showmsgchildage<?php echo $currModID ?>').val(0);
	jQuery(".mod_bookingforsearch-childrenages select:visible option:selected").each(function(i) {
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
		jQuery('#mod_bookingforsearch-childrenages<?php echo $currModID ?>').popover({
			content : jQuery("#bfi_childrenagesmsg<?php echo $currModID ?>").html(),
			container: "body",
			placement:"bottom",
			html :"true"
		});
		jQuery('#mod_bookingforsearch-childrenages<?php echo $currModID ?>').popover("show");
}
jQuery(window).resize(function(){
	jQuery('#mod_bookingforsearch-childrenages<?php echo $currModID ?>').popover("hide");
});

function showhideCategories<?php echo $currModID ?>() {
	var currTab = jQuery('#navbookingforsearch<?php echo $currModID ?> li.active a[data-toggle="tab"]').first();
    var target = jQuery(currTab).attr("class");
	var merchantCategoriesResource =  <?php echo json_encode($merchantCategoriesResource) ?> ;

	var merchantCategoriesSelectedBooking = [<?php echo implode(',', $merchantCategoriesSelectedBooking) ?>];
    var merchantCategoriesSelectedServices = [<?php echo implode(',', $merchantCategoriesSelectedServices) ?>];
    var merchantCategoriesSelectedActivities = [<?php echo implode(',', $merchantCategoriesSelectedActivities) ?>];

	var unitCategoriesResource = <?php echo json_encode($unitCategoriesResource) ?>;
	
	var unitCategoriesSelectedBooking = [<?php echo implode(',', $unitCategoriesSelectedBooking) ?>];
    var unitCategoriesSelectedServices = [<?php echo implode(',', $unitCategoriesSelectedServices) ?>];
    var unitCategoriesSelectedActivities = [<?php echo implode(',', $unitCategoriesSelectedActivities) ?>];
	
	var currentMerchantCategoriesSelected = jQuery("#merchantCategoryId<?php echo $currModID ?>").val()?jQuery("#merchantCategoryId<?php echo $currModID ?>").val():0;
	var currentUnitCategoriesSelected = jQuery("#masterTypeId<?php echo $currModID ?>").val()?jQuery("#masterTypeId<?php echo $currModID ?>").val():0;

	jQuery("#merchantCategoryId<?php echo $currModID ?>").val(0);
	jQuery("#masterTypeId<?php echo $currModID ?>").val(0);
	
	var currMerchantCategory = jQuery("#merchantCategoryId<?php echo $currModID ?>");
	currMerchantCategory.find('option:gt(0)').remove().end();
	var currUnitCategory = jQuery("#masterTypeId<?php echo $currModID ?>");
	currUnitCategory.find('option:gt(0)').remove().end();

	if (target == "searchResources") {		
		if(merchantCategoriesSelectedBooking.length>0){
			jQuery("#merchantCategoryId<?php echo $currModID ?>").closest("div").show();
            for (var i = 0; i < merchantCategoriesSelectedBooking.length; i++) {
				var currMC = merchantCategoriesResource[merchantCategoriesSelectedBooking[i]];
                currMerchantCategory.append(jQuery('<option>').text(currMC).attr('value', merchantCategoriesSelectedBooking[i]));
            }
			
		}else{
			jQuery("#merchantCategoryId<?php echo $currModID ?>").closest("div").hide();
		}
		if(unitCategoriesSelectedBooking.length>0){
			jQuery("#masterTypeId<?php echo $currModID ?>").closest("div").show();
            for (var i = 0; i < unitCategoriesSelectedBooking.length; i++) {
				var currUC = unitCategoriesResource[unitCategoriesSelectedBooking[i]];
                currUnitCategory.append(jQuery('<option>').text(currUC).attr('value', unitCategoriesSelectedBooking[i]));
            }
		}else{
			jQuery("#masterTypeId<?php echo $currModID ?>").closest("div").hide();
		}
		if(jQuery.inArray(Number(currentMerchantCategoriesSelected), merchantCategoriesSelectedBooking) != -1){
			jQuery("#merchantCategoryId<?php echo $currModID ?>").val(currentMerchantCategoriesSelected);
		}
		if(jQuery.inArray(Number(currentUnitCategoriesSelected), unitCategoriesSelectedBooking) != -1){
			jQuery("#masterTypeId<?php echo $currModID ?>").val(currentUnitCategoriesSelected);
		}
	}
	if (target == "searchServices") {
		if(merchantCategoriesSelectedServices.length>0){
			jQuery("#merchantCategoryId<?php echo $currModID ?>").closest("div").show();
            for (var i = 0; i < merchantCategoriesSelectedServices.length; i++) {
				var currMC = merchantCategoriesResource[merchantCategoriesSelectedServices[i]];
                currMerchantCategory.append(jQuery('<option>').text(currMC).attr('value', merchantCategoriesSelectedServices[i]));
            }
		}else{
			jQuery("#merchantCategoryId<?php echo $currModID ?>").closest("div").hide();
		}
		if(unitCategoriesSelectedServices.length>0){
			jQuery("#masterTypeId<?php echo $currModID ?>").closest("div").show();
            for (var i = 0; i < unitCategoriesSelectedServices.length; i++) {
				var currUC = unitCategoriesResource[unitCategoriesSelectedServices[i]];
                currUnitCategory.append(jQuery('<option>').text(currUC).attr('value', unitCategoriesSelectedServices[i]));
            }
		}else{
			jQuery("#masterTypeId<?php echo $currModID ?>").closest("div").hide();
		}
		if(jQuery.inArray(Number(currentMerchantCategoriesSelected), merchantCategoriesSelectedServices) != -1){
			jQuery("#merchantCategoryId<?php echo $currModID ?>").val(currentMerchantCategoriesSelected);
		}
		if(jQuery.inArray(Number(currentUnitCategoriesSelected), unitCategoriesSelectedServices) != -1){
			jQuery("#masterTypeId<?php echo $currModID ?>").val(currentUnitCategoriesSelected);
		}
	}
	if (target == "searchTimeSlots") {
		if(merchantCategoriesSelectedActivities.length>0){
			jQuery("#merchantCategoryId<?php echo $currModID ?>").closest("div").show();
            for (var i = 0; i < merchantCategoriesSelectedActivities.length; i++) {
				var currMC = merchantCategoriesResource[merchantCategoriesSelectedActivities[i]];
                currMerchantCategory.append(jQuery('<option>').text(currMC).attr('value', merchantCategoriesSelectedActivities[i]));
            }
		}else{
			jQuery("#merchantCategoryId<?php echo $currModID ?>").closest("div").hide();
		}
		if(unitCategoriesSelectedActivities.length>1){
			jQuery("#masterTypeId<?php echo $currModID ?>").closest("div").show();
            for (var i = 0; i < unitCategoriesSelectedActivities.length; i++) {
				var currUC = unitCategoriesResource[unitCategoriesSelectedActivities[i]];
                currUnitCategory.append(jQuery('<option>').text(currUC).attr('value', unitCategoriesSelectedActivities[i]));
            }
		}else{
			jQuery("#masterTypeId<?php echo $currModID ?>").closest("div").hide();
		}
		if(jQuery.inArray(Number(currentMerchantCategoriesSelected), merchantCategoriesSelectedActivities) != -1){
			jQuery("#merchantCategoryId<?php echo $currModID ?>").val(currentMerchantCategoriesSelected);
		}
		if(jQuery.inArray(Number(currentUnitCategoriesSelected), unitCategoriesSelectedActivities) != -1){
			jQuery("#masterTypeId<?php echo $currModID ?>").val(currentUnitCategoriesSelected);
		}
	}
}


    jQuery('#navbookingforsearch<?php echo $currModID ?> a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var target = jQuery(e.target).attr("class")
		var resbynight = jQuery(jQuery(e.target).attr("href")).find(".resbynighthd").first();
		
		var availabilityTypeList = <?php echo json_encode($availabilityTypeList) ?>;

		var availabilityTypesSelectedBooking = [<?php echo implode(',', $availabilityTypesSelectedBooking) ?>];
		var availabilityTypesSelectedServices = [<?php echo implode(',', $availabilityTypesSelectedServices) ?>];
		var availabilityTypesSelectedActivities = [<?php echo implode(',', $availabilityTypesSelectedActivities) ?>];
		
		var currentavailabilityTypesSelected = resbynight.val()?resbynight.val():1;
//		resbynight.find('option').remove().end();

		jQuery("#divcalendarnightsearch<?php echo $currModID ?>").hide();

		if (target == "searchResources") {
            jQuery("#divcheckoutsearch<?php echo $currModID ?>").css("display", "inline-block");
			if(availabilityTypesSelectedBooking.length>0){
				resbynight.val(currentavailabilityTypesSelected);
				if((availabilityTypesSelectedBooking =="0" || availabilityTypesSelectedBooking =="1" || availabilityTypesSelectedBooking =="0,1" ) ){
					jQuery("#divcalendarnightsearch<?php echo $currModID ?>").show();
				}
			}
//			if((availabilityTypesSelectedBooking =="0" || availabilityTypesSelectedBooking =="1" ) ){
//				jQuery("#divcalendarnightsearch<?php echo $currModID ?>").show();
//				for (var i = 0; i < availabilityTypesSelectedBooking.length; i++) {
//					var currAT = availabilityTypeList[availabilityTypesSelectedBooking[i]];
//					resbynight.append(jQuery('<option>').text(currAT).attr('value', availabilityTypesSelectedBooking[i]));
//				}
//			}else{
//				jQuery("#divcalendarnightsearch<?php echo $currModID ?>").hide();
//			}
//			if(jQuery.inArray(Number(currentavailabilityTypesSelected), availabilityTypesSelectedBooking) != -1){
//				resbynight.val(currentavailabilityTypesSelected);
//			}

            jQuery("#searchtypetab<?php echo $currModID ?>").val("0");
            var d = jQuery('#<?php echo $checkinId; ?>').datepicker('getDate');
            if (jQuery(resbynight).val() == 1) {
                d.setDate(d.getDate() + 1);
            }
            jQuery('#<?php echo $checkoutId; ?>').datepicker("option", "minDate", d);
            jQuery('#<?php echo $checkoutId; ?>').datepicker("option", "maxDate", Infinity);
            if (jQuery('#<?php echo $checkoutId; ?>').datepicker("getDate") <= d) {
                jQuery('#<?php echo $checkoutId; ?>').datepicker("setDate", Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
            }

        }
        if (target == "searchServices") {
            jQuery("#divcheckoutsearch<?php echo $currModID ?>").css("display", "inline-block");
            
			if(availabilityTypesSelectedServices.length>0){
				resbynight.val(availabilityTypesSelectedServices);
				if((availabilityTypesSelectedServices =="0" || availabilityTypesSelectedServices =="1"  || availabilityTypesSelectedServices =="0,1" ) ){
					jQuery("#divcalendarnightsearch<?php echo $currModID ?>").show();
				}
			}
//			if(availabilityTypesSelectedServices.length>0){
//				jQuery("#divcalendarnightsearch<?php echo $currModID ?>").show();
//				for (var i = 0; i < availabilityTypesSelectedServices.length; i++) {
//					var currAT = availabilityTypeList[availabilityTypesSelectedServices[i]];
//					resbynight.append(jQuery('<option>').text(currAT).attr('value', availabilityTypesSelectedServices[i]));
//				}
//			}else{
//				jQuery("#divcalendarnightsearch<?php echo $currModID ?>").hide();
//			}
//			if(jQuery.inArray(Number(currentavailabilityTypesSelected), availabilityTypesSelectedServices) != -1){
//				resbynight.val(currentavailabilityTypesSelected);
//			}

			jQuery("#searchtypetab<?php echo $currModID ?>").val("1");
            var d = jQuery('#<?php echo $checkinId; ?>').datepicker('getDate');
            if (jQuery(resbynight).val() == 1) {
                d.setDate(d.getDate() + 1);
            }
            jQuery('#<?php echo $checkoutId; ?>').datepicker("option", "minDate", d);
            jQuery('#<?php echo $checkoutId; ?>').datepicker("option", "maxDate", Infinity);
            if (jQuery('#<?php echo $checkoutId; ?>').datepicker("getDate") <= d) {
                jQuery('#<?php echo $checkoutId; ?>').datepicker("setDate", Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
            }
        }
        if (target == "searchTimeSlots") {
            jQuery("#divcheckoutsearch<?php echo $currModID ?>").css("display", "none");
//            jQuery("#divcalendarnightsearch<?php echo $currModID ?>").css("display", "none");
 			if(availabilityTypesSelectedActivities.length>0){
				resbynight.val(availabilityTypesSelectedActivities);
				if((availabilityTypesSelectedActivities =="0" || availabilityTypesSelectedActivities =="1"  || availabilityTypesSelectedActivities =="0,1" ) ){
					jQuery("#divcalendarnightsearch<?php echo $currModID ?>").show();
				}
			}
           jQuery("#searchtypetab<?php echo $currModID ?>").val("2");
        }
        if (target == "searchSelling") {
            jQuery("#searchtypetab<?php echo $currModID ?>").val("3");
        }
		insertNight<?php echo $currModID ?>();
		showhideCategories<?php echo $currModID ?>();
    })
 
//-->
</script>
