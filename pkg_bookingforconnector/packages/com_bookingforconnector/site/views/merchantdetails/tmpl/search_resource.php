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
$cal_img = JURI::base() . 'components/com_bookingforconnector/assets/images/calendaricon.png';
$debugmode = false;
$useragent=$_SERVER['HTTP_USER_AGENT'];
if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){
$numberOfMonth = 1;
}else{
$numberOfMonth = 2;
}
$document     = JFactory::getDocument();
$language     = $document->getLanguage();

$config = $this->config;
$isportal = $config->get('isportal', 1);
$usessl = $config->get('usessl', 0);

$extras = null; // $this->Extras;
$priceTypes = null; //$this->PriceTypes;

$merchant = $this->item;

$checkoutspan = '+1 day';
$startDate = DateTime::createFromFormat('d/m/Y',BFCHelper::getStartDateByMerchantId($merchant->MerchantId));
$endDate = DateTime::createFromFormat('d/m/Y',BFCHelper::getEndDateByMerchantId($merchant->MerchantId));
$startDate2 = clone $startDate;
//$startDate2->modify($checkoutspan);

//$format = BFCHelper::getVar('format');
//$tmpl = BFCHelper::getVar('tmpl');
//$formRoute = JRoute::_('index.php?option=com_bookingforconnector&format='.$format.'&tmpl='.$tmpl.'&view=resource&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName));

//-------------------pagina per i l redirect di tutte le risorse 

$db   = JFactory::getDBO();
$uri  = 'index.php?option=com_bookingforconnector&view=resource';

$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );

$itemId = intval($db->loadResult());

$itemIdMerchant=0;
$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
if($isportal){
  $db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
  $itemIdMerchant = intval($db->loadResult());
}

$currUriMerchant = $uriMerchant. '&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name);

if ($itemIdMerchant<>0)
	$currUriMerchant.='&Itemid='.$itemIdMerchant;

$routeMerchant = JRoute::_($currUriMerchant);

