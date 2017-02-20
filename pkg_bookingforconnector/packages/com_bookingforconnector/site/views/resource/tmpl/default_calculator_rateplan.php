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

$document     = JFactory::getDocument();
$language     = $document->getLanguage();

$db   = JFactory::getDBO();
$uri  = 'index.php?option=com_bookingforconnector&view=resource';

$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );

$itemId = intval($db->loadResult());


$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;
$usessl = COM_BOOKINGFORCONNECTOR_USESSL;
$currencyclass = "eur";

$resource = $this->item;
$merchant = $resource->Merchant;
$cartType = $merchant->CartType;
$currentCartConfiguration = null;
$ProductAvailabilityType = $resource->AvailabilityType;
$resourceId = $resource->ResourceId;

$CartMultimerchantEnabled = BFCHelper::getCartMultimerchantEnabled(); 
$uriCart  = 'index.php?option=com_bookingforconnector&view=cart';
$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriCart .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
$itemIdCart= ($db->getErrorNum())? 0 : intval($db->loadResult());
if ($itemIdCart<>0)
	$uriCart.='&Itemid='.$itemIdCart;

$url_cart_page = JRoute::_($uriCart);
$base_url = JURI::root();


//$resourceName = BFCHelper::getLanguage($resource->Name, $this->language);
$resourceName = BFCHelper::getLanguage($resource->Name, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
$formRoute = JRoute::_('index.php?option=com_bookingforconnector&view=resource&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName));
$formRouteBook = JRoute::_('index.php?option=com_bookingforconnector&view=resource&layout=form&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName));
if($usessl){
	$formRouteBook = JRoute::_('index.php?option=com_bookingforconnector&view=resource&layout=form&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName),true,1);
}

$formOrderRouteBook = $formRouteBook ;

//$MerchantBookingTypes = $model->getMerchantBookingTypesFromService();
$pars = $this->params;

$checkoutspan = '+1 day';
if ($ProductAvailabilityType== 0)
{
	$checkoutspan = '+0 day';
}

$startDate = DateTime::createFromFormat('d/m/Y',BFCHelper::getStartDateByMerchantId($resource->MerchantId));
$endDate = DateTime::createFromFormat('d/m/Y',BFCHelper::getEndDateByMerchantId($resource->MerchantId));
$startDate2 = clone $startDate;
$startDate2->modify($checkoutspan);

$checkin = new JDate('now'); 
$checkout = new JDate('now'); 

$paxes = 2;
$paxages = array();
$currentState ='';

$ratePlanId = '';
$pricetype = '';
$selVariationId ='';
$selectablePrices ='';
$packages ='';


if (!empty($pars)){

	$checkin = !empty($pars['checkin']) ? new JDate($pars['checkin']->format('Y-m-d')) : new DateTime();
	$checkout = !empty($pars['checkout']) ? new JDate($pars['checkout']->format('Y-m-d')) : new DateTime();

	if (!empty($pars['paxes'])) {
		$paxes = $pars['paxes'];
	}
	if (!empty($pars['paxages'])) {
		$paxages = $pars['paxages'];
	}
	if (empty($pars['checkout'])){
		$checkout->modify($checkoutspan); 
	}

	$currentState = isset($pars['state'])?$pars['state']:'';
	$pricetype = isset($params['pricetype']) ? $params['pricetype'] : BFCHelper::getVar('pricetype','');
	$ratePlanId = isset($params['rateplanId']) ? $params['rateplanId'] : $pricetype;
//	$selExtras = explode('|',$pars['extras']);
	$selectablePrices = isset($params['extras']) ? $params['extras'] : '';
//	$selPackages = explode('|',(isset($pars['packages'])? $pars['packages'] : ''));
	$packages = isset($params['packages']) ? $params['packages'] : '';
	$selVariationId = isset($pars['variationPlanId'])? $pars['variationPlanId'] : ''; // $pars['variationPlanId'];
}

$variationPlanId = $selVariationId;
if ($checkin < $startDate){
	$checkin = clone $startDate;
	$checkout = clone $checkin;
    $checkout->modify($checkoutspan); 
}

$checkout->setTime(0,0,0);
$checkin->setTime(0,0,0);

if ($checkin == $checkout){
    $checkout->modify($checkoutspan); 
}
if ($checkout < $checkin){
	$checkout = clone $checkin;
    $checkout->modify($checkoutspan); 
}



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

$adults = array(
	JHTML::_('select.option', '0', JText::_('0') ),
	JHTML::_('select.option', '1', JText::_('1') ),
	JHTML::_('select.option', '2', JText::_('2') ),
	JHTML::_('select.option', '3', JText::_('3') ),
	JHTML::_('select.option', '4', JText::_('4') ),
	JHTML::_('select.option', '5', JText::_('5') ),
	JHTML::_('select.option', '6', JText::_('6') ),
	JHTML::_('select.option', '7', JText::_('7') ),
	JHTML::_('select.option', '8', JText::_('8') )
);

$children = array(
	JHTML::_('select.option', '0', JText::_('0') ),
	JHTML::_('select.option', '1', JText::_('1') ),
	JHTML::_('select.option', '2', JText::_('2') ),
	JHTML::_('select.option', '3', JText::_('3') ),
	JHTML::_('select.option', '4', JText::_('4') )
);

$childrenAges = array(
	JHTML::_('select.option', '0', JText::_('0') ),
	JHTML::_('select.option', '1', JText::_('1') ),
	JHTML::_('select.option', '2', JText::_('2') ),
	JHTML::_('select.option', '3', JText::_('3') ),
	JHTML::_('select.option', '4', JText::_('4') ),
	JHTML::_('select.option', '5', JText::_('5') ),
	JHTML::_('select.option', '6', JText::_('6') ),
	JHTML::_('select.option', '7', JText::_('7') ),  
	JHTML::_('select.option', '8', JText::_('8') ),
	JHTML::_('select.option', '9', JText::_('9') ),
	JHTML::_('select.option', '10', JText::_('10') ),  
	JHTML::_('select.option', '11', JText::_('11') ),  
	JHTML::_('select.option', '12', JText::_('12') ),  
	JHTML::_('select.option', '13', JText::_('13') ),  
	JHTML::_('select.option', '14', JText::_('14') ),  
	JHTML::_('select.option', '15', JText::_('15') ),  
	JHTML::_('select.option', '16', JText::_('16') ),  
	JHTML::_('select.option', '17', JText::_('17') ),  
);

$seniores = array(
	JHTML::_('select.option', '0', JText::_('0') ),
	JHTML::_('select.option', '1', JText::_('1') ),
	JHTML::_('select.option', '2', JText::_('2') ),
	JHTML::_('select.option', '3', JText::_('3') ),
	JHTML::_('select.option', '4', JText::_('4') ),
	JHTML::_('select.option', '5', JText::_('5') ),
	JHTML::_('select.option', '6', JText::_('6') ),
	JHTML::_('select.option', '7', JText::_('7') ),
	JHTML::_('select.option', '8', JText::_('8') )
);


$duration = 1;

if ($ProductAvailabilityType == 2)
{
	$currAvailCalHour = json_decode(BFCHelper::GetCheckInDatesPerTimes($resourceId, $checkin, null));
	$AvailabilityTimePeriod = $currAvailCalHour;
	if (count($currAvailCalHour)>0)
	{
		$minuteStart = BFCHelper::ConvertIntTimeToMinutes($currAvailCalHour[0]->TimeMinStart);
		$minuteEnd = BFCHelper::ConvertIntTimeToMinutes($currAvailCalHour[0]->TimeMinEnd);
		$duration   = $minuteEnd - $minuteStart;
		$checkin->modify('+'.$minuteStart.' minutes'); 
	}
	$checkout = clone $checkin;
	$checkout = $checkout->modify($checkoutspan); 
}
if ($ProductAvailabilityType== 3)
{
	$checkout = clone $checkin;
	$checkout = $checkout->modify($checkoutspan); 
}

if($ProductAvailabilityType != 3 && $ProductAvailabilityType != 2){
	$duration = $checkin->diff($checkout)->format('%a');
}

if ($ProductAvailabilityType== 0)
{
	$duration +=1; 
}


$dateStringCheckin =  $checkin->format('d/m/Y');
$dateStringCheckout =  $checkout->format('d/m/Y');

$dateStayCheckin = new DateTime();
$dateStayCheckout = new DateTime();

$totalPerson = $nad + $nch + $nse;

$checkinId = uniqid('checkin');
$checkoutId = uniqid('checkout');

$checkInDates = BFCHelper::getCheckInDates($resource->ResourceId,$startDate);


$allStaysToView = array();


$alternativeDateToSearch = clone $startDate;
if ($checkin > $alternativeDateToSearch)
{
	$alternativeDateToSearch = clone $checkin;
}


$resRatePlans = array();
$relatedRatePlans =  array();
$altLstRatePlans =  array();

//$ratePlans = BFCHelper::GetCompleteRatePlansStayWP($resourceId,$checkin,$duration,$paxages,$selectablePrices,$packages,$pricetype,$ratePlanId,$variationPlanId,$language);
$defaultRatePlan = null;

//if(!empty($ratePlans)){
//	foreach ($ratePlans as $rslt)
//	{
//		$resRatePlan = new stdClass();
//		$resRatePlan->RatePlan = $rslt;
//		$resRatePlan->ResourceId = $resource->ResourceId;
//		$resRatePlan->ResName = $resource->Name;
//		$resRatePlan->MinPaxes = $resource->MinCapacityPaxes;
//		$resRatePlan->MaxPaxes = $resource->MaxCapacityPaxes;
//		$resRatePlan->ImageUrl = $resource->ImageUrl;
//		$resRatePlan->RateplanName = $rslt->Name;
//		$resRatePlan->RateplanId = $rslt->RatePlanId;
//		$resRatePlan->Price = $rslt->TotalDiscounted;
//		$resRatePlan->TotalPrice = $rslt->TotalAmount;
//		$resRatePlan->Availability = min($rslt->SuggestedStay->Availability, COM_BOOKINGFORCONNECTOR_MAXQTSELECTABLE);
//		$resRatePlan->AvailabilityType = $resource->AvailabilityType;
//		$resRatePlan->IsBookable = $rslt->IsBookable;
//		$resRatePlan->IsBase = $rslt->IsBase;
//		$resRatePlan->PercentVariation = $rslt->PercentVariation;
//		array_push($resRatePlans,$resRatePlan);
//		
//		if ($resRatePlan->RateplanId == $pricetype)
//		{
//			$defaultRatePlan = $resRatePlan;
//		}
//
//	}
//}

$lstRatePlans = $resRatePlans;

$excludedIds = $resourceId;

$fromSearch =  BFCHelper::getVar('s','0');

if(!empty($resource) && !empty($fromSearch)){
	$relatedRatePlans = BFCHelper::GetRelatedResourceStays($merchant->MerchantId, $resourceId, $resourceId, $checkin,$duration,$paxages, $variationPlanId,$language);
	if(!empty($relatedRatePlans) && is_array($relatedRatePlans) && count($relatedRatePlans)>0){
		$excludedRelatedIds =  implode(',',array_unique(array_map(function ($i) { return $i->ResourceId; }, $relatedRatePlans)));
		if(!empty($excludedRelatedIds)){
			$excludedIds = ",". $excludedRelatedIds;
		}
	}else{
		$relatedRatePlans = array();
	}
}

//$parentId = isset($resource->CondominiumId) ? $resource->CondominiumId : null;
//
//$altLstRatePlans = BFCHelper::getSearchResults(0, 5, null, null, $merchant->MerchantId, $parentId, false, false, $excludedIds);

$allRatePlans = array();

//if !function_exists("bfi_sortRatePlans"){
//	function bfi_sortRatePlans($a, $b)
//	{
//		return $a->SortOrder - $b->SortOrder;
//	}
//}
//

//if(!empty($lstRatePlans)){
//	usort($lstRatePlans, "BFCHelper::bfi_sortRatePlans");
//	$allRatePlans = array_merge($allRatePlans,$lstRatePlans);
//}

if(!empty($relatedRatePlans)){
//	usort($relatedRatePlans, "BFCHelper::bfi_sortResourcesRatePlans");
	$allRatePlans = array_merge($allRatePlans,$relatedRatePlans);
}
//if(!empty($altLstRatePlans) && is_array($altLstRatePlans) && count($altLstRatePlans)>0){
//	
//	usort($altLstRatePlans, "BFCHelper::bfi_sortResourcesRatePlans");
//	$allRatePlans = array_merge($allRatePlans,$altLstRatePlans);
//}

$defaultRatePlans =  array_values(array_filter($allRatePlans, function($p) use ($resourceId) {return $p->ResourceId == $resourceId ;})); // c#: allRatePlans.Where(p => p.ResourceId == resId);

if(is_array($defaultRatePlans)){
$defaultRatePlan =  reset($defaultRatePlans);
}

$calPrices = null;

$stayAvailability = 0;

$selPriceType = 0;
$selBookingType=0;

$tmpSearchModel = new stdClass;
$tmpSearchModel->FromDate = $checkin;
$tmpSearchModel->ToDate = $checkout;
//$MerchantBookingTypes = BFCHelper::GetMerchantBookingTypeList($tmpSearchModel, $resourceId, $language);
//
//
//if(!empty($MerchantBookingTypes)){
//	$bookingTypesValues = array();
//	foreach($MerchantBookingTypes as $bt)
//	{
//		$bookingTypesValues[$bt->BookingTypeId] = $bt;
//
//		if($bt->IsDefault == true ){
//			$selBookingType = $bt->BookingTypeId;
//		}
//
//	}
//
//	if(empty($selBookingType)){
//		$bt = array_values($bookingTypesValues)[0]; 
//		$selBookingType = $bt->BookingTypeId;
//	}
//}


foreach($allRatePlans as $p) {
	if (!empty($p->BookingType)) {
		$selBookingType = $p->BookingType;
		break;
	}
}

//if(isset($completestay->CalculatedPricesDetails)){
//	$calPrices = $completestay->CalculatedPricesDetails;
//}

if(!empty($defaultRatePlan) && !empty($defaultRatePlan->RatePlanId) ){
	$selPriceType = $defaultRatePlan->RatePlanId;
}

//$viewSummary = !empty($this->params['fromExtForm']) ?: false || $currentState =='optionalPackages' || $currentState =='booking';
//$viewExtra = $currentState =='optionalPackages';
//$viewForm = !empty($this->params['fromExtForm']) ?: false || $currentState =='booking';


//$selPriceTypeObj = null;
//
//if (count($priceTypes) > 0){
//	foreach ($priceTypes as $pt) {
//		if ($pt->Type != $selPriceType)  { 
//			continue;
//		}
//		$selPriceTypeObj = $pt;
//		break;
//	}
//}
//
//$singleRateplan = $this->stay;
//
//
//if(!empty($stay)){
//	if(!empty($stay->CheckIn)){
//		$dateStayCheckin = BFCHelper::parseJsonDate($stay->CheckIn);
//	}
//	if(!empty($stay->CheckOut)){
//		$dateStayCheckout = BFCHelper::parseJsonDate($stay->CheckOut);
//	}
//}
//
//
//
//
//
//
//
//
////$extras = $this->Extras;
////$priceTypes = $this->PriceTypes;
////$currentState = $this->params['state'];
////$refreshState = isset($this->params['refreshcalc']);
//$completestay = null;
//$stay = null;
//
//if(isset($this->stay)) {
//	$stay = $this->stay->SuggestedStay;
//	$completestay = $this->stay;
//}
//
//$calPrices = null;
//$calPkgs = null;
//
//$stayAvailability = 0;
//
//$selPriceType = null;
//$selPriceTags = '';
//$selBookingType=null;
//
//if(isset($stay->Availability)){
//	$stayAvailability = $stay->Availability;
//}
//
//if(isset($this->stay->CalculatedPricesDetails)){
//	$calPrices = $this->stay->CalculatedPricesDetails;
//}
//
//if(!empty($this->stay)){
//	$selPriceType = $this->stay->RatePlanId;
//	$selPriceTags = $this->stay->Tags;
//}
//if(!empty($stay)){
//	$selBookingType = $stay->BookingType;
//}
//
////echo $selPriceType;
////TODO: cosa farne dei pacchetti nel calcolo singolo della risorsa?
//$packages = null;
///*
//if(isset($stay->CalculatedPackages)){
//	$calPkgs = $stay->CalculatedPackages;
//}
//*/
//
//
//$allStaysToView = array();
//
//function pushStay($arr, $resourceid, $resStay, $defaultResource = null) {
//	$selected = array_values(array_filter($arr, function($itm) use ($resourceid) {
//		return $itm->ResourceId == $resourceid;
//	}));
//	$index = 0;
//	if(count($selected) == 0) {
//		$obj = new stdClass();
//		$obj->ResourceId = $resourceid;
//		if(isset($defaultResource) && $defaultResource->ResourceId == $resourceid) {
//			$obj->MerchantName = $defaultResource->MerchantName;
//			$obj->MinCapacityPaxes = $defaultResource->MinCapacityPaxes;
//			$obj->MaxCapacityPaxes = $defaultResource->MaxCapacityPaxes;
//			$obj->Name = $defaultResource->Name;
//			$obj->MrcCategoryName = $defaultResource->MerchantCategoryName;
//			$obj->ImageUrl = $defaultResource->ImageUrl;
//			$obj->Availability = $defaultResource->Availability;
//			$obj->Policy = $resStay->Policy;
//		} else {
//			$obj->MerchantName = $resStay->MrcName;
//			$obj->MinCapacityPaxes = $resStay->MinCapacityPaxes;
//			$obj->MaxCapacityPaxes = $resStay->MaxCapacityPaxes;
//			$obj->Availability = $resStay->Availability;
//			$obj->Name = $resStay->ResName;
//			$obj->MrcCategoryName = $resStay->MrcCategoryName;
//			$obj->ImageUrl = $resStay->ImageUrl;
//			$obj->Policy = $resStay->Policy;
//		}
//		$obj->RatePlans = array();
//		//$obj->Policy = $completestay->Policy;
//		//$obj->Description = $singleRateplan->Description;
//		$arr[] = $obj;
//		$index = count($arr) - 1;
//	} else {
//		$index = array_search($selected[0], $arr);
//		//$obj = $selected[0];
//	}
//
//	$rt = new stdClass();
//	$rt->RatePlanId = $resStay->RatePlanId;
//	$rt->Name = $resStay->Name;	
//	$rt->RatePlanRefId = isset($resStay->RefId) ? $resStay->RefId : "";	
//	$rt->PercentVariation = $resStay->PercentVariation;	
//	
//	$rt->TotalPrice=0;
//	$rt->TotalPriceString ="";
//	$rt->Days=0;
//	$rt->BookingType=$resStay->BookingType;
//	$rt->IsBookable=$resStay->IsBookable;
//	$rt->CheckIn = BFCHelper::parseJsonDate($resStay->CheckIn); 
//	$rt->CheckOut= BFCHelper::parseJsonDate($resStay->CheckOut);
//
//	$rt->CalculatedPricesDetails = $resStay->CalculatedPricesDetails;
//	$rt->SelectablePrices = $resStay->SelectablePrices;
//	$rt->Variations = $resStay->Variations;
//	$rt->SimpleDiscountIds = implode(',', $resStay->SimpleDiscountIds);	
//	if(!empty($resStay->SuggestedStay->DiscountedPrice)){
//		$rt->TotalPrice = (float)$resStay->SuggestedStay->TotalPrice;
//		$rt->TotalPriceString = BFCHelper::priceFormat((float)$resStay->SuggestedStay->TotalPrice);
//		$rt->Days = $resStay->SuggestedStay->Days;
//		$rt->DiscountedPriceString = BFCHelper::priceFormat((float)$resStay->SuggestedStay->DiscountedPrice);
//		$rt->DiscountedPrice = (float)$resStay->SuggestedStay->DiscountedPrice;
//	}
//	
//	$arr[$index]->RatePlans[] = $rt;
//	
//	return $arr;
//}
//
//$hasResourceStay = false;
//
//$eecstays = array();
//
//if(!empty($this->resstays)) {
//	$hasResourceStay = true;
//	foreach($this->resstays as $rst) {
//		$allStaysToView = pushStay($allStaysToView, $resource->ResourceId, $rst, $resource);
//	}
//}
//
//foreach($this->allstays as $rst) {
//	$allStaysToView = pushStay($allStaysToView, $rst->ResourceId, $rst);
//}
//
//$duration = $checkin->diff($checkout);
$resourceImageUrl = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";
if(!empty($resource->ImageUrl)){
	$resourceImageUrl = BFCHelper::getImageUrlResized('resources', $resource->ImageUrl,'small');	
}

$showChildrenagesmsg = isset($_REQUEST['showmsgchildage']) ? $_REQUEST['showmsgchildage'] : 0;
$btnSearchclass=" not-active"; 
if(empty($fromSearch)){
	$btnSearchclass=""; 
}

?>
<script type="text/javascript">
    var daysToEnable = [<?php echo $checkInDates?>];
    var unitId = '<?php echo $resource->ResourceId ?>';
    var checkOutDaysToEnable = [];
	var allStays = <?php echo json_encode($allStaysToView); ?>;
</script>
<br />
<!-- <?php  echo $currentState;?> -->
<!-- FORM -->
		<h4 class="titleform"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_TITLE') ?></h4>
		<form id="calculatorForm" action="<?php echo $formRoute?>#calc" method="POST" class="com_bookingforconnector_resource-calculatorForm com_bookingforconnector_resource-calculatorTable ">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> com_bookingforconnector_resource-calculatorForm-mandatory nopadding">
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> nopadding">
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
							<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> nopadding">
								<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMALL ?>6" id="calcheckin">
									<span class="fieldLabel"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_CHECKIN') ?>:</span>
									<div class="lastdate checking-container">
									<input name="checkin" type="hidden" value="<?php echo $checkin->format('d/m/Y'); ?>" id="<?php echo $checkinId; ?>" readonly="readonly" />
									</div>
									<?php 
										$checkintext = '"<div class=\'buttoncalendar checkinBooking\'><div class=\'dateone day\'><span>'.$checkin->format("d").'</span></div><div class=\'dateone daterwo monthyear\'><p>'.$checkin->format("D").'<br />'.$checkin->format("M").' '.$checkin->format("Y").'  </p></div></div>"';
									?>					
								</div>
								<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMALL ?>6" id="calcheckout">
									<span class="fieldLabel"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_CHECKOUT') ?>:</span>
									<div class="lastdate">
									<input type="hidden" name="checkout" value="<?php echo $checkout->format('d/m/Y'); ?>" id="<?php echo $checkoutId; ?>" readonly="readonly"/>
									</div>
									<?php 
										$checkouttext = '"<div class=\'buttoncalendar checkoutBooking\'><div class=\'dateone day\'><span>'.$checkout->format("d").'</span></div><div class=\'dateone daterwo monthyear\'><p>'.$checkout->format("D").'<br />'.$checkout->format("M").' '.$checkout->format("Y").'  </p></div></div>"';
									?>
								</div>
							</div>
						</div>
						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
							<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> nopadding">
								<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMALL ?>4 com_bookingforconnector_resource-calculatorForm-adult">
									<span class="fieldLabel"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_ADULTS') ?>:</span>
									<?php echo JHTML::_('select.genericlist', $adults, 'adults', array('onchange'=>'quoteCalculatorChanged();','class'=>'input-mini'), 'value', 'text', $nad, "adultscalculator");?>
								</div>
								<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMALL ?>4 com_bookingforconnector_resource-calculatorForm-senior" >
									<span class="fieldLabel"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_SENIORES') ?>:</span>
									<?php echo JHTML::_('select.genericlist', $seniores, 'seniores', array('onchange'=>'quoteCalculatorChanged();','class'=>'input-mini'), 'value', 'text', $nse, "seniorescalculator");?>
								</div>
								<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMALL ?>4 com_bookingforconnector_resource-calculatorForm-children">
									<span class="fieldLabel"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_CHILDREN') ?>:</span>
									<?php echo JHTML::_('select.genericlist', $children, 'children', array('onchange'=>'quoteCalculatorChanged();','class'=>'input-mini'), 'value', 'text', $nch, "childrencalculator");?>
								</div>
								<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMALL ?>4 ">
									<a href="javascript:calculateQuote()" id="calculateButton" class="calculateButton3 <?php echo $btnSearchclass ?>" ><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_CALCULATE') ?> </a>
								</div>
							</div>
						</div>
					</div>

					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> com_bookingforconnector_resource-calculatorForm-childrenages" style="display:none;" >
						<span class="fieldLabel" style="display:inline"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_CHILDRENAGE'); ?></span>
						<span class="fieldLabel" style="display:inline" id="bfi_lblchildrenagesatcalculator"><?php echo JTEXT::_('MOD_BOOKINGFORSEARCH_CHILDRENAGESAT') . " " .$checkout->format("d"). " " .$checkout->format("M"). " " . $checkout->format("Y") ?></span><br />
						<select id="childages1calculator" name="childages1" onchange="quoteCalculatorChanged();" class="inputmini inlineblock" style="display: none;">
							<option value="<?php echo COM_BOOKINGFORCONNECTOR_CHILDRENSAGE ?>" ></option>
							<?php
							foreach (range(0, $maxchildrenAge) as $number) {
								?> <option value="<?php echo $number ?>" <?php echo ($nchs[0] === $number)?"selected":""; //selected( $nchs[0], $number ); ?>><?php echo $number ?></option><?php
							}
							?>
						</select>
						<select id="childages2calculator" name="childages2" onchange="quoteCalculatorChanged();" class="inputmini inlineblock" style="display: none;">
							<option value="<?php echo COM_BOOKINGFORCONNECTOR_CHILDRENSAGE ?>" ></option>
							<?php
							foreach (range(0, $maxchildrenAge) as $number) {
								?> <option value="<?php echo $number ?>" <?php echo ($nchs[1] === $number)?"selected":""; //selected( $nchs[1], $number ); ?>><?php echo $number ?></option><?php
							}
							?>
						</select>
						<br>
						<select id="childages3calculator" name="childages3" onchange="quoteCalculatorChanged();" class="inputmini inlineblock" style="display: none;">
							<option value="<?php echo COM_BOOKINGFORCONNECTOR_CHILDRENSAGE ?>" ></option>
							<?php
							foreach (range(0, $maxchildrenAge) as $number) {
								?> <option value="<?php echo $number ?>" <?php echo ($nchs[2] === $number)?"selected":""; //selected( $nchs[2], $number ); ?>><?php echo $number ?></option><?php
							}
							?>
						</select>
						<select id="childages4calculator" name="childages4" onchange="quoteCalculatorChanged();" class="inputmini inlineblock" style="display: none;">
							<option value="<?php echo COM_BOOKINGFORCONNECTOR_CHILDRENSAGE ?>" ></option>
							<?php
							foreach (range(0, $maxchildrenAge) as $number) {
								?> <option value="<?php echo $number ?>" <?php echo ($nchs[3] === $number)?"selected":""; //selected( $nchs[3], $number ); ?>><?php echo $number ?></option><?php
							}
							?>
						</select>
						<select id="childages5calculator" name="childages5" onchange="quoteCalculatorChanged();" class="inputmini inlineblock" style="display: none;">
							<option value="<?php echo COM_BOOKINGFORCONNECTOR_CHILDRENSAGE ?>" ></option>
							<?php
							foreach (range(0, $maxchildrenAge) as $number) {
								?> <option value="<?php echo $number ?>" <?php echo ($nchs[4] === $number)?"selected":""; //selected( $nchs[4], $number ); ?>><?php echo $number ?></option><?php
							}
							?>
						</select>
					</div>
					<span  id="bfi_lblchildrenagescalculator"> </span>
			</div>	<!-- END com_bookingforconnector_resource-calculatorForm-mandatory -->
			
			<div class="clear"></div>
				<input name="calculate" type="hidden" value="true" />
				<input name="resourceId" type="hidden" value="<?php echo $resource->ResourceId?>" />
				<input name="pricetype" type="hidden" value="<?php echo $selPriceType ?>" />
				<input name="bookingType" type="hidden" value="<?php echo $selBookingType ?>" />
				<input name="state" type="hidden" value="<?php echo $currentState ?>" />
				<input name="extras[]" type="hidden" value="<?php echo $selectablePrices ?>" />
				<input name="refreshcalc" type="hidden" value="1" />
				<input name="s" type="hidden" value="1" />
				<input name="availabilitytype" id="productAvailabilityType" type="hidden" value="<?php echo $ProductAvailabilityType?>" />
				<input type="hidden" value="0" name="showmsgchildage" id="showmsgchildagecalculator"/>
				<div class="hide" id="bfi_childrenagesmsgcalculator">
					<div class="pull-right" style="cursor:pointer;color:red">&nbsp;<i class="fa fa-times-circle" aria-hidden="true" onclick="jQuery('#bfi_lblchildrenagescalculator').popover('destroy');"></i></div>
					<?php echo sprintf(JTEXT::_('MOD_BOOKINGFORSEARCH_CHILDRENAGESMSG'),COM_BOOKINGFORCONNECTOR_CHILDRENSAGE) ?>
				</div>
		</form>		
<!-- END FORM -->

<!-- RESULT -->	
<?php 
$showResult= " hide";
if(!empty($fromSearch)){
	$showResult= "";
} 


	

$resCount = 0;
$totalResCount = count($allRatePlans);

$allResourceId = array_unique(array_map(function ($i) { return $i->ResourceId; }, $allRatePlans));
$keyfirst = array_search($resourceId, $allResourceId);
$tempfirst = array($keyfirst => $allResourceId[$keyfirst]);
unset($allResourceId[$keyfirst]);
$allResourceId = $tempfirst + $allResourceId;
?>

	
<div class="clearfix"></div>
<?php if($CartMultimerchantEnabled) { ?>
	<a href="<?php echo $url_cart_page ?>" class="bookingfor-shopping-cart"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_CART') ?></a>
	<div class="modal fade" id="bfimodalcart">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_CART') ?></h4>
				</div>
				<div class="modal-body">
					<p>One fine body&hellip;</p>
				</div>
				<div class="modal-footer">
					<a href="<?php echo $base_url; ?>" class="btn btn-secondary"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_CART_CONTINUE') ?></a>
					<a href="<?php echo $url_cart_page ?>" class="btn btn-primary">Checkout</a>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
<?php } ?>

<div class="table-responsive tablesearch <?php echo $showResult ?>">
		<table class="table  table-bordered table-resources" style="margin-top: 20px;display: <?php echo $currentState=='optionalPackages' ? 'none' : 'block'; ?>;">
			<thead>
				<tr>
					<th class="resourceimage"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_DESC') ?></th>
					<th></th>
					<th></th>
					<th></th>
					<th><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_QT')?></th>
					<th width="160"></th>
				</tr>
			</thead>
			<tbody>
		<?php if($totalResCount == 0) : ?>
			<tr>
				<td class="resourceimage tab">
					<img src="<?php echo $resourceImageUrl; ?>">
					<a  class="com_bookingforconnector_resourcelist-resourcename-grey" href="<?php echo $formRoute?>">
						<i aria-hidden="true" class="fa fa-check-circle"></i> <?php echo $resource->Name; ?>
					</a>

				</td>
				<td colspan="4" style="vertical-align:middle;text-align:center;">
					<div class="errorbooking" id="errorbooking">
						<strong><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_NORESULT') ?></strong>
						<!-- No disponibile -->
						<?php if($resource->MaxCapacityPaxes > 0 && ( $totalPerson > $resource->MaxCapacityPaxes || $totalPerson < $resource->MinCapacityPaxes )) :?><!-- Errore persone-->
							<br /><?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_ERRORPAXS'), $resource->MinCapacityPaxes, $resource->MaxCapacityPaxes) ?>
						<?php endif;?>
					</div>
				</td>
				<td >&nbsp;</td>
			</tr>
		<?php elseif(!in_array($resource->ResourceId,$allResourceId)) :?>
			<tr>
				<td class="resourceimage tab">
					<img src="<?php echo $resourceImageUrl; ?>">
					<a  class="com_bookingforconnector_resourcelist-resourcename-grey" href="<?php echo $formRoute?>">
						<i aria-hidden="true" class="fa fa-check-circle"></i> <?php echo $resource->Name; ?>
					</a>
				</td>
				<td colspan="4" style="vertical-align:middle;text-align:center;">
					<div class="errorbooking" id="errorbooking">
						<strong><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_NORESULT') ?></strong>
						<!-- No disponibile -->
						<?php if($resource->MaxCapacityPaxes > 0 && ( $totalPerson > $resource->MaxCapacityPaxes || $totalPerson < $resource->MinCapacityPaxes )) :?><!-- Errore persone-->
							<br /><?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_ERRORPAXS'), $resource->MinCapacityPaxes, $resource->MaxCapacityPaxes) ?>
						<?php endif;?>
					</div>
				</td>
				<td >&nbsp;</td>
			</tr>

		<?php endif;?>
<?php 

$allSelectablePrices = array();

$loadScriptTimeSlot = false;
$loadScriptTimePeriod = false;

$allTimeSlotResourceId = array();
$allTimePeriodResourceId = array();

//foreach($allRatePlans as $reskey => $resRateplans) {
$reskey = -1;
foreach($allResourceId as $resId) {
	$reskey += 1;

	$resRateplans =  array_filter($allRatePlans, function($p) use ($resId) {return $p->ResourceId == $resId ;}); // c#: allRatePlans.Where(p => p.ResourceId == resId);

	$res = null;
	if(!empty($defaultRatePlan)){
		foreach($resRateplans as $p) {
			if ($p->RatePlan->RatePlanId == $defaultRatePlan->RatePlan->RatePlanId) {
				$res = $p;
				break;
			}
		}
	}
	if(empty($res)){
		$res = array_values($resRateplans)[0];
	}
 
	$IsBookable = 0;
	
	$isResourceBlock = $res->ResourceId == $resource->ResourceId;


//	if(empty($completestay )){
//		$completestay = $res->RatePlans[0];
//	}		
	
	
	//$completestay->BookingType;
	$IsBookable = $res->IsBookable;
	$showQuote = false;
			
	if (($res->Price > 0 && $res->Availability > 0) && ($res->MaxPaxes == 0 || ($totalPerson <= $res->MaxPaxes && $totalPerson >= $res->MinPaxes)) &&
		(($res->AvailabilityType == 3 || $res->AvailabilityType == 2) && BFCHelper::parseJsonDate($res->RatePlan->CheckIn) == $dateStringCheckin)
		|| 
		(($res->AvailabilityType == 0 || $res->AvailabilityType == 1) && BFCHelper::parseJsonDate($res->RatePlan->CheckIn) == $dateStringCheckin && BFCHelper::parseJsonDate($res->RatePlan->CheckOut) == $dateStringCheckout))		{
			$showQuote = true;
		}


//	$showQuote = ($DiscountedPrice > 0 && $stayAvailability > 0) && ($res->MaxCapacityPaxes ==0 ||(($totalPerson) <= $res->MaxCapacityPaxes && ($totalPerson) >= $res->MinCapacityPaxes) )  && $stayDateStayCheckin == $dateStringCheckin && $stayDateStayCheckout == $dateStringCheckout;
	
	$resourceImageUrl = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";
	if(!empty($res->ImageUrl)){
		$resourceImageUrl = BFCHelper::getImageUrlResized('resources', $res->ImageUrl,'small');	
	}
	$currUriresource = $uri.'&resourceId=' . $res->ResourceId . ':' . BFCHelper::getSlug($res->ResName);
		
	$formRouteSingle = JRoute::_('index.php?option=com_bookingforconnector&view=resource&resourceId=' . $res->ResourceId . ':' . BFCHelper::getSlug($res->ResName));

	if ($res->ResourceId == $resource->ResourceId && !$showQuote)
	{ ?>
		<tr>
			<td class="resourceimage tab">
				<img src="<?php echo $resourceImageUrl ?>">
				<a id="resource-name-<?php echo $resId ?>" class="com_bookingforconnector_resourcelist-resourcename-grey" href="<?php echo $formRouteSingle ?>">
					<i aria-hidden="true" class="fa fa-check-circle hide"></i> <?php echo $res->ResName ?>
				</a>
			</td>
			<td colspan="4" style="text-align:center;vertical-align: middle;">
				<div class="errorbooking" id="errorbooking">
						<strong><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_NORESULT') ?></strong>
					<?php if ($resource->MaxCapacityPaxes > 0 && ($totalPerson > $resource->MaxCapacityPaxes || $totalPerson < $resource->MinCapacityPaxes)) :?>
							<br /><?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_ERRORPAXS'), $resource->MinCapacityPaxes, $resource->MaxCapacityPaxes) ?>
					<?php endif;?>
				</div>
			</td>
			<td></td>
		</tr>
	<?php 
		$resCount++;
		continue; //skip to new resource
	}


	$priceTypes = array();
	foreach($allRatePlans as $p) {
		if ($p->ResourceId == $resId) {
//			$res = $p;

			$type = new stdClass;
			$type->Type = $p->RateplanId;
			$type->IsBase = $p->IsBase;
			$type->Name = $p->RatePlan->Name;
			$type->refid = $p->RatePlan->RefId;
			$type->SortOrder = $p->RatePlan->SortOrder;
			$priceTypes[] = $type;
//			break;
		}
	}

	usort($priceTypes, "BFCHelper::bfi_sortRatePlans");


	$classselect = (count($priceTypes) >1 && $res->AvailabilityType != 3)? '': 'hide';
	
	$SimpleDiscountIds = "";

	if(!empty($res->RatePlan->AllVariationsString)){
		$allVar = json_decode($res->RatePlan->AllVariationsString);
		$SimpleDiscountIds = implode(',',array_unique(array_map(function ($i) { return $i->VariationPlanId; }, $allVar)));
	}
				
		$availability = array();
		$startAvailability = 0;
		$selectedtAvailability = 0;
		if ($cartType == 0)
		{
			$startAvailability = 1;
		}
		for ($i = $startAvailability; $i <= min($res->Availability, COM_BOOKINGFORCONNECTOR_MAXQTSELECTABLE); $i++)
		{
			array_push($availability, $i);
		}
		if ($cartType == 0 && count($availability)>0)
		{
			$selectedtAvailability = $availability[0];
		}else{
			if ($res->ResourceId == $resource->ResourceId && count($availability) > 1)
				{ 
					$selectedtAvailability = $availability[1];
				}
		}


		$eecstay = new stdClass;
		$eecstay->id = "" . $res->ResourceId . " - Resource";
		$eecstay->name = "" . $res->ResName;
		$eecstay->category = $merchant->MainCategoryName;
		$eecstay->brand = $merchant->Name;
//		$eecstay->variant = $showQuote ? strtoupper($selRateName) : "NS";
		$eecstay->position = $reskey;
		if($isResourceBlock) {
			$eecmainstay = $eecstay;
		} else {
			$eecstays[] = $eecstay;
		}
//	$currUriresource = $uri.'&resourceId=' . $res->ResourceId . ':' . BFCHelper::getSlug($res->ResName);
//		
//	$formRouteSingle = JRoute::_('index.php?option=com_bookingforconnector&view=resource&resourceId=' . $res->ResourceId . ':' . BFCHelper::getSlug($res->ResName));
		
//	$formRouteBook = JRoute::_('index.php?option=com_bookingforconnector&view=resource&layout=form&resourceId=' . $res->ResourceId . ':' . BFCHelper::getSlug($res->ResName));
//	if($usessl){
//		$formRouteBook = JRoute::_('index.php?option=com_bookingforconnector&view=resource&layout=form&resourceId=' . $res->ResourceId . ':' . BFCHelper::getSlug($res->ResName),true,1);
//	}

	$btnText = JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_BUTTON');
	$btnClass = "";
	if ($IsBookable){
		$btnText = JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_BUTTON_HTTPS');
		$btnClass = "com_bookingforconnector-bookable";
	}
	$formRouteBook = "";
	if(count(json_decode($res->RatePlan->CalculablePricesString))>0){
		$formRouteBook = "showSelectablePrices"; 
	}

//	$showTitleOtherResource = false;
//	if(($isResourceBlock && $totalResCount > 1) || (!$isResourceBlock && $resCount==0)){
//		$showTitleOtherResource = true;
//	}
//
//	$selState= "optionalPackages";
//
//	if(empty($completestay->SelectablePrices)){
//		$selState= "booking";
//		$formRouteBook = JRoute::_($currUriresource.'&layout=form');
//		if($usessl){
//			$formRouteBook = JRoute::_($currUriresource.'&layout=form',true,1);
//		}
//	}else{	
//		$allSelectablePrices[]=$res;
//	}


	?>
<!-- Quotazione -->
					<tr data-id="<?php echo $res->ResourceId;?>" id="data-id-<?php echo $res->ResourceId ?>">
						<td class="resourceimage tab">
							<img id="resource-img-<?php echo $resId ?>" src="<?php echo $resourceImageUrl; ?>" class="img-responsive" />
							<?php if ($resId == $resource->ResourceId){ ?>
								<a id="resource-name-<?php echo $resId ?>" class="com_bookingforconnector_resourcelist-resourcename-blue" href="<?php echo $formRouteSingle ?>">
									<i aria-hidden="true" class="fa fa-check-circle"></i> <?php echo $res->ResName; ?>
								</a>
							<?php } else { ?>
								<a id="resource-name-<?php echo $resId ?>" class="com_bookingforconnector_resourcelist-resourcename-grey" href="<?php echo $formRouteSingle ?>">
									<i aria-hidden="true" class="fa fa-check-circle" style="display:none;"></i> <?php echo $res->ResName; ?>
								</a>
							<?php } ?>
						</td>							
						<td class="resourcepaxes tab" style="text-align: center;">
						<?php if ($res->MaxPaxes>0):?>
							<div class="com_bookingforconnector-search-resource-paxes com_bookingforconnector-item-secondary-paxes" style="padding-top: 0;font-size:16px;">
								<i class="fa fa-user"></i>
								<span style="white-space: nowrap;"><?php echo ($res->MinPaxes != $res->MaxPaxes)? $res->MinPaxes . "-" : "" ?><?php echo  $res->MaxPaxes ?></span>
							</div>
							<?php endif; ?>
						</td>
						<td class="resourcepricetypes tab" style="text-align: center;">
							<div class="com_bookingforconnector_merchantdetails-resource-pricetypes">
								<?php
/*-------------------------------*/	
									if ($res->AvailabilityType == 2)
									{
										$loadScriptTimePeriod = true;
										array_push($allTimePeriodResourceId, $res->ResourceId);
									?>
									<div class="bookingfor_field <?php echo ($res->ResourceId != $resource->ResourceId) ? " " : "hide" ?>">
										<!-- blocked-->
										<div class="buttoncalendar check-availibility-date">
											<div class="dateone lastdate timpeslot">
												<input type="text" name="checkin" value="<?php echo $checkin->format('d/m/Y'); ?>" class="ChkAvailibilityFromDateTimePeriod" hidden = "hidden" data-id="<?php echo $res->ResourceId ?>" id="ChkAvailibilityFromDateTimePeriod_<?php echo $res->ResourceId ?>" data-availabilityType="<?php echo $res->AvailabilityType ?>" data-timeLength="" readonly="readonly" />
											</div>
										</div>
									</div>
									<select class="selectpickerTimePeriodStart selectpickerTimePeriodStart-<?php echo $res->ResourceId ?>" data-resid="<?php echo $res->ResourceId ?>" data-id="ResourceId-<?php echo $res->ResourceId ?>"></select>
									<select class="selectpickerTimePeriodEnd selectpickerTimePeriodEnd-<?php echo $res->ResourceId ?>" data-resid="<?php echo $res->ResourceId ?>" data-id="ResourceId-<?php echo $res->ResourceId ?>"></select>
								<?php
									}
/*-------------------------------*/	
									if ($res->AvailabilityType == 3)
									{
										$loadScriptTimeSlot = true;
										array_push($allTimeSlotResourceId, $res->ResourceId);

									?>
									<div class="bookingfor_field <?php echo ($res->ResourceId != $resource->ResourceId) ? " " : "hide" ?> ">
										<!-- blocked-->
										<div class="buttoncalendar check-availibility-date">
											<div class="dateone lastdate timpeslot">
												<input type="text" name="checkin" value="<?php echo $checkin->format('d/m/Y'); ?>" class="ChkAvailibilityFromDateTimeSlot" hidden = "hidden" data-id="<?php echo $res->ResourceId ?>" id="ChkAvailibilityFromDateTimeSlot_<?php echo $res->ResourceId ?>" readonly="readonly" />
											</div>
										</div>
									</div>
									<select class="selectpickerTimeSlotRange selectpickerTimeSlotRange-<?php echo $res->ResourceId ?> input-group-btn" data-resid="<?php echo $res->ResourceId ?>"></select>
								<?php
									}								

/*-------------------------------*/									

								
							?>
							<select id="pricetypeselected<?php echo $res->ResourceId ?>" name="pricetype" onchange="changePriceType(jQuery,this);" class="pricetypes <?php echo $classselect ?> input-group-btn" data-resource="<?php echo $res->ResourceId ?>">
								<?php
								foreach ($priceTypes as $pt) {
									?> <option value="<?php echo $pt->Type ?>" <?php echo ($res->RateplanId == $pt->Type )?"selected":""; //selected( $res->RateplanId, $pt->Type ); ?> data-refid="<?php echo $pt->refid ?>"><?php echo BFCHelper::getLanguage($pt->Name, $language) ?></option><?php
								}
								?>
							</select>

							</div>
							<?php if (count($priceTypes) == 1 && !$priceTypes[0]->IsBase): ?>
								<div style="text-align:left;">
									<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_TREATMENT')?>:<br />
									<?php echo $priceTypes[0]->Name ?>
								</div>
							<?php endif; ?>
							<ul style="padding:0px;padding-left:10px;margin:0;">
								<?php if(!empty($res->RatePlan->Policy)): ?>
									<li style="font-size: 12px; text-align: left;text-indent: -4px;"><?php echo $res->RatePlan->Policy ?></li>
								<?php endif;?>
							</ul>
						</td>
						<td class="resourcetotal tab">
							<?php if( $res->Price> 0) :?><!-- disponibile -->

								<div class="com_bookingforconnector_merchantdetails-resource-stay com_bookingforconnector_resource-quote">
									<div class="com_bookingforconnector_merchantdetails-resource-stay com_bookingforconnector_resource-quote" style="text-align:center;">
										<span data-value="<?php echo $res->Price ?>" class="com_bookingforconnector_merchantdetails-resourcelist-stay-total wd-100 <?php echo ($res->Price < $res->TotalPrice ? "red-color" : "") ?> bfi_<?php echo $currencyclass ?>"><?php echo BFCHelper::priceFormat($res->Price) ?></span>
									</div>
									<?php if ($res->Price < $res->TotalPrice) 
									{ ?>
										<div class="com_bookingforconnector_merchantdetails-resource-stay-price originalquote  wd-100">
											<span class="com_bookingforconnector_strikethrough notvars  wd-100">
												<span data-value="<?php echo $res->TotalPrice ?>" class="com_bookingforconnector_merchantdetails-resource-stay-discount gray-highlight wd-100 bfi_<?php echo $currencyclass ?>"><?php echo BFCHelper::priceFormat($res->TotalPrice) ?></span>
											</span>
										</div>
									<?php } ?>
									<div class="clearfix"></div>
									<div class="specialoffer variationlabel" style="<?php echo ($res->PercentVariation < 0 ? " display:block" : "display:none"); ?>" rel="<?php echo $SimpleDiscountIds ?>" rel1="<?php echo $res->ResourceId ?>">
										<?php echo $res->PercentVariation ?>% <?php echo JTEXT::_('MOD_BOOKINGFORSEARCHFILTER_OFFERS') ?> <i class="fa fa-angle-down" aria-hidden="true"></i>
									</div>
								</div>

				<!-- bottone Continua -->
							<?php else:?>
								<strong><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_NORESULT') ?></strong>
							<?php endif;?>
						</td>
						<td>
							<select name="ddlrooms-<?php echo $res->ResourceId ?>" class="ddlrooms inputmini" id="ddlrooms-<?php echo $res->ResourceId ?>" onchange="UpdateQuote(this);" data-resid="<?php echo $res->ResourceId ?>" data-availabilitytype="<?php echo $res->AvailabilityType ?>">
							<?php 
								foreach ($availability as $number) {
									?> <option value="<?php echo $number ?>" <?php echo ($selectedtAvailability== $number)?"selected":""; //selected( $selectedtAvailability, $number ); ?>><?php echo $number ?></option><?php
								}
							?>
							</select>
						</td>
<!-- button Book-->
<?php if($cartType ==0){ ?>
						<td >
							<div id="dvTotal-<?php echo $res->ResourceId ?>">
							<p class="text-center lblLodging"><span>1</span> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_QT')?></p>
							<div class="com_bookingforconnector_merchantdetails-resource-stay com_bookingforconnector_resource-quote">
								<span class="totalQuote com_bookingforconnector_merchantdetails-resourcelist-stay-total wd-100 <?php echo ($res->Price < $res->TotalPrice ? " red-color" : "" ) ?> bfi_<?php echo $currencyclass ?>" style="text-align:center;">
									<?php echo BFCHelper::priceFormat($res->Price) ?>
								</span>
							</div>
							<div class="com_bookingforconnector_merchantdetails-resource-stay-price  wd-100">
								<span class="com_bookingforconnector_strikethrough  wd-100">
									<span class="totalQuoteDiscount com_bookingforconnector_merchantdetails-resource-stay-discount gray-highlight wd-100 <?php echo ($res->Price < $res->TotalPrice ? " " : " hidden") ?> bfi_<?php echo $currencyclass ?>">
										<?php echo BFCHelper::priceFormat($res->TotalPrice) ?>
									</span>
								</span>
							</div>

							<div class="clearfix"></div>
							<button data-resid="<?php echo $res->ResourceId ?>" class="<?php echo $btnClass ?> com_bookingforconnector-item-secondary-more " type="button" data-formroute="<?php echo $formRouteBook ?>" onclick="ChangeVariation(this);">
								<?php echo $btnText ?>
							</button>
						</div>
					</td>
<?php }else{ ?>
						<?php // if($res->ResourceId == $resource->ResourceId){ ?>
						<?php if($reskey ==0){ ?>
							<?php if ($cartType == 1 && $currentCartConfiguration != null) //////// && ($currentCartConfiguration as List<CartOrder>).Any(t => t.Resources.Any(r => r.MerchantId != Model.MerchantId)))
							{ ?>
								<td rowspan="<?php echo (count($allResourceId) > 1 ? count($allResourceId) + 1 : 1) ?>" style="vertical-align: middle;"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_NO_ANOTHER_MERCHANT') ?></td>
							
							<?php } else{ ?>
								<td rowspan="<?php echo (count($allResourceId) > 1 ? count($allResourceId) + 1 : 1) ?>">
									<p class="text-center lblLodging"><span>1</span> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_QT')?></p>
									<div class="com_bookingforconnector_merchantdetails-resource-stay com_bookingforconnector_resource-quote">
										<span class="totalQuote com_bookingforconnector_merchantdetails-resourcelist-stay-total wd-100 <?php echo ($res->Price < $res->TotalPrice ? " red-color" : "" ) ?> bfi_<?php echo $currencyclass ?>" style="text-align:center;">
											<?php echo BFCHelper::priceFormat($res->Price) ?>
										</span>
									</div>
									<div class="com_bookingforconnector_merchantdetails-resource-stay-price  wd-100">
										<span class="com_bookingforconnector_strikethrough  wd-100">
											<span class="totalQuoteDiscount com_bookingforconnector_merchantdetails-resource-stay-discount gray-highlight wd-100 <?php echo ($res->Price < $res->TotalPrice ? " " : " hidden") ?> bfi_<?php echo $currencyclass ?>">
												<?php echo BFCHelper::priceFormat($res->TotalPrice) ?>
											</span>
										</span>
									</div>

									<div class="clearfix"></div>
									<button id="btnBookNow" class="<?php echo $btnClass ?> com_bookingforconnector-item-secondary-more" type="button" data-formroute="<?php echo $formRouteBook ?>" onclick="ChangeVariation(this);">
										<?php echo $btnText ?>
									</button>
								</td>
						<?php } ?>
					<?php } ?>
<?php } ?>

					</tr>
					<tr id="troffers<?php echo  $res->ResourceId ?>" style="display:none ;">
						<td colspan="<?php echo ($cartType == 0 ? 6 : 5) ?>">
							<div class="divoffers" id="divoffers<?php echo  $res->ResourceId ?>">
									<i class="fa fa-spinner fa-spin fa-fw margin-bottom"></i>
									<span class="sr-only">Loading...</span>
							</div>
						</td>
					</tr>
		<?php
		if (count($allResourceId) > 1 && (($resId == $resource->ResourceId && $resCount > 1) || ($resId == $resource->ResourceId && $resCount == 0))): ?>
					<tr><td colspan="<?php echo ($cartType == 0 ? 6 : 5) ?>"><h3 style="color: #0895FF"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_OTHER_RESOURCE') ?></h3></td></tr>
		<?php endif; ?>
	<?php 
		$resCount++;
 } 
 ?>
			</tbody>
		</table>
		<!-- end table-resources -->
  
<!-- Service -->
<script>
    var servicesAvailability=[];
</script>

<?php if(count($allResourceId)>0){ ?>
    <div class="div-selectableprice table-responsive" style="display:none;">
		<h4 class="titleform"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_SERVICE') ?></h4>
<?php 
	foreach($allResourceId as $resourceId) {
		$resRateplans =  array_filter($allRatePlans, function($p) use ($resourceId) {return $p->ResourceId == $resourceId ;}); // c#: allRatePlans.Where(p => p.ResourceId == resId);
		$res = null;
		if(!empty($defaultRatePlan)){
			foreach($resRateplans as $p) {
				if ($p->RatePlan->RatePlanId == $defaultRatePlan->RatePlan->RatePlanId) {
					$res = $p;
					break;
				}
			}
		}
		if(empty($res)){
			$res = array_values($resRateplans)[0];
		}
		$selectablePrices = json_decode($res->RatePlan->CalculablePricesString);
		if (count($selectablePrices) == 0)
		{
			continue; //don't display table skip to next
		}

		$SimpleDiscountIds = "";

		if(!empty($res->RatePlan->AllVariationsString)){
			$allVar = json_decode($res->RatePlan->AllVariationsString);
			$SimpleDiscountIds = implode(',',array_unique(array_map(function ($i) { return $i->VariationPlanId; }, $allVar)));
		}


?>
		<div id="services-room-1-<?php echo $res->ResourceId ?>" style="display:none;">
		<h5 class="titleform"><?php echo $res->ResName;?></h5>
		<!-- table-selectableprice -->
		<table class="table  table-bordered table-selectableprice" style="margin-top: 20px;">
			<thead>
				<tr>
					<th class="resourceimage"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_SERVICE') ?></th>
					<th></th>
					<th></th>
					<th><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_QT')?></th>
					<th width="160" style="white-space: nowrap;"></th>

				</tr>
			</thead>
			<tbody>
<?php 
		foreach($selectablePrices as $selPrice) {		
			$selPriceImageUrl =  Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";

			if (!empty($selPrice->DefaultImg)){ 
				$selPriceImageUrl = BFCHelper::getImageUrlResized("resources", $selPrice->DefaultImg, "small"); 
			}
			$ddlQty = array();
			$countPrices = 0;
			$min = $selPrice->MinQt != null ? (int)$selPrice->MinQt : 0;
			$max = $selPrice->MaxQt != null ? (int)$selPrice->MaxQt : 0;
			for ($i = $min; $i <= $max; $i++) {
				$value = $selPrice->PriceId . ':' . $i;
				$text = $i > 0 ? $i : JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_NOSERVICE');
				$ddlQty[] =  '<option value="'.$value.'" '. ($selPrice->DefaultQt == $i ? "selected" : "") .' >'.$text.'</option>';
			}			

		
//		foreach($selPrices as $selPrice) {		
//			$extraopts = array();
//			$selPrice->DefaultQt = isset($selPrice->DefaultQt) ? $selPrice->DefaultQt : 0;
//			$min = $selPrice->MinQt != null ? (int)$selPrice->MinQt : 0;
//			$max = $selPrice->MaxQt != null ? (int)$selPrice->MaxQt : 0;
//			for ($i = $min; $i <= $max; $i++) {
//				$opt = new stdClass;
//				$opt->Id = $selPrice->PriceId . ':' . $i;
//				$opt->Name = $i > 0 ? $i : JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_NOSERVICE');
//				$opt->attr = array(
//					'data-priceid' => $selPrice->PriceId
//				);
//				/*$value = $selPrice->PriceId . ':' . $i;
//				$text = $i > 0 ? $i : JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_NOSERVICE');
//				$extraopts[] = JHTML::_('select.option', $value, $text);*/
//				$extraopts[] = $opt;
//			}
//			$selectedExtra = '';
//			$totalPrice = $selPrice->TotalAmount;
//			$totalDiscountedPrice = $selPrice->TotalDiscounted;
//			foreach ($calPrices as $calPrice) {
//				if ($calPrice->PriceId != $selPrice->PriceId) continue;
//				$selectedExtra = $selPrice->PriceId . ':' . $calPrice->CalculatedQt;
//				$totalPrice = $calPrice->TotalAmount;
//				$totalDiscountedPrice = $calPrice->TotalAmount;
//				break;
//			}
//			
//			if ($selectedExtra == '') {
//				if ($selPrice->DefaultQt != null) {
//					$selectedExtra = $selPrice->PriceId . ':' . $selPrice->DefaultQt;
//				}
//				foreach ($selExtras as $se) {
//					$vals = explode(':', $se);
//					if (count($vals) < 2 || $vals[0] != $selPrice->PriceId) continue;
//					$selectedExtra = $se . ''; // secure cast to string
//					break;
//				}
//			}
?>
				<tr class="data-sel-id-<?php echo $res->ResourceId ?>">
					<td class="resourceimage tab">
						<img src="<?php echo $selPriceImageUrl; ?>"  class="img-responsive" >
						<span class="name-selectableprice-1-<?php echo $selPrice->PriceId ?>-<?php echo $res->ResourceId ?> com_bookingforconnector_resourcelist-resourcename-grey">
							<i aria-hidden="true" class="fa fa-check-circle" style="display:none;"></i> <?php echo $selPrice->Name; ?>
						</span>
					</td>
					<td class="bfi_pricetype">
                                   <script>
                                        servicesAvailability[<?php echo $selPrice->PriceId ?>] =<?php echo (!empty($selPrice->Availability)? min($selPrice->Availability, COM_BOOKINGFORCONNECTOR_MAXQTSELECTABLE) : 0) ?> ;
                                    </script>
                                    <?php if ($selPrice->AvailabilityType == 2)
                                    {
                                        $loadScriptTimePeriod = true;
                                        array_push($allTimePeriodResourceId, $selPrice->PriceId);
                                    ?>
                                        <div>
                                            <!-- blocked-->
                                            <div class="buttoncalendar check-availibility-date">
                                                <div class="dateone lastdate timpeslot">
													<input type="text" name="checkin" value="<?php echo $checkin->format('d/m/Y'); ?>" class="ChkAvailibilityFromDateTimePeriod extraprice" hidden = "hidden" data-id="<?php echo $selPrice->RelatedProductId ?>" id="ChkAvailibilityFromDateTimePeriod_<?php echo $selPrice->RelatedProductId ?>_<?php echo $selPrice->PriceId ?>_<?php echo uniqid() ?>" data-availabilityType="<?php echo $selPrice->AvailabilityType ?>" data-timeLength="" readonly="readonly" />
                                                </div>
                                            </div>
                                        </div>
                                        <select class="selectpickerTimePeriodStart selectpickerTimePeriodStart-<?php echo $selPrice->RelatedProductId ?>" data-resid="<?php echo $selPrice->RelatedProductId ?>" data-id="extra-<?php echo $selPrice->RelatedProductId ?>"></select>
                                        <select class="selectpickerTimePeriodEnd selectpickerTimePeriodEnd-<?php echo $selPrice->RelatedProductId ?>" data-resid="<?php echo $selPrice->RelatedProductId ?>" data-id="extra-<?php echo $selPrice->RelatedProductId ?>"></select>
                                   <?php  } ?>



                                    <?php if ($selPrice->AvailabilityType == 3)
                                    {
                                        $loadScriptTimeSlot = true;
										array_push($allTimeSlotResourceId, $selPrice->PriceId);
									?> 
                                        <div>
                                            <!-- blocked-->
                                            <div class="buttoncalendar check-availibility-date">
												<div class="dateone lastdate timpeslot">
													<input type="text" name="checkin" value="<?php echo $checkin->format('d/m/Y'); ?>" class="ChkAvailibilityFromDateTimeSlot" hidden = "hidden" data-id="<?php echo $selPrice->RelatedProductId ?>" id="ChkAvailibilityFromDateTimeSlot_<?php echo $selPrice->RelatedProductId ?>_<?php echo uniqid() ?>" readonly="readonly" />
                                                </div>
                                            </div>
                                        </div>
                                        <select class="selectpickerTimeSlotRange selectpickerTimeSlotRange-<?php echo $selPrice->RelatedProductId ?> input-group-btn" data-resid="<?php echo $selPrice->RelatedProductId ?>"></select>
                                    <?php } ?>

					


					</td>
					<td class="totalextrasselect-1-<?php echo $selPrice->PriceId ?>-<?php echo $res->ResourceId ?>">
						<div class="totalextrasselectdiv" style="<?php echo ($selPrice->TotalDiscounted==0) ? "display:none;" : ""; ?>">
							<div class="com_bookingforconnector_merchantdetails-resource-stay com_bookingforconnector_resource-quote">
								<div class="com_bookingforconnector_merchantdetails-resource-stay com_bookingforconnector_resource-quote" style="text-align:center;">
									<span data-value="<?php echo $selPrice->TotalDiscounted ?>" class="com_bookingforconnector_merchantdetails-resourcelist-stay-total wd-100 <?php echo ($selPrice->TotalDiscounted < $selPrice->TotalAmount ? "red-color" : "") ?> bfi_<?php echo $currencyclass ?>"><?php echo BFCHelper::priceFormat($selPrice->TotalDiscounted) ?></span>
								</div>
								<div class="com_bookingforconnector_merchantdetails-resource-stay-price originalquote wd-100" style="<?php echo ($selPrice->TotalDiscounted < $selPrice->TotalAmount ? "display:none;" : "") ?>">
									<span class="com_bookingforconnector_strikethrough notvars wd-100">
										<span data-value="<?php echo $selPrice->TotalAmount ?>" class="com_bookingforconnector_merchantdetails-resource-stay-discount gray-highlight wd-100 bfi_<?php echo $currencyclass ?>"><?php echo BFCHelper::priceFormat($selPrice->TotalAmount) ?></span>
									</span>
								</div>
								<div class="clearfix"></div>
								<div class="variationlabel specialoffer" style="display:<?php echo ($res->PercentVariation < 0 ? "block" : "none") ?>;" rel="<?php echo $SimpleDiscountIds ?>" rel1="<?php echo $res->ResourceId ?>">
									<?php echo $res->PercentVariation ?> % <?php echo JTEXT::_('MOD_BOOKINGFORSEARCHFILTER_OFFERS') ?> 
								</div>
							</div>
						</div>
					</td>
					<td>
						<select id="extras-1-<?php echo $selPrice->PriceId?>-<?php echo $res->ResourceId?>" name="extras-1-<?php echo $selPrice->PriceId?>-<?php echo $res->ResourceId?>" onchange="quoteCalculatorServiceChanged(this);" class="extrasselect-1-<?php echo $res->ResourceId ?> dllextraselected_<?php echo $selPrice->RelatedProductId ?> ddlrooms inputmini" data-maxvalue="<?php echo $selPrice->MaxQt ?>" data-minvalue="<?php echo $selPrice->MinQt ?>" data_resid="<?php echo $selPrice->RelatedProductId ?>" data-availabilityType="<?php echo $selPrice->AvailabilityType ?>">
							<?php
							foreach ($ddlQty as $extraopt) { 
								echo $extraopt;
							}
							?>
						</select>
					</td>
					<td>
					<?php if($countPrices==0){ ?>
					
						<div class="totalextrasstay" style="display:none;">
							<p class="text-center lblLodging"><span>1</span> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_QT')?></p>
							<div class="com_bookingforconnector_merchantdetails-resource-stay com_bookingforconnector_resource-quote">
								<span class="totalQuote com_bookingforconnector_merchantdetails-resourcelist-stay-total wd-100 bfi_<?php echo $currencyclass ?>" style="text-align:center;">
									<?php echo  BFCHelper::priceFormat(0) ?>
								</span>
							</div>
							<div class="com_bookingforconnector_merchantdetails-resource-stay-price wd-100 ">
								<span class="com_bookingforconnector_strikethrough  wd-100">
									<span class="totalQuoteDiscount com_bookingforconnector_merchantdetails-resource-stay-discount gray-highlight wd-100 hidden bfi_<?php echo $currencyclass ?>"><?php echo  BFCHelper::priceFormat(0) ?></span>
								</span>
							</div>
							<div class="clearfix"></div>
							<button class="<?php echo $btnClass ?> com_bookingforconnector-item-secondary-more " type="button" onclick="BookNow(this);">
								<?php echo $btnText ?>
							</button>
						<?php } ?>
					</td>
				</tr>
					<tr id="troffersextras<?php echo  $res->ResourceId?>_<?php echo  $selPrice->PriceId ?>" style="display:none ;">
						<td colspan="5">
							<div class="divoffers" id="divoffersextras<?php echo  $res->ResourceId?>_<?php echo  $selPrice->PriceId ?>">
									<i class="fa fa-spinner fa-spin fa-fw margin-bottom"></i>
									<span class="sr-only">Loading...</span>
							</div>
						</td>
					</tr>
					<tr id="troffersextra<?php echo  $res->ResourceId?>" style="display:none ;">
						<td colspan="5">
							<div class="divoffers" id="divoffersextra<?php echo  $res->ResourceId?>">
									<i class="fa fa-spinner fa-spin fa-fw margin-bottom"></i>
									<span class="sr-only">Loading...</span>
							</div>
						</td>
					</tr>
<?php 
$countPrices+=1;
		}//end foreach selPrices
?>

			</tbody>
		</table>
		<!-- end table-selectableprice -->
		</div>
<?php 

	}//end foreach div-selectableprice allResourceId
?>
    </div>
<?php 
 } //end if(!empty div-selectableprice
?>

<form action="<?php echo $formOrderRouteBook ?>" id="frm-order" method="post"></form>

<script type="text/javascript">
var localeSetting = "<?php echo substr($language,0,2); ?>";
var productAvailabilityType = <?php echo $ProductAvailabilityType?>;
var offersLoaded = [];
var allStays = <?php echo json_encode($allRatePlans) ?>; 

//	function showSelectablePrices() {
//		jQuery(".table-resources").hide();
//		resourceSelected = jQuery('input[name="resourceId"]','#calculatorForm').val();
////		console.log(resourceSelected);
//		jQuery(".div-selectableprice"+resourceSelected).show();
//		jQuery('html, body').animate({
//			scrollTop: jQuery(".div-selectableprice"+resourceSelected).offset().top
//		}, 100);
//		var currentRateplan = jQuery('input[name="pricetype"]','#calculatorForm').val();
//		var currentResource = jQuery('input[name="resourceId"]','#calculatorForm').val();
//		
//		var resource = jQuery.grep(allStays, function(rs) {
//			return rs.ResourceId == parseInt(currentResource);
//		})[0];
//		
//		var allCalc = jQuery.grep(resource.RatePlans, function(rt) {
//			return rt.RatePlanId == parseInt(currentRateplan);
//		});
//		if(allCalc.length) {
//			var calc = allCalc[0];
//			
//			<?php if($this->analyticsEnabled): ?>
//			
//			callAnalyticsEEc("addProduct", [{
//				"id": currentResource + " - Resource",
//				"name": resource.Name,
//				"category": resource.MrcCategoryName,
//				"price": calc.TotalDiscounted,
//				"brand": resource.MerchantName,
//				"variant": calc.RatePlanRefId.toUpperCase(),
//			}], "addToCart", null, null,"Resources");
//			currentCalculatedPrices = calc.CalculatedPricesDetails;
//			callAnalyticsEEc("addImpression", jQuery.makeArray(jQuery.map(calc.SelectablePrices, function(svc, idx) {
//				return {
//					"id": "" + svc.PriceId + " - Service",
//					"category": "Services",
//					"name": svc.Name,
//					"brand": resource.MerchantName,
//					"variant": resource.Name,
//					"position": idx
//				};
//			})), "list", "Services List");
//			<?php endif; ?>
//		}
//	}
//	
//	function showResources(){
//		jQuery(".table-resources").show();
//		jQuery(".div-selectableprice").hide();
//	}
	
	function updateTitleBooking(classToAdd,classToRemove){
		jQuery("#ui-datepicker-div").addClass("notranslate");
		jQuery("#ui-datepicker-div").addClass(classToAdd);
		jQuery("#ui-datepicker-div").removeClass(classToRemove);
		var checkindate = jQuery('#<?php echo $checkinId; ?>').val();
		var checkoutdate = jQuery('#<?php echo $checkoutId; ?>').val();

		var d1 = checkindate.split("/");
		var d2 = checkoutdate.split("/");

		var from = new Date(Date.UTC(d1[2], d1[1]-1, d1[0]));
		var to   = new Date(Date.UTC(d2[2], d2[1]-1, d2[0]));

		diff  = new Date(to - from),
		days  = Math.ceil(diff/1000/60/60/24);
//		var productAvailabilityType = jQuery('#productAvailabilityType').val();
		var strSummary = 'Check-in '+('0' + from.getDate()).slice(-2)+' '+from.toLocaleString(localeSetting, { month: "short" });
		var strSummaryDays = "(" +days+" <?php echo strtolower (JTEXT::_('MOD_BOOKINGFORSEARCH_NIGHT')) ?>)";
		if (productAvailabilityType == 0) {
			days += 1;
			strSummaryDays ="(" +days+" <?php echo strtolower (JTEXT::_('MOD_BOOKINGFORSEARCH_DAYS')) ?>)";
		}
		if (productAvailabilityType == 0 || productAvailabilityType == 1) {
			strSummary += ' Check-out '+('0' + to.getDate()).slice(-2)+' '+to.toLocaleString(localeSetting, { month: "short" })+' '+d2[2]+' ' + strSummaryDays;
		}
		jQuery("#durationdays").html(days);

		jQuery('#ui-datepicker-div').attr('data-before',strSummary);
	
	}

	function printChangedDateBooking(date, elem) {
		var checkindate = jQuery('#<?php echo $checkinId; ?>').val();
		var checkoutdate = jQuery('#<?php echo $checkoutId; ?>').val();

		var d1 = checkindate.split("/");
		var d2 = checkoutdate.split("/");

		var from = new Date(d1[2], d1[1]-1, d1[0]);
		var to   = new Date(d2[2], d2[1]-1, d2[0]);

		day1  = ('0' + from.getDate()).slice(-2), 
			
		month1 = from.toLocaleString(localeSetting, { month: "short" }),              
		year1 =  from.getFullYear(),
		weekday1 = from.toLocaleString(localeSetting, { weekday: "short" });

		day2  = ('0' + to.getDate()).slice(-2),  
		month2 = to.toLocaleString(localeSetting, { month: "short" }),              
		year2 =  to.getFullYear(),
		weekday2 = to.toLocaleString(localeSetting, { weekday: "short" });
		
		jQuery('.checkinBooking').find('.day span').html(day1);
		jQuery('.checkoutBooking').find('.day span').html(day2);
		if (typeof Intl == 'object' && typeof Intl.NumberFormat == 'function') {
			jQuery('.checkinBooking').find('.monthyear p').html(weekday1 + "<br />" + month1+" "+year1); 
			jQuery('.checkoutBooking').find('.monthyear p').html(weekday2 + "<br />" + month2+" "+year2);
			jQuery('#bfi_lblchildrenagesatcalculator').html("<?php echo strtolower (JTEXT::_('MOD_BOOKINGFORSEARCH_CHILDRENAGESAT')) ?>" + day2 + " " + month2 + " " + year2);
		} else {
			jQuery('.checkinBooking').find('.monthyear p').html(d1[1]+"/"+d1[2]);  
			jQuery('.checkoutBooking').find('.monthyear p').html(d2[1]+"/"+d2[2]);
			jQuery('#bfi_lblchildrenagesatcalculator').html("<?php echo strtolower (JTEXT::_('MOD_BOOKINGFORSEARCH_CHILDRENAGESAT')) ?>" + day2 + " " + d2[1] + " " + d2[2]);
		}

		diff  = new Date(to - from),
		days  = Math.ceil(diff/1000/60/60/24);
		if (productAvailabilityType == 0) {
			days += 1;
		}
		jQuery("#durationdays").html(days);

	}

	function insertCheckinTitleBooking() {
		setTimeout(function() {updateTitleBooking("checkin","checkout")}, 1);
	}
	function insertCheckoutTitleBooking() {
		setTimeout(function() {updateTitleBooking("checkout","checkin")}, 1);
	}
	var calculator_checkin = null;
	var calculator_checkout = null;

	jQuery(function($) {
        if(bookingfor.bsVersion ==3){
			$('.pricetypes').selectpicker({
				container: 'body',
				template: {
					caret: '<i class="fa fa-cutlery"></i>'
				}
			});
		}
        $(".variationlabel").on("click", function () {
            var show = function (resourceId, text) {
                $("#divoffers-" + resourceId).empty();
                $("#divoffers-" + resourceId).html(text);
            };
            var discountIds = $(this).attr('rel');
            var resourceId = $(this).attr('rel1');

            if ($("#troffers-" + resourceId).is(":visible")) {
                $("#troffers-" + resourceId).slideUp("slow");
                $(this).find("i").toggleClass("fa-angle-up fa-angle-down");
            } else {
                $("#troffers-" + resourceId).slideDown("slow");
                $(this).find("i").toggleClass("fa-angle-up fa-angle-down");
            }

            if (!offersLoaded.hasOwnProperty(discountIds)) {
                GetDiscountsInfo(discountIds, resourceId, show);

            } else {
                show(resourceId, offersLoaded[discountIds]);
            }
        });		
		function GetDiscountsInfo(discountIds, obj, fn) {
			var query = "discountIds=" + discountIds;
			if (cultureCode.length>1)
			{
				cultureCode = cultureCode.substring(0, 2).toLowerCase();
			}
			if (defaultcultureCode.length>1)
			{
				defaultcultureCode = defaultcultureCode.substring(0, 2).toLowerCase();
			}

			var queryDiscount = "discountId=" + discountIds + "&language=<?php echo $language ?>&task=getDiscountDetails";
//			$.getJSON(urlCheck + "?" + query, function (data) {
			jQuery.post(urlCheck, queryDiscount, function(data) {

				$html = '';
				$.each(data || [], function (key, val) {
					var name = val.Name;
					var descr = val.Description;
					name = bookingfor.nl2br($("<p>" + name + "</p>").text());
					$html += '<p class="title">' + name + '</p>';
					descr = bookingfor.nl2br($("<p>" + descr + "</p>").text());
					$html += '<p class="description ">' + descr + '</p>';
				});
				offersLoaded[discountIds] = $html;
				fn(obj, $html);
//			});
			},'json');
		}

		calculator_checkin = function() { $("#<?php echo $checkinId; ?>").datepicker({
			numberOfMonths: parseInt("<?php echo COM_BOOKINGFORCONNECTOR_MONTHINCALENDAR;?>")
			,defaultDate: "+2d"
			,dateFormat: "dd/mm/yy"
			, minDate: '<?php echo $startDate->format('d/m/Y') ?>'
			, maxDate: '<?php echo $endDate->format('d/m/Y') ?>'
			, onSelect: function(date) {  
				jQuery(".ui-datepicker a").removeAttr("href"); 
				checkDate<?php echo $checkinId; ?>(jQuery, jQuery(this), date); 
				if(productAvailabilityType ==2 || productAvailabilityType==3){
					calculateQuote();
				}else{
					dateCalculatorChanged();
					printChangedDateBooking(date, jQuery(this)); 
				}
			}
			, showOn: 'button'
			, beforeShowDay: function (date) {return closedBooking(date, 1, daysToEnable); }
			, beforeShow: function(dateText, inst) {
				$('#ui-datepicker-div').addClass('notranslate');  
				jQuery(this).attr("readonly", true); insertCheckinTitleBooking(); 
				}
			, onChangeMonthYear: function(dateText, inst) { insertCheckinTitleBooking(); }
          , buttonText: <?php echo $checkintext; ?>,
		})};
		calculator_checkin();
		calculator_checkout = function() { $("#<?php echo $checkoutId; ?>").datepicker({
			numberOfMonths: parseInt("<?php echo COM_BOOKINGFORCONNECTOR_MONTHINCALENDAR;?>")
			,defaultDate: "+2d"
			,dateFormat: "dd/mm/yy"
			, minDate: '<?php echo $startDate2->format('d/m/Y') ?>'
			, maxDate: '<?php echo $endDate->format('d/m/Y') ?>'
			, onSelect: function(date) {  $(".ui-datepicker a").removeAttr("href"); dateCalculatorChanged();}
			, showOn: 'button'
			, beforeShowDay: function (date) {return closedBooking(date, 0, checkOutDaysToEnable); }
			, beforeShow: function(dateText, inst) {$('#ui-datepicker-div').addClass('notranslate');  jQuery(this).attr("readonly", true); insertCheckoutTitleBooking(); }
			, onChangeMonthYear: function(dateText, inst) { insertCheckoutTitleBooking(); }
          , buttonText: <?php echo $checkouttext; ?>,
		})};
		
		calculator_checkout();
		
		//fix Google Translator and datepicker
		$('.ui-datepicker').addClass('notranslate');

		$(".com_bookingforconnector_resource-calculatorForm-childrenages").hide();
		$(".com_bookingforconnector_resource-calculatorForm-childrenages select").hide();
		checkChildren(<?php echo $nch ?>,<?php echo $showChildrenagesmsg ?>);
		$(".com_bookingforconnector_resource-calculatorForm-children select#childrencalculator").change(function() {
			checkChildren($(this).val(),0);
		});

//		$(".com_bookingforconnector-item-secondary-more").click(function() {
////			changeVariation($, $(currButton).attr("data-variation"), $(currButton));
//			changeVariation($, $(this).attr("data-variation"), $(this));
//			return false;
//		});
		
		

		jQuery(".variationlabel").on("click", function(){
			var show = function(resourceId, text){
//						console.log("->" + resourceId);
//						console.log("->" + text);
						jQuery("#divoffers"+resourceId).empty();
						//jQuery("#divoffers"+resourceId).removeClass("com_bookingforconnector_loading");
						jQuery("#divoffers"+resourceId).html(text);
			};
			var discountIds = jQuery(this).attr('rel'); 
			var resourceId = jQuery(this).attr('rel1'); 
//console.log(resourceId);
	//		jQuery("#divoffers"+resourceId).slideToggle( "slow" );
			if(jQuery("#troffers"+resourceId).is(":visible")){
				jQuery("#troffers"+resourceId).slideUp("slow");
				jQuery(this).find("i").toggleClass("fa-angle-up fa-angle-down");
			  } else {
				jQuery("#troffers"+resourceId).slideDown("slow");
				jQuery(this).find("i").toggleClass("fa-angle-up fa-angle-down");
			  }

			//if (jQuery.inArray(discountIds,offersLoaded)===-1)
			if(!offersLoaded.hasOwnProperty(discountIds))
			{
				getDiscountsAjaxInformations(discountIds,resourceId,show);
				//offersLoaded.push(discountIds);
			}else{
				show(resourceId,offersLoaded[discountIds]);
				//jQuery("#divoffers"+resourceId).html(offersLoaded[discountIds]);
			}
		});
	});



function getDiscountsAjaxInformations(discountIds,obj, fn){
	if (cultureCode.length>1)
	{
		cultureCode = cultureCode.substring(0, 2).toLowerCase();
	}
	if (defaultcultureCode.length>1)
	{
		defaultcultureCode = defaultcultureCode.substring(0, 2).toLowerCase();
	}

	var query = "discountId=" + discountIds + "&language=<?php echo $language ?>&task=getDiscountDetails";
//	jQuery.getJSON(urlCheck + "?" + query, function(data) {
	jQuery.post(urlCheck, query, function(data) {
			$html = '';
			jQuery.each(data || [], function(key, val) {
				var name = val.Name;
				var descr = val.Description;
				name = bookingfor.nl2br(jQuery("<p>" + name + "</p>").text());
				$html += '<p class="title">' + name + '</p>';
				descr = bookingfor.nl2br(jQuery("<p>" + descr + "</p>").text());
				$html += '<p class="description ">' + descr + '</p>';
			});
			offersLoaded[discountIds] = $html;
			fn(obj,$html);
			//jQuery(obj).html($html);
	},'json');

}

	function closedBooking(date, offset, enableDays) {
	  var checkindate = jQuery('#<?php echo $checkinId; ?>').val();
	  var checkoutdate = jQuery('#<?php echo $checkoutId; ?>').val();
	  var strdate = ("0" + date.getDate()).slice(-2) + "/" + ("0" + (date.getMonth()+1)).slice(-2) + "/" + date.getFullYear();
	  
	  var d1 = checkindate.split("/");
	  var d2 = checkoutdate.split("/");
	  var c = strdate.split("/");

	  var from = new Date(d1[2], d1[1]-1, d1[0]);
	  var to   = new Date(d2[2], d2[1]-1, d2[0]);
	  var check = new Date(c[2], c[1]-1, c[0]);
	if(productAvailabilityType ==2 || productAvailabilityType ==3){
		to = from;
	}

	  var dayenabled = false;
		var month = date.getMonth() + 1;
		var day = date.getDate();
		var year = date.getFullYear();
		var copyarray = jQuery.extend(true, [], enableDays);
		for (var i = 0; i < offset; i++)
			copyarray.pop();
		var datereformat = year + '' + bookingfor.pad(month,2) + '' + bookingfor.pad(day,2);
		if (jQuery.inArray(Number(datereformat), copyarray) != -1) {
			dayenabled = true;
			//return [true, 'greenDay'];
		}
	//	return [false, 'redDay'];


	  arr = [dayenabled, ''];  
	  if(check.getTime() == from.getTime()) {
	//  	console.log(from);
	//  console.log(to);
	//  console.log(check);
		arr = [dayenabled, 'date-start-selected', 'date-selected'];
	  }
	  if(check.getTime() == to.getTime()) {
	//  	console.log(from);
	//  console.log(to);
	//  console.log(check);
		arr = [dayenabled, 'date-end-selected', 'date-selected'];  
	  }
	  if(check > from && check < to) {
		arr = [dayenabled, 'date-selected', 'date-selected'];
	  }
	  return arr;
	}

	function checkChildren(nch,showMsg) {
		jQuery(".com_bookingforconnector_resource-calculatorForm-childrenages").hide();
		jQuery(".com_bookingforconnector_resource-calculatorForm-childrenages select").hide();
		if (nch > 0) {
			jQuery(".com_bookingforconnector_resource-calculatorForm-childrenages select").each(function(i) {
				if (i < nch) {
					var id=jQuery(this).attr('id');
					jQuery(this).css('display', 'inline-block');
//					jQuery(this).show();
//					console.log(id);
//				   jQuery('#calculatorForm #s2id_'+id).show();
				}
			});
			jQuery(".com_bookingforconnector_resource-calculatorForm-childrenages").show();
			if(showMsg===1) { 
				showpopovercalculator();
			}
		}
	}

	function changeBooking($) {
		$(".com_bookingforconnector_resource-calculatorTable").removeClass("com_bookingforconnector_resource-calculatorTable-viewsummary");
		$(".com_bookingforconnector_resource-calculatorTable").removeClass("com_bookingforconnector_resource-calculatorTable-viewextras");
		$(".com_bookingforconnector_resource-calculator-requestForm").hide();
		$("#calculatorForm").show();
		$('input[name="variationPlanId"]','#calculatorForm').val('');
		$('input[name="state"]','#calculatorForm').val('');
		$('#calculatorForm').ajaxSubmit(getAjaxOptions());
	}

	function showBooking($) {
		$(".com_bookingforconnector_resource-calculatorTable").addClass("com_bookingforconnector_resource-calculatorTable-viewsummary");
		$(".com_bookingforconnector_resource-calculatorTable").removeClass("com_bookingforconnector_resource-calculatorTable-viewextras");
		$(".com_bookingforconnector_resource-calculator-requestForm").show();
	}

	function showOptionalPackages($) {
		$(".com_bookingforconnector_resource-calculatorTable").addClass("com_bookingforconnector_resource-calculatorTable-viewsummary");
		$(".com_bookingforconnector_resource-calculatorTable").addClass("com_bookingforconnector_resource-calculatorTable-viewextras");
		$('input[name="state"]','#calculatorForm').val('optionalPackages');
	}
	
	function changePriceType($, priceType) {
//		debugger;
		var ratePlanId = parseInt($(priceType).val());
		var resourceId = $(priceType).attr("data-resource");
//		$('input[name="pricetype"]','#calculatorForm').val(ratePlanId);
		

		if($("#divoffers" + resourceId).is(":visible")){
			$("#divoffers" + resourceId).slideUp("slow");
			$($(".variationlabel[rel1='" + resourceId + "']")).find("i").toggleClass("fa-angle-up fa-angle-down");
		  }
		  
		if(!$.grep(allStays, function(rs) {
			return (rs.ResourceId == parseInt(resourceId) && rs.RateplanId == parseInt(ratePlanId));
		}).length) {
			return false;
		}

        var calc = $.grep(allStays, function (rs) {
            return (rs.ResourceId == parseInt(resourceId) && rs.RateplanId == parseInt(ratePlanId));
        });

		if(calc.length) {
			currStay = calc[0];

			currStay.SimpleDiscountIds = "";
			$.each(JSON.parse(currStay.RatePlan.AllVariationsString), function (index,obj) {
				currStay.SimpleDiscountIds += obj.VariationPlanId + ",";
			});
			if(currStay.SimpleDiscountIds.length > 0)
				currStay.SimpleDiscountIds = currStay.SimpleDiscountIds.substr(0,currStay.SimpleDiscountIds.length - 1);

			var container = $($(priceType).closest("table"));
            $("#data-id-" + resourceId).find(".com_bookingforconnector_merchantdetails-resource-stay-discount").html(bookingfor.number_format(currStay.TotalPrice, 2, '.', ''));
            $("#data-id-" + resourceId).find(".com_bookingforconnector_merchantdetails-resource-stay-discount").attr("data-value",currStay.TotalPrice);
            $("#data-id-" + resourceId).find(".com_bookingforconnector_merchantdetails-resourcelist-stay-total").html(bookingfor.number_format(currStay.Price, 2, '.', ''));
            $("#data-id-" + resourceId).find(".com_bookingforconnector_merchantdetails-resourcelist-stay-total").attr("data-value",currStay.Price);

            $("#data-id-" + resourceId).find(".variationlabel").show();
            $("#data-id-" + resourceId).find(".variationlabel").attr("rel", currStay.SimpleDiscountIds);
            if (currStay.Price >= currStay.TotalPrice) {
                $("#data-id-" + resourceId).find(".com_bookingforconnector_merchantdetails-resourcelist-stay-total").removeClass("red-color");
                $("#data-id-" + resourceId).find(".variationlabel").hide();
            } else {
                $("#data-id-" + resourceId).find(".com_bookingforconnector_merchantdetails-resourcelist-stay-total").addClass("red-color");
                $("#data-id-" + resourceId).find(".variationlabel_percent").html(currStay.VariationPercent);
            }
//			container.find("tr[data-id='" + resourceId + "'] .com_bookingforconnector_merchantdetails-resource-stay-discount").html("&euro; " + bookingfor.number_format(currStay.TotalPrice, 2, '.', ''));
//			container.find("tr[data-id='" + resourceId + "'] .com_bookingforconnector_merchantdetails-resourcelist-stay-total").html("&euro; " + bookingfor.number_format(currStay.DiscountedPrice, 2, '.', '') );
//			container.find("tr[data-id='" + resourceId + "'] .com_bookingforconnector-item-secondary-more").attr("data-rateplan", val);
//			container.find("tr[data-id='" + resourceId + "'] .com_bookingforconnector-item-secondary-more").attr("data-bookingtype", currStay.BookingType);
//			container.find("tr[data-id='" + resourceId + "'] .originalquote").show();
//			container.find("tr[data-id='" + resourceId + "'] .variationlabel").show();
//			container.find("tr[data-id='" + resourceId + "'] .variationlabel").attr("rel", currStay.SimpleDiscountIds);
//			if(currStay.DiscountedPrice >= currStay.TotalPrice) {
//				container.find("tr[data-id='" + resourceId + "'] .com_bookingforconnector_resourcelist_strikethrough").addClass("notvars");
//				container.find("tr[data-id='" + resourceId + "'] .originalquote").hide();
//				container.find("tr[data-id='" + resourceId + "'] .variationlabel").hide();
//			} else {
//				container.find("tr[data-id='" + resourceId + "'] .com_bookingforconnector_resourcelist_strikethrough").removeClass("notvars");
//				container.find("tr[data-id='" + resourceId + "'] .variationlabel_percent").html(currStay.VariationPercent);
//			}

			UpdateQuote($(priceType).parent().parent().next().next().find('select')[0]);
//			var container = jQuery(".totalextrasstay" + resourceId);
//				container.find(".com_bookingforconnector_merchantdetails-resource-stay-discount").html("&euro; " +  bookingfor.number_format(currStay.TotalPrice, 2, '.', '') );
//				container.find(".com_bookingforconnector_merchantdetails-resourcelist-stay-total").html("&euro; " + bookingfor.number_format(currStay.DiscountedPrice, 2, '.', ''));
//				container.find(".com_bookingforconnector-item-secondary-more").attr("data-rateplan", val);
//				container.find(".com_bookingforconnector-item-secondary-more").attr("data-bookingtype", currStay.BookingType);
//				container.find(".originalquote").show();
//				container.find(".variationlabel").show();
//				container.find(".variationlabel").attr("rel", currStay.SimpleDiscountIds);
//				if(currStay.DiscountedPrice >= currStay.TotalPrice) {
//					container.find(".com_bookingforconnector_resourcelist_strikethrough").addClass("notvars");
//					container.find(".originalquote").hide();
//					container.find(".variationlabel").hide();
//				} else {
//					container.find(".com_bookingforconnector_resourcelist_strikethrough").removeClass("notvars");
//					container.find(".variationlabel_percent").html(currStay.VariationPercent);
//				}

		}
		//$('input[name="pricetype"]','#calculatorForm').val(val);
		//$('#calculatorForm').ajaxSubmit(getAjaxOptions());
		//$('#calculatorForm').submit();
		<?php if(COM_BOOKINGFORCONNECTOR_GAENABLED == 1 && !empty(COM_BOOKINGFORCONNECTOR_GAACCOUNT) && COM_BOOKINGFORCONNECTOR_EECENABLED == 1 ): ?>
		var currentItm = {
			"id": resourceId + " - Resource",
			"name": jQuery(priceType).attr("data-resourcename"),
			"category": jQuery.grep(allStays, function(rs) {
				return rs.ResourceId == parseInt(resourceId);
			})[0].MrcCategoryName,
			"brand": jQuery(priceType).attr("data-brand"),
			"variant": jQuery(jQuery(priceType).find("option:selected")).attr("data-refid").toUpperCase(),
		};
		callAnalyticsEEc("addProduct", [currentItm], "changeRate", jQuery(priceType).attr("data-list"), jQuery(jQuery(priceType).find("option:selected")).attr("data-refid").toUpperCase(), "Resources");
		<?php endif; ?>
	}
	
</script>
<script type="text/javascript">
<!--
function getDisplayDate(date) {
	return date == null ? "" : bookingfor.pad(date.getDate(),2) + '/' + bookingfor.pad((date.getMonth() + 1),2) + '/' + date.getFullYear();
}

function enableSpecificDates(date, offset, enableDays) {
	var month = date.getMonth() + 1;
	var day = date.getDate();
	var year = date.getFullYear();
	var copyarray = jQuery.extend(true, [], enableDays);
	for (var i = 0; i < offset; i++)
		copyarray.pop();
	var datereformat = year + '' + bookingfor.pad(month,2) + '' + bookingfor.pad(day,2);
	if (jQuery.inArray(Number(datereformat), copyarray) != -1) {
		return [true, 'greenDay'];
	}
	return [false, 'redDay'];
}

function onEnsureCheckOutDaysToEnableSuccess() {
	if (!checkOutDaysToEnable || checkOutDaysToEnable.length == 0) {
		jQuery("#calcheckout").unblock();
		return;
	}
	if (checkOutDaysToEnable[0] == 0)
	{
		jQuery("#calcheckout").unblock();
		return;
	}
//alert(checkOutDaysToEnable[0]);
//	var date = jQuery.datepicker.parseDate('yyyymmdd', checkOutDaysToEnable[0]);
	var strDate = '' + checkOutDaysToEnable[0]
	var date = new Date(strDate.substr(0,4),strDate.substr(4,2)-1,strDate.substr(6,2));

	jQuery('#<?php echo $checkoutId?>').datepicker("option", "minDate", date);
	var datetocheck = jQuery('#<?php echo $checkoutId?>').datepicker("getDate");
	//checkout.datepicker("option", "minDate", date);
	//var datetocheck = checkout.datepicker("getDate");
	if (!enableSpecificDates(datetocheck, 0, checkOutDaysToEnable)[0]) {
		jQuery("#<?php echo $checkoutId?>").val(getDisplayDate(date));
//		printChangedDateBooking(date, jQuery("#<?php echo $checkoutId?>"))
	}
//		printChangedDateBooking(date, jQuery("#<?php echo $checkoutId?>"))
	printChangedDateBooking(date, jQuery("#<?php echo $checkoutId?>"))
	jQuery("#calcheckout").unblock();

//	if (raiseUpdate) {
//		btnClick().click();
//	}
}


function checkDate<?php echo $checkinId?>($, obj, selectedDate) {
	instance = obj.data("datepicker");
	date = $.datepicker.parseDate(
			instance.settings.dateFormat ||
			$.datepicker._defaults.dateFormat,
			selectedDate, instance.settings);
	var d = new Date(date);
	d.setDate(d.getDate() + 1);

	var offsetDate = new Date(date.getFullYear(), date.getMonth(), date.getDate());

	switch (productAvailabilityType) {
		case "0":
			if ($("#<?php echo $checkoutId?>").datepicker("getDate") < date) {
				$("#<?php echo $checkoutId?>").datepicker("option", "minDate", offsetDate);
				$("#<?php echo $checkoutId?>").datepicker("setDate", Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
			}
		case "1":
			if ($("#<?php echo $checkoutId?>").datepicker("getDate") <= date) {
				offsetDate.setDate(offsetDate.getDate() + 1);
				$("#<?php echo $checkoutId?>").datepicker("option", "minDate", offsetDate);
				$("#<?php echo $checkoutId?>").datepicker("setDate", Date.UTC(offsetDate.getFullYear(), offsetDate.getMonth(), offsetDate.getDate()));

			}
			break;
		case "3":
			offsetDate.setDate(offsetDate.getDate() + 1);
			$("#<?php echo $checkoutId?>").datepicker("option", "minDate", offsetDate);
			$("#<?php echo $checkoutId?>").datepicker("option", "maxDate", offsetDate);
			$("#<?php echo $checkoutId?>").datepicker("setDate", Date.UTC(offsetDate.getFullYear(), offsetDate.getMonth(), offsetDate.getDate()));
			//printChangedDate();
			break;
	}

}

function quoteCalculatorServiceChanged(el){
	var selectableprice = jQuery(el).val();
	currSelPrice = selectableprice.split(":");
	var ddlid = jQuery(el).attr('id').split("-");
	var resId = jQuery(el).attr('id').split("-").pop();
	var currProdRelatedId = jQuery(el).attr("data-resid");


	var currMaxAvailability = servicesAvailability[currProdRelatedId];
	jQuery(".dllextraselected_"+currProdRelatedId).each(function(){
		var currselectableprice = jQuery(this).val().split(":");
		currMaxAvailability -= parseInt(currselectableprice[1]);
	});

	//rebuild ddl
	jQuery(".dllextraselected_"+currProdRelatedId).not(this).each(function(){
		var currMaxValue = jQuery(this).children("option:last").val().split(":");
		var currValue = jQuery(this).val().split(":");

		jQuery(this).children("option").prop('disabled',false);
		var maxValue = parseInt(currValue[1])+currMaxAvailability;

		if(parseInt(currMaxValue[1])>maxValue){
			var prodRelatedId = jQuery(this).attr("data-resid");
			var selIndx  = jQuery(this).children("option").index( jQuery(this).children("option[value='"+ prodRelatedId +":" +maxValue +"']"));
			if(selIndx>-1){
				jQuery(this).children("option:gt("+selIndx+")").prop('disabled',true);

			}
		}
	});

	if(currSelPrice[1]!="0"){
		jQuery('.name-selectableprice-'+ddlid[1]+'-'+currSelPrice[0] + '-' + resId).addClass("com_bookingforconnector_resourcelist-resourcename-blue");
		jQuery('.name-selectableprice-'+ddlid[1]+'-'+currSelPrice[0] + '-' + resId).removeClass("com_bookingforconnector_resourcelist-resourcename-grey");
		jQuery('.name-selectableprice-'+ddlid[1]+'-'+currSelPrice[0] + '-' + resId).show();
	}else{
		jQuery('.name-selectableprice-'+ddlid[1]+'-'+currSelPrice[0] + '-' + resId).removeClass("com_bookingforconnector_resourcelist-resourcename-blue");
		jQuery('.name-selectableprice-'+ddlid[1]+'-'+currSelPrice[0] + '-' + resId).addClass("com_bookingforconnector_resourcelist-resourcename-grey");
	}
	jQuery('.totalextrasselect-'+ ddlid[1]+'-'+currSelPrice[0]+'-'+resId).block({message: ''});
	getcompleterateplansstaybyrateplanid(resId,currSelPrice[0],selectableprice,ddlid[1]);
//	if(qt>0){
//	}
//	console.log("Recalc");
}

function quoteCalculatorChanged(callback) {
	jQuery('#resourceQuote').hide();
	jQuery('#resourceSummary').hide();
	jQuery('#errorbooking').hide();
	jQuery('input[name="refreshcalc"]').val("1");
	if (countMinAdults()>0)
	{
		jQuery('#calculateButton').removeClass("not-active");
		jQuery('.tablesearch').hide();
	}else{
		jQuery('#calculateButton').addClass("not-active");
	}
//	jQuery('#calculateButton').show();
	/*calculateQuote();*/
}
function dateCalculatorChanged(callback) {
	getAjaxDate(callback)
	jQuery('#resourceQuote').hide();
	jQuery('#resourceSummary').hide();
	jQuery('input[name="refreshcalc"]').val("1");
	if (countMinAdults()>0)
	{
		jQuery('#calculateButton').removeClass("not-active");
		jQuery('.tablesearch').hide();
	}else{
		jQuery('#calculateButton').addClass("not-active");
	}
//	jQuery('#calculateButton').show();
	/*calculateQuote();*/
}

function countMinAdults(){
	var minAdults = 0;
	var numAdults = new Number(jQuery('#adultscalculator').val());
	var numSeniores = new Number(jQuery('#seniorescalculator').val());
	minAdults = numAdults + numSeniores;
	return minAdults;
}


function calculateQuote() {
//		jQuery('#calculatorForm').attr("action","<?php echo $formRoute?>?format=calc&tmpl=component")
//		jQuery('#calculatorForm').submit();
	jQuery('#showmsgchildagecalculator').val(0);
	jQuery(".com_bookingforconnector_resource-calculatorForm-childrenages select:visible option:selected").each(function(i) {
		if(jQuery(this).text()==""){
			jQuery('#showmsgchildagecalculator').val(1);
			return;
		}
	});

	jQuery('input[name="state"]','#calculatorForm').val('');
	jQuery('input[name="extras[]"]','#calculatorForm').val('');

	jQuery('#calculatorForm').ajaxSubmit(getAjaxOptions());
}

function showpopovercalculator() {
		jQuery('#bfi_lblchildrenagescalculator').popover({
			content : jQuery("#bfi_childrenagesmsgcalculator").html(),
			container: "body",
			placement:"bottom",
			html :"true"
		});
		jQuery('#bfi_lblchildrenagescalculator').popover("show");
}
jQuery(window).resize(function(){
	jQuery('#bfi_lblchildrenagescalculator').popover("hide");
});


function getAjaxDate(callback) {
	// prepare Options Object 
	var fromDate = jQuery('#<?php echo $checkinId?>').datepicker('getDate');
	var month = fromDate.getMonth() + 1;
	var day = fromDate.getDate();
	var year = fromDate.getFullYear();
	var datereformat = year + '' + bookingfor.pad(month,2) + '' + bookingfor.pad(day,2);	
	jQuery('#calcheckout').block({message: ''});
	var options = { 
	    url:        '<?php echo Juri::root()?>index.php?option=com_bookingforconnector&task=listDate&resourceId=' + unitId + '&checkin=' + datereformat, 
	    dataType: 'text',
		success: function(data) { 
            checkOutDaysToEnable = data.split(',');
			for(var i=0; i<checkOutDaysToEnable.length; i++) { checkOutDaysToEnable[i] = +checkOutDaysToEnable[i]; } 
			onEnsureCheckOutDaysToEnableSuccess();
			if (callback) {
				callback;
			}
	    } 
	}; 
	jQuery.ajax(options);

	//return options;
	//form.ajaxForm(options);
}


function getAjaxOptions(callback) {
	// prepare Options Object 
	var options = { 
	    target:     '#booknow',
	    replaceTarget: true, 
	    url:        '<?php echo $formRoute?>' + '?format=calc&tmpl=component', 
	    beforeSend: function() {
	    	jQuery('#booknow').block({
					message:"",
					overlayCSS: {backgroundColor: '#ffffff', opacity: 0.7}  
				}				
				);
		},
	    success: function() { 
			jQuery('#booknow').unblock();
			calculator_checkin();
			calculator_checkout();
			if (callback) {
				callback;
			}
	    } 
	}; 
	return options;
	//form.ajaxForm(options);
}

    var totalOrderPriceLoaded = false;
    var totalOrderPrice = 0;
    var totalOrderPriceWhitOutDiscount = 0;


    function getcompleterateplansstaybyrateplanid(resId,priceId,selectableprice,rowId) {
        //console.log("calcolo prezzo per id: " + priceId);
		
//		debugger;
        
		el = jQuery('.totalextrasselect-'+rowId+'-' + priceId + '-' + resId);
        currTable = el.closest("table");
        jQuery(currTable).block({
            message: '<i class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></i><span class="sr-only">Loading...</span>',
            css: {border: '2px solid #1D668B', padding: '20px', backgroundColor: '#fff', '-webkit-border-radius': '10px', '-moz-border-radius': '10px', color: '#1D668B', width: '80%'},
            overlayCSS: {backgroundColor: '#1D668B', opacity: .7}
        });

        var extrasselect = [];
        jQuery(".extrasselect-" + rowId + '-' + resId).each( function( index, element ){
            var currSelPriceValue = jQuery( this ).val()
            currSelPrice = currSelPriceValue.split(":");
            if(currSelPrice[1]!="0"){
                extrasselect.push(currSelPriceValue);
            }
        });

        obj = jQuery("tr[id^=data-id-"+resId+"]")
        var ddlroom = jQuery(obj).find(".ddlrooms");
        var currentSlot = jQuery(obj).find('.selectpickerTimeSlotRange');
        var currentSlotId = currentSlot.val();
        if(currentSlotId){currentSlotId = parseInt(currentSlotId); }
        var accomodation = {
            ResourceId: resId,
            RatePlanId: jQuery(obj).find("select.pricetypes").val(),
            AvailabilityType:ddlroom.attr("data-availabilityType"),
            TimeMinStart:0,
            TimeMinEnd:0,
            FromDate:"",
            ExtraServices: extrasselect
        };
        if(currentSlotId > 0){
            accomodation.TimeSlotId =currentSlotId;
            accomodation.TimeSlotStart = currentSlot.find(":selected").data("timeslotstart");
            accomodation.TimeSlotEnd = currentSlot.find(":selected").data("timeslotend");
        }

        if(ddlroom.attr("data-availabilityType")==2){
            var currentTimeStart = jQuery(".selectpickerTimePeriodStart").find("option:selected");
            var currentTimeEnd= jQuery(".selectpickerTimePeriodEnd").find("option:selected");

            if(currentTimeStart){
                var currFromDate =jQuery("#ChkAvailibilityFromDateTimePeriod_"+resId);
//				var mcurrFromDate = moment(currFromDate.val(),"DD/MM/YYYY").format("YYYYMMDD", { forceLength: true, trim: false });
				var mcurrFromDate = bookingfor.convertDateToInt(jQuery(currFromDate).datepicker( "getDate" ));
                
                accomodation.TimeMinStart = currentTimeStart.attr("data-TimeMinStart");
                accomodation.TimeMinEnd = currentTimeStart.attr("data-TimeMinEnd");
                accomodation.FromDate=currFromDate.val();
                
				var tmpDate = new Date();
				tmpDate.setHours(0,0,0,0);
				var newValStart = bookingfor.dateAdd(tmpDate,"minute",Number(currentTimeStart.attr("data-TimeMinStart")));
				var newValEnd = bookingfor.dateAdd(tmpDate,"minute",Number(currentTimeEnd.attr("data-TimeMinEnd")));
				var diffMs = (newValEnd - newValStart);
				var duration =  Math.floor((diffMs/1000)/60);
				accomodation.CheckInTime = mcurrFromDate+bookingfor.pad(currentTimeStart.attr("data-TimeMinStart"),6);
                accomodation.TimeDuration = duration;



//				var newValStart = moment(pad(currentTimeStart.attr("data-TimeMinStart"),6), "HHmmss");
//              var newValEnd = moment(pad(currentTimeEnd.attr("data-TimeMinEnd"),6), "HHmmss");
//              var duration = moment.duration(newValEnd.diff(newValStart));
//				accomodation.CheckInTime = mcurrFromDate+pad(currentTimeStart.attr("data-TimeMinStart"),6);
//                accomodation.TimeDuration = duration.asMinutes();

            }
        }
        var searchModel = jQuery('#calculatorForm').serializeObject();
		var dataarray = jQuery('#calculatorForm').serializeArray();
		dataarray.push({name: 'resourceId', value: resId});
		dataarray.push({name: 'id', value: resId});
		dataarray.push({name: 'pricetype', value:  accomodation.RatePlanId});
		dataarray.push({name: 'rateplanid', value: accomodation.RatePlanId});
		dataarray.push({name: 'timeMinStart', value: accomodation.TimeMinStart});
		dataarray.push({name: 'timeMinEnd', value: accomodation.TimeMinEnd});
		dataarray.push({name: 'selectableprices', value: accomodation.ExtraServices.join("|")});
		dataarray.push({name: 'productAvailabilityType', value: accomodation.AvailabilityType});
		dataarray.push({name: 'searchModel', value: searchModel});

        var jqxhr = jQuery.ajax({
            url: bookingfor.updateQueryStringParameter(urlCheck,"task","getCompleteRateplansStay"),
            type: "POST",
            dataType: "json",
			data : dataarray
//            data: {
//                id: resId,
//                rateplanid: accomodation.RatePlanId,
//                timeMinStart: accomodation.TimeMinStart,
//                timeMinEnd: accomodation.TimeMinEnd,
//                selectableprices: accomodation.ExtraServices.join("|"),
//                productAvailabilityType : accomodation.AvailabilityType,
//                searchModel: searchModel
//            }
        });

        jqxhr.done(function(result, textStatus, jqXHR)
        {
            if (result) {
                if(result.length > 0)
                {
//                    debugger;
                    var currResult = jQuery.grep(result, function (rs) {
                        return (rs.RatePlanId == parseInt(accomodation.RatePlanId));
                    });

                    currStay = currResult[0].SuggestedStay;
                    var CalculatedPrices = JSON.parse(currResult[0].CalculatedPricesString);
//                    console.log(CalculatedPrices)
                    var showPrice = false;
                    var currentDivPrice = el.find(".totalextrasselectdiv");
                    currentDivPrice.hide();


                    var container = currTable.find(".totalextrasstay").first();

                    var containerTotalQuote = jQuery('#booknow').find('.totalQuote:visible:first');
                    var containerTD = containerTotalQuote.closest("td");
                    var containerTotalQuoteDiscount = containerTD.find('.totalQuoteDiscount').first();
                    var containerTotalRooms = containerTD.find('.lblLodging span').first();
                    var totalRooms = parseInt(containerTotalRooms.html());
                    var currTotal = 0
                    var currTotalNotDiscouted = 0
                    if(!totalOrderPriceLoaded){
                        totalOrderPrice = parseFloat(containerTotalQuote.html());
//                        if(containerTotalQuoteDiscount.length){
                            totalOrderPriceWhitOutDiscount = parseFloat(containerTotalQuoteDiscount.html());
//                        }
                        totalOrderPriceLoaded =true;
                    }
                    //var totalPrice = parseFloat(jQuery("#dvTotal-" + resId + " .totalQuote").html().replace("&euro;&nbsp;",""));
                    //var totalDiscount = jQuery("#dvTotal-" + resId + " .totalQuoteDiscount").length > 0 ? parseFloat(jQuery("#dvTotal-" + resId + " .totalQuoteDiscount").html().replace("&euro;&nbsp;","")) : 0;
                    //var totalRooms = parseInt(jQuery("#dvTotal-" + resId + " .lblLodging span").html());



                    CalculatedPrices.forEach(function (cprice) {
                        //if (cprice.PriceId == priceId) {
                        if (cprice.RelatedProductId == priceId) {

//                            console.log("Visualizzo prezzo id: " + priceId);

                            showPrice = true;
                            cprice.TotalPrice = cprice.TotalAmount;
                            cprice.DiscountedPrice = cprice.TotalDiscounted;
                            var simpleDiscountIds = [];
                            cprice.Variations.forEach(function (variation) {
                                simpleDiscountIds.push(variation.VariationPlanId);
                            });
                            cprice.SimpleDiscountIds = simpleDiscountIds.join(",");

                            el.find(".com_bookingforconnector_merchantdetails-resource-stay-discount").html(bookingfor.number_format(cprice.TotalPrice, 2, '.', '') );
                            el.find(".com_bookingforconnector_merchantdetails-resourcelist-stay-total").html(bookingfor.number_format(cprice.DiscountedPrice, 2, '.', '') );
                            el.find(".variationlabel").show();
                            el.find(".variationlabel").attr("rel", cprice.SimpleDiscountIds);

                            if(cprice.DiscountedPrice == cprice.TotalPrice) {
                                el.find(".com_bookingforconnector_merchantdetails-resourcelist-stay-total").removeClass("red-color");
                                el.find(".com_bookingforconnector_merchantdetails-resource-stay-discount").hide();
                                el.find(".variationlabel").hide();
                            } else {
                                el.find(".com_bookingforconnector_merchantdetails-resourcelist-stay-total").addClass("red-color");
                                el.find(".variationlabel_percent").html(cprice.VariationPercent);
                            }
                        }
                    });

                    if(showPrice){
                        currentDivPrice.show();
                    }

                    var totalServices = 0;
                    jQuery("[class^='totalextrasselect-']").each( function( index, element ){
                        if(!jQuery(this).find(".com_bookingforconnector_merchantdetails-resourcelist-stay-total").is(':hidden'))
                        {
                            totalServices++;
                            currTotal += ( parseFloat( jQuery(this).find(".com_bookingforconnector_merchantdetails-resourcelist-stay-total").html()));
                            currTotalNotDiscouted += ( parseFloat( jQuery(this).find(".com_bookingforconnector_merchantdetails-resource-stay-discount").html()));
                        }
//                        if(!jQuery(this).find(".com_bookingforconnector_merchantdetails-resource-stay-discount").is(':hidden'))
//                        {
//                            currTotalNotDiscouted += ( parseFloat( jQuery(this).find(".com_bookingforconnector_merchantdetails-resource-stay-discount").html()));
//                        }
//                        else
//                            currTotalNotDiscouted += ( parseFloat( jQuery(this).find(".com_bookingforconnector_merchantdetails-resourcelist-stay-total").html()));

                    });

                    if(totalServices == 0){
                        containerTotalRooms.closest(".lblLodging").html("<span>" + totalRooms + "</span>" + " Qt.");
                    }else{
                        containerTotalRooms.closest(".lblLodging").html("<span>" + totalRooms + "</span>" + " Qt." + " + " + totalServices + " Services" );
                    }
                    containerTotalQuote.html( bookingfor.number_format(totalOrderPrice+currTotal, 2, '.', ''));
                    containerTotalQuoteDiscount.html(bookingfor.number_format(totalOrderPriceWhitOutDiscount+currTotalNotDiscouted, 2, '.', ''));
                    containerTotalQuoteDiscount.addClass("hidden");
                    if((totalOrderPriceWhitOutDiscount + currTotalNotDiscouted)> (totalOrderPrice+currTotal)){
                        containerTotalQuoteDiscount.removeClass("hidden");
                    }
                }
            }
            jQuery(el).unblock();

        });


        jqxhr.always(function() {
            jQuery(currTable).unblock();
        });
    }

//-->
</script>
<script type="text/javascript">
	
jQuery(document).ready(function() {
    jQuery("#calculatorForm .checking-container .ui-datepicker-trigger").click(function() {
        jQuery(".ui-datepicker-calendar td").click(function() {
            if (jQuery(this).hasClass('ui-state-disabled') == false) {
                if(jQuery("#calculatorForm .lastdatecheckout button.ui-datepicker-trigger").is(":visible")){
					jQuery("#calculatorForm .lastdate button.ui-datepicker-trigger").trigger("click");
				}
                jQuery("#calculatorForm .ui-datepicker-trigger").each(function() {
                    jQuery(this).addClass("activeclass");
                });
                jQuery("#calculatorForm .checking-container .ui-datepicker-trigger").removeClass("activeclass");
                jQuery(".ui-icon-circle-triangle-w").addClass("fa fa-angle-left").removeClass("ui-icon ui-icon-circle-triangle-w").html("");
                jQuery(".ui-icon-circle-triangle-e").addClass("fa fa-angle-right").removeClass("ui-icon ui-icon-circle-triangle-e").html("");
                jQuery("#ui-datepicker-div").css("top", jQuery(this).position().top + 35 + "px");
            }
        });
    })
    jQuery("#calculatorForm .ui-datepicker-trigger").click(function() {
        jQuery("#ui-datepicker-div").css("top", jQuery(this).position().top + 35 + "px");
        jQuery("#calculatorForm .ui-datepicker-trigger").each(function() {
            jQuery(this).removeClass("activeclass");
        });
        jQuery(this).addClass("activeclass");
        jQuery(".ui-icon-circle-triangle-w").addClass("fa fa-angle-left").removeClass("ui-icon ui-icon-circle-triangle-w").html("");
        jQuery(".ui-icon-circle-triangle-e").addClass("fa fa-angle-right").removeClass("ui-icon ui-icon-circle-triangle-e").html("");

    });
    jQuery("#ui-datepicker-div").click(function() {
        jQuery(".ui-icon-circle-triangle-w").addClass("fa fa-angle-left").removeClass("ui-icon ui-icon-circle-triangle-w").html("");
        jQuery(".ui-icon-circle-triangle-e").addClass("fa fa-angle-right").removeClass("ui-icon ui-icon-circle-triangle-e").html("");
    });
    jQuery("#calculatorForm").hover(function(){
        jQuery(".ui-datepicker-trigger").click(function() {
            jQuery("#ui-datepicker-div").css("top", jQuery(this).position().top + 35 + "px");
            jQuery(".ui-datepicker-trigger").each(function() {
                jQuery(this).removeClass("activeclass");
            });
            jQuery(this).addClass("activeclass");
            jQuery(".ui-icon-circle-triangle-w").addClass("fa fa-angle-left").removeClass("ui-icon ui-icon-circle-triangle-w").html("");
            jQuery(".ui-icon-circle-triangle-e").addClass("fa fa-angle-right").removeClass("ui-icon ui-icon-circle-triangle-e").html("");

        });
    });
});

<?php if(COM_BOOKINGFORCONNECTOR_GAENABLED == 1 && !empty(COM_BOOKINGFORCONNECTOR_GAACCOUNT) && COM_BOOKINGFORCONNECTOR_EECENABLED == 1 ): ?>
jQuery(function($) {
	<?php if(isset($eecmainstay)): ?>
	callAnalyticsEEc("addProduct", [<?php echo json_encode($eecmainstay); ?>], "item");
	<?php endif; ?>
	<?php if(count($eecstays) > 0 && $currentState != 'optionalPackages'): ?>
	callAnalyticsEEc("addImpression", <?php echo json_encode($eecstays); ?>, "list", "Suggested Products");
	<?php endif; ?>
});
<?php endif; ?>
<?php if(isset($criteoConfig) && !empty($criteoConfig) && $criteoConfig->enabled): ?>
window.criteo_q = window.criteo_q || []; 
window.criteo_q.push( 
	{ event: "setAccount", account: <?php echo $criteoConfig->campaignid ?>}, 
	{ event: "setSiteType", type: "d" }, 
	{ event: "viewSearch", checkin_date: "<?php echo $checkin->format('d/m/Y') ?>", checkout_date: "<?php echo $checkout->format('d/m/Y') ?>"},
	{ event: "setEmail", email: "" }, 
	{ event: "viewItem", item: "<?php echo $criteoConfig->merchants[0] ?>" }
);
<?php endif; ?>

</script>
<!-- --------------------------------------------------------------------------------------------------------------------------------------------------------- -->
<?php if($loadScriptTimePeriod || $loadScriptTimeSlot) { ?>
    <script>
        //TimeSlot
        var strAlternativeDateToSearch = "<?php echo $alternativeDateToSearch->format('d/m/Y') ?>";
        var strEndDate = "<?php echo $checkout->format('d/m/Y') ?>";
        var dateToUpdate = <?php echo $checkin->format('Ymd') ?>;

    </script>
<?php } ?>

<?php if($loadScriptTimePeriod) { 
	$listDayTP = array();
	$allTimePeriodResourceId = array_unique($allTimePeriodResourceId);
	foreach ($allTimePeriodResourceId as $resId) { 
		$listDayTP[$resId] = json_decode(BFCHelper::GetCheckInDatesPerTimes($resId,$alternativeDateToSearch));
	}
	?>
    <script>
        var txtSelectADay = "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_SELECTDAY') ?>";
        var daysToEnableTimePeriod = <?php echo json_encode($listDayTP) ?>; 
        var strbuttonTextTimePeriod = "<div class='buttoncalendar timePeriodli'><div class='dateone day'><span><?php echo $checkin->format("d") ?></span></div><div class='dateone daterwo monthyear'><p><?php echo $checkin->format("D") ?><br /><?php echo $checkin->format("M").' '.$checkin->format("Y") ?> </p></div><div class='dateone'><i class='fa fa-calendar'></i></div></div>";
//        var urlGetCompleteRatePlansStay = urlCheck + '?task=getCompleteRateplansStay';
//        var urlGetListCheckInDayPerTimes = urlCheck + '?task=getListCheckInDayPerTimes';
		var urlGetCompleteRatePlansStay = bookingfor.updateQueryStringParameter(urlCheck,"task","getCompleteRateplansStay");
		var urlGetListCheckInDayPerTimes = bookingfor.updateQueryStringParameter(urlCheck,"task","getListCheckInDayPerTimes");

        var carttypeCorrector = <?php echo ($cartType == 0?1:0) ?>;
		jQuery(document).ready(function() {
			initDatepickerTimePeriod();
            jQuery(".ChkAvailibilityFromDateTimePeriod:not(.extraprice)").each(function(){
                updateTimePeriodRange(<?php echo $checkin->format('Ymd') ?>, jQuery(this).attr("data-id"), jQuery(this));
            });
        });
    </script>
<?php } ?>

<?php if($loadScriptTimeSlot) { 
	$listDayTS = array();
	$allTimeSlotResourceId = array_unique($allTimeSlotResourceId);
	foreach ($allTimeSlotResourceId as $resId) { 
		$listDayTS[$resId] = json_decode(BFCHelper::GetCheckInDatesTimeSlot($resId,$alternativeDateToSearch));
	}

	?>
    <script>
        //TimeSlot
        var strbuttonTextTimeSlot = "<div class='buttoncalendar timeSlotli'><div class='dateone day'><span><?php echo $checkin->format("d") ?></span></div><div class='dateone daterwo monthyear'><p><?php echo $checkin->format("D") ?><br /><?php echo $checkin->format("M").' '.$checkin->format("Y") ?> </p></div><div class='dateone'><i class='fa fa-calendar'></i></div></div>";
        var daysToEnableTimeSlot = <?php echo json_encode($listDayTS) ?>;
        var currTimeSlotDisp = {};
		jQuery(document).ready(function () {
			initDatepickerTimeSlot();
			updateTimeSlotRange(dateToUpdate);
		});
    </script>

<?php } ?>


<script type="text/javascript">
<!--
    <?php 

	$CartMultimerchantEnabled = BFCHelper::getCartMultimerchantEnabled(); 
	?>
	var CartMultimerchantEnabled = <?php echo $CartMultimerchantEnabled  ? "true" : "false" ?>;
	    function BookNow() {
//        debugger;

		var Order = { Resources: [], SearchModel: {}, TotalAmount: 0, TotalDiscountedAmount: 0 };
        var FirstResourceId = 0;

        jQuery("tr[id^=data-id-]").each(function(index,obj) {
            if (jQuery(obj).attr('IsSelected') == 'true')
            {
                //var resId = $(obj).find(".pricetypes").attr("data-resource");
                var ddlroom = jQuery(obj).find(".ddlrooms");
                var resId = jQuery(ddlroom).attr('id').split('-').pop();

                if(ddlroom.val() > 0)
                {
                    for (var i = 1; i <= ddlroom.val(); i++) {

                        var currentSlot = jQuery(obj).find('.selectpickerTimeSlotRange');
                        var currentSlotId = currentSlot.val();
                        if(currentSlotId){currentSlotId = parseInt(currentSlotId); }
                        var accomodation = {
                            ResourceId: resId,
                            MerchantId: <?php echo $merchant->MerchantId ?>,
                            RatePlanId: jQuery(obj).find("select.pricetypes").val(),
                            SelectedQt: 1,
                            ExtraServices: []
                        };
                        if(currentSlotId > 0){
                            accomodation.TimeSlotId =currentSlotId;
                            accomodation.TimeSlotStart = currentSlot.find(":selected").data("timeslotstart");
                            accomodation.TimeSlotEnd = currentSlot.find(":selected").data("timeslotend");
                        }

                        if(ddlroom.attr("data-availabilityType")==2){
                            var currentTimeStart = jQuery(obj).find(".selectpickerTimePeriodStart").find("option:selected");
                            var currentTimeEnd= jQuery(obj).find(".selectpickerTimePeriodEnd").find("option:selected");

                            if(currentTimeStart){
                                var currFromDate =jQuery("#ChkAvailibilityFromDateTimePeriod_"+resId);
//                                var mcurrFromDate = moment(currFromDate.val(),"DD/MM/YYYY").format("YYYYMMDD", { forceLength: true, trim: false });
//                                var newValStart = moment(bookingfor.pad(currentTimeStart.attr("data-TimeMinStart"),6), "HHmmss");
//                                var newValEnd = moment(bookingfor.pad(currentTimeEnd.attr("data-TimeMinEnd"),6), "HHmmss");
//                                var duration = moment.duration(newValEnd.diff(newValStart));
//                                accomodation.CheckInTime = mcurrFromDate+bookingfor.pad(currentTimeStart.attr("data-TimeMinStart"),6);
//                                accomodation.TimeDuration = duration.asMinutes();
		var newValStart = new Date(1,1,1);
		var tmpCorrTimeStart = bookingfor.pad(currentTimeStart.attr("data-TimeMinStart"), 6);
		newValStart.setHours(Number(tmpCorrTimeStart.substring(0, 2)),Number(tmpCorrTimeStart.substring(2, 4)),0,0);
		var newValEnd = new Date(1,1,1);
		var tmpCorrTimeEnd = bookingfor.pad(currentTimeEnd.attr("data-TimeMinEnd"), 6);
		newValEnd.setHours(Number(tmpCorrTimeEnd.substring(0, 2)),Number(tmpCorrTimeEnd.substring(2, 4)),0,0);

		var diffMs = (newValEnd - newValStart);
		var duration =  Math.floor((diffMs/1000)/60);
		var mcurrFromDate = bookingfor.convertDateToInt(jQuery(currFromDate).datepicker( "getDate" ));
		var checkInTime = mcurrFromDate + bookingfor.pad(currentTimeStart.attr("data-TimeMinStart"), 6);
                                accomodation.CheckInTime = mcurrFromDate+bookingfor.pad(currentTimeStart.attr("data-TimeMinStart"),6);
                                accomodation.TimeDuration = duration;



                            }
                        }




                        jQuery(".extrasselect-" + i + "-" + resId).each( function( index, element ){
                            var currSelPriceValue = jQuery( this ).val();



                            if(currSelPriceValue != null){
                                currSelPrice = currSelPriceValue.split(":");
                                if(currSelPriceValue != "" && currSelPrice[1] != "0"){
//                                    accomodation.ExtraServices.push({
//                                        Value:currSelPriceValue,
//                                        PriceId: currSelPrice[0],
//                                        CalculatedQt: currSelPrice[1],
//                                        ResourceId: resId });
                                    accomodation.ExtraServices.push({
                                        Value:currSelPriceValue,
                                        PriceId: currSelPrice[0],
                                        CalculatedQt: currSelPrice[1],
                                        ResourceId: resId });
                                }
                            }
                        });

                        Order.Resources.push(accomodation);
                    }

                }
            }
        });

        FirstResourceId = Order.Resources[0].ResourceId;
        Order.SearchModel = jQuery('#calculatorForm').serializeObject();
        Order.SearchModel.MerchantId = <?php echo $merchant->MerchantId ?>;
        Order.SearchModel.AdultCount = Order.SearchModel.adults;
        Order.SearchModel.ChildrenCount = Order.SearchModel.children;
        Order.SearchModel.SeniorCount = Order.SearchModel.seniores;
		jQuery('#frm-order').html('');
        
		if (CartMultimerchantEnabled && jQuery('.bookingfor-shopping-cart').length )
        {
            jQuery('#frm-order').prepend('<input id=\"hdnOrderDataCart\" name=\"hdnOrderData\" type=\"hidden\" value=' + "'" + JSON.stringify(Order) + "'" + '\>');
            jQuery('#frm-order').prepend('<input id=\"hdnBookingType\" name=\"hdnBookingType\" type=\"hidden\" value=' + "'" + jQuery('input[name="bookingType"]').val() + "'" + '\>');
			
            bookingfor.addToCart(jQuery("#divcalculator"));
        }else
        {
            jQuery('#frm-order').prepend('<input id=\"hdnOrderData\" name=\"hdnOrderData\" type=\"hidden\" value=' + "'" + JSON.stringify(Order) + "'" + '\>');
            jQuery('#frm-order').prepend('<input id=\"hdnResourceId\" name=\"hdnResourceId\" type=\"hidden\" value=' + FirstResourceId + '\>');
			bookingfor.waitBlockUI();
            jQuery('#frm-order').submit();

        }

            //jQuery('#frm-order > #hdnOrderData').remove();
        }

//-->
</script>