$fromSearch =  BFCHelper::getVar('s','0');
$formRoute = JRoute::_('index.php?option=com_bookingforconnector&format=search&tmpl=component&view=merchantdetails&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name)) ;

$routeInfoRequest = JRoute::_($currUriMerchant.'&layout=contactspopup&tmpl=component');

$gotCalculator = BFCHelper::getBool('calculate');
$checkin = new JDate('now'); 
$checkout = new JDate('now'); 
$paxes = 2;
$paxages = array();
$merchantCategoryId = 0;

$refreshState = isset($this->params['refreshcalc']);


$pars = $this->params;

if (!empty($pars)){

	if (!empty($pars['checkin'])){
		$checkin = new JDate($pars['checkin']->format('Y-m-d')); 		
	}
	if (!empty($pars['checkout'])){
		$checkout = new JDate($pars['checkout']->format('Y-m-d')); 
	}
	if (!empty($pars['paxes'])) {
		$paxes = $pars['paxes'];
	}
	if (!empty($pars['merchantCategoryId'])) {
		$merchantCategoryId = $pars['merchantCategoryId'];
	}
	if (!empty($pars['paxages'])) {
		$paxages = $pars['paxages'];
	}
	if ($pars['checkout'] == null){
		$checkout->modify($checkoutspan); 
	}
}

BFCHelper::setSearchParamsSession($pars);

if ($checkin == $checkout){
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




$dateStringCheckin =  $checkin->format('d/m/Y');
$dateStringCheckout =  $checkout->format('d/m/Y');

$dateStayCheckin = new DateTime();
$dateStayCheckout = new DateTime();


$totalPerson = $nad + $nch + $nse;
//$checkinId = 'calculator_checkin';// uniqid('checkin');
//$checkoutId = 'calculator_checkout'; //uniqid('checkout');

$checkinId = uniqid('checkin');
$checkoutId = uniqid('checkout');


$allStaysToView = array();

$duration = $checkin->diff($checkout);

function pushStay($arr, $resourceid, $resStay, $defaultResource = null) {
	$selected = array_values(array_filter($arr, function($itm) use ($resourceid) {
		return $itm->ResourceId == $resourceid;
	}));
	$index = 0;
	if(count($selected) == 0) {
		$obj = new stdClass();
		$obj->ResourceId = $resourceid;
		if(isset($defaultResource) && $defaultResource->ResourceId == $resourceid) {
			$obj->MinCapacityPaxes = $defaultResource->MinCapacityPaxes;
			$obj->MaxCapacityPaxes = $defaultResource->MaxCapacityPaxes;
			$obj->Name = $defaultResource->Name;
			$obj->MrcCategoryName = $defaultResource->MrcCategoryName;
			$obj->ImageUrl = $defaultResource->ImageUrl;
			$obj->Availability = $defaultResource->Availability;
			$obj->Policy = $resStay->Policy;
		} else {
			$obj->MinCapacityPaxes = $resStay->MinCapacityPaxes;
			$obj->MaxCapacityPaxes = $resStay->MaxCapacityPaxes;
			$obj->Availability = $resStay->Availability;
			$obj->Name = $resStay->ResName;
			$obj->MrcCategoryName = $resStay->MrcCategoryName;
			$obj->ImageUrl = $resStay->ImageUrl;
			$obj->Policy = $resStay->Policy;
		}
		$obj->RatePlans = array();
		//$obj->Policy = $completestay->Policy;
		//$obj->Description = $singleRateplan->Description;
		$arr[] = $obj;
		$index = count($arr) - 1;
	} else {
		$index = array_search($selected[0], $arr);
		//$obj = $selected[0];
	}

	$rt = new stdClass();
	$rt->RatePlanId = $resStay->RatePlanId;
	$rt->Name = $resStay->Name;	
	$rt->RatePlanRefId = isset($resStay->RefId) ? $resStay->RefId : "";	
	$rt->PercentVariation = $resStay->PercentVariation;	
	$rt->TotalPrice = (float)$resStay->SuggestedStay->TotalPrice;
	$rt->TotalPriceString = BFCHelper::priceFormat((float)$resStay->SuggestedStay->TotalPrice);
	$rt->CalculatedPricesDetails = $resStay->CalculatedPricesDetails;
	$rt->SelectablePrices = $resStay->SelectablePrices;
	$rt->Variations = $resStay->Variations;
	$rt->Days = $resStay->SuggestedStay->Days;
	$rt->SimpleDiscountIds = implode(',', $resStay->SimpleDiscountIds);	
	$rt->IsBookable=$resStay->IsBookable;
	if(!empty($resStay->SuggestedStay->DiscountedPrice)){
		$rt->DiscountedPriceString = BFCHelper::priceFormat((float)$resStay->SuggestedStay->DiscountedPrice);
		$rt->DiscountedPrice = (float)$resStay->SuggestedStay->DiscountedPrice;
	}
	
	$arr[$index]->RatePlans[] = $rt;
	
	return $arr;
}

$eecstays = array();

foreach($this->allstays as $rst) {
	$allStaysToView = pushStay($allStaysToView, $rst->ResourceId, $rst);
}
$showChildrenagesmsg = isset($_REQUEST['showmsgchildage']) ? $_REQUEST['showmsgchildage'] : 0;
$btnSearchclass=" not-active"; 
if(empty($fromSearch)){
	$btnSearchclass=""; 
}

?>
<script type="text/javascript">
	var allStays = <?php echo json_encode($allStaysToView); ?>;
</script>
<br />
	<!-- fields -->
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
										//$checkintext = '"<div class=\'checkinBooking\'><div class=\'day\'> <span>'.$checkin->format("d").'</span> </div><div class=\'monthyear\'><p>'.$checkin->format("M").' '.$checkin->format("Y").'</p></div><img src=\''.$cal_img.'\'></div>"'; 
										$checkintext = '"<div class=\'buttoncalendar checkinBooking\'><div class=\'dateone day\'><span>'.$checkin->format("d").'</span></div><div class=\'dateone daterwo monthyear\'><p>'.$checkin->format("D").'<br />'.$checkin->format("M").' '.$checkin->format("Y").'  </p></div></div>"';
									?>					
								</div>
								<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6 <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COLSMALL ?>6" id="calcheckout">
									<span class="fieldLabel"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_CHECKOUT') ?>:</span>
									<div class="lastdate">
									<input type="hidden" name="checkout" value="<?php echo $checkout->format('d/m/Y'); ?>" id="<?php echo $checkoutId; ?>" readonly="readonly"/>
									</div>
									<?php 
			//							$checkouttext = '"<div class=\'checkoutBooking\'><div class=\'day\'> <span>'.$checkout->format("d").'</span> </div><div class=\'monthyear\'><p>'.$checkout->format("M").' '.$checkout->format("Y").'</p></div><img src=\''.$cal_img.'\'></div>"'; 
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
									<a href="javascript:calculateQuote()" id="calculateButton" class="calculateButton3  <?php echo $btnSearchclass ?>" ><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_CALCULATE') ?> </a>
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
			<input name="refreshcalc" type="hidden" value="<?php echo $refreshState ?>" />
			<input name="pricetype" type="hidden" value="" />
			<input name="variationPlanId" type="hidden" value="" />
			<input name="s" type="hidden" value="1" />
			<input name="state" type="hidden" value="" />
				<input type="hidden" value="0" name="showmsgchildage" id="showmsgchildagecalculator"/>
				<div class="hide" id="bfi_childrenagesmsgcalculator">
					<div class="pull-right" style="cursor:pointer;color:red">&nbsp;<i class="fa fa-times-circle" aria-hidden="true" onclick="jQuery('#bfi_lblchildrenagescalculator').popover('destroy');"></i></div>
					<?php echo sprintf(JTEXT::_('MOD_BOOKINGFORSEARCH_CHILDRENAGESMSG'),COM_BOOKINGFORCONNECTOR_CHILDRENSAGE) ?>
				</div>
		</form>		
<!-- RESULT -->	
<?php if(!empty($allStaysToView )): ?>

	<div class="table-responsive">
		<table class="table  table-bordered" style="margin-top: 20px;">
			<thead>
				<tr>
					<th class="resourceimage"><?php echo JText::_('COM_BOOKINGFORCONNECTOR_ORDERS_VIEW_RESOURCE') ?></th>
					<th><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_FEATURES_PAXES') ?></th>
					<th><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CONDITIONS') ?></th>
					<th><?php echo sprintf(JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_PRICEDAYS'),$duration->format('%a')) ?></th>
					<th><?php echo JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_BOOKHEADER') ?></th>
				</tr>
			</thead>
			<tbody>
	
<?php 
$resCount = 0;
$selPriceType=0;

foreach($allStaysToView as $reskey => $res) {
	$completestay = $res->RatePlans[0];

	$stayAvailability = $res->Availability;
		
	$DiscountedPrice = 0;
	$total = 0;
	
	$total = (float)$completestay->TotalPrice;

	

	if(!empty($completestay->DiscountedPrice)){
		$DiscountedPrice = (float)$completestay->DiscountedPrice;
	}
	//$showQuote = ($stay->DiscountedPrice > 0 || $stayAvailability > 0) && ( ($nad + $nch) <= $resource->MaxCapacityPaxes && ($nad + $nch) >= $resource->MinCapacityPaxes );
	//$showQuote = ($DiscountedPrice > 0 && $stayAvailability > 0) && ( ($totalPerson) <= $res->MaxCapacityPaxes && ($totalPerson) >= $res->MinCapacityPaxes )  && $dateStayCheckin == $dateStringCheckin && $dateStayCheckout == $dateStringCheckout;
	
	$showQuote = ($DiscountedPrice > 0 && $stayAvailability > 0);
	$totalDiscounted = $DiscountedPrice;
	//$totalWithVariation = $this->totalWithVariation;
	$totalWithVariation = $totalDiscounted;

	// data-resource=" echo $res->ResourceId;" data-rateplan=""
	$selRate = count(array_filter($res->RatePlans, function($itm) use($selPriceType) {
		return $itm->RatePlanId == $selPriceType;
	})) > 0 ? $selPriceType : $res->RatePlans[0]->RatePlanId;
		
	
	$selRateName = array_filter($res->RatePlans, function($itm) use($selRate) {
		return $itm->RatePlanId == $selRate;
	})[0]->RatePlanRefId;
	if(empty($selRateName)){
		$selRateName = array_filter($res->RatePlans, function($itm) use($selRate) {
			return $itm->RatePlanId == $selRate;
		})[0]->Name;
	}
	
	$eecstay = new stdClass;
	$eecstay->id = "" . $res->ResourceId . " - Resource";
	$eecstay->name = $res->Name;
	$eecstay->category = $merchant->MainCategoryName;
	$eecstay->brand = $merchant->Name;
	$eecstay->variant = strtoupper($selRateName);
	$eecstay->position = $reskey;
	$eecstays[] = $eecstay;
	
	
	
	$currUriresource = $uri.'&resourceId=' . $res->ResourceId . ':' . BFCHelper::getSlug($res->Name);

//	$formRouteSingle = JRoute::_('index.php?option=com_bookingforconnector&view=resource&resourceId=' . $res->ResourceId . ':' . BFCHelper::getSlug($res->Name));
	if ($itemId<>0){
		$currUriresource.='&Itemid='.$itemId;
	}
	
	$formRouteSingle= JRoute::_($currUriresource);
	$formRouteBook = $formRouteSingle;
	$eecAddToCart = 0;
	
	$selState= "optionalPackages";

	if(empty($completestay->SelectablePrices)){
		$eecAddToCart = 1;
		$selState= "booking";
		$formRouteBook = JRoute::_($currUriresource.'&layout=form');
		if($usessl){
			$formRouteBook = JRoute::_($currUriresource.'&layout=form',true,1);
		}
	}
//	$formRouteBook = JRoute::_($currUriresource .'&layout=form');
//	if($usessl){
//		$formRouteBook = JRoute::_($currUriresource .'&layout=form',true,1);
//	}
	
//	$isOffers= ($totalDiscounted < $total);
	$offersClass="";
//	if($isOffers){
//		$offersClass="offerslist";
//	}
//$CalculatedPricesDetails = json_decode($completestay->CalculatedPricesString);
//$SelectablePrices = json_decode($completestay->CalculablePricesString);

//echo "<pre>SelectablePrices:";
//echo print_r($completestay->SelectablePrices);
//echo "</pre>";

	$IsBookable = $completestay->IsBookable;
	$btnText = JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_BUTTON');
	$btnClass = "";
	if ($IsBookable){
		$btnText = JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_BUTTON_HTTPS');
		$btnClass = "com_bookingforconnector-bookable";
	}


	?>
		<?php if ($showQuote):?>
				<tr data-id="<?php echo $res->ResourceId;?>">
					<td class="resourceimage tab">
						<?php 
						$resourceImageUrl = Juri::root() . "components/com_bookingforconnector/assets/images/defaults/default-s6.jpeg";
						if(!empty($res->ImageUrl)){
							$resourceImageUrl = BFCHelper::getImageUrlResized('resources', $res->ImageUrl,'small');	
						}
						?>
						<a href="<?php echo $formRouteSingle;?>"><img src="<?php echo $resourceImageUrl; ?>"></a>
						<span class="com_bookingforconnector_resourcelist-resourcename <?php echo $offersClass ?>"><i class="fa fa-check-circle" aria-hidden="true"></i> <a href="<?php echo $formRouteSingle;?>"><?php echo $res->Name; ?></a></span>
					</td>
					<td class="resourcepaxes tab" style="text-align: center;">
						<div class="com_bookingforconnector-search-resource-paxes com_bookingforconnector-item-secondary-paxes" style="padding-top: 0;font-size:16px;">
							<i class="fa fa-user"></i>
							<?php if ($res->MinCapacityPaxes == $res->MaxCapacityPaxes):?>
										   <?php echo  $res->MaxCapacityPaxes ?>
							<?php else: ?>
										   <?php echo  $res->MinCapacityPaxes ?>-<?php echo  $res->MaxCapacityPaxes ?>
							<?php endif; ?>
						</div>
					</td>
					<td class="resourcepricetypes tab" style="text-align: center;">
						<div class="com_bookingforconnector_merchantdetails-resource-pricetypes">
							<?php
							
							$priceTypes = array();
							foreach ($res->RatePlans as $ratePlan) {
								$type = new stdClass;
								$type->Type = $ratePlan->RatePlanId;
								$type->Name = $ratePlan->Name;
								$type->attr = array(
									'data-refid' => $ratePlan->RatePlanRefId
								);
								$priceTypes[] = $type;
							}

							$classselect = (count($priceTypes) >1)? '': 'hide';
								?>
								<?php echo JHtmlSelect::genericlist($priceTypes, 'pricetypeselected', array( 
									'list.select' => $selRate, 
									'option.key' => 'Type', 
									'option.text' => 'Name', 
									'option.attr' => 'attr', 
									'list.attr' => array( 
										'class' => $classselect, 
										'data-resource' => $res->ResourceId,
										'data-resourcename' => $res->Name,
										'onchange' => 'changePriceType(jQuery,this);',
										'data-categoryname' => $merchant->MainCategoryName
									)
								)); ?>
						</div>
						<ul style="padding:0px;padding-left:10px;margin:0;">
							<?php if(!empty($res->Policy)): ?>
								<li style="font-size: 12px; text-align: left;text-indent: -4px;"><?php echo $res->Policy ?></li>
							<?php endif;?>
						</ul>
					</td>
					<td class="resourcetotal tab">
					   <?php if( $completestay->DiscountedPrice > 0) :?><!-- No disponibile -->

							<div class="com_bookingforconnector_merchantdetails-resource-stay com_bookingforconnector_resource-quote">
								<div class="com_bookingforconnector_resource-quote-desc">
									<div class="com_bookingforconnector_merchantdetails-resource-stay-price fullquote" style="text-align:center;">
										<span class="com_bookingforconnector_resourcelist_strikethrough <?php echo $totalDiscounted >= $total ? "notvars" : "" ?>" style="display:none;">
											<span class="com_bookingforconnector_merchantdetails-resource-stay-discount gray-highlight">&euro; <?php echo  BFCHelper::priceFormat($total) ?></span>
										</span>
										<span class="com_bookingforconnector_merchantdetails-resourcelist-stay-total">&euro; <?php echo  BFCHelper::priceFormat($totalDiscounted) ?></span>
									</div>
								</div>
							</div>
						<?php else:?>
							<strong><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_NORESULT') ?></strong>
						<?php endif;?>
						<div class="com_bookingforconnector_merchantdetails-resource-stay-price originalquote" style="text-align: center; <?php echo $totalDiscounted >= $total ? "display:none;" : ""; ?>">
							<span class="com_bookingforconnector_resourcelist_strikethrough <?php echo $totalDiscounted >= $total ? "notvars" : "" ?>"><span class="com_bookingforconnector_merchantdetails-resource-stay-discount gray-highlight">&euro; <?php echo  BFCHelper::priceFormat($total) ?></span></span>
							<span class="com_bookingforconnector_merchantdetails-resourcelist-stay-total" style="display:none;">&euro; <?php echo  BFCHelper::priceFormat($totalDiscounted) ?></span>
							<br />
							<div class="specialoffer variationlabel " style="<?php echo $totalDiscounted >= $total ? "display:none;" : ""; ?>" rel="<?php echo $completestay->SimpleDiscountIds ?>" rel1="<?php echo  $res->ResourceId ?>" >
								<span class="variationlabel_percent"><?php echo $completestay->PercentVariation ?></span>% <?php echo JTEXT::_('MOD_BOOKINGFORSEARCHFILTER_OFFERS') ?> <i class="fa fa-angle-down" aria-hidden="true"></i>
							</div>
						</div>
					</td>
					<td>
						<?php if( $completestay->DiscountedPrice > 0) :?><!-- No disponibile -->
								<a class="com_bookingforconnector-item-secondary-more <?php echo $btnClass ?>" data-formroute="<?php echo $formRouteBook;?>" data-resource="<?php echo $res->ResourceId; ?>" data-rateplan="<?php echo $selRate; ?>" data-variation="" data-resourcename="<?php echo $res->Name; ?>" data-categoryname="<?php echo $merchant->MainCategoryName; ?>" data-brand="<?php echo $merchant->Name?>" href="#" data-state="<?php echo $selState; ?>"><?php echo $btnText ?></a>
						<?php endif;?>
					</td>
				</tr>
				<tr id="troffers<?php echo  $res->ResourceId ?>" style="display:none ;">
					<td colspan="5">
						<div class="divoffers" id="divoffers<?php echo  $res->ResourceId ?>">
								<i class="fa fa-spinner fa-spin fa-fw margin-bottom"></i>
								<span class="sr-only">Loading...</span>
						</div>
					</td>
				</tr>
		<?php endif;?>
	<?php 
		$resCount++;
	 } ?>
				</tbody>
			</table>
			<br />
			<div class="text-center bfsearchfilter"><?php echo JHTML::link(JRoute::_('index.php?option=com_bookingforconnector&layout=resources&view=merchantdetails&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name). "&limitstart=0"), JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_ALL')) ?></div>
	
	<?php if($resCount==0) :?>
		<div class="errorbooking" id="errorbooking">
			<strong><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_NORESULT') ?></strong>
		</div>
	<?php endif;?>
	</div>
<?php else :?>
<?php if(!empty($fromSearch)) { ?>

	<div class="errorbooking" id="errorbooking">
		<strong><?php echo JText::_('COM_BOOKINGFORCONNECTOR_RESOURCE_VIEW_CALCULATOR_NORESULT') ?></strong>
		<br />
		<div class="text-center bfsearchfilter">
			<?php echo JHTML::link(JRoute::_('index.php?option=com_bookingforconnector&layout=resources&view=merchantdetails&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name). "&limitstart=0"), JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_ALL')) ?>
			<a class="boxedpopup com_bookingforconnector_email" href="<?php echo $routeInfoRequest?>" rel="{handler:'iframe'}" ><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_EMAIL') ?></a>
		</div>
	</div>
<?php } ?>
<?php endif;?>
<form action="" id="frm-order" method="post"></form>

<script type="text/javascript">
<!--
var offersLoaded = [];
	function insertCheckinTitleBooking() {
		setTimeout(function() {
			jQuery("#ui-datepicker-div").addClass("checkin");
			jQuery("#ui-datepicker-div").removeClass("checkout");
			var checkindate = jQuery('#<?php echo $checkinId; ?>').val();
			var checkoutdate = jQuery('#<?php echo $checkoutId; ?>').val();

			var d1 = checkindate.split("/");
			var d2 = checkoutdate.split("/");

			var from = new Date(Date.UTC(d1[2], d1[1]-1, d1[0]));
			var to   = new Date(Date.UTC(d2[2], d2[1]-1, d2[0]));

			locale = "en-us";
			diff  = new Date(to - from),
			days  = Math.ceil(diff/1000/60/60/24);
			var strSummaryDays = "(" +days+" <?php echo strtolower (JTEXT::_('MOD_BOOKINGFORSEARCH_NIGHT')) ?>)";
			jQuery('#ui-datepicker-div').attr('data-before','Check-in '+('0' + from.getDate()).slice(-2)+' '+from.toLocaleString(locale, { month: "short" })+' Check-out '+('0' + to.getDate()).slice(-2)+' '+to.toLocaleString(locale, { month: "short" })+' '+d2[2]+ strSummaryDays);
		}, 1);
}
	function insertCheckoutTitleBooking() {
		setTimeout(function() {
			jQuery("#ui-datepicker-div").addClass("checkout");
			jQuery("#ui-datepicker-div").removeClass("checkin");
			var checkindate = jQuery('#<?php echo $checkinId; ?>').val();
			var checkoutdate = jQuery('#<?php echo $checkoutId; ?>').val();

			var d1 = checkindate.split("/");
			var d2 = checkoutdate.split("/");

			var from = new Date(Date.UTC(d1[2], d1[1]-1, d1[0]));
			var to   = new Date(Date.UTC(d2[2], d2[1]-1, d2[0]));

			locale = "en-us";
			diff  = new Date(to - from),
			days  = Math.ceil(diff/1000/60/60/24);
			var strSummaryDays = "(" +days+" <?php echo strtolower (JTEXT::_('MOD_BOOKINGFORSEARCH_NIGHT')) ?>)";
			jQuery('#ui-datepicker-div').attr('data-before','Check-in '+('0' + from.getDate()).slice(-2)+' '+from.toLocaleString(locale, { month: "long" })+' Check-out '+('0' + to.getDate()).slice(-2)+' '+to.toLocaleString(locale, { month: "long" })+' '+d2[2]+strSummaryDays);
		}, 1);
}
	var calculator_checkin = null;
	var calculator_checkout = null;

	jQuery(function($) {
		
		calculator_checkin = function() { $("#<?php echo $checkinId; ?>").datepicker({
			numberOfMonths: parseInt("<?php echo $numberOfMonth;?>")
			,defaultDate: "+2d"
			,dateFormat: "dd/mm/yy"
			, minDate: '<?php echo $startDate->format('d/m/Y') ?>'
			, maxDate: '<?php echo $endDate->format('d/m/Y') ?>'
			, onSelect: function(date) {  $(".ui-datepicker a").removeAttr("href"); checkDate<?php echo $checkinId; ?>(jQuery, jQuery(this), date); dateCalculatorChanged();printChangedDateBooking(date, jQuery(this)); }
			, showOn: 'button'
			, beforeShowDay: function (date) {return closedBooking(date); }
			, beforeShow: function(dateText, inst) { jQuery(this).attr("readonly", true); insertCheckinTitleBooking(); }
			, onChangeMonthYear: function(dateText, inst) { insertCheckinTitleBooking(); }
			, buttonText: <?php echo $checkintext; ?>,
		})};
		
		calculator_checkin();
		
		calculator_checkout = function() { $("#<?php echo $checkoutId; ?>").datepicker({
			numberOfMonths: parseInt("<?php echo $numberOfMonth;?>")
			,defaultDate: "+1d"
			,dateFormat: "dd/mm/yy"
			, orientation: "top"
			, minDate: '<?php echo $startDate2->format('d/m/Y') ?>'
			, maxDate: '<?php echo $endDate->format('d/m/Y') ?>'
			, onSelect: function(date) {  $(".ui-datepicker a").removeAttr("href"); dateCalculatorChanged();printChangedDateBooking(date, jQuery(this));}
			, showOn: 'button'
			, beforeShowDay: function (date) {return closedBooking(date); }
			, beforeShow: function(dateText, inst) { jQuery(this).attr("readonly", true); insertCheckoutTitleBooking(); }
			, onChangeMonthYear: function(dateText, inst) { insertCheckoutTitleBooking(); }
			, buttonText: <?php echo $checkouttext; ?>,
		})};
		
		calculator_checkout();
		
		$(".com_bookingforconnector_resource-calculatorForm-childrenages").hide();
		$(".com_bookingforconnector_resource-calculatorForm-childrenages select").hide();
		
		checkChildren(<?php echo $nch ?>,<?php echo $showChildrenagesmsg ?>);
		
		$(".com_bookingforconnector_resource-calculatorForm-children select#childrencalculator").change(function() {
			checkChildren($(this).val(),0);
		});

		$(".com_bookingforconnector-item-secondary-more").click(function() {
			changeVariation($, $(this).attr("data-variation"), $(this));
			return false;
		});
		
		
	jQuery(".variationlabel").on("click", function(){
			var show = function(resourceId, text){
						jQuery("#divoffers"+resourceId).empty();
						jQuery("#divoffers"+resourceId).html(text);
			};
			var discountIds = jQuery(this).attr('rel'); 
			var resourceId = jQuery(this).attr('rel1'); 

			if(jQuery("#troffers"+resourceId).is(":visible")){
				jQuery("#troffers"+resourceId).slideUp("slow");
				jQuery(this).find("i").toggleClass("fa-angle-up fa-angle-down");
			  } else {
				jQuery("#troffers"+resourceId).slideDown("slow");
				jQuery(this).find("i").toggleClass("fa-angle-up fa-angle-down");
			  }

			if(!offersLoaded.hasOwnProperty(discountIds))
			{
				getDiscountsAjaxInformations(discountIds,resourceId,show);
			}else{
				show(resourceId,offersLoaded[discountIds]);
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
				name = nl2br(jQuery("<p>" + name + "</p>").text());
				$html += '<p class="title">' + name + '</p>';
				descr = nl2br(jQuery("<p>" + descr + "</p>").text());
				$html += '<p class="description ">' + descr + '</p>';
			});
			offersLoaded[discountIds] = $html;
			fn(obj,$html);
			//jQuery(obj).html($html);
	},'json');

}

	function printChangedDateBooking(date, elem) {
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
		
		jQuery('.checkinBooking').find('.day span').html(day1);
		jQuery('.checkoutBooking').find('.day span').html(day2);
		if (typeof Intl == 'object' && typeof Intl.NumberFormat == 'function') {
			jQuery('.checkinBooking').find('.monthyear p').html(weekday1 + "<br/>" + month1+" "+year1);
			jQuery('.checkoutBooking').find('.monthyear p').html(weekday2 + "<br/>" + month2+" "+year2); 
			jQuery('#bfi_lblchildrenagesatcalculator').html("<?php echo strtolower (JTEXT::_('MOD_BOOKINGFORSEARCH_CHILDRENAGESAT')) ?>" + day2 + " " + month2 + " " + year2);
		} else {
			jQuery('.checkinBooking').find('.monthyear p').html(d1[1]+"/"+d1[2]);  
			jQuery('.checkoutBooking').find('.monthyear p').html(d2[1]+"/"+d2[2]);
			jQuery('#bfi_lblchildrenagesatcalculator').html("<?php echo strtolower (JTEXT::_('MOD_BOOKINGFORSEARCH_CHILDRENAGESAT')) ?>" + day2 + " " + d2[1] + " " + d2[2]);
		} 

	}

	function closedBooking(date) {
		var checkindate = jQuery('#<?php echo $checkinId; ?>').val();
		var checkoutdate = jQuery('#<?php echo $checkoutId; ?>').val();
		var strdate = ("0" + date.getDate()).slice(-2) + "/" + ("0" + (date.getMonth()+1)).slice(-2) + "/" + date.getFullYear();

		var d1 = checkindate.split("/");
		var d2 = checkoutdate.split("/");
		var c = strdate.split("/");

		var from = new Date(d1[2], d1[1]-1, d1[0]);
		var to   = new Date(d2[2], d2[1]-1, d2[0]);
		var check = new Date(c[2], c[1]-1, c[0]);

		arr = [true, ''];  
		if(check.getTime() == from.getTime()) {
			arr = [true, 'date-start-selected', 'date-selected'];
		}
		if(check.getTime() == to.getTime()) {
			arr = [true, 'date-end-selected', 'date-selected'];  
		}
		if(check > from && check < to) {
			arr = [true, 'date-selected', 'date-selected'];
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
					jQuery(this).show();
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

	function changePriceType($, priceType) {
		//debugger;
		var val = parseInt($(priceType).val());
		var resourceId = $(priceType).attr("data-resource");
		if($("#troffers" + resourceId).is(":visible")){
			$("#troffers" + resourceId).slideUp("slow");
			$($(".variationlabel[rel1='" + resourceId + "']")).find("i").toggleClass("fa-angle-up fa-angle-down");
		  }
		  
		if(!$.grep(allStays, function(rs) {
			return rs.ResourceId == parseInt(resourceId);
		}).length) {
			return false;
		}
		var calc = $.grep($.grep(allStays, function(rs) {
			return rs.ResourceId == parseInt(resourceId);
		})[0].RatePlans, function(rt) {
			return rt.RatePlanId == val;
		});
		if(calc.length) {
			var container = $($(priceType).closest("table"));
			container.find("tr[data-id='" + resourceId + "'] .com_bookingforconnector_merchantdetails-resource-stay-discount").html("&euro; " + calc[0].TotalPriceString);
			container.find("tr[data-id='" + resourceId + "'] .com_bookingforconnector_merchantdetails-resourcelist-stay-total").html("&euro; " + calc[0].DiscountedPriceString);
			container.find("tr[data-id='" + resourceId + "'] .com_bookingforconnector-item-secondary-more").attr("data-rateplan", val);
			container.find("tr[data-id='" + resourceId + "'] .originalquote").show();
			container.find("tr[data-id='" + resourceId + "'] .variationlabel").show();
			container.find("tr[data-id='" + resourceId + "'] .variationlabel").attr("rel", calc[0].SimpleDiscountIds);
			//container.find(".fullquote .com_bookingforconnector_resourcelist_strikethrough").show();
			//container.find(".originalquote .com_bookingforconnector_merchantdetails-resourcelist-stay-total").show();
			if(calc[0].DiscountedPrice >= calc[0].TotalPrice) {
				container.find("tr[data-id='" + resourceId + "'] .com_bookingforconnector_resourcelist_strikethrough").addClass("notvars");
				//container.find(".fullquote .com_bookingforconnector_resourcelist_strikethrough").hide();
				container.find("tr[data-id='" + resourceId + "'] .originalquote").hide();
				container.find("tr[data-id='" + resourceId + "'] .variationlabel").hide();
			} else {
				container.find("tr[data-id='" + resourceId + "'] .com_bookingforconnector_resourcelist_strikethrough").removeClass("notvars");
				container.find("tr[data-id='" + resourceId + "'] .variationlabel_percent").html(calc[0].VariationPercent);
				//container.find(".originalquote .com_bookingforconnector_merchantdetails-resourcelist-stay-total").hide();
			}
			<?php if($this->analyticsEnabled): ?>
			var currentItm = {
				"id": resourceId + " - Resource",
				"name": $(priceType).attr("data-resourcename"),
				"category": $(priceType).attr("data-categoryname"),
				"brand": "<?php echo $merchant->Name; ?>",
				"variant": $($(priceType).find("option:selected")).attr("data-refid").toUpperCase(),
			};
			callAnalyticsEEc("addProduct", [currentItm], "changeRate", "Merchant Resources Search List", jQuery(jQuery(priceType).find("option:selected")).attr("data-refid").toUpperCase(), "Resources");
			<?php endif; ?>
		}
		//$('input[name="pricetype"]','#calculatorForm').val(val);
		//$('#calculatorForm').ajaxSubmit(getAjaxOptions());
		//$('#calculatorForm').submit();
		
	}

	function changeVariation($, val, elm) {
//		console.log($(elm));
		var resId = $(elm).attr("data-resource");
		var rateplanId = $(elm).attr("data-rateplan");
		$('input[name="resourceId"]','#calculatorForm').val($(elm).attr("data-resource"));
		$('input[name="pricetype"]','#calculatorForm').val($(elm).attr("data-rateplan"));
		$('input[name="variationPlanId"]','#calculatorForm').val(val);
//		$('input[name="state"]','#calculatorForm').val('optionalPackages');
		$('input[name="state"]','#calculatorForm').val($(elm).attr("data-state"));
		
		<?php if($this->analyticsEnabled): ?>
		var calc = $.grep($.grep(allStays, function(rs) {
			return rs.ResourceId == parseInt($(elm).attr("data-resource"));
		})[0].RatePlans, function(rt) {
			return rt.RatePlanId == parseInt($(elm).attr("data-rateplan"));
		})[0];
		if($(elm).attr("data-state") == "booking") {
			callAnalyticsEEc("addProduct", [{
				"id": jQuery(elm).attr("data-resource") + " - Resource",
				"name": $(elm).attr("data-resourcename"),
				"category": $(elm).attr("data-categoryname"),
				"brand": $(elm).attr("data-brand"),
				"variant": calc.RatePlanRefId,
				"price": calc.DiscountedPrice,
				"quantity": 1
			}], "addToCart", null, null, "Resources");
		}
		<?php endif; ?>
		var Order = { Resources: [], SearchModel: {}, TotalAmount: 0, TotalDiscountedAmount: 0 };
		var accomodation = {
			ResourceId: resId,
			MerchantId: <?php echo $merchant->MerchantId ?>,
			RatePlanId: rateplanId,
			SelectedQt: 1,
			ExtraServices: []
		};
		Order.Resources.push(accomodation);

		FirstResourceId = Order.Resources[0].ResourceId;
        Order.SearchModel = jQuery('#calculatorForm').serializeObject();
        Order.SearchModel.MerchantId = <?php echo $merchant->MerchantId ?>;
        Order.SearchModel.AdultCount = Order.SearchModel.adults;
        Order.SearchModel.ChildrenCount = Order.SearchModel.children;
        Order.SearchModel.SeniorCount = Order.SearchModel.seniores;

		Order.SearchModel.ChildAges = [Order.SearchModel.childages1,Order.SearchModel.childages2,Order.SearchModel.childages3,Order.SearchModel.childages4,Order.SearchModel.childages5];

		jQuery('#frm-order').html('');
//		if (CartMultimerchantEnabled && jQuery('.bookingfor-shopping-cart').length )
//        {
//            jQuery('#frm-order').prepend('<input id=\"hdnOrderDataCart\" name=\"hdnOrderData\" type=\"hidden\" value=' + "'" + JSON.stringify(Order) + "'" + '\>');
//            jQuery('#frm-order').prepend('<input id=\"hdnBookingType\" name=\"hdnBookingType\" type=\"hidden\" value=' + "'" + jQuery('input[name="bookingType"]').val() + "'" + '\>');
//			
//            bookingfor.addToCart(jQuery("#divcalculator"));
//        }else
//        {
            jQuery('#frm-order').prepend('<input id=\"hdnOrderData\" name=\"hdnOrderData\" type=\"hidden\" value=' + "'" + JSON.stringify(Order) + "'" + '\>');
            jQuery('#frm-order').prepend('<input id=\"hdnResourceId\" name=\"hdnResourceId\" type=\"hidden\" value=' + FirstResourceId + '\>');

		msg1 = "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_BOOKINGFORSEARCH_MSG1'); ?>";
		msg2 = "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_BOOKINGFORSEARCH_MSG2'); ?>";
			bookingfor.waitBlockUI(msg1,msg2,null);
            $('#frm-order').attr("action",$(elm).attr("data-formroute"));
			jQuery('#frm-order').submit();

//        }
		
//		$('#calculatorForm').attr("action",$(elm).attr("data-formroute"));
//		msg1 = "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_BOOKINGFORSEARCH_MSG1'); ?>";
//		msg2 = "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_BOOKINGFORSEARCH_MSG2'); ?>";
//		waitBlockUI(msg1,msg2,null);
//		$('#calculatorForm').submit();
		//$('#calculatorForm').ajaxSubmit(getAjaxOptions(function() { showBooking($); }));
	}	

function formatWithLeadingZero(n) {
	return (n > 9 ? n : "0" + n);
}


function checkDate<?php echo $checkinId?>($, obj, selectedDate) {
	instance = obj.data("datepicker");
	date = $.datepicker.parseDate(
			instance.settings.dateFormat ||
			$.datepicker._defaults.dateFormat,
			selectedDate, instance.settings);
	var d = new Date(date);
	d.setDate(d.getDate() + 1);
	$("#<?php echo $checkoutId?>").datepicker("option", "minDate", d);
}

function quoteCalculatorChanged(callback) {
	jQuery('#errorbooking').hide();
	jQuery('input[name="refreshcalc"]').val("1");
	if (countMinAdults()>0)
	{
		jQuery('#calculateButton').removeClass("not-active");
	}else{
		jQuery('#calculateButton').addClass("not-active");
	}
}
function dateCalculatorChanged(callback) {
	jQuery('input[name="refreshcalc"]').val("1");
	if (countMinAdults()>0)
	{
		jQuery('#calculateButton').removeClass("not-active");
	}else{
		jQuery('#calculateButton').addClass("not-active");
	}
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

function getAjaxOptions(callback) {
	// prepare Options Object 
	var options = { 
	    target:     '#calculator',
	    replaceTarget: true, 
	    url:        '<?php echo $formRoute?>', 
	    beforeSend: function() {
	    	//jQuery('#calculator').block();
			msg1 = "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_BOOKINGFORSEARCH_MSG1'); ?>";
			msg2 = "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_BOOKINGFORSEARCH_MSG2'); ?>";
			waitBlock(msg1,msg2,jQuery('#calculator'));
		},
	    success: function() { 
			jQuery('#calculator').unblock();
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

jQuery(function($) {
	// prepare Options Object 
	//var form = $('#calculatorForm');
	
	//form.ajaxForm(getAjaxOptions());
});

<?php if($this->analyticsEnabled): ?>
jQuery(function($) {
	<?php if(count($eecstays)): ?>
	callAnalyticsEEc("addImpression", <?php echo json_encode($eecstays); ?>, "list", "Merchant Resources Search List");
	<?php endif; ?>
});
<?php endif; ?>
<?php if(isset($this->criteoConfig) && !empty($this->criteoConfig) && $this->criteoConfig->enabled): ?>
window.criteo_q = window.criteo_q || []; 
window.criteo_q.push( 
	{ event: "setAccount", account: <?php echo $this->criteoConfig->campaignid ?>}, 
	{ event: "setSiteType", type: "d" }, 
	{ event: "viewSearch", checkin_date: "<?php echo $checkin->format('d/m/Y') ?>", checkout_date: "<?php echo $checkout->format('d/m/Y') ?>"},
	{ event: "setEmail", email: "" }, 
	{ event: "viewItem", item: "<?php echo $this->criteoConfig->merchants[0] ?>" }
);
<?php endif; ?>

//-->

jQuery(document).ready(function() {
//	jQuery("#calculatorForm .checking-container button").attr("disabled","disabled")
    jQuery("#calculatorForm .checking-container .ui-datepicker-trigger").click(function() {
        jQuery(".ui-datepicker-calendar td").click(function() {
            if (jQuery(this).hasClass('ui-state-disabled') == false) {
                jQuery("#calculatorForm .lastdate button.ui-datepicker-trigger").trigger("click");
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
</script>
